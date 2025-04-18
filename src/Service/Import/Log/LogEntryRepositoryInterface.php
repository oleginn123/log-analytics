<?php

declare(strict_types=1);

namespace App\Service\Import\Log;

interface LogEntryRepositoryInterface
{
    public function isExists(LogEntry $logEntry): bool;

    /**
     * @param LogEntry[] $entries
     */
    public function createEntries(array $entries): void;
}
