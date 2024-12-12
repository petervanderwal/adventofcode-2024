<?php

declare(strict_types=1);

namespace App\Model\Grid;

use App\Model\DirectedPoint;
use App\Model\Direction;
use App\Model\Iterator\AbstractArrayIterator;
use App\Model\Grid;
use App\Model\Point;

class Area extends AbstractArrayIterator
{
    /** @var Point[] */
    private array $points;

    public function __construct(
        public readonly Grid $grid,
        Point ...$points
    ) {
        $this->points = $points;
    }

    /**
     * @return Point[]
     */
    public function getPoints(): array
    {
        return $this->points;
    }

    public function getFirstPoint(): Point
    {
        return $this->points[0];
    }

    public function getFirstValue(): mixed
    {
        return $this->grid->get($this->getFirstPoint());
    }

    public function toArray(): array
    {
        return $this->points;
    }

    public function addPoint(Point $point): static
    {
        $this->points[] = $point;
        return $this;
    }

    public function isBorderArea(): bool
    {
        return $this->has(fn (Point $point) => $this->grid->isBorderPoint($point));
    }

    public function getSize(): int
    {
        return count($this);
    }

    /**
     * @return DirectedPoint[]
     */
    public function getOuterBorder(): array
    {
        $thisPoints = [];
        foreach ($this->points as $point) {
            $thisPoints[(string)$point] = $point;
        }

        $outerBorder = [];
        foreach ($thisPoints as $point) {
            foreach (Direction::straightCases() as $direction) {
                $neighbour = $point->moveDirection($direction);
                if (isset($thisPoints[(string)$neighbour])) {
                    continue;
                }

                $border = new DirectedPoint($direction, $point->x, $point->y, $point->z);
                $borderKey = (string)$border;
                if (!isset($outerBorder[$borderKey])) {
                    $outerBorder[$borderKey] = $border;
                }
            }
        }
        return array_values($outerBorder);
    }
}
