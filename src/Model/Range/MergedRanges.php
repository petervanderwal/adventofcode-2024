<?php

declare(strict_types=1);

namespace App\Model\Range;

use App\Model\Iterator\AbstractIterator;
use Traversable;

class MergedRanges extends AbstractIterator implements RangeInterface
{
    /**
     * @var Range[]
     */
    private array $sections = [];
    private ?int $lowerBound = null;
    private ?int $upperBound = null;

    public function __construct(int|RangeInterface ...$ranges)
    {
        $this->setOptimizedSections(...static::determineOptimizeSections(...$ranges));
    }

    /**
     * @return Range[]
     */
    private static function determineOptimizeSections(int|RangeInterface ...$ranges): array
    {
        // Retrieve individual ranges
        $sections = [];
        foreach ($ranges as $range) {
            if (is_int($range)) {
                $sections[] = new Range($range, $range);
                continue;
            }

            $sections = [...$sections, ...$range->getSections()];
        }

        // Sort based on "from" position. In case the "from" position is the same, then prefer the largest range first
        // (so that the second range is a full overlap)
        usort(
            $sections,
            fn (Range $a, Range $b) => $a->from <=> $b->from ?: $b->to <=> $a->to
        );

        // And merge
        $result = [];
        $previousSection = null;

        foreach ($sections as $section) {
            if (!$previousSection?->isPartiallyOverlapping($section)) {
                // No overlap with previous range, append to ranges
                $result[] = $previousSection = $section;
                continue;
            }

            if ($previousSection->isFullyOverlapping($section)) {
                // Ignore full overlaps, we don't need to extend the previous range
                continue;
            }

            // Partially overlaps with the previous range: extend previous range (by creating a new range object)
            $result[count($result) - 1] = $previousSection = new Range($previousSection->from, $section->to);
        }

        return $result;
    }

    private function setOptimizedSections(Range ...$sections): void
    {
        $this->sections = $sections;
        if (empty($sections)) {
            return;
        }
        $this->lowerBound = $sections[0]->from;
        $this->upperBound = $sections[count($sections) - 1]->to;
    }

    public function isEmpty(): bool
    {
        return empty($this->sections);
    }

    public function getLowerBound(): ?int
    {
        return $this->lowerBound;
    }

    public function getUpperBound(): ?int
    {
        return $this->upperBound;
    }

    public function count(): int
    {
        return array_sum(array_map(fn (Range $range) => count($range), $this->sections));
    }

    public function getIterator(): Traversable
    {
        foreach ($this->sections as $range) {
            foreach ($range as $number) {
                yield $number;
            }
        }
    }

    public function getSections(): array
    {
        return $this->sections;
    }

    public function contains(int $number): bool
    {
        foreach ($this->sections as $section) {
            if ($section->contains($number)) {
                return true;
            }
        }
        return false;
    }

    public function isPartiallyOverlapping(int|RangeInterface $given): bool
    {
        if (is_int(($given))) {
            return $this->contains($given);
        }
        if ($this->isEmpty() || $given->isEmpty()) {
            return false;
        }

        foreach ($this->sections as $section) {
            if ($section->isPartiallyOverlapping($given)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns true if the GIVEN range fully overlaps (either is exactly the same or smaller) THIS range
     */
    public function isFullyOverlapping(int|RangeInterface $given): bool
    {
        if (is_int($given)) {
            return $this->contains($given);
        }
        if ($this->isEmpty() || $given->isEmpty()) {
            return false;
        }

        // THIS : XXXXXXXXXXXXX..XXXXX
        // GIVEN: ...XXXXXXXXX....X...
        foreach ($given->getSections() as $givenSection) {
            $fullOverlapFound = false;

            foreach ($this->sections as $thisSection) {
                if ($thisSection->isFullyOverlapping($givenSection)) {
                    $fullOverlapFound = true;
                    break;
                }
            }

            if (!$fullOverlapFound) {
                return false;
            }
        }

        return true;
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
        if (is_int($remove)) {
            $remove = new Range($remove, $remove);
        }

        $result = $this;
        foreach ($remove->getSections() as $sectionToRemove) {
            $result = $result->removeSection($sectionToRemove);
        }
        return $result;
    }

    private function removeSection(Range $remove): MergedRanges
    {
        $newSections = [];
        $isAltered = false;
        foreach ($this->sections as $section) {
            if (!$section->isPartiallyOverlapping($remove)) {
                // This section has no overlap with the section to be removed, just add it again
                $newSections[] = $section;
                continue;
            }

            if ($section->from < $remove->from) {
                $newSections[] = new Range($section->from, $remove->from - 1);
            }
            if ($section->to > $remove->to) {
                $newSections[] = new Range($remove->to + 1, $section->to);
            }
            $isAltered = true;
        }

        if (!$isAltered) {
            // All sections are just added to the array the same as it was already, we can just return the current range
            return $this;
        }

        $result = new MergedRanges();
        $result->setOptimizedSections(...$newSections);
        return $result;
    }
}