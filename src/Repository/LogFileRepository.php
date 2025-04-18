<?php

namespace App\Repository;

use App\Entity\LogFile;
use App\Service\Import\Log\LogFileInterface;
use App\Service\Import\Log\LogFileRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogFile>
 */
class LogFileRepository extends ServiceEntityRepository implements LogFileRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogFile::class);
    }

    public function getByPathOrCreate(
        string $path,
        ?callable $modifyEntity = null
    ): LogFileInterface {
        $logFile = $this->findOneBy(['path' => $path]);
        if ($logFile === null) {
            $logFile = new LogFile();
            $logFile->setPath($path);
            $logFile->setCurrentPosition(0);
            $logFile->setIsEof(false);

            if ($modifyEntity !== null) {
                $logFile = $modifyEntity($logFile);
            }

            $this->getEntityManager()->persist($logFile);
            $this->getEntityManager()->flush();
        }

        return $logFile;
    }

    public function update(LogFileInterface $file): void
    {
        $this->getEntityManager()->persist($file);
        $this->getEntityManager()->flush();
    }
}
