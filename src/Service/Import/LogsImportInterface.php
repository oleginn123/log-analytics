<?php declare(strict_types=1);

namespace App\Service\Import;

interface LogsImportInterface
{
    public const DEFAULT_PAGE_SIZE = 100;

    public function importNext(
        string $filePath,
        int $pageSize = self::DEFAULT_PAGE_SIZE
    ): ImportResult;

    public function importPage(
        string $filePath,
        int $offset,
        int $pageSize = self::DEFAULT_PAGE_SIZE
    ): ImportResult;
}
