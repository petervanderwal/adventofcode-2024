<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use App\Model\DirectedPoint;
use App\Model\Direction;
use App\Model\Grid;
use App\Model\Point;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;

class Day06
{
    #[Puzzle(2024, day: 6, part: 1)]
    #[TestWithDemoInput(
        input: <<<EOF
            ....#.....
            .........#
            ..........
            ..#.......
            .......#..
            ..........
            .#..^.....
            ........#.
            #.........
            ......#...
            EOF,
        expectedAnswer: 41,
    )]
    public function part1(PuzzleInput $input): int
    {
        $guard = null;
        $grid = Grid::read($input, function (string $character, Point $position) use (&$guard): string {
            if ($character !== '.' && $character !== '#') {
                $guard = new DirectedPoint(Direction::fromCharacter($character), $position->x, $position->y);
                return 'x';
            }
            return $character;
        });

        while (true) {
            $nextStep = $guard->moveCurrentDirection();
            if (!$grid->hasPoint($nextStep)) {
                if ($input->isDemoInput()) {
                    echo "\n" . $grid->plot() . "\n\n";
                }
                return $grid->whereEquals('x')->count();
            }

            if ($grid->get($nextStep) === '#') {
                $guard = $guard->turnRight();
            } else {
                $guard = $nextStep;
                $grid->set($guard, 'x');
            }
        }
    }
}
