<?php

declare(strict_types=1);

namespace App\Model\Iterator;

use Traversable;

/**
 * @template TKey
 * @extends AbstractIterator<int, TKey>
 */
class KeyIterator extends AbstractIterator
{
    /**
     * @param AbstractIterator<TKey, mixed> $iterator
     */
    public function __construct(
        protected AbstractIterator $iterator,
    ) {}

    public function getIterator(): Traversable
    {
        foreach ($this->iterator as $key => $item) {
            yield $key;
        }
    }
}
