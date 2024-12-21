<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use App\Model\Grid;
use App\Model\Point;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;

class Day21
{
    private const string NUMERIC_KEYPAD = <<<EOF
        789
        456
        123
         0A
        EOF;

    private const array NUMERIC_KEYPAD_PREFERRED_ORDER = ['>' => 0, '^' => 1, 'v' => 2, '<' => 3];

    private const string DIRECTIONAL_KEYPAD = <<<EOF
         ^A
        <v>
        EOF;

    private const array DIRECTIONAL_KEYPAD_PREFERRED_ORDER = ['>' => 0, 'v' => 1, '^' => 2, '<' => 3];

    private const string DEMO_INPUT = <<<EOF
        029A
        980A
        179A
        456A
        379A
        EOF;

    private array $numericKeypadCoordinates;
    private array $directionalKeypadCoordinates;
    private array $cache;

    #[Puzzle(2024, day: 21, part: 1)]
    #[TestWithDemoInput('029A', 29 * 12, '1 robot')] // <A^A>^^AvvvA
    #[TestWithDemoInput('029A', 29 * 28, '2 robots')] // v<<A>>^A<A>AvA<^AA>A<vAAA>^A
    #[TestWithDemoInput('029A', 29 * 68, '029A')] // <vA<AA>>^AvAA<^A>A<v<A>>^AvA^A<vA>^A<v<A>^A>AAvA^A<v<A>A>^AAAvA<^A>A
    #[TestWithDemoInput('456A', 456 * 64, '456A')] // Failing at the moment
    #[TestWithDemoInput('379A', 379 * 64, '379A')] // Failing at the moment
    #[TestWithDemoInput(self::DEMO_INPUT, 126384)]
    public function part1(PuzzleInput $input): int
    {
        $this->initialize();
        $amountOfRobots = match($input->demoInputName) {
            '1 robot' => 1,
            '2 robots' => 2,
            default => 3,
        };

        $result = 0;
        foreach ($input->splitLines() as $line) {
            $presses = 0;
            $cursor = 'A';
            foreach (str_split($line) as $char) {
                $presses += $this->calculateNumericKeypadCost($cursor, $char, $amountOfRobots);
                $cursor = $char;
            }

            if ($input->isDemoInput()) {
                echo "$line cost $presses presses with $amountOfRobots robots\n";
            }

            $result += ((int)$line) * $presses;
        }
        return $result;
    }

    private function initialize(): void
    {
        $this->cache = [];
        $this->numericKeypadCoordinates = $this->getCoordinatesFrom(self::NUMERIC_KEYPAD);
        $this->directionalKeypadCoordinates = $this->getCoordinatesFrom(self::DIRECTIONAL_KEYPAD);
    }

    private function getCoordinatesFrom(string $keypad): array
    {
        $result = [];
        Grid::read($keypad, function (string $char, Point $coordinate) use (&$result) {
            if ($char !== ' ') {
                $result[$char] = $coordinate;
            }
        });
        return $result;
    }

    private function calculateNumericKeypadCost(string $cursor, string $press, int $amountOfRobots): int
    {
        if (isset($this->cache['numeric']["$cursor$press"])) {
            return $this->cache['numeric']["$cursor$press"];
        }

        $directionalSequence = $this->getDirectionalSequence(
            $this->numericKeypadCoordinates[$cursor],
            $this->numericKeypadCoordinates[$press],
            self::NUMERIC_KEYPAD_PREFERRED_ORDER,
        );

        return $this->cache['numeric']["$cursor$press"] =
            $this->calculateDirectionalSequenceCost($directionalSequence, $amountOfRobots - 1);
    }

    private function getDirectionalSequence(Point $cursor, Point $press, array $preferredOrder): array
    {
        $diffX = $press->x - $cursor->x;
        if ($diffX < 0) {
            $presses = array_fill(0, -$diffX, '<');
        } elseif ($diffX > 0) {
            $presses = array_fill(0, $diffX, '>');
        } else {
            $presses = [];
        }

        $diffY = $cursor->y - $press->y;
        if ($diffY < 0) {
            $presses = [...$presses, ...array_fill(0, -$diffY, '^')];
        } elseif ($diffY > 0) {
            $presses = [...$presses, ...array_fill(0, $diffY, 'v')];
        }

        usort($presses, fn(string $a, string $b) => $preferredOrder[$a] <=> $preferredOrder[$b]);

        $presses[] = 'A';
        return $presses;
    }

    private function calculateDirectionalSequenceCost(array $directionalSequence, int $amountOfRobots): int
    {
        if ($amountOfRobots === 0) {
            return count($directionalSequence);
        }

        $presses = 0;
        $cursor = 'A';
        foreach ($directionalSequence as $press) {
            $presses += $this->calculateDirectionalKeypadCost($cursor, $press, $amountOfRobots);
            $cursor = $press;
        }
        return $presses;
    }

    private function calculateDirectionalKeypadCost(string $cursor, string $press, int $amountOfRobots): int
    {
        if (isset($this->cache["robot $amountOfRobots"]["$cursor$press"])) {
            return $this->cache["robot $amountOfRobots"]["$cursor$press"];
        }

        $directionalSequence = $this->getDirectionalSequence(
            $this->directionalKeypadCoordinates[$cursor],
            $this->directionalKeypadCoordinates[$press],
            self::DIRECTIONAL_KEYPAD_PREFERRED_ORDER,
        );

        return $this->cache["robot $amountOfRobots"]["$cursor$press"] =
            $this->calculateDirectionalSequenceCost($directionalSequence, $amountOfRobots - 1);
    }
}
