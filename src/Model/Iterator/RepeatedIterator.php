<?php

declare(strict_types=1);

namespace App\Model\Iterator;

use Traversable;

class RepeatedIterator extends AbstractWrappedIterator
{
    public function __construct(
        IteratorInterface $internalIterator,
        public ?int $amountOfRepeats = null,
    ) {
        parent::__construct($internalIterator);
    }

    public function getIterator(): Traversable
    {
        for ($i = 0; $this->amountOfRepeats === null || $i < $this->amountOfRepeats; $i++) {
            yield from $this->internalIterator;
        }
    }
}