<?php

namespace App\Entity;

use App\Repository\LogFileRepository;
use App\Service\Import\Log\LogFileInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogFileRepository::class)]
class LogFile implements LogFileInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $path = null;

    #[ORM\Column(length: 255)]
    private ?string $temp_path = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?int $current_position = null;

    #[ORM\Column]
    private ?bool $is_eof = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): string
    {
        return $this->path ?? '';
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getTempPath(): string
    {
        return $this->temp_path ?? '';
    }

    public function setTempPath(string $temp_path): static
    {
        $this->temp_path = $temp_path;

        return $this;
    }

    public function getCurrentPosition(): ?int
    {
        return $this->current_position;
    }

    public function setCurrentPosition(int $current_position): static
    {
        $this->current_position = $current_position;

        return $this;
    }

    public function isEof(): ?bool
    {
        return $this->is_eof;
    }

    public function setIsEof(bool $is_eof): static
    {
        $this->is_eof = $is_eof;

        return $this;
    }
}
