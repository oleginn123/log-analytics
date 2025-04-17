<?php declare(strict_types=1);

namespace App\Service\Import;

use App\Entity\LogEntry;
use App\Entity\LogFile;
use App\Service\Import\Source\FileManager;
use App\Service\Import\Source\FileReader;
use App\Service\Import\Source\Parser;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class LogsImport implements LogsImportInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ContainerBagInterface $params,
        private readonly FileManager $fileManager,
        private readonly Parser $parser
    ) {
    }

    public function import(
        ?string $filePath = null,
        ?int $offset = null,
        ?int $pageSize = null
    ): ImportResult {
        $filePath = $this->resolveFilePath($filePath);

        $fileRepository = $this->entityManager->getRepository(LogFile::class);
        $logFile = $fileRepository->findOneBy(['path' => $filePath]);
        if ($logFile === null) {
            $tmpFilePath = $this->fileManager->toTmp($filePath);

            $logFile = new LogFile();
            $logFile->setPath($filePath);
            $logFile->setCurrentPosition(0);
            $logFile->setTempPath($tmpFilePath);
            $logFile->setIsEof(false);

            $this->entityManager->persist($logFile);
            $this->entityManager->flush();
        }

        if ($logFile->isEof()) {
            $logFile->setTempPath(
                $this->fileManager->toTmp($filePath)
            );
        }

        if ($offset === null) {
            $offset = $logFile->getCurrentPosition();
        }

        $handle = @fopen($logFile->getTempPath(), 'rb');
        if ($handle === false) {
            throw new RuntimeException('Could not open file ' . $logFile->getTempPath() . '.');
        }

        $reader = new FileReader($handle, $offset, $pageSize);
        $logEntryRepository = $this->entityManager->getRepository(LogEntry::class);

        $count = 0;
        try {
            foreach ($reader as $line) {
                $logEntry = $this->parser->parseLine($line);
                if ($logEntry === null
                    || $logEntryRepository->findOneBy(['timestamp' => $logEntry->getTimestamp()]) !== null
                ) {
                    continue;
                }

                $this->entityManager->persist($logEntry);
                $count++;

                if ($reader->isEOF()) {
                    break;
                }
            }
        } catch (\Exception $exception) {
        }

        $logFile->setCurrentPosition($reader->getCurrentPosition());
        $logFile->setIsEof($reader->isEOF());

        $this->entityManager->persist($logFile);

        $this->entityManager->flush();

        return new ImportResult(true, $count);
    }

    private function resolveFilePath(?string $filePath = null): string
    {
        if ($filePath === null) {
            $filePath = $this->params->get('app.import-logs.file-path');
        }
        if ($filePath === null) {
            throw new \Exception('Unable to resolve log filePath.');
        }

        return $filePath;
    }
}
