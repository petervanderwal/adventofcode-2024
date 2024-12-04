<?php

declare(strict_types=1);

namespace App\Model\Iterator;

use Traversable;

/**
 * @template TKey
 * @template TValue
 * @extends AbstractWrappedIterator<TKey, TValue>
 */
class MergeIterator extends AbstractWrappedIterator
{
    public function getIterator(): Traversable
    {
        foreach ($this->internalIterator as $iterator) {
            yield from $iterator;
        }
    }
}
