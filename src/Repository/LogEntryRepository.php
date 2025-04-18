<?php

namespace App\Repository;

use App\Entity\LogEntry;
use App\Repository\LogEntry\CountSearchCriteria;
use App\Service\Import\Log\LogEntry as LogEntryDto;
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

    public function isExists(LogEntryDto $logEntry): bool
    {
        return $this->findOneBy(['timestamp' => $logEntry->getTimestamp()]) !== null;
    }

    /**
     * @param LogEntryDto[] $entries
     */
    public function createEntries(array $entries): void
    {
        foreach ($entries as $entry) {
            $this->getEntityManager()->persist(
                (new LogEntry())
                    ->setServiceName($entry->getServiceName())
                    ->setBody($entry->getBody())
                    ->setTimestamp($entry->getTimestamp())
                    ->setCode($entry->getCode())
            );
        }

        $this->getEntityManager()->flush();
    }
}
