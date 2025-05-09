<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Import\Source;

use App\Service\Import\Source\FileManager;
use App\Service\Import\Source\FileSystemInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class FileManagerTest extends TestCase
{
    private const FILE_PATH = '/var/www/html/import/logs.log';

    private const TEMP_FILE_PATH = '/var/www/html/import/logs.log.tmp';

    private FileManager $fileManager;

    /**
     * @var FileSystemInterface|MockObject
     */
    private MockObject $filesystemMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->filesystemMock = $this->createMock(FileSystemInterface::class);
        $this->fileManager = new FileManager($this->filesystemMock);
    }

    public function testToTmp(): void
    {
        $this->filesystemMock->expects($this->once())
            ->method('copy')
            ->with(self::FILE_PATH, self::TEMP_FILE_PATH);

        $this->assertEquals(
            self::TEMP_FILE_PATH,
            $this->fileManager->toTmp(self::FILE_PATH)
        );
    }
}
