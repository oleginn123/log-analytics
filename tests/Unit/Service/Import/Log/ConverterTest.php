<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Import\Log;

use App\Service\Import\Log\Converter;
use App\Service\Import\Log\LogEntry;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{
    private Converter $converter;

    public function setUp(): void
    {
        parent::setUp();

        $this->converter = new Converter();
    }

    /**
     * @param string[] $lineData
     * @dataProvider convertDataProvider
     */
    public function testConvert(array $lineData, ?LogEntry $expectedResult): void
    {
        $this->assertEquals($expectedResult, $this->converter->convert($lineData));
    }

    public static function convertDataProvider(): array
    {
        return [
            'Correct line data' => [
                [
                    'INVOICE-SERVICE - - [17/Aug/2018:09:23:53 +0000] "POST /invoices HTTP/1.1" 201',
                    'INVOICE-SERVICE',
                    '17/Aug/2018:09:23:53 +0000',
                    'POST /invoices HTTP/1.1',
                    '201'
                ],
                new LogEntry(
                    'INVOICE-SERVICE',
                    DateTimeImmutable::createFromFormat( // @phpstan-ignore-line
                        'd/M/Y:H:i:s O',
                        '17/Aug/2018:09:23:53 +0000'
                    ),
                    'POST /invoices HTTP/1.1',
                    '201'
                )
            ],
            'Incorrect timestamp format' => [
                [
                    'INVOICE-SERVICE - - [17/Aug/2018:09:23:53 +0000] "POST /invoices HTTP/1.1" 201',
                    'INVOICE-SERVICE',
                    '2018-08-17 09:27:00',
                    'POST /invoices HTTP/1.1',
                    '201'
                ],
                null
            ]
        ];
    }
}
