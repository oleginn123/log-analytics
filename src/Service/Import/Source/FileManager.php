<?php

declare(strict_types=1);

namespace App\Service\Import\Source;

class FileManager
{
    public function __construct(
        private readonly FileSystemInterface $filesystem,
    ) {
    }

    public function toTmp(string $filePath): string
    {
        $tmpPath = $this->getTmpFilePath($filePath);

        $this->filesystem->copy($filePath, $tmpPath);

        return $tmpPath;
    }

    private function getTmpFilePath(string $filePath): string
    {
        return $filePath.'.tmp';
    }
}
