<?php

declare(strict_types=1);

namespace App\Model\Iterator;

use Traversable;

class RepeatedIterator extends WrappedIterator
{
    public function __construct(
        iterable $internalIterator,
        public ?int $amountOfRepeats = null,
    ) {
        parent::__construct($internalIterator);
    }

    public function getIterator(): Traversable
    {
        for ($i = 0; $this->amountOfRepeats === null || $i < $this->amountOfRepeats; $i++) {
            yield from parent::getIterator();
        }
    }
}