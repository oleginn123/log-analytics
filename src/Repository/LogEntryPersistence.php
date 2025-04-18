<?php

namespace App\Repository;

use App\Service\Import\Log\LogEntry as LogEntryDto;
use App\Service\Import\Log\LogEntryPersistenceInterface;
use App\Service\Import\Log\LogFile as LogFileDto;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class LogEntryPersistence implements LogEntryPersistenceInterface
{
    public function __construct(
        private readonly LogFileRepository $fileRepository,
        private readonly LogEntryRepository $entryRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @param LogEntryDto[] $entries
     * @throws Exception
     */
    public function persist(LogFileDto $file, array $entries): void
    {
        $connection = $this->entityManager->getConnection();

        $connection->beginTransaction();

        try {
            $this->entryRepository->createEntries($entries);
            $this->fileRepository->update($file);

            $connection->commit();
        } catch (Exception $exception) {
            $connection->rollback();

            throw $exception;
        }
    }
}
