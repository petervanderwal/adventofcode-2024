<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use App\Model\Grid;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;

class Day25
{
    private const string DEMO_INPUT = <<<EOF
        #####
        .####
        .####
        .####
        .#.#.
        .#...
        .....
        
        #####
        ##.##
        .#.##
        ...##
        ...#.
        ...#.
        .....
        
        .....
        #....
        #....
        #...#
        #.#.#
        #.###
        #####
        
        .....
        .....
        #.#..
        ###..
        ###.#
        ###.#
        #####
        
        .....
        .....
        .....
        #....
        #.#..
        #.#.#
        #####
        EOF;

    #[Puzzle(2024, day: 25, part: 1)]
    #[TestWithDemoInput(self::DEMO_INPUT, expectedAnswer: 3)]
    public function solve(PuzzleInput $input): int
    {
        $locks = [];
        $keys = [];

        foreach ($input->split("\n\n") as $block) {
            $block = (string)$block;

            $pattern = [];
            foreach (Grid::read($block)->getColumns() as $column) {
                $pattern[] = $column->whereEquals('#')->count() - 1;
            }

            if ($block[0] === '#') {
                $locks[] = $pattern;
            } else {
                $keys[] = $pattern;
            }
        }

        $answer = 0;
        foreach ($locks as $lock) {
            foreach ($keys as $key) {
                if ($this->matches($lock, $key)) {
                    $answer++;
                }
            }
        }
        return $answer;
    }

    private function matches(array $lock, array $key): bool
    {
        foreach ($key as $index => $keyLength) {
            if ($lock[$index] > 5 - $keyLength) {
                return false;
            }
        }
        return true;
    }
}
