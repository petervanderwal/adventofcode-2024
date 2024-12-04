<?php

declare(strict_types=1);

namespace App\Model\Grid;

/**
 * @method Column[]|iterable getIterator()
 */
class ColumnIterator extends AbstractRowColumnGridIterator
{
    public function count(): int
    {
        return $this->grid->getNumberOfColumns();
    }

    protected function getItem(int $index): Column
    {
        return new Column($this->grid, $index);
    }
}