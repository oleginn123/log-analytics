<?php

declare(strict_types=1);

namespace App\Service\Import\Log;

class LogFile
{
    public function __construct(
        private string $path,
        private string $tmpPath,
        private int $currentPosition,
        private bool $isEof,
    ) {
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getTempPath(): string
    {
        return $this->tmpPath;
    }

    public function setTempPath(string $tmpPath): static
    {
        $this->tmpPath = $tmpPath;

        return $this;
    }

    public function getCurrentPosition(): int
    {
        return $this->currentPosition;
    }

    public function setCurrentPosition(int $currentPosition): static
    {
        $this->currentPosition = $currentPosition;

        return $this;
    }

    public function isEof(): bool
    {
        return $this->isEof;
    }

    public function setIsEof(bool $isEof): static
    {
        $this->isEof = $isEof;

        return $this;
    }
}
