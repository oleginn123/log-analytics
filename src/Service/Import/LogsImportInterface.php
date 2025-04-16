<?php declare(strict_types=1);

namespace App\Service\Import;

interface LogsImportInterface
{
    public function import(
        ?string $filePath = null,
        ?int $offset = null,
        ?int $pageSize = null
    ): ImportResult;
}