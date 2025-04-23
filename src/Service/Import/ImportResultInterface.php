<?php

declare(strict_types=1);

namespace App\Service\Import;

interface ImportResultInterface
{
    public function isSuccess(): bool;

    public function getCount(): int;
}