<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use App\Model\DirectedPoint;
use App\Model\Grid;
use App\Model\Point;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;

class Day12
{
    private const string SMALL_DEMO = <<<EOF
        AAAA
        BBCD
        BBCC
        EEEC
        EOF;

    private const string LARGE_DEMO = <<<EOF
        RRRRIICCFF
        RRRRIICCCF
        VVRRRCCFFF
        VVRCCCJFFF
        VVVVCJJCFE
        VVIVCCJJEE
        VVIIICJJEE
        MIIIIIJJEE
        MIIISIJEEE
        MMMISSJEEE
        EOF;

    #[Puzzle(2024, day: 12, part: 1)]
    #[TestWithDemoInput(self::SMALL_DEMO, expectedAnswer: 140)]
    #[TestWithDemoInput(self::LARGE_DEMO, expectedAnswer: 1930)]
    public function part1(PuzzleInput $input): int
    {
        $result = 0;
        foreach (
            Grid::read($input)->getAreas(
                fn(string $neighbour, Point $point, Grid\Area $area) => $neighbour === $area->getFirstValue(),
            ) as $area
        ) {
            $size = $area->getSize();
            $perimeter = count($area->getOuterBorder());

            if ($input->isDemoInput()) {
                echo sprintf(
                    "Area %s has a size of %d and a perimeter of %d.\n",
                    $area->getFirstValue(),
                    $size,
                    $perimeter
                );
            }

            $result += $size * $perimeter;
        }
        return $result;
    }

    #[Puzzle(2024, day: 12, part: 2)]
    #[TestWithDemoInput(self::SMALL_DEMO, expectedAnswer: 80)]
    #[TestWithDemoInput(self::LARGE_DEMO, expectedAnswer: 1206)]
    public function part2(PuzzleInput $input): int
    {
        $result = 0;
        foreach (
            Grid::read($input)->getAreas(
                fn(string $neighbour, Point $point, Grid\Area $area) => $neighbour === $area->getFirstValue(),
            ) as $area
        ) {
            $size = $area->getSize();
            $sides = $this->countSides(...$area->getOuterBorder());

            if ($input->isDemoInput()) {
                echo sprintf(
                    "Area %s has a size of %d and the border has %d sides.\n",
                    $area->getFirstValue(),
                    $size,
                    $sides
                );
            }

            $result += $size * $sides;
        }
        return $result;
    }

    private function countSides(DirectedPoint ...$border): int
    {
        $borderIndexed = [];
        foreach ($border as $borderPoint) {
            $borderIndexed[(string)$borderPoint] = $borderPoint;
        }

        $seen = [];
        $sides = 0;
        foreach ($borderIndexed as $key => $borderPoint) {
            if (in_array($key, $seen)) {
                continue;
            }

            $seen[] = (string)$borderPoint;
            $sides++;

            $moveDirection = $borderPoint->direction->turnRight();
            $movedBorder = $borderPoint->moveDirection($moveDirection);
            while (isset($borderIndexed[(string)$movedBorder])) {
                $seen[] = (string)$movedBorder;
                $movedBorder = $movedBorder->moveDirection($moveDirection);
            }

            $moveDirection = $borderPoint->direction->turnLeft();
            $movedBorder = $borderPoint->moveDirection($moveDirection);
            while (isset($borderIndexed[(string)$movedBorder])) {
                $seen[] = (string)$movedBorder;
                $movedBorder = $movedBorder->moveDirection($moveDirection);
            }
        }

        return $sides;
    }
}
