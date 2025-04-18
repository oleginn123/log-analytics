<?php

declare(strict_types=1);

namespace App\Service\Import\Log;

interface LogFileRepositoryInterface
{
    public function getByPathOrCreate(
        string $path,
        ?callable $getTmpPathCallback = null
    ): LogFile;

    public function update(LogFile $file): void;
}
