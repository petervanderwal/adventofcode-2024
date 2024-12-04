<?php

declare(strict_types=1);

namespace App\Model\Grid;

use Traversable;

abstract class AbstractRowColumnGridIterator extends AbstractGridIterator
{
    abstract protected function getItem(int $index): AbstractGridRowColumn;

    /**
     * @return Traversable<int, AbstractGridRowColumn>
     * @throws \Exception
     */
    public function getIterator(): Traversable
    {
        yield from parent::getIterator();
    }
}