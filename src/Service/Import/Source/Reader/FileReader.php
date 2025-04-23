<?php

declare(strict_types=1);

namespace App\Service\Import\Source\Reader;

final class FileReader implements ReaderInterface
{
    private int $currentPosition;

    /**
     * @param resource $fileHandle
     */
    public function __construct(
        private $fileHandle,
        private readonly int $offset = 0,
        private readonly int $pageSize = 100,
    ) {
        $this->currentPosition = $offset;
    }

    public function getIterator(): \Traversable
    {
        fseek($this->fileHandle, $this->offset);

        $linesCounter = 1;
        while (($line = fgets($this->fileHandle)) !== false
            && $linesCounter <= $this->pageSize
        ) {
            $this->currentPosition += strlen($line);
            ++$linesCounter;

            yield $line;
        }
    }

    public function getCurrentPosition(): int
    {
        return $this->currentPosition;
    }

    public function isEOF(): bool
    {
        return feof($this->fileHandle);
    }

    public function __destruct()
    {
        fclose($this->fileHandle);
    }
}
