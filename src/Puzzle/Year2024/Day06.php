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
    private const string DEMO_INPUT = <<<EOF
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
        EOF;

    #[Puzzle(2024, day: 6, part: 1)]
    #[TestWithDemoInput(input: self::DEMO_INPUT, expectedAnswer: 41)]
    public function part1(PuzzleInput $input): int
    {
        [$grid] = $this->getWalkedGrid($input);
        if ($input->isDemoInput()) {
            echo "\n" . $grid->plot() . "\n\n";
        }
        return $grid->whereEquals('x')->count();
    }

    /**
     * @param PuzzleInput $input
     * @return array{0: Grid, 1: DirectedPoint}
     */
    private function getWalkedGrid(PuzzleInput $input): array
    {
        $startPosition = null;
        $grid = Grid::read($input, function (string $character, Point $position) use (&$startPosition): string {
            if ($character !== '.' && $character !== '#') {
                $startPosition = new DirectedPoint(Direction::fromCharacter($character), $position->x, $position->y);
                return 'x';
            }
            return $character;
        });

        $guard = $startPosition;
        while (true) {
            $nextStep = $guard->moveCurrentDirection();
            if (!$grid->hasPoint($nextStep)) {
                return [$grid, $startPosition];
            }

            if ($grid->get($nextStep) === '#') {
                $guard = $guard->turnRight();
            } else {
                $guard = $nextStep;
                $grid->set($guard, 'x');
            }
        }
    }

    #[Puzzle(2024, day: 6, part: 2)]
    #[TestWithDemoInput(input: self::DEMO_INPUT, expectedAnswer: 6)]
    public function part2(PuzzleInput $input): int
    {
        [$grid, $startPosition] = $this->getWalkedGrid($input);

        $result = 0;
        foreach ($grid->whereEquals('x')->keys() as $blockPosition) {
            if (
                !$startPosition->equalsCoordinates($blockPosition)
                && $this->causesLoop($grid, $startPosition, $blockPosition)
            ) {
                $result++;
            }
        }
        return $result;
    }

    private function causesLoop(Grid $grid, DirectedPoint $guard, Point $blockPosition): bool
    {
        $seen = [];
        while (true) {
            $seen[] = $guard->toString();

            $nextStep = $guard->moveCurrentDirection();
            if (!$grid->hasPoint($nextStep)) {
                return false;
            }

            if (in_array($nextStep->toString(), $seen, true)) {
                return true;
            }

            if ($grid->get($nextStep) === '#' || $nextStep->equalsCoordinates($blockPosition)) {
                $guard = $guard->turnRight();
            } else {
                $guard = $nextStep;
            }
        }
    }
}
