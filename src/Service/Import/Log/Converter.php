<?php

declare(strict_types=1);

namespace App\Service\Import\Log;

use DateTimeImmutable;

class Converter implements ConverterInterface
{
    private const LOG_DATE_TIME_FORMAT = 'd/M/Y:H:i:s O';

    /**
     * @param string[] $lineData
     */
    public function convert(array $lineData): ?LogEntry
    {
        $timestamp = DateTimeImmutable::createFromFormat(self::LOG_DATE_TIME_FORMAT, $lineData[2]);
        if (!$timestamp) {
            return null;
        }

        return new LogEntry(
            $lineData[1],
            $timestamp,
            $lineData[3],
            $lineData[4]
        );
    }
}
