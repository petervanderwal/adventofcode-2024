<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;

class Day02
{
    #[Puzzle(2024, day: 2, part: 1)]
    #[TestWithDemoInput(
        input: <<<EOF
        7 6 4 2 1
        1 2 7 8 9
        9 7 6 2 1
        1 3 2 4 5
        8 6 4 4 1
        1 3 6 7 9
        EOF,
        expectedAnswer: 2,
    )]
    public function part1(PuzzleInput $input): int
    {
        $result = 0;
        foreach ($input->trim()->split("\n") as $report) {
            $result += (int)$this->isSafe($report->split(' '));
        }
        return $result;
    }

    #[Puzzle(2024, day: 2, part: 2)]
    #[TestWithDemoInput(
        input: <<<EOF
        7 6 4 2 1
        1 2 7 8 9
        9 7 6 2 1
        1 3 2 4 5
        8 6 4 4 1
        1 3 6 7 9
        EOF,
        expectedAnswer: 4,
    )]
    public function part2(PuzzleInput $input): int
    {
        $result = 0;
        foreach ($input->trim()->split("\n") as $report) {
            $result += (int)$this->isSafe($report->split(' '), 1);
        }
        return $result;
    }

    private function isSafe(array $report, int $dampener = 0): bool
    {
        $previous = $expectedSign = null;
        $false = $dampener === 0 ?
            fn (int $position): bool => false :
            fn (int $position): bool => $this->isSafeWithMissingLevel($report, $position, $dampener - 1);

        foreach ($report as $position => $level) {
            $level = (int)(string)$level;
            if ($previous === null) {
                $previous = $level;
                continue;
            }

            if ($level === $previous || abs($level - $previous) > 3) {
                // Any two adjacent levels differ by at least one and at most three.
                return $false($position);
            }

            $sign = $level <=> $previous;
            if ($expectedSign === null) {
                $expectedSign = $sign;
            } elseif ($sign !== $expectedSign) {
                // The levels are either all increasing or all decreasing.
                return $false($position);
            }

            $previous = $level;
        }

        return true;
    }

    private function isSafeWithMissingLevel(array $report, int $missingLevel, int $dampener): bool
    {
        // Try to remove the level where we spot the issue
        if ($this->isSafe($this->removeIndex($report, $missingLevel), $dampener)) {
            return true;
        }

        // Try to remove the previous level from where we spot the issue
        $missingLevel--;
        if ($missingLevel >= 0 && $this->isSafe($this->removeIndex($report, $missingLevel), $dampener)) {
            return true;
        }

        // If we found the issue on position 3: then try to remove the very first item too
        if ($missingLevel === 1 && $this->isSafe($this->removeIndex($report, 0), $dampener)) {
            return true;
        }

        return false;
    }

    private function removeIndex(array $data, int $index): array
    {
        array_splice($data, $index, 1);
        return $data;
    }
}
