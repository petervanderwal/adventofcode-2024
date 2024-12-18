<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use App\Model\Direction;
use App\Model\Point;
use App\Model\WeightedQueue;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

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

        // Minus 1 because both start and end point are in there and we need amount of steps
        return count($this->getPathToBottomRight($gridSize, $accessiblePoints)) - 1;
    }

    #[Puzzle(2024, day: 18, part: 2)]
    #[TestWithDemoInput(self::DEMO_INPUT, expectedAnswer: '6,1')]
    public function part2(PuzzleInput $input): string
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

        $lines = $input->splitLines();
        $index = 0;
        $path = [];

        $progressBar = new ProgressBar(new ConsoleOutput(), max: count($lines) - 1);
        $progressBar->start();

        while (true) {
            // Let (more) bytes fall
            while (true) {
                $lastByteFallen = $lines[$index];
                unset($accessiblePoints[$lastByteFallen]);
                $index++;

                if (
                    $index === $fallenBytes // Test the part1 path
                    || in_array($lastByteFallen, $path) // We blocked our previous path, lets regroup and find a new path
                ) {
                    break;
                }
            }

            $path = $this->getPathToBottomRight($gridSize, $accessiblePoints);
            if ($path === null) {
                // No path anymore, answer found
                $progressBar->finish();
                echo "\n";
                return $lastByteFallen;
            }
            $progressBar->setProgress($index);
            $progressBar->display(); // Force redraw
        }
    }

    /**
     * @param int $gridSize
     * @param array<string, Point> $accessiblePoints
     * @return string[]|null
     */
    private function getPathToBottomRight(int $gridSize, array $accessiblePoints): ?array
    {
        // Dijkstra Algorithm
        $queue = new WeightedQueue();
        $distance['0,0'] = 0;
        $previous = [];
        $goal = "$gridSize,$gridSize";
        $queue->addWithPriority('0,0', 0);
        while (!$queue->isEmpty()) {
            $testId = $queue->shiftLowestPriority();
            $nextDistance = $distance[$testId] + 1;
            foreach (Direction::straightCases() as $direction) {
                $nextId = (string)$accessiblePoints[$testId]->moveDirection($direction);
                if ($nextId === $goal) {
                    $previous[$nextId] = $testId;
                    return $this->getPathFromPrevious($previous, $goal);
                }
                if (!isset($accessiblePoints[$nextId])) {
                    continue; // Not accessible
                }
                if (isset($distance[$nextId]) && $distance[$nextId] <= $nextDistance) {
                    continue; // Not better
                }

                $distance[$nextId] = $nextDistance;
                $previous[$nextId] = $testId;
                $queue->addWithPriority($nextId, $nextDistance);
            }
        }

        return null;
    }

    private function getPathFromPrevious(array $previous, string $last): array
    {
        $path = [];
        do {
            $path[] = $last;
            $last = $previous[$last] ?? null;
        } while ($last !== null);
        return array_reverse($path);
    }
}
