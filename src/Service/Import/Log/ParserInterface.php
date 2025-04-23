<?php

declare(strict_types=1);

namespace App\Service\Import\Log;

interface ParserInterface
{
    /**
     * @return string[]|null
     */
    public function parseLine(string $line): ?array;

    public function parseLineAndConvert(string $line): ?LogEntry;
}
