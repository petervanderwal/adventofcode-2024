<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;

class Day11
{
    #[Puzzle(2024, day: 11, part: 1)]
    #[TestWithDemoInput('125 17', expectedAnswer: 55312)]
    public function part1(PuzzleInput $input): int
    {
        return $this->solve($input, 25);
    }

    #[Puzzle(2024, day: 11, part: 2)]
    public function part2(PuzzleInput $input): int
    {
        return $this->solve($input, 75);
    }

    private function solve(PuzzleInput $input, int $amountOfIterations): int
    {
        $numberAmount = [];
        foreach ($input->splitInt(' ') as $number) {
            $numberAmount[$number] = ($numberAmount[$number] ?? 0) + 1;
        }

        for ($i = 0; $i < $amountOfIterations; $i++) {
            $nextIteration = [];
            foreach ($numberAmount as $number => $amount) {
                if ($number === 0) {
                    $nextIteration[1] = ($nextIteration[1] ?? 0) + $amount;
                } elseif (strlen((string)$number) % 2 === 0) {
                    $numbers = str_split((string)$number, strlen((string)$number) / 2);
                    $nextIteration[(int)$numbers[0]] = ($nextIteration[(int)$numbers[0]] ?? 0) + $amount;
                    $nextIteration[(int)$numbers[1]] = ($nextIteration[(int)$numbers[1]] ?? 0) + $amount;
                } else {
                    $nextIteration[$number * 2024] = ($nextIteration[$number * 2024] ?? 0) + $amount;
                }
            }

            $numberAmount = $nextIteration;
            if ($i % 5 === 0 && !$input->isDemoInput()) {
                echo '[' . date('H:i:s') . "] Done first $i iterations...\n";
            }
        }
        return array_sum($numberAmount);
    }
}
