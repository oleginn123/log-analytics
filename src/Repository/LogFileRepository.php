<?php

namespace App\Repository;

use App\Entity\LogFile;
use App\Service\Import\Log\LogFile as LogFileDto;
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
        ?callable $getTmpPathCallback = null
    ): LogFileDto {
        $logFile = $this->findOneBy(['path' => $path]);
        if ($logFile === null) {
            $logFile = new LogFile();
            $logFile->setPath($path);
            $logFile->setCurrentPosition(0);
            $logFile->setIsEof(false);

            if ($getTmpPathCallback !== null) {
                $logFile->setTempPath($getTmpPathCallback($path));
            }

            $this->getEntityManager()->persist($logFile);
            $this->getEntityManager()->flush();
        }

        return new LogFileDto(
            $logFile->getPath(),
            $logFile->getTempPath(),
            $logFile->getCurrentPosition(),
            $logFile->isEof()
        );
    }

    public function update(LogFileDto $file): void
    {
        $logFile = $this->findOneBy(['path' => $file->getPath()]);
        if ($logFile === null) {
            throw new \Exception('LogFile entity with path = ' . $file->getPath() .  ' doesn\'t exists');
        }

        $logFile->setPath($file->getPath());
        $logFile->setTempPath($file->getTempPath());
        $logFile->setCurrentPosition($file->getCurrentPosition());
        $logFile->setIsEof($file->isEof());

        $this->getEntityManager()->persist($logFile);
        $this->getEntityManager()->flush();
    }
}
