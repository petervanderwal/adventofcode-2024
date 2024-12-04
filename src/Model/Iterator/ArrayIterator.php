<?php

declare(strict_types=1);

namespace App\Model\Iterator;

/**
 * @template TKey
 * @template TValue
 * @extends AbstractArrayIterator<TKey, TValue>
 */
class ArrayIterator extends AbstractArrayIterator
{
    /**
     * @param array<TKey, TValue> $data
     */
    public function __construct(
        private readonly array $data,
    ) {}

    public function toArray(): array
    {
        return $this->data;
    }
}
