<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use App\Model\Direction;
use App\Model\Grid;
use App\Model\Point;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;

class Day20
{
    private const string DEMO_INPUT = <<<EOF
        ###############
        #...#...#.....#
        #.#.#.#.#.###.#
        #S#...#.#.#...#
        #######.#.#.###
        #######.#.#...#
        #######.#.###.#
        ###..E#...#...#
        ###.#######.###
        #...###...#...#
        #.#####.#.###.#
        #.#...#.#.#...#
        #.#.#.#.#.#.###
        #...#...#...###
        ###############
        EOF;

    #[Puzzle(2024, day: 20, part: 1)]
    #[TestWithDemoInput(self::DEMO_INPUT, expectedAnswer: 8)]
    public function part1(PuzzleInput $input): int
    {
        $minimumCheatScore = $input->isDemoInput() ? 12 : 100;

        $start = null;
        $map = Grid::read($input, function (string $char, Point $point) use (&$start) {
            if ($char === 'S') {
                $start = $point;
                return 0;
            }
            return $char;
        });

        $track = [$start];
        $char = 'S';
        while ($char !== 'E') {
            foreach (Direction::straightCases() as $direction) {
                $moved = $start->moveDirection($direction);
                if ($map->hasPoint($moved)) {
                    $char = $map->get($moved);
                    if ($char === '.' || $char === 'E') {
                        $map->set($moved, count($track));
                        $track[] = $start = $moved;
                        break;
                    }
                }
            }
        }

        $cheats = 0;
        foreach ($track as $fromDistance => $point) {
            foreach (Direction::straightCases() as $direction) {
                $cheatTo = $point->moveDirection($direction, 2);
                if ($map->hasPoint($cheatTo)) {
                    $cheatToDistance = $map->get($cheatTo);
                    if (
                        is_int($cheatToDistance)
                        && $cheatToDistance - $fromDistance - 2 >= $minimumCheatScore
                    ) {
                        $cheats++;
                    }
                }
            }
        }
        return $cheats;
    }
}
