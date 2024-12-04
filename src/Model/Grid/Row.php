<?php

declare(strict_types=1);

namespace App\Model\Grid;

use App\Model\Point;

class Row extends AbstractGridRowColumn
{
    public function count(): int
    {
        return $this->grid->getNumberOfColumns();
    }

    protected function getCoordinate(int $index): Point
    {
        return new Point($index, $this->index);
    }
}