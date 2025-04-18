<?php

declare(strict_types=1);

namespace App\Service\Import\Log;

interface LogEntryRepositoryInterface
{
    public function newEntry(): LogEntryInterface;

    public function isExists(LogEntryInterface $logEntry): bool;

    /**
     * @param LogEntryInterface[] $entries
     */
    public function createEntries(array $entries): void;
}
