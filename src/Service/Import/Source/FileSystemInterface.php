<?php

declare(strict_types=1);

namespace App\Service\Import\Source;

interface FileSystemInterface
{
    public function copy(string $originFile, string $targetFile): void;
}