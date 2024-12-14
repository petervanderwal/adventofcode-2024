<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use App\Utility\ArrayUtility;
use App\Utility\NumberUtility;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;
use PeterVanDerWal\AdventOfCode\Cli\Service\FixtureService;

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

    private int $xSize;
    private int $ySize;
    private int $xSplit;
    private int $ySplit;
    private array $instructions;

    public function __construct(
        private readonly FixtureService $fixtureService,
    ) {
    }

    private function init(PuzzleInput $input): void
    {
        $this->instructions = $input->splitMap("\n", NumberUtility::getNumbersFromLine(...));

        if ($input->isDemoInput()) {
            $this->xSize = 11;
            $this->ySize = 7;
        } else {
            $this->xSize = 101;
            $this->ySize = 103;
        }
        $this->xSplit = ($this->xSize - 1) / 2;
        $this->ySplit = ($this->ySize - 1) / 2;
    }

    #[Puzzle(2024, day: 14, part: 1)]
    #[TestWithDemoInput(self::DEMO_INPUT, expectedAnswer: 12)]
    public function part1(PuzzleInput $input): int
    {
        $this->init($input);
        $seconds = 100;

        $getQuadrant = function ($x, $y): ?string {
            if ($x === $this->xSplit || $y === $this->ySplit) {
                return null;
            }
            return ($y < $this->ySplit ? 'T' : 'B') . ($x < $this->xSplit ? 'L' : 'R');
        };

        $quadrants = ['TL' => 0, 'TR' => 0, 'BL' => 0, 'BR' => 0];
        foreach ($this->instructions as [$x, $y, $vx, $vy]) {
            $x = $this->moveOnAxis($x, $vx, $seconds, $this->xSize);
            $y = $this->moveOnAxis($y, $vy, $seconds, $this->ySize);
            $quadrant = $getQuadrant($x, $y);
            if ($quadrant !== null) {
                $quadrants[$quadrant]++;
            }
        }
        return ArrayUtility::multiply($quadrants);
    }

    #[Puzzle(2024, day: 14, part: 2)]
    public function part2(PuzzleInput $input): string
    {
        $this->init($input);
        $fixtureFolder = '2024/14/part-2-images/';
        $limit = 10000;

        for ($seconds = 0; $seconds < $limit; $seconds++) {
            if ($seconds % 1000 === 0) {
                echo sprintf("[%s] %d...\n", date('H:i:s'), $seconds);
            }

            $image = imagecreate($this->xSize, $this->ySize);
            $white = imagecolorallocate($image, 255, 255, 255);
            imagefill($image, 0, 0, $white);

            $red = imagecolorallocate($image, 255, 0, 0);

            foreach ($this->instructions as [$x, $y, $vx, $vy]) {
                $x = $this->moveOnAxis($x, $vx, $seconds, $this->xSize);
                $y = $this->moveOnAxis($y, $vy, $seconds, $this->ySize);
                imagesetpixel($image, $x, $y, $red);
            }

            ob_start();
            imagepng($image);
            $imageContent = ob_get_clean();
            imagedestroy($image);
            $this->fixtureService->storeFixture($fixtureFolder . $seconds . '.png', $imageContent);
        }

        return sprintf(
            "Open up the folder %s and find the christmas tree",
            $this->fixtureService->getFullFilename($fixtureFolder)
        );
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
