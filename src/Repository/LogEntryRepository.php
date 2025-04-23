<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\LogEntry;
use App\Repository\LogEntry\CountSearchCriteria;
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

    /**
     * @param LogEntry[] $logEntries
     */
    public function saveMultiple(array $logEntries): void
    {
        foreach ($logEntries as $entry) {
            $this->getEntityManager()->persist($entry);
        }

        $this->getEntityManager()->flush();
    }
}
