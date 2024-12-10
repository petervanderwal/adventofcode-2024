<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use App\Model\Direction;
use App\Model\Grid;
use App\Model\Point;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;
use Symfony\Component\Console\Color;

class Day10
{
    private const string DEMO_INPUT = <<<EOF
        89010123
        78121874
        87430965
        96549874
        45678903
        32019012
        01329801
        10456732
        EOF;


    #[Puzzle(2024, day: 10, part: 1)]
    #[TestWithDemoInput(self::DEMO_INPUT, expectedAnswer: 36)]
    public function part1(PuzzleInput $input): int
    {
        $debug = $input->isDemoInput();

        $map = Grid::read($input, fn (string $char, Point $point) => [
            'height' => (int)$char,
            'reachable' => $char === '9' ? [(string)$point] : [],
        ]);

        for ($height = 8; $height > 0; $height--) {
            foreach ($map->where(fn ($info) => $info['height'] === $height)->keys() as $point) {
                $map->set($point, [
                    'height' => $height,
                    'reachable' => $this->getPointReachable($map, $point, $height),
                ]);
            }
        }

        $result = 0;
        foreach ($map->where(fn ($info) => $info['height'] === 0)->keys() as $point) {
            $pointReachable = $this->getPointReachable($map, $point, 0);
            $result += count(array_unique($pointReachable));

            if ($debug) {
                $map->set($point, [
                    'height' => 0,
                    'reachable' => $pointReachable,
                ]);
            }
        }

        if ($debug) {
            $green = new Color('green', options: ['bold']);
            $blue = new Color('blue', options: ['bold']);
            echo $map->plot(fn (array $data) => sprintf(
                '%s %-19s ',
                $green->apply((string)$data['height']),
                '(' . $blue->apply((string)count(array_unique($data['reachable']))) . ')',
            )) . "\n\n";
        }

        return $result;
    }

    private function getPointReachable(Grid $map, Point $point, int $pointHeight): array
    {
        $pointReachable = [];
        foreach (Direction::straightCases() as $direction) {
            $uphill = $point->moveDirection($direction);
            if (!$map->hasPoint($uphill)) {
                continue;
            }

            $uphillData = $map->get($uphill);
            if ($uphillData['height'] === $pointHeight + 1) {
                $pointReachable = [...$pointReachable, ...$uphillData['reachable']];
            }
        }
        return $pointReachable;
    }

    #[Puzzle(2024, day: 10, part: 2)]
    #[TestWithDemoInput(self::DEMO_INPUT, expectedAnswer: 81)]
    public function part2(PuzzleInput $input): int
    {
        $debug = $input->isDemoInput();

        $map = Grid::read($input, fn (string $char) => [
            'height' => (int)$char,
            'score' => $char === '9' ? 1 : null,
        ]);

        for ($height = 8; $height > 0; $height--) {
            foreach ($map->where(fn ($info) => $info['height'] === $height)->keys() as $point) {
                $map->set($point, [
                    'height' => $height,
                    'score' => $this->getPointScore($map, $point, $height),
                ]);
            }
        }

        $result = 0;
        foreach ($map->where(fn ($info) => $info['height'] === 0)->keys() as $point) {
            $pointScore = $this->getPointScore($map, $point, 0);
            $result += $pointScore;

            if ($debug) {
                $map->set($point, [
                    'height' => 0,
                    'score' => $pointScore,
                ]);
            }
        }

        if ($debug) {
            $green = new Color('green', options: ['bold']);
            $blue = new Color('blue', options: ['bold']);
            echo $map->plot(fn (array $data) => sprintf(
                    '%s %-19s ',
                    $green->apply((string)$data['height']),
                    '(' . $blue->apply((string)$data['score']) . ')',
                )) . "\n\n";
        }

        return $result;
    }

    private function getPointScore(Grid $map, Point $point, int $pointHeight): int
    {
        $pointScore = 0;
        foreach (Direction::straightCases() as $direction) {
            $uphill = $point->moveDirection($direction);
            if (!$map->hasPoint($uphill)) {
                continue;
            }

            $uphillData = $map->get($uphill);
            if ($uphillData['height'] === $pointHeight + 1) {
                $pointScore += $uphillData['score'];
            }
        }
        return $pointScore;
    }
}
