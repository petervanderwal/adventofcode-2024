<?php

declare(strict_types=1);

namespace App\Model\Iterator;

use Traversable;

/**
 * @template TKey
 * @template TValue
 * @extends IteratorInterface<TKey, TValue>
 */
class GeneratedIterator extends AbstractIterator
{
    /**
     * @var callable
     */
    private $generator;

    public function __construct(callable $generator)
    {
        $this->generator = $generator;
    }

    public function getIterator(): Traversable
    {
        yield from ($this->generator)();
    }
}
