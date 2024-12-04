<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;

class Day03
{
    #[Puzzle(2024, day: 3, part: 1)]
    #[TestWithDemoInput(
        input: 'xmul(2,4)%&mul[3,7]!@^do_not_mul(5,5)+mul(32,64]then(mul(11,8)mul(8,5))',
        expectedAnswer: 161,
    )]
    public function part1(PuzzleInput $input): int
    {
        preg_match_all('/mul\(([0-9]{1,3}),([0-9]{1,3})\)/', (string)$input, $matches, PREG_SET_ORDER);

        $result = 0;
        foreach ($matches as $match) {
            $result += $match[1] * $match[2];
        }
        return $result;
    }

    #[Puzzle(2024, day: 3, part: 2)]
    #[TestWithDemoInput(
        input: "xmul(2,4)&mul[3,7]!^don't()_mul(5,5)+mul(32,64](mul(11,8)undo()?mul(8,5))",
        expectedAnswer: 48,
    )]
    public function part2(PuzzleInput $input): int
    {
        preg_match_all(
            '/do\(\)|don\'t\(\)|mul\(([0-9]{1,3}),([0-9]{1,3})\)/',
            (string)$input,
            $matches,
            PREG_SET_ORDER
        );

        $result = 0;
        $enabled = true;
        foreach ($matches as $match) {
            switch ($match[0]) {
                case 'do()': $enabled = true; break;
                case 'don\'t()': $enabled = false; break;
                default: $result += $enabled ? $match[1] * $match[2] : 0;
            }
        }
        return $result;
    }
}
