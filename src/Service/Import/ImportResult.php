<?php

declare(strict_types=1);

namespace App\Service\Import;

final class ImportResult implements ImportResultInterface
{
    public function __construct(
        private readonly bool $isSuccess = true,
        private readonly int $recordsCount = 0
    ) {
    }

    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }

    public function getCount(): int
    {
        return $this->recordsCount;
    }
}