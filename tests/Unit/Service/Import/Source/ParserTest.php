<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Import\Source;

use App\Service\Import\Source\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    private Parser $parser;

    public function setUp(): void
    {
        parent::setUp();

        $this->parser = new Parser();
    }

    /**
     * @param string[] $expectedResult
     * @dataProvider parseLineDataProvider
     */
    public function testParseLine(string $rawLine, ?array $expectedResult): void
    {
        $this->assertEquals($expectedResult, $this->parser->parseLine($rawLine));
    }

    public static function parseLineDataProvider(): array
    {
        return [
            'Correct log entry' => [
                'USER-SERVICE - - [17/Aug/2018:09:29:13 +0000] "POST /users HTTP/1.1" 201',
                [
                    'USER-SERVICE - - [17/Aug/2018:09:29:13 +0000] "POST /users HTTP/1.1" 201',
                    'USER-SERVICE',
                    '17/Aug/2018:09:29:13 +0000',
                    'POST /users HTTP/1.1',
                    '201'
                ]
            ],
            'Incorrect log entry' => [
                'USER-SERVICE - - "POST /users HTTP/1.1" 201',
                null
            ]
        ];
    }
}
