<?php

declare(strict_types=1);

namespace App\Service\Import\Log;

use DateTimeImmutable;

class Converter implements ConverterInterface
{
    private const LOG_DATE_TIME_FORMAT = 'd/M/Y:H:i:s O';

    public function __construct(
        private readonly LogEntryRepositoryInterface $entryRepository
    ) {
    }

    public function convert(array $lineData): ?LogEntryInterface
    {
        $logEntry = $this->entryRepository->newEntry();

        $timestamp = DateTimeImmutable::createFromFormat(self::LOG_DATE_TIME_FORMAT, $lineData[2]);
        if (!$timestamp) {
            return null;
        }

        $logEntry->setServiceName($lineData[1]);
        $logEntry->setTimestamp($timestamp);
        $logEntry->setBody($lineData[3]);
        $logEntry->setCode($lineData[4]);

        return $logEntry;
    }
}
