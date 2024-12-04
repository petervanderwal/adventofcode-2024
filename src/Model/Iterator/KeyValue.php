<?php

declare(strict_types=1);

namespace App\Model\Iterator;

/**
 * @template TKey
 * @template TValue
 */
class KeyValue
{
    /**
     * @param TKey $key
     * @param TValue $value
     */
    public function __construct(
        public readonly mixed $key,
        public readonly mixed $value,
    ) {}
}
