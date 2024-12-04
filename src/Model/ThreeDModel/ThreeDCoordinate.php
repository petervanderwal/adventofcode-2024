<?php

declare(strict_types=1);

namespace App\Model\ThreeDModel;

class ThreeDCoordinate
{
    public function __construct(
        public readonly int $x,
        public readonly int $y,
        public readonly int $z,
    ) {
    }

    public function __toString(): string
    {
        return sprintf('%d,%d,%d', $this->x, $this->y, $this->z);
    }

    /**
     * @return ThreeDCoordinate[]
     */
    public function getNeighbours(): array
    {
        return [
            new self($this->x - 1, $this->y, $this->z),
            new self($this->x + 1, $this->y, $this->z),
            new self($this->x, $this->y - 1, $this->z),
            new self($this->x, $this->y + 1, $this->z),
            new self($this->x, $this->y, $this->z - 1),
            new self($this->x, $this->y, $this->z + 1),
        ];
    }

    public function inBounds(ThreeDCoordinate $minCoordinate, ThreeDCoordinate $maxCoordinate): bool
    {
        return $this->x >= $minCoordinate->x && $this->x <= $maxCoordinate->x
            && $this->y >= $minCoordinate->y && $this->y <= $maxCoordinate->y
            && $this->z >= $minCoordinate->z && $this->z <= $maxCoordinate->z;
    }
}
