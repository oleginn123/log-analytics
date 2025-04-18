<?php

namespace App\Repository;

use App\Entity\LogEntry;
use App\Repository\LogEntry\CountSearchCriteria;
use App\Service\Import\Log\LogEntryInterface;
use App\Service\Import\Log\LogEntryRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogEntry>
 */
class LogEntryRepository extends ServiceEntityRepository implements LogEntryRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogEntry::class);
    }

    public function getCount(CountSearchCriteria $searchCriteria): int
    {
        return $this->matching($searchCriteria->toFilterCriteria())
            ->count();
    }

    public function newEntry(): LogEntryInterface
    {
        return new LogEntry();
    }

    public function isExists(LogEntryInterface $logEntry): bool
    {
        if ($logEntry->getTimestamp() === null) {
            return false;
        }

        return $this->findOneBy(['timestamp' => $logEntry->getTimestamp()]) !== null;
    }

    /**
     * @param LogEntryInterface[] $entries
     */
    public function createEntries(array $entries): void
    {
        foreach ($entries as $entry) {
            $this->getEntityManager()->persist($entry);
        }

        $this->getEntityManager()->flush();
    }
}
