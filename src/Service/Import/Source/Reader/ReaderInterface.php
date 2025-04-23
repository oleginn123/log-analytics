<?php

declare(strict_types=1);

namespace App\Service\Import\Source\Reader;

use IteratorAggregate;
use Traversable;

/**
 * @extends IteratorAggregate<int, string>
 */
interface ReaderInterface extends IteratorAggregate
{
    public function getIterator(): Traversable;

    public function getCurrentPosition(): int;

    public function isEOF(): bool;
}
