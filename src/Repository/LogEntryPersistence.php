<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\LogEntry;
use App\Entity\LogFile;
use App\Repository\Exception\NotExistsException;
use App\Service\Import\Log\LogEntry as LogEntryDto;
use App\Service\Import\Log\LogEntryPersistenceInterface;
use App\Service\Import\Log\LogFile as LogFileDto;
use Doctrine\ORM\EntityManagerInterface;

class LogEntryPersistence implements LogEntryPersistenceInterface
{
    public function __construct(
        private readonly LogFileRepository $fileRepository,
        private readonly LogEntryRepository $entryRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param LogEntryDto[] $entries
     *
     * @throws \Exception
     */
    public function persist(LogFileDto $file, array $entries): void
    {
        $connection = $this->entityManager->getConnection();

        $connection->beginTransaction();

        try {
            $this->entryRepository->saveMultiple(
                array_map(
                    function (LogEntryDto $entry) {
                        return (new LogEntry())
                            ->setServiceName($entry->getServiceName())
                            ->setBody($entry->getBody())
                            ->setTimestamp($entry->getTimestamp())
                            ->setCode($entry->getCode());
                    },
                    $entries
                )
            );
            $this->updateFile($file);

            $connection->commit();
        } catch (\Exception $exception) {
            $connection->rollback();

            throw $exception;
        }
    }

    public function getFileByPathOrCreate(string $path, ?callable $getTmpPathCallback = null): LogFileDto
    {
        $logFile = $this->fileRepository->findOneBy(['path' => $path]);
        if (null === $logFile) {
            $logFile = new LogFile();
            $logFile->setPath($path);
            $logFile->setCurrentPosition(0);
            $logFile->setIsEof(false);

            if (null !== $getTmpPathCallback) {
                $logFile->setTempPath($getTmpPathCallback($path));
            }

            $this->fileRepository->save($logFile);
        }

        return new LogFileDto(
            $logFile->getPath() ?? '',
            $logFile->getTempPath() ?? '',
            $logFile->getCurrentPosition() ?? 0,
            $logFile->isEof() ?? false
        );
    }

    public function isEntryExists(LogEntryDto $logEntry): bool
    {
        return null !== $this->entryRepository->findOneBy(['timestamp' => $logEntry->getTimestamp()]);
    }

    private function updateFile(LogFileDto $file): void
    {
        $logFile = $this->fileRepository->findOneBy(['path' => $file->getPath()]);
        if (null === $logFile) {
            throw new NotExistsException('LogFile entity with path = ' . $file->getPath().' doesn\'t exists');
        }

        $logFile->setPath($file->getPath());
        $logFile->setTempPath($file->getTempPath());
        $logFile->setCurrentPosition($file->getCurrentPosition());
        $logFile->setIsEof($file->isEof());

        $this->fileRepository->save($logFile);
    }
}
