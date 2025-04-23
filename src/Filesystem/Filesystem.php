<?php

declare(strict_types=1);

namespace App\Filesystem;

use App\Service\Import\Source\FileSystemInterface;
use Symfony\Component\Filesystem\Filesystem as CoreFilesystem;

class Filesystem implements FileSystemInterface
{
    public function copy(string $originFile, string $targetFile): void
    {
        $coreFilesystem = new CoreFilesystem();
        $coreFilesystem->copy($originFile, $targetFile);
    }
}
