<?php

declare(strict_types=1);

namespace App\Model\Shape;

use App\Model\Point;

class CoordinatedShape extends Shape
{
    public function __construct(
        public Point $coordinate,
        Point ...$points
    ) {
        parent::__construct(...$points);
    }

    /**
     * @return Point[]
     */
    public function getCoordinates(): array
    {
        return array_map(
            fn (Point $point) => new Point($point->x + $this->coordinate->x, $point->y + $this->coordinate->y),
            $this->getPoints()
        );
    }
}