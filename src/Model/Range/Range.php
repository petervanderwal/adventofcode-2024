<?php

declare(strict_types=1);

namespace App\Model\Range;

use App\Model\Iterator\AbstractIterator;
use Traversable;

class Range extends AbstractIterator implements RangeInterface
{
    public function __construct(
        public readonly int $from,
        public readonly int $to,
    ) {
        if ($this->from > $this->to) {
            throw new \InvalidArgumentException('The start of the range can\'t be higher then the end of the range', 221215193515);
        }
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function getLowerBound(): ?int
    {
        return $this->from;
    }

    public function getUpperBound(): ?int
    {
        return $this->to;
    }

    public function getIterator(): Traversable
    {
        for ($number = $this->from; $number <= $this->to; $number++) {
            yield $number;
        }
    }

    public function count(): int
    {
        return $this->to - $this->from + 1;
    }

    public function getSections(): array
    {
        return [$this];
    }

    public function contains(int $number): bool
    {
        return $number >= $this->from && $number <= $this->to;
    }

    public function isPartiallyOverlapping(int|RangeInterface $given): bool
    {
        if (is_int($given)) {
            return $this->contains($given);
        }
        if ($given->isEmpty()) {
            return false;
        }

        // THIS :  XXXXXXXXX.......
        // GIVEN: ....XXXXXXXXXXXX.
        //   or
        // THIS :  XXXXXXXXX.......
        // GIVEN: ....XX.......XXX.
        //   or
        // THIS : ....XXXXXXXXXXXX
        // GIVEN: XXXXXXXXX.......
        //   or
        // THIS : ....XXXXXXXX....
        // GIVEN: XX.....XX.....XX
        //   but not
        // THIS : ....XXXXXXXX....
        // GIVEN: XX............XX
        foreach ($given->getSections() as $section) {
            if ($this->contains($section->from) || $this->contains($section->to)) {
                return true;
            }
        }
        return false;
    }

    public function isFullyOverlapping(int|RangeInterface $given): bool
    {
        if (is_int($given)) {
            return $this->contains($given);
        }
        if ($given->isEmpty()) {
            return false;
        }

        // THIS : XXXXXXXXXXXXXXXXXXXX
        // GIVEN: ...XXXXXXXXXX..XXX..
        return $this->contains($given->getLowerBound()) && $this->contains($given->getUpperBound());
    }

    public function mergeWith(int|RangeInterface $range): RangeInterface
    {
        if ($this->isFullyOverlapping($range)) {
            // Already covered
            return $this;
        }
        return new MergedRanges($this, $range);
    }

    public function diffWith(int|RangeInterface $remove): RangeInterface
    {
        if (!$this->isPartiallyOverlapping($remove)) {
            // Nothing to remove, that's convenient
            return $this;
        }

        // Forward to MergedRanges implementation
        return (new MergedRanges($this))->diffWith($remove);
    }
}