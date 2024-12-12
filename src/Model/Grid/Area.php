<?php

declare(strict_types=1);

namespace App\Model\Grid;

use App\Model\DirectedPoint;
use App\Model\Direction;
use App\Model\Grid;
use App\Model\Grid\Area\Perimeter;
use App\Model\Iterator\AbstractArrayIterator;
use App\Model\Point;

class Area extends AbstractArrayIterator
{
    /** @var Point[] */
    private array $points;
    private Perimeter $perimeter;

    public function __construct(
        public readonly Grid $grid,
        Point ...$points
    ) {
        foreach ($points as $point) {
            $this->addPoint($point);
        }
    }

    /**
     * @return array<string, Point>
     */
    public function getPoints(): array
    {
        return $this->points;
    }

    public function getFirstPoint(): Point
    {
        return reset($this->points);
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
        $this->points[(string)$point] = $point;
        unset($this->perimeter);
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

    public function getPerimeter(): Perimeter
    {
        if (isset($this->perimeter)) {
            return $this->perimeter;
        }

        $perimeter = [];
        foreach ($this->points as $point) {
            foreach (Direction::straightCases() as $direction) {
                $neighbour = $point->moveDirection($direction);
                if (isset($this->points[(string)$neighbour])) {
                    continue;
                }

                $border = new DirectedPoint($direction, $point->x, $point->y, $point->z);
                $borderKey = (string)$border;
                if (!isset($perimeter[$borderKey])) {
                    $perimeter[$borderKey] = $border;
                }
            }
        }
        return $this->perimeter = new Perimeter(...$perimeter);
    }
}
