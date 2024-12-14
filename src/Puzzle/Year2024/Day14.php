<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use App\Utility\ArrayUtility;
use App\Utility\NumberUtility;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;

class Day14
{
    private const string DEMO_INPUT = <<<EOF
        p=0,4 v=3,-3
        p=6,3 v=-1,-3
        p=10,3 v=-1,2
        p=2,0 v=2,-1
        p=0,0 v=1,3
        p=3,0 v=-2,-2
        p=7,6 v=-1,-3
        p=3,0 v=-1,-2
        p=9,3 v=2,3
        p=7,3 v=-1,2
        p=2,4 v=2,-3
        p=9,5 v=-3,-3
        EOF;

    #[Puzzle(2024, day: 14, part: 1)]
    #[TestWithDemoInput(self::DEMO_INPUT, expectedAnswer: 12)]
    public function part1(PuzzleInput $input): int
    {
        $seconds = 100;

        if ($input->isDemoInput()) {
            $xSize = 11;
            $ySize = 7;
        } else {
            $xSize = 101;
            $ySize = 103;
        }
        $xSplit = ($xSize - 1) / 2;
        $ySplit = ($ySize - 1) / 2;

        $getQuadrant = function ($x, $y) use ($xSplit, $ySplit): ?string {
            if ($x === $xSplit || $y === $ySplit) {
                return null;
            }
            return ($y < $ySplit ? 'T' : 'B') . ($x < $xSplit ? 'L' : 'R');
        };

        $quadrants = ['TL' => 0, 'TR' => 0, 'BL' => 0, 'BR' => 0];
        foreach ($input->splitLines() as $line) {
            [$x, $y, $vx, $vy] = NumberUtility::getNumbersFromLine($line);
            $x = $this->moveOnAxis($x, $vx, $seconds, $xSize);
            $y = $this->moveOnAxis($y, $vy, $seconds, $ySize);
            $quadrant = $getQuadrant($x, $y);
            if ($quadrant !== null) {
                $quadrants[$quadrant]++;
            }
        }
        return ArrayUtility::multiply($quadrants);
    }

    private function moveOnAxis(int $value, int $velocity, int $seconds, int $axisLength): int
    {
        $value = ($value + $velocity * $seconds) % $axisLength;
        if ($value < 0) {
            // Modulo operations can return negative numbers
            $value += $axisLength;
        }
        return $value;
    }
}
