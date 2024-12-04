<?php

declare(strict_types=1);

namespace App\Model\Shape;

use App\Model\Point;

class Shape
{
    /**
     * @var Point[]
     */
    private array $points;
    private int $width;
    private int $height;

    public function __construct(Point ...$points)
    {
        $this->points = $points;
        if (empty($points)) {
            throw new \InvalidArgumentException('Shape points can\'t be empty', 221217103553);
        }
    }

    public function getPoints(): array
    {
        return $this->points;
    }

    public function getWidth(): int
    {
        if (isset($this->width)) {
            return $this->width;
        }
        return $this->width = $this->calculateDimension(fn (Point $point) => $point->x);
    }

    public function getHeight(): int
    {
        if (isset($this->height)) {
            return $this->height;
        }
        return $this->height = $this->calculateDimension(fn (Point $point) => $point->y);
    }

    private function calculateDimension(callable $axis): int
    {
        $values = array_map($axis, $this->points);
        return max(...$values) - min(...$values) + 1;
    }
}
