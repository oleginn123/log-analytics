<?php

declare(strict_types=1);

namespace App\Service\Import\Log;

interface LogEntryInterface
{
    public function setServiceName(string $service_name): static;

    public function getTimestamp(): ?\DateTimeImmutable;

    public function setTimestamp(\DateTimeImmutable $timestamp): static;

    public function setBody(string $body): static;

    public function setCode(string $code): static;
}
