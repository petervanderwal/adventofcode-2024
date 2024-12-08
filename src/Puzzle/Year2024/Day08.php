<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use App\Model\Grid;
use App\Model\Point;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;
use Symfony\Component\Console\Color;

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
        return $this->solve($input, true);
    }

    #[Puzzle(2024, day: 8, part: 2)]
    #[TestWithDemoInput(
        input: <<<EOF
            T.........
            ...T......
            .T........
            ..........
            ..........
            ..........
            ..........
            ..........
            ..........
            ..........
            EOF,
        expectedAnswer: 9,
        name: 'T-demo',
    )]
    #[TestWithDemoInput(self::DEMO_INPUT, expectedAnswer: 34)]
    public function part2(PuzzleInput $input): int
    {
        return $this->solve($input, false);
    }

    private function solve(PuzzleInput $input, bool $singleInterference): int
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

                foreach (
                    $this->getInterferenceLocations(
                        $interferenceMap,
                        $firstLocation,
                        $otherLocation,
                        $singleInterference,
                    ) as $interferenceLocation
                ) {
                    $interferenceMap->set($interferenceLocation, true);
                }
            }
        }

        if ($input->isDemoInput()) {
            echo $interferenceMap->plot(
                function (bool $interference, Point $point) use ($antennaMap): string {
                    $antenna = $antennaMap->get($point);
                    if (!$interference) {
                        return $antenna;
                    }
                    return (new Color('green', '', ['bold']))->apply(
                        $antenna === '.' ? '#' : $antenna
                    );
                }
            ) . "\n\n";
        }

        return $interferenceMap->whereEquals(true)->count();
    }

    /**
     * @return iterable<Point>
     */
    private function getInterferenceLocations(
        Grid $bounds,
        Point $firstLocation,
        Point $otherLocation,
        bool $singleInterference
    ): iterable {
        if (!$singleInterference) {
            yield $firstLocation;
            yield $otherLocation;
        }
        yield from $this->getInterferenceLocationSingleDirection($bounds, $firstLocation, $otherLocation, $singleInterference);
        yield from $this->getInterferenceLocationSingleDirection($bounds, $otherLocation, $firstLocation, $singleInterference);
    }

    /**
     * @return iterable<Point>
     */
    private function getInterferenceLocationSingleDirection(Grid $bounds, Point $from, Point $to, bool $singleInterference): iterable
    {
        while (true) {
            $interference = $from->mirror($to);
            if (!$bounds->hasPoint($interference)) {
                break;
            }

            yield $interference;

            if ($singleInterference) {
                break;
            }

            $from = $to;
            $to = $interference;
        }
    }
}
