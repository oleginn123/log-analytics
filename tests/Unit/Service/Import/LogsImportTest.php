<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Import;

use App\Service\Import\ImportResult;
use App\Service\Import\Log\ConverterInterface;
use App\Service\Import\Log\LogEntry;
use App\Service\Import\Log\LogEntryPersistenceInterface;
use App\Service\Import\Log\LogEntryRepositoryInterface;
use App\Service\Import\Log\LogFile;
use App\Service\Import\Log\LogFileRepositoryInterface;
use App\Service\Import\LogsImport;
use App\Service\Import\Source\FileManager;
use App\Service\Import\Source\Parser;
use App\Service\Import\Source\Reader\FileReader;
use App\Service\Import\Source\Reader\ReaderFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub\ReturnCallback as ReturnCallbackStub;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class LogsImportTest extends TestCase
{
    private const CURRENT_POSITION = 10;

    private const OFFSET = 0;

    private const PAGE_SIZE = 100;

    private const LINE = 'USER-SERVICE - - [17/Aug/2018:09:29:13 +0000] "POST /users HTTP/1.1" 201';

    private const LINE_PARSED = [
        'USER-SERVICE - - [17/Aug/2018:09:29:13 +0000] "POST /users HTTP/1.1" 201',
        'USER-SERVICE',
        '17/Aug/2018:09:29:13 +0000',
        'POST /users HTTP/1.1',
        '201'
    ];

    private const FILE_PATH = '/var/www/html/import/logs.log';

    private const TEMP_FILE_PATH = '/var/www/html/import/logs.log.tmp';

    private const READER_EXCEPTION_MESSAGE = 'Could not open file /var/www/html/import/logs.log.tmp';

    private LogsImport $logsImport;

    /**
     * @var MockObject|LogFileRepositoryInterface
     */
    private MockObject $fileRepositoryMock;

    /**
     * @var MockObject|LogEntryRepositoryInterface
     */
    private MockObject $entryRepositoryMock;

    /**
     * @var MockObject|LogEntryPersistenceInterface
     */
    private MockObject $persistenceMock;

    /**
     * @var MockObject|ConverterInterface
     */
    private MockObject $converterMock;

    /**
     * @var MockObject|FileManager
     */
    private MockObject $fileManagerMock;

    /**
     * @var MockObject|ReaderFactory
     */
    private MockObject $fileReaderFactorMock;

    /**
     * @var MockObject|Parser
     */
    private MockObject $parserMock;

    /**
     * @var MockObject|LoggerInterface
     */
    private MockObject $loggerMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->fileRepositoryMock = $this->createMock(LogFileRepositoryInterface::class);
        $this->entryRepositoryMock = $this->createMock(LogEntryRepositoryInterface::class);
        $this->persistenceMock = $this->createMock(LogEntryPersistenceInterface::class);
        $this->converterMock = $this->createMock(ConverterInterface::class);
        $this->fileManagerMock = $this->createMock(FileManager::class);
        $this->fileReaderFactorMock = $this->createMock(ReaderFactory::class);
        $this->parserMock = $this->createMock(Parser::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->logsImport = new LogsImport(
            $this->fileRepositoryMock,
            $this->entryRepositoryMock,
            $this->persistenceMock,
            $this->converterMock,
            $this->fileManagerMock,
            $this->fileReaderFactorMock,
            $this->parserMock,
            $this->loggerMock
        );
    }

    public function testDoImport(): void
    {
        $fileMock = $this->createMock(LogFile::class);

        $fileMock->expects($this->once())
            ->method('isEof')
            ->willReturn(false);
        $fileReaderMock = $this->createMock(FileReader::class);
        $this->fileReaderFactorMock->expects($this->once())
            ->method('create')
            ->with($fileMock, self::OFFSET, self::PAGE_SIZE)
            ->willReturn($fileReaderMock);

        $logEntryMock = $this->createMock(LogEntry::class);
        $this->setUpMocksForReadNewEntries(
            $fileReaderMock,
            self::LINE,
            self::LINE_PARSED,
            $logEntryMock,
            false,
            false
        );

        $fileReaderMock->expects($this->once())
            ->method('getCurrentPosition')
            ->willReturn(self::CURRENT_POSITION);
        $fileMock->expects($this->once())
            ->method('setCurrentPosition')
            ->with(self::CURRENT_POSITION);

        $fileMock->expects($this->once())
            ->method('setIsEof')
            ->with(false);

        $this->persistenceMock->expects($this->once())
            ->method('persist')
            ->with($fileMock, [$logEntryMock]);

        $result = $this->callDoImport($fileMock, self::OFFSET, self::PAGE_SIZE);

        $this->assertTrue($result->isSuccess());
        $this->assertEquals(1, $result->getCount());
    }

    public function testDoImportNoNewLines(): void
    {
        $fileMock = $this->createMock(LogFile::class);

        $fileMock->expects($this->once())
            ->method('isEof')
            ->willReturn(true);
        $fileMock->expects($this->once())
            ->method('getPath')
            ->willReturn(self::FILE_PATH);
        $this->fileManagerMock->expects($this->once())
            ->method('toTmp')
            ->with(self::FILE_PATH)
            ->willReturn(self::TEMP_FILE_PATH);
        $fileMock->expects($this->once())
            ->method('setTempPath')
            ->with(self::TEMP_FILE_PATH);

        $fileReaderMock = $this->createMock(FileReader::class);
        $this->fileReaderFactorMock->expects($this->once())
            ->method('create')
            ->with($fileMock, self::OFFSET, self::PAGE_SIZE)
            ->willReturn($fileReaderMock);

        $fileReaderMock->expects($this->once())
            ->method('getIterator')
            ->will($this->generate([]));

        $fileReaderMock->expects($this->once())
            ->method('getCurrentPosition')
            ->willReturn(self::OFFSET);
        $fileMock->expects($this->once())
            ->method('setCurrentPosition')
            ->with(self::OFFSET);

        $fileReaderMock->expects($this->once())
            ->method('isEOF')
            ->willReturn(true);
        $fileMock->expects($this->once())
            ->method('setIsEof')
            ->with(true);

        $this->persistenceMock->expects($this->once())
            ->method('persist')
            ->with($fileMock, []);

        $result = $this->callDoImport($fileMock, self::OFFSET, self::PAGE_SIZE);

        $this->assertTrue($result->isSuccess());
        $this->assertEquals(0, $result->getCount());
    }

    public function testDoImportException(): void
    {
        $fileMock = $this->createMock(LogFile::class);

        $fileMock->expects($this->once())
            ->method('isEof')
            ->willReturn(false);
        $this->fileReaderFactorMock->expects($this->once())
            ->method('create')
            ->with($fileMock, self::OFFSET, self::PAGE_SIZE)
            ->willThrowException(new \RuntimeException(self::READER_EXCEPTION_MESSAGE));

        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with(self::READER_EXCEPTION_MESSAGE);

        $result = $this->callDoImport($fileMock, self::OFFSET, self::PAGE_SIZE);

        $this->assertFalse($result->isSuccess());
        $this->assertEquals(0, $result->getCount());
    }

    private function callDoImport(...$arguments): ImportResult // @phpstan-ignore-line
    {
        $reflection = new \ReflectionClass($this->logsImport);

        $doImportMethod = $reflection->getMethod('doImport');
        $doImportMethod->setAccessible(true);

        return $doImportMethod->invokeArgs($this->logsImport, $arguments); // @phpstan-ignore-line
    }

    /**
     * @param string[]|null $parseResult
     */
    private function setUpMocksForReadNewEntries(
        MockObject $fileReaderMock,
        string $line,
        ?array $parseResult,
        ?LogEntry $logEntry,
        bool $isExists,
        bool $isEof
    ): void {
        $fileReaderMock->expects($this->once())
            ->method('getIterator')
            ->will($this->generate([$line]));
        $this->parserMock->expects($this->once())
            ->method('parseLine')
            ->with($line)
            ->willReturn($parseResult);
        if ($parseResult === null) {
            return;
        }

        $this->converterMock->expects($this->once())
            ->method('convert')
            ->with($parseResult)
            ->willReturn($logEntry);
        if ($logEntry === null) {
            return;
        }

        $this->entryRepositoryMock->expects($this->once())
            ->method('isExists')
            ->with($logEntry)
            ->willReturn($isExists);
        if (!$isExists) {
            return;
        }

        $fileReaderMock->expects($this->any())
            ->method('isEOF')
            ->willReturn($isEof);
    }

    /**
     * @param string[] $values
     */
    private function generate(array $values): ReturnCallbackStub
    {
        return $this->returnCallback(
            function () use ($values) {
                foreach ($values as $value) {
                    yield $value;
                }
            }
        );
    }
}
