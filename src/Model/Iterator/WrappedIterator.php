<?php

declare(strict_types=1);

namespace App\Model\Iterator;

use Closure;
use Traversable;

/**
 * @template TKey
 * @template TValue
 * @extends AbstractIterator<TKey, TValue>
 */
class WrappedIterator extends AbstractIterator
{
    private readonly mixed $internalIterator;

    /**
     * @param iterable<TKey, TValue>|callable(): Closure<TKey, TValue> $internalIterator
     */
    public function __construct(
        mixed $internalIterator,
    ) {
        if (!$internalIterator instanceof Closure && !is_iterable($internalIterator)) {
            throw new \InvalidArgumentException('$internalIterator must be iterable or a Closure returning an iterable');
        }
        $this->internalIterator = $internalIterator;
    }

    public function getIterator(): Traversable
    {
        yield from ($this->internalIterator instanceof Closure ? ($this->internalIterator)() : $this->internalIterator);
    }
}
