<?php

declare(strict_types=1);

namespace App\Service\Import\Log;

use Exception;

interface LogEntryPersistenceInterface
{
    /**
     * @param LogEntry[] $entries
     * @throws Exception
     */
    public function persist(LogFile $file, array $entries): void;
}
