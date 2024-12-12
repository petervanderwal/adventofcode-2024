<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\Algorithm\ShortestPath\VertexInterface;
use Symfony\Component\String\UnicodeString;

class Point implements VertexInterface
{
    public function __construct(
        public readonly int $x,
        public readonly int $y,
        public readonly ?int $z = null,
    ) {}

    public static function fromString(string|UnicodeString $string, string $separator = ','): static
    {
        $parts = explode($separator, (string)$string);
        return new static((int)$parts[0], (int)$parts[1], isset($parts[2]) ? (int)$parts[2] : null);
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getColumn(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function getRow(): int
    {
        return $this->y;
    }

    public function getZ(): ?int
    {
        return $this->z;
    }

    public function getHeight(): ?int
    {
        return $this->z;
    }

    public function toString(): string
    {
        $result = $this->x . ',' . $this->y;
        if ($this->z !== null) {
            $result .= ',' . $this->z;
        }
        return $result;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function getVertexIdentifier(): string
    {
        return $this->toString();
    }

    public function getDistance(Point $other): float
    {
        $result = sqrt(
            pow($this->x - $other->x, 2)
            + pow($this->y - $other->y, 2)
        );

        if (null !== $diffZ = $this->getDiffZ($other)) {
            $result = sqrt(
                pow($result, 2)
                + pow($diffZ, 2)
            );
        }

        return $result;
    }

    public function getManhattanDistance(Point $other): int
    {
        $result = abs($this->x - $other->x) + abs($this->y - $other->y);
        if (null !== $diffZ = $this->getDiffZ($other)) {
            $result += $diffZ;
        }
        return $result;
    }

    protected function getDiffZ(Point $other): ?int
    {
        if ($this->z === null && $other->z === null) {
            return null;
        }

        if ($this->z === null || $other->z === null) {
            throw new \InvalidArgumentException('Can\'t calculate distance between two points where only 1 of them has a Z position', 231222123004);
        }

        return $this->z - $other->z;
    }

    public function moveX(int $steps): static
    {
        return $this->moveXY($steps, 0);
    }

    public function moveY(int $steps): static
    {
        return $this->moveXY(0, $steps);
    }

    public function moveZ(int $steps): static
    {
        return $this->moveXYZ(0, 0, $steps);
    }

    public function moveXY(int $xSteps, int $ySteps): static
    {
        return $this->moveXYZ($xSteps, $ySteps, null);
    }

    public function moveXYZ(int $xSteps, int $ySteps, ?int $zSteps): static
    {
        $newZ = $this->z;
        if ($zSteps !== null) {
            if ($newZ === null) {
                throw new \InvalidArgumentException('Can\'t move a point without z-coordinate over the z-axis', 231222123638);
            }
            $newZ += $zSteps;
        }

        return $this->getNew($this->x + $xSteps, $this->y + $ySteps, $newZ);
    }

    public function offset(Point $offset): static
    {
        return $this->moveXY($offset->x, $offset->y);
    }

    public function multiply(int $amount): static
    {
        return $this->moveXYZ(
            ($amount - 1) * $this->x,
            ($amount - 1) * $this->y,
            $this->z === null ? null : ($amount - 1) * $this->z,
        );
    }

    protected function getNew(int $x, int $y, ?int $z = null): static
    {
        return new Point($x, $y, $z);
    }

    public function moveDirection(Direction $direction, int $steps = 1): static
    {
        $zSteps = $direction->getZStep() * $steps;
        if ($this->z === null && $zSteps === 0) {
            $zSteps = null;
        }
        return $this->moveXYZ($direction->getXStep() * $steps, $direction->getYStep() * $steps, $zSteps);
    }

    public function isWithinAxis(int $minX, int $maxX, int $minY, int $maxY, ?int $minZ = null, ?int $maxZ = null): bool
    {
        if ($this->x < $minX || $this->x > $maxX || $this->y < $minY || $this->y > $maxY) {
            return false;
        }

        if ($this->z === null) {
            if ($minZ !== null || $maxZ !== null) {
                throw new \InvalidArgumentException('This point has no z-coordinate, can\'t verify it is within a z-axis', 231222124953);
            }
            return true;
        }

        if ($minZ === null || $maxZ === null) {
            throw new \InvalidArgumentException('This point has a z-coordinate but no z-axis bounds are given', 231222125039);
        }
        return $this->z >= $minZ && $this->z <= $maxZ;
    }

    public function normalizeOnAxis(int $minX, int $maxX, int $minY, int $maxY, ?int $minZ = null, ?int $maxZ = null): static
    {
        if ($this->isWithinAxis($minX, $maxX, $minY, $maxY, $minZ, $maxZ)) {
            return $this;
        }
        return $this->getNew(
            self::normalizeOnSingleAxis($this->x, $minX, $maxX),
            self::normalizeOnSingleAxis($this->y, $minY, $maxY),
            $this->z === null ? null : self::normalizeOnSingleAxis($this->z, $minZ, $maxZ),
        );
    }

    private static function normalizeOnSingleAxis(int $current, int $min, int $max): int
    {
        $normalized = ($current - $min) % ($max - $min + 1);
        if ($normalized < 0) {
            $normalized += $max - $min + 1;
        }
        return $min + $normalized;
    }

    public function equals(?Point $other): bool
    {
        if ($other === null) {
            return false;
        }
        return $this->x === $other->x && $this->y === $other->y && $this->z === $other->z;
    }

    public function mirror(Point $mirrorLocation): Point
    {
        return $mirrorLocation->moveXYZ(
            xSteps: $mirrorLocation->x - $this->x,
            ySteps: $mirrorLocation->y - $this->y,
            zSteps: $this->z === null ? null : $mirrorLocation->z - $this->z,
        );
    }

    public function addDirection(Direction $direction): DirectedPoint
    {
        return new DirectedPoint($direction, $this->x, $this->y, $this->z);
    }
}