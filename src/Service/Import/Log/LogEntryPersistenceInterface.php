<?php

declare(strict_types=1);

namespace App\Service\Import\Log;

use Exception;

interface LogEntryPersistenceInterface
{
    /**
     * @param LogEntryInterface[] $entries
     * @throws Exception
     */
    public function persist(LogFileInterface $file, array $entries): void;
}
