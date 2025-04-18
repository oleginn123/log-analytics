<?php

namespace App\Repository;

use App\Service\Import\Log\LogEntryInterface;
use App\Service\Import\Log\LogEntryPersistenceInterface;
use App\Service\Import\Log\LogFileInterface;
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
     * @param LogEntryInterface[] $entries
     * @throws Exception
     */
    public function persist(LogFileInterface $file, array $entries): void
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
