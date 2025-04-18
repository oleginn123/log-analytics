<?php

declare(strict_types=1);

namespace App\Service\Import\Log;

use DateTimeImmutable;

class LogEntry
{
    public function __construct(
        private string $serviceName,
        private DateTimeImmutable $timestamp,
        private string $body,
        private string $code,
    ) {
    }

    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    public function getTimestamp(): DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
