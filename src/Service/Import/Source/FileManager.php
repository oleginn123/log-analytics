<?php

declare(strict_types=1);

namespace App\Service\Import\Source;

use Symfony\Component\Filesystem\Filesystem;

class FileManager
{
    public function toTmp(string $filePath): string
    {
        $tmpPath = $this->getTmpFilePath($filePath);

        $filesystem = new Filesystem();
        $filesystem->copy($filePath, $tmpPath);

        return $tmpPath;
    }

    private function getTmpFilePath(string $filePath): string
    {
        return $filePath . '.tmp';
    }
}
