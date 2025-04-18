<?php

declare(strict_types=1);

namespace App\Service\Import;

use App\Service\Import\Log\ConverterInterface;
use App\Service\Import\Log\LogEntryPersistenceInterface;
use App\Service\Import\Log\LogEntryRepositoryInterface;
use App\Service\Import\Log\LogFileInterface;
use App\Service\Import\Log\LogFileRepositoryInterface;
use App\Service\Import\Source\FileManager;
use App\Service\Import\Source\FileReader;
use App\Service\Import\Source\Parser;
use Exception;
use Psr\Log\LoggerInterface;
use RuntimeException;

class LogsImport implements LogsImportInterface
{
    public function __construct(
        private readonly LogFileRepositoryInterface $fileRepository,
        private readonly LogEntryRepositoryInterface $entryRepository,
        private readonly LogEntryPersistenceInterface $persistence,
        private readonly ConverterInterface $converter,
        private readonly FileManager $fileManager,
        private readonly Parser $parser,
        private readonly LoggerInterface $logger
    ) {
    }

    public function importNext(
        string $filePath,
        int $pageSize = self::DEFAULT_PAGE_SIZE
    ): ImportResult {
        $file = $this->getFile($filePath);

        return $this->doImport($file, $file->getCurrentPosition(), $pageSize);
    }

    public function importPage(
        string $filePath,
        int $offset,
        int $pageSize = self::DEFAULT_PAGE_SIZE
    ): ImportResult {
        return $this->doImport(
            $this->getFile($filePath),
            $offset,
            $pageSize,
            false
        );
    }

    private function doImport(
        LogFileInterface $file,
        int $offset,
        int $pageSize = self::DEFAULT_PAGE_SIZE,
        bool $isUpdateFileState = true
    ): ImportResult {
        if ($file->isEof()) {
            $file->setTempPath(
                $this->fileManager->toTmp($file->getPath())
            );
        }

        try {
            $reader = $this->getReader($file, $offset, $pageSize);

            $toCreate = $this->readNewEntries($reader);

            if ($isUpdateFileState) {
                $file->setCurrentPosition($reader->getCurrentPosition());
                $file->setIsEof($reader->isEOF());
            }

            $this->persistence->persist($file, $toCreate);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());

            return new ImportResult(false);
        }

        return new ImportResult(true, count($toCreate));
    }

    private function getFile(string $filePath): LogFileInterface
    {
        return $this->fileRepository->getByPathOrCreate(
            $filePath,
            function (LogFileInterface $file) use ($filePath) {
                $file->setTempPath($this->fileManager->toTmp($filePath));

                return $file;
            }
        );
    }

    /**
     * @throws RuntimeException
     */
    private function getReader(
        LogFileInterface $file,
        int $offset,
        int $pageSize = self::DEFAULT_PAGE_SIZE
    ): FileReader {
        $handle = @fopen($file->getTempPath(), 'rb');
        if ($handle === false) {
            throw new RuntimeException('Could not open file ' . $file->getTempPath() . '.');
        }

        return new FileReader($handle, $offset, $pageSize);
    }

    private function readNewEntries(FileReader $reader): array
    {
        $entries = [];
        foreach ($reader as $line) {
            $parsed = $this->parser->parseLine($line);
            if ($parsed === null) {
                continue;
            }

            $logEntry = $this->converter->convert($parsed);
            if ($logEntry === null || $this->entryRepository->isExists($logEntry)) {
                continue;
            }

            $entries[] = $logEntry;

            if ($reader->isEOF()) {
                break;
            }
        }

        return $entries;
    }
}
