<?php declare(strict_types=1);

namespace App\Service\Import\Source;

use App\Entity\LogEntry;
use DateTimeImmutable;

class Parser
{
    private const PATTERN = '/([A-Za-z0-9_\-]+) - - \[([A-Za-z0-9\/\:\+\ ]+)\] "([A-Za-z0-9\/\:\+\.\ ]+)" ([0-9]+)/';

    public function parseLine(string $line): ?LogEntry
    {
        $matches = [];
        if (preg_match(self::PATTERN, $line, $matches)) {
            $logEntry = new LogEntry();

            $timestamp = DateTimeImmutable::createFromFormat('d/M/Y:H:i:s O', $matches[2]);
            if (!$timestamp) {
                return null;
            }

            $logEntry->setServiceName($matches[1]);
            $logEntry->setTimestamp($timestamp);
            $logEntry->setBody($matches[3]);
            $logEntry->setCode($matches[4]);

            return $logEntry;
        }

        return null;
    }
}
