<?php

declare(strict_types=1);

namespace App\Model\Grid\Area\Perimeter;

use App\Model\Point;

class PerimeterGridOriginalValue
{
    public function __construct(
        public readonly Point $pointInOriginalGrid,
        public readonly mixed $value,
    ) {
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }
}
