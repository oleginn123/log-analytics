<?php

declare(strict_types=1);

namespace App\Service\Import\Log;

interface ConverterInterface
{
    /**
     * @param string[] $lineData
     */
    public function convert(array $lineData): ?LogEntryInterface;
}
