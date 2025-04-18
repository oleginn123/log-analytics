<?php

declare(strict_types=1);

namespace App\Service\Import\Log;

interface LogFileInterface
{
    public function getPath(): ?string;

    public function getTempPath(): ?string;

    public function setTempPath(string $temp_path): static;

    public function getCurrentPosition(): ?int;

    public function setCurrentPosition(int $current_position): static;

    public function isEof(): ?bool;

    public function setIsEof(bool $is_eof): static;
}
