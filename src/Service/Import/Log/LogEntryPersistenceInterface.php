<?php

declare(strict_types=1);

namespace App\Service\Import\Log;

interface LogEntryPersistenceInterface
{
    public function getFileByPathOrCreate(
        string $path,
        ?callable $getTmpPathCallback = null,
    ): LogFile;

    /**
     * @param LogEntry[] $entries
     *
     * @throws \Exception
     */
    public function persist(LogFile $file, array $entries): void;

    public function isEntryExists(LogEntry $logEntry): bool;
}
