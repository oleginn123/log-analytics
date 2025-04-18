<?php

declare(strict_types=1);

namespace App\Service\Import\Log;

interface LogFileRepositoryInterface
{
    public function getByPathOrCreate(
        string $path,
        ?callable $modifyEntity = null
    ): LogFileInterface;

    public function update(LogFileInterface $file): void;
}
