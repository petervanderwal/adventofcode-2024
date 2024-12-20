<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use App\Model\Direction;
use App\Model\Grid;
use App\Model\Point;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;
use Spatie\Async\Pool;

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

    private Grid $map;

    /**
     * @var array<int, Point>
     */
    private array $track;

    #[Puzzle(2024, day: 20, part: 1)]
    #[TestWithDemoInput(self::DEMO_INPUT, expectedAnswer: 8)]
    public function part1(PuzzleInput $input): int
    {
        $minimumCheatScore = $input->isDemoInput() ? 12 : 100;
        $this->init($input);
        return $this->getCheats($minimumCheatScore);
    }

    #[Puzzle(2024, day: 20, part: 2)]
    #[TestWithDemoInput(self::DEMO_INPUT, expectedAnswer: 3 + 4 + 22 + 12)]
    public function part2(PuzzleInput $input): int
    {
        $minimumCheatScore = $input->isDemoInput() ? 70 : 100;
        $this->init($input);

        $cheats = 0;
        $pool = Pool::create();
        for ($cheatLength = 2; $cheatLength <= 20; $cheatLength++) {
            $pool->add(function () use ($minimumCheatScore, $cheatLength) {
                return $this->getCheats($minimumCheatScore, $cheatLength);
            })->then(function (int $answer) use (&$cheats) {
                $cheats += $answer;
            });
        }
        $pool->wait();
        return $cheats;
    }

    private function init(PuzzleInput $input): void
    {
        $start = null;
        $this->map = Grid::read($input, function (string $char, Point $point) use (&$start) {
            if ($char === 'S') {
                $start = $point;
                return 0;
            }
            return $char;
        });

        $this->track = [$start];
        $char = 'S';
        while ($char !== 'E') {
            foreach (Direction::straightCases() as $direction) {
                $moved = $start->moveDirection($direction);
                if ($this->map->hasPoint($moved)) {
                    $char = $this->map->get($moved);
                    if ($char === '.' || $char === 'E') {
                        $this->map->set($moved, count($this->track));
                        $this->track[] = $start = $moved;
                        break;
                    }
                }
            }
        }
    }

    private function getCheats(int $minimumCheatScore, int $exactCheatLength = 2): int
    {
        $cheats = 0;
        for ($diffX = 0; $diffX <= $exactCheatLength; $diffX++) {
            foreach ($diffX === 0 ? [0] : [-$diffX, $diffX] as $offsetX) {
                $diffY = $exactCheatLength - $diffX;
                foreach ($diffY === 0 ? [0] : [-$diffY, $diffY] as $offsetY) {
                    $cheats += $this->getCheatsByOffset($minimumCheatScore, $exactCheatLength, $offsetX, $offsetY);
                }
            }
        }
        return $cheats;
    }

    private function getCheatsByOffset(int $minimumCheatScore, int $cheatLength, int $offsetX, int $offsetY): int
    {
        $cheats = 0;
        foreach ($this->track as $fromDistance => $point) {
            $cheatTo = $point->moveXY($offsetX, $offsetY);
            if ($this->map->hasPoint($cheatTo)) {
                $cheatToDistance = $this->map->get($cheatTo);
                if (
                    is_int($cheatToDistance)
                    && $cheatToDistance - $fromDistance - $cheatLength >= $minimumCheatScore
                ) {
                    $cheats++;
                }
            }
        }
        return $cheats;
    }
}
