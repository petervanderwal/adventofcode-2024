<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use App\Model\Direction;
use App\Model\Grid;
use App\Model\Point;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;

class Day08
{
    private const string DEMO_INPUT = <<<EOF
        ............
        ........0...
        .....0......
        .......0....
        ....0.......
        ......A.....
        ............
        ............
        ........A...
        .........A..
        ............
        ............
        EOF;

    #[Puzzle(2024, day: 8, part: 1)]
    #[TestWithDemoInput(self::DEMO_INPUT, expectedAnswer: 14)]
    public function part1(PuzzleInput $input): int
    {
        $antennaMap = Grid::read($input);
        $interferenceMap = Grid::fill(
            numberOfRows: $antennaMap->getNumberOfRows(),
            numberOfColumns: $antennaMap->getNumberOfColumns(),
            initialValueGenerator: fn() => false,
        );

        foreach ($antennaMap->where(fn (string $antenna) => $antenna !== '.') as $firstLocation => $antenna) {
            /** @var Point $firstLocation */
            foreach ($antennaMap->startingFrom($firstLocation, excluding: true)->whereEquals($antenna)->keys() as $otherLocation) {
                /** @var Point $otherLocation */

                foreach ($this->getInterferenceLocations($firstLocation, $otherLocation) as $interferenceLocation) {
                    if ($interferenceMap->hasPoint($interferenceLocation)) {
                        $interferenceMap->set($interferenceLocation, true);
                    }
                }
            }
        }

        if ($input->isDemoInput()) {
            echo $interferenceMap->plot(
                fn (bool $interference, Point $point): string => $interference ? '#' : $antennaMap->get($point)
            ) . "\n\n";
        }

        return $interferenceMap->whereEquals(true)->count();
    }

    /**
     * @return Point[]
     */
    private function getInterferenceLocations(Point $firstLocation, Point $otherLocation): array
    {
        return [
            $firstLocation->mirror($otherLocation),
            $otherLocation->mirror($firstLocation),
        ];
    }
}
