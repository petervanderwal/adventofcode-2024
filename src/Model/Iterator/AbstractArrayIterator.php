<?php

declare(strict_types=1);

namespace App\Model\Iterator;

use Traversable;

/**
 * @template TKey
 * @template TValue
 * @extends AbstractIterator<TKey, TValue>
 */
abstract class AbstractArrayIterator extends AbstractIterator
{
    public function getIterator(): Traversable
    {
        yield from $this->toArray();
    }

    public function count(): int
    {
        return count($this->toArray());
    }

    public function isEmpty(): bool
    {
        return empty($this->toArray());
    }
}