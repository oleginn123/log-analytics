<?php

declare(strict_types=1);

namespace App\Service\Import;

use App\Service\Import\Log\LogEntry;
use App\Service\Import\Log\LogEntryPersistenceInterface;
use App\Service\Import\Log\LogFile;
use App\Service\Import\Log\ParserInterface;
use App\Service\Import\Source\FileManager;
use App\Service\Import\Source\Reader\ReaderFactory;
use App\Service\Import\Source\Reader\ReaderInterface;
use Psr\Log\LoggerInterface;

class LogsImport implements LogsImportInterface
{
    public function __construct(
        private readonly LogEntryPersistenceInterface $persistence,
        private readonly ParserInterface $parser,
        private readonly FileManager $fileManager,
        private readonly ReaderFactory $readerFactory,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function importNext(
        string $filePath,
        int $pageSize = self::DEFAULT_PAGE_SIZE,
    ): ImportResult {
        $file = $this->getFile($filePath);

        return $this->doImport(
            $file,
            $file->getCurrentPosition(),
            $pageSize
        );
    }

    public function importPage(
        string $filePath,
        int $offset,
        int $pageSize = self::DEFAULT_PAGE_SIZE,
    ): ImportResult {
        return $this->doImport(
            $this->getFile($filePath),
            $offset,
            $pageSize,
            false
        );
    }

    private function doImport(
        LogFile $file,
        int $offset,
        int $pageSize = self::DEFAULT_PAGE_SIZE,
        bool $isUpdateFileState = true,
    ): ImportResult {
        if ($file->isEof()) {
            $file->setTempPath(
                $this->fileManager->toTmp($file->getPath())
            );
        }

        try {
            $reader = $this->readerFactory->create($file, $offset, $pageSize);

            $toCreate = $this->readNewEntries($reader);

            if ($isUpdateFileState) {
                $file->setCurrentPosition($reader->getCurrentPosition());
                $file->setIsEof($reader->isEOF());
            }

            $this->persistence->persist($file, $toCreate);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());

            return new ImportResult(false);
        }

        return new ImportResult(true, count($toCreate));
    }

    private function getFile(string $filePath): LogFile
    {
        return $this->persistence->getFileByPathOrCreate(
            $filePath,
            function (string $path) {
                return $this->fileManager->toTmp($path);
            }
        );
    }

    /**
     * @return LogEntry[]
     */
    private function readNewEntries(ReaderInterface $reader): array
    {
        $entries = [];
        /** @var string $line */
        foreach ($reader as $line) {
            $logEntry = $this->parser->parseLineAndConvert($line);
            if (null === $logEntry || $this->persistence->isEntryExists($logEntry)) {
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
