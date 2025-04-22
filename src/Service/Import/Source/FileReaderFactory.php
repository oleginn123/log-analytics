<?php

declare(strict_types=1);

namespace App\Service\Import\Source;

use App\Service\Import\Log\LogFile;
use App\Service\Import\LogsImportInterface;
use RuntimeException;

class FileReaderFactory
{
    public function create(
        LogFile $file,
        int $offset,
        int $pageSize = LogsImportInterface::DEFAULT_PAGE_SIZE
    ): FileReader {
        $handle = @fopen($file->getTempPath(), 'rb');
        if ($handle === false) {
            throw new RuntimeException('Could not open file ' . $file->getTempPath() . '.');
        }

        return new FileReader($handle, $offset, $pageSize);
    }
}
