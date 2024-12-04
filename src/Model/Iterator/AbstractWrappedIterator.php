<?php

declare(strict_types=1);

namespace App\Model\Iterator;

/**
 * @template TKey
 * @template TValue
 * @extends AbstractIterator<TKey, TValue>
 */
abstract class AbstractWrappedIterator extends AbstractIterator
{
    /**
     * @param IteratorInterface<TKey, TValue> $internalIterator
     */
    public function __construct(
        public readonly IteratorInterface $internalIterator,
    ) {}
}
