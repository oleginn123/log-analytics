<?php

declare(strict_types=1);

namespace App\Service\Import\Source;

use App\Service\Import\Log\LogEntry;
use App\Service\Import\Log\ParserInterface;

final class Parser implements ParserInterface
{
    private const PATTERN = '/([A-Za-z0-9_\-]+) - - \[([A-Za-z0-9\/\:\+\ ]+)\] "([A-Za-z0-9\/\:\+\.\ ]+)" ([0-9]+)/';

    private const LOG_DATE_TIME_FORMAT = 'd/M/Y:H:i:s O';

    /**
     * @return string[]|null
     */
    public function parseLine(string $line): ?array
    {
        $matches = [];
        if (preg_match(self::PATTERN, $line, $matches)) {
            return $matches;
        }

        return null;
    }

    public function parseLineAndConvert(string $line): ?LogEntry
    {
        $lineData = $this->parseLine($line);
        if (null === $lineData) {
            return null;
        }

        $timestamp = \DateTimeImmutable::createFromFormat(self::LOG_DATE_TIME_FORMAT, $lineData[2]);
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
