<?php

declare(strict_types=1);

namespace App\Model\Grid\Area\Perimeter;

use App\Model\DirectedPoint;
use App\Model\Direction;
use InvalidArgumentException;

class PerimeterGridBorderValue
{
    public function __construct(
        public readonly DirectedPoint $directedPoint,
        public readonly bool $isInnerCorner = false,
    ) {
        if ($this->isInnerCorner && $this->directedPoint->direction->isStraightCase()) {
            throw new InvalidArgumentException('Inner corner is not available for this direction', 241212205826);
        }
    }

    public function __toString(): string
    {
        $direction = $this->directedPoint->direction;
        if ($this->isInnerCorner) {
            $direction = $direction->turnAround();
        }
        return match ($direction) {
            Direction::NORTH, Direction::SOUTH => '─',
            Direction::EAST, Direction::WEST => '│',
            Direction::NORTH_EAST => '┐',
            Direction::SOUTH_EAST => '┘',
            Direction::SOUTH_WEST => '└',
            Direction::NORTH_WEST => '┌',
        };
    }
}
