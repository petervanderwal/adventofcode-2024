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
        $lineOfNumbers = $input->splitInt(' ');
        for ($i = 0; $i < 25; $i++) {
            $nextIteration = [];
            foreach ($lineOfNumbers as $number) {
                if ($number === 0) {
                    $nextIteration[] = 1;
                } elseif (strlen((string)$number) % 2 === 0) {
                    $numbers = str_split((string)$number, strlen((string)$number) / 2);
                    $nextIteration[] = (int)$numbers[0];
                    $nextIteration[] = (int)$numbers[1];
                } else {
                    $nextIteration[] = $number * 2024;
                }
            }

            $lineOfNumbers = $nextIteration;
            if ($i === 5 && $input->isDemoInput()) {
                echo "After 6 blinks: \n" . implode(' ', $lineOfNumbers) . "\n\n";
            } elseif ($i % 5 === 0 && !$input->isDemoInput()) {
                echo '[' . date('H:i:s') . "] Done first $i iterations...\n";
            }
        }
        return count($lineOfNumbers);
    }
}
