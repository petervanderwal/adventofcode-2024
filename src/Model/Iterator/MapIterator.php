<?php

declare(strict_types=1);

namespace App\Model\Iterator;

use Traversable;

/**
 * @template TKey
 * @template TIteratorValue
 * @template TMappedValue
 * @extends AbstractIterator<TKey, TMappedValue>
 */
class MapIterator extends AbstractIterator
{
    /**
     * @var callable(TIteratorValue, TKey): TMappedValue
     */
    protected mixed $callback;

    /**
     * @param AbstractIterator<TKey, TIteratorValue> $iterator
     * @param callable(TIteratorValue, TKey): TMappedValue $callback
     */
    public function __construct(
        protected AbstractIterator $iterator,
        callable $callback
    ) {
        $this->callback = $callback;
    }

    /**
     * @return Traversable<TKey, TMappedValue>
     */
    public function getIterator(): Traversable
    {
        foreach ($this->iterator as $key => $item) {
            yield $key => ($this->callback)($item, $key);
        }
    }
}