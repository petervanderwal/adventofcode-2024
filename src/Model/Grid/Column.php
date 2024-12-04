<?php

declare(strict_types=1);

namespace App\Model\Grid;

use App\Model\Point;

class Column extends AbstractGridRowColumn
{
    public function count(): int
    {
        return $this->grid->getNumberOfRows();
    }

    protected function getCoordinate(int $index): Point
    {
        return new Point($this->index, $index);
    }
}