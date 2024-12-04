<?php

declare(strict_types=1);

namespace App\Model\ThreeDModel;

use App\Model\Iterator\AbstractIterator;
use Traversable;

class ThreeDModel extends AbstractIterator
{
    private array $points = [];

    public function set(ThreeDCoordinate $coordinate, mixed $value): static
    {
        $this->points[$coordinate->x][$coordinate->y][$coordinate->z] = $value;
        return $this;
    }

    public function hasCoordinate(ThreeDCoordinate $coordinate): bool
    {
        return array_key_exists($coordinate->z, $this->points[$coordinate->x][$coordinate->y] ?? []);
    }

    public function get(ThreeDCoordinate $coordinate): Block
    {
        if (!$this->hasCoordinate($coordinate)) {
            throw new \OutOfBoundsException(sprintf('Coordinate %s does not exist in 3D model', $coordinate), 221218094227);
        }
        return new Block($this, $coordinate->x, $coordinate->y, $coordinate->z, $this->points[$coordinate->x][$coordinate->y][$coordinate->z]);
    }

    /**
     * @return Traversable|Block[]
     */
    public function getIterator(): Traversable
    {
        foreach ($this->points as $x => $xAxis) {
            foreach ($xAxis as $y => $yAxis) {
                foreach ($yAxis as $z => $value) {
                    yield new Block($this, $x, $y, $z, $value);
                }
            }
        }
    }

    public function getMinCoordinate(): ThreeDCoordinate
    {
        $minX = PHP_INT_MAX;
        $minY = PHP_INT_MAX;
        $minZ = PHP_INT_MAX;
        foreach ($this as $coordinate) {
            $minX = min($minX, $coordinate->x);
            $minY = min($minY, $coordinate->y);
            $minZ = min($minZ, $coordinate->z);
        }
        return new ThreeDCoordinate($minX, $minY, $minZ);
    }

    public function getMaxCoordinate(): ThreeDCoordinate
    {
        $maxX = PHP_INT_MIN;
        $maxY = PHP_INT_MIN;
        $maxZ = PHP_INT_MIN;
        foreach ($this as $coordinate) {
            $maxX = max($maxX, $coordinate->x);
            $maxY = max($maxY, $coordinate->y);
            $maxZ = max($maxZ, $coordinate->z);
        }
        return new ThreeDCoordinate($maxX, $maxY, $maxZ);
    }
}
