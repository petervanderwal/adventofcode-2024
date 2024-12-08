<?php

declare(strict_types=1);

namespace App\Model\Iterator;

use Traversable;

/**
 * @template TKey
 * @template TValue
 * @extends WrappedIterator<TKey, TValue>
 */
class MergeIterator extends WrappedIterator
{
    public function getIterator(): Traversable
    {
        foreach (parent::getIterator() as $iterator) {
            yield from $iterator;
        }
    }
}
