<?php

declare(strict_types=1);

namespace App\Tests\App\Controller;

use App\Repository\LogEntry\CountSearchCriteria;
use App\Repository\LogEntryRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LogApiControllerTest extends WebTestCase
{
    private const COUNT = 10;

    /**
     * @var MockObject|LogEntryRepositoryInterface
     */
    private MockObject $repositoryMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = $this->createMock(LogEntryRepositoryInterface::class);
    }

    /**
     * @dataProvider getCountDataProvider
     */
    public function testGetCount(string $query, CountSearchCriteria $expectedCriteria): void
    {
        $client = static::createClient();

        static::getContainer()
            ->set(LogEntryRepositoryInterface::class, $this->repositoryMock);

        $this->repositoryMock->expects($this->once())
            ->method('getCount')
            ->with($expectedCriteria)
            ->willReturn(self::COUNT);

        $client->jsonRequest('GET', '/api/count' . $query);

        $content = $client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($content);

        $json = json_decode($content, true);
        $this->assertEquals(
            ['counter' => self::COUNT],
            $json
        );
    }

    public static function getCountDataProvider(): array
    {
        return [
            'No filters' => [
                '',
                new CountSearchCriteria()
            ],
            'Filter by serviceNames' => [
                '?' . http_build_query(
                    [
                        'serviceNames' => ['INVOICE-SERVICE', 'INVOICE-SERVICE']
                    ]
                ),
                new CountSearchCriteria(['INVOICE-SERVICE', 'INVOICE-SERVICE'])
            ],
            'Filter by date range' => [
                '?' . http_build_query(
                    [
                        'startDate' => '2018-08-17 09:23:00',
                        'endDate' => '2018-08-17 09:27:00'
                    ]
                ),
                new CountSearchCriteria(null, '2018-08-17 09:23:00', '2018-08-17 09:27:00')
            ],
            'Filter by status code' => [
                '?' . http_build_query(
                    [
                        'statusCode' => '201'
                    ]
                ),
                new CountSearchCriteria(null, null, null, 201)
            ]
        ];
    }
}
