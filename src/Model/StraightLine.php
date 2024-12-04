<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\Iterator\AbstractIterator;
use Symfony\Component\String\UnicodeString;
use Traversable;

class StraightLine extends AbstractIterator
{
    public function __construct(
        public readonly Point $from,
        public readonly Point $to,
    ) {
        $diffX = abs($this->from->x - $this->to->x);
        $diffY = abs($this->from->y - $this->to->y);
        if ($diffX !== 0 && $diffY !== 0 && $diffX !== $diffY) {
            throw new \InvalidArgumentException(sprintf('"%s" isn\'t a straight line', $this), 230923131829);
        }
    }

    public static function fromString(
        string|UnicodeString $string,
        string $pointSeparator = ' -> ',
        string $coordinateSeparator = ','
    ): static {
        [$from, $to] = explode($pointSeparator, (string)$string);
        return new static(
            Point::fromString($from, $coordinateSeparator),
            Point::fromString($to, $coordinateSeparator),
        );
    }

    public function getMaxX(): int
    {
        return max($this->from->x, $this->to->x);
    }

    public function getMaxY(): int
    {
        return max($this->from->y, $this->to->y);
    }

    public function __toString(): string
    {
        return $this->from . ' -> ' . $this->to;
    }

    /**
     * @return Point[]|Traversable
     */
    public function getIterator(): Traversable
    {
        $xValues = $this->getRange($this->from->x, $this->to->x);
        $yValues = $this->getRange($this->from->y, $this->to->y);

        $maxIndex = max(count($xValues), count($yValues));
        for ($index = 0; $index < $maxIndex; $index++) {
            yield new Point($xValues[$index] ?? $xValues[0], $yValues[$index] ?? $yValues[0]);
        }
    }

    public function isHorizontal(): bool
    {
        return $this->from->y === $this->to->y;
    }

    public function isVertical(): bool
    {
        return $this->from->x === $this->to->x;
    }

    public function isDiagonal(): bool
    {
        return !$this->isHorizontal() && !$this->isVertical();
    }

    /**
     * @return int[]
     */
    private function getRange(int $from, int $to): array
    {
        return range($from, $to, $from >= $to ? 1 : -1);
    }
}
