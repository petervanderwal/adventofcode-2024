<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;

class Day01
{
    #[Puzzle(2024, day: 1, part: 1)]
    #[TestWithDemoInput(
        input: <<<EOF
            3   4
            4   3
            2   5
            1   3
            3   9
            3   3
            EOF,
        expectedAnswer: 11)]
    public function part1(PuzzleInput $input): int
    {
        [$listA, $listB] = $this->parseInput($input);
        sort($listA);
        sort($listB);

        $result = 0;
        foreach ($listA as $index => $a) {
            $b = $listB[$index];
            $result += abs($a - $b);
        }
        return $result;
    }

    #[Puzzle(2024, day: 1, part: 2)]
    #[TestWithDemoInput(
        input: <<<EOF
            3   4
            4   3
            2   5
            1   3
            3   9
            3   3
            EOF,
        expectedAnswer: 31)]
    public function part2(PuzzleInput $input): int
    {
        [$listA, $listB] = $this->parseInput($input);
        $listBCount = array_count_values($listB);

        $result = 0;
        foreach ($listA as $a) {
            $result += $a * ($listBCount[$a] ?? 0);
        }
        return $result;
    }

    /**
     * @param PuzzleInput $input
     * @return array{0: int[], 1: int[]}
     */
    private function parseInput(PuzzleInput $input): array
    {
        $listA = [];
        $listB = [];
        foreach ($input->split("\n") as $line) {
            [$a, $b] = preg_split('/\s+/', (string)$line);
            $listA[] = (int)$a;
            $listB[] = (int)$b;
        }
        return [$listA, $listB];
    }
}
