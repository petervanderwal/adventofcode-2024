<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use App\Model\Direction;
use App\Model\Point;
use App\Model\WeightedQueue;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;

class Day18
{
    private const string DEMO_INPUT = <<<EOF
        5,4
        4,2
        4,5
        3,0
        2,1
        6,3
        2,4
        1,5
        0,6
        3,3
        2,6
        5,1
        1,2
        5,5
        2,5
        6,5
        1,4
        0,4
        6,4
        1,1
        6,1
        1,0
        0,5
        1,6
        2,0
        EOF;

    #[Puzzle(2024, day: 18, part: 1)]
    #[TestWithDemoInput(self::DEMO_INPUT, expectedAnswer: 22)]
    public function part1(PuzzleInput $input): int
    {
        $gridSize = $input->isDemoInput() ? 6 : 70;
        $fallenBytes = $input->isDemoInput() ? 12 : 1024;

        /** @var array<string, Point> $accessiblePoints */
        $accessiblePoints = [];
        for ($x = 0; $x <= $gridSize; $x++) {
            for ($y = 0; $y <= $gridSize; $y++) {
                $accessiblePoints["$x,$y"] = new Point($x, $y);
            }
        }

        foreach (array_slice($input->splitLines(), 0, $fallenBytes) as $fallenByte) {
            unset($accessiblePoints[$fallenByte]);
        }

        // Dijkstra Algorithm
        $queue = new WeightedQueue();
        $distance['0,0'] = 0;
        $goal = "$gridSize,$gridSize";
        $queue->addWithPriority('0,0', 0);
        while (!$queue->isEmpty()) {
            $testId = $queue->shiftLowestPriority();
            $nextDistance = $distance[$testId] + 1;
            foreach (Direction::straightCases() as $direction) {
                $nextId = (string)$accessiblePoints[$testId]->moveDirection($direction);
                if ($nextId === $goal) {
                    return $nextDistance; // Goal found
                }
                if (!isset($accessiblePoints[$nextId])) {
                    continue; // Not accessible
                }
                if (isset($distance[$nextId]) && $distance[$nextId] <= $nextDistance) {
                    continue; // Not better
                }

                $distance[$nextId] = $nextDistance;
                $queue->addWithPriority($nextId, $nextDistance);
            }
        }

        throw new \UnexpectedValueException("No path found to $goal", 241218073930);
    }
}
