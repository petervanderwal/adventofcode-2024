<?php

declare(strict_types=1);

namespace App\Model\Range;

use App\Model\Iterator\IteratorInterface;

interface RangeInterface extends IteratorInterface
{
    public function isEmpty(): bool;

    /**
     * @return int|null The lower bound of this range, returns null if $this->>isEmpty()
     */
    public function getLowerBound(): ?int;

    /**
     * @return int|null The upper bound of this range, returns null if $this->>isEmpty()
     */
    public function getUpperBound(): ?int;

    /**
     * @return Range[]
     */
    public function getSections(): array;
    public function contains(int $number): bool;

    /**
     * Returns true if there is a partial overlap between this range and the given range. It is overlapping when at
     * least one number exists in both this and the given range. If this range or the given range is empty, this method
     * will always return false (no overlap).
     */
    public function isPartiallyOverlapping(int|RangeInterface $given): bool;

    /**
     * Returns true if THIS range fully overlaps (either is exactly the same or is larger) the GIVEN range. If this
     * range or the given range is empty, this method will always return false (no overlap)
     */
    public function isFullyOverlapping(int|RangeInterface $given): bool;
    public function mergeWith(int|RangeInterface $range): RangeInterface;
    public function diffWith(int|RangeInterface $remove): RangeInterface;
}