<?php

declare(strict_types=1);

namespace App\Model\Iterator;

use Traversable;

/**
 * @template TKey
 * @template TValue
 * @extends AbstractIterator<TKey, TValue>
 */
class WhereIterator extends AbstractIterator
{
    /**
     * @var callable(TValue, TKey): bool
     */
    protected mixed $where;

    /**
     * @param AbstractIterator<TKey, TValue> $iterator
     * @param callable(TValue, TKey): bool $where
     */
    public function __construct(
        protected AbstractIterator $iterator,
        callable $where
    ) {
        $this->where = $where;
    }

    public function getIterator(): Traversable
    {
        $where = $this->where;
        foreach ($this->iterator as $key => $item) {
            if ($where($item, $key)) {
                yield $key => $item;
            }
        }
    }
}