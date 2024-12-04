<?php

declare(strict_types=1);

namespace App\Model\Grid;

class RowIterator extends AbstractRowColumnGridIterator
{
    public function count(): int
    {
        return $this->grid->getNumberOfRows();
    }

    protected function getItem(int $index): Row
    {
        return new Row($this->grid, $index);
    }
}