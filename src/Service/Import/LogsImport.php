<?php declare(strict_types=1);

namespace App\Service\Import;

use App\Service\Import\Source\FileReader;

class LogsImport implements LogsImportInterface
{
    public function import(
        ?string $filePath = null,
        ?int $offset = null,
        ?int $pageSize = null
    ): ImportResult {
        return new ImportResult();
    }
}