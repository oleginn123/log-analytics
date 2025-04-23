<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\LogFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogFile>
 */
class LogFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogFile::class);
    }

    public function save(LogFile $logFile): LogFile
    {
        $this->getEntityManager()->persist($logFile);
        $this->getEntityManager()->flush();

        return $logFile;
    }
}
