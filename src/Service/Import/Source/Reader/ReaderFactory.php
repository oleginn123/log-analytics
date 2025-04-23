<?php

declare(strict_types=1);

namespace App\Service\Import\Source\Reader;

use App\Service\Import\Exception\ReaderException;
use App\Service\Import\Log\LogFile;
use App\Service\Import\LogsImportInterface;

class ReaderFactory
{
    /**
     * @throws ReaderException
     */
    public function create(
        LogFile $file,
        int $offset,
        int $pageSize = LogsImportInterface::DEFAULT_PAGE_SIZE,
    ): ReaderInterface {
        $handle = fopen($file->getTempPath(), 'rb');
        if (false === $handle) {
            throw new ReaderException('Could not open file '.$file->getTempPath().'.');
        }

        return new FileReader($handle, $offset, $pageSize);
    }
}
