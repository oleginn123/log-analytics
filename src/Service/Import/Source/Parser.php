<?php

declare(strict_types=1);

namespace App\Service\Import\Source;

class Parser
{
    private const PATTERN = '/([A-Za-z0-9_\-]+) - - \[([A-Za-z0-9\/\:\+\ ]+)\] "([A-Za-z0-9\/\:\+\.\ ]+)" ([0-9]+)/';

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
}
