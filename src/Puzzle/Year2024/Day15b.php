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
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\String\UnicodeString;

class Day15b
{
    private const string SMALL_DEMO = <<<EOF
        #######
        #...#.#
        #.....#
        #..OO@#
        #..O..#
        #.....#
        #######
        
        <vv<<^^<<^^
        EOF;

    private ConsoleSectionOutput $consoleOutputSection;
    private const array ANIMATE = [
        'Small demo',
        // 'Large demo',
        // null, // Full puzzle
    ];

    #[Puzzle(2024, day: 15, part: 2)]
    #[TestWithDemoInput(self::SMALL_DEMO, expectedAnswer: 618, name: 'Small demo')]
    #[TestWithDemoInput(Day15a::LARGE_DEMO, expectedAnswer: 9021, name: 'Large demo')]
    public function solve(PuzzleInput $input): int
    {
        $this->consoleOutputSection = (new ConsoleOutput(decorated: true))->section();

        $blocks = $input->split("\n\n");
        $map = $this->transformGrid(Grid::read($blocks[0]));
        $instructions = array_map(
            fn(UnicodeString $instruction) => Direction::fromCharacter((string)$instruction),
            $blocks[1]->replace("\n", '')->chunk()
        );

        if (in_array($input->demoInputName, self::ANIMATE, true)) {
            $this->plotMap($map, 'Initial state');
        }

        /** @var Point $robotPosition */
        $robotPosition = $map->whereEquals('@')->keys()->first();
        foreach ($instructions as $index => $instruction) {
            $boxesToMove = $this->getBoxesToMove($map, $robotPosition, $instruction);
            if ($boxesToMove === null) {
                // Moving into wall
                if (in_array($input->demoInputName, self::ANIMATE, true)) {
                    $this->plotMap($map, $index . ': Blocked move ' . $instruction->character());
                }
                continue;
            }

            // Move boxes in reverse order
            foreach (array_reverse($boxesToMove) as $box) {
                /** @var Point $box */
                // Clear box old position
                $map->set($box, '.');
                $map->set($box->moveX(1), '.');
                // And set new box position
                $box = $box->moveDirection($instruction);
                $map->set($box, '[');
                $map->set($box->moveX(1), ']');
            }

            // Clear current position
            $map->set($robotPosition, '.');
            // And actually moving robot
            $robotPosition = $robotPosition->moveDirection($instruction);
            // Set robot position (should be last as clearing the box could overwrite it to .)
            $map->set($robotPosition, '@');

            if (in_array($input->demoInputName, self::ANIMATE, true)) {
                $this->plotMap($map, $index . ': Move ' . $instruction->character());
            }
        }

        if (in_array($input->demoInputName, self::ANIMATE, true)) {
            $this->plotMap($map, 'Final state');
        }

        $result = 0;
        foreach ($map->whereEquals('[')->keys() as $boxPosition) {
            /** @var Point $boxPosition */
            $result += $boxPosition->y * 100 + $boxPosition->x;
        }
        return $result;
    }

    private function plotMap(Grid $map, string $header): void
    {
        $box = new Color('green', options: ['bold']);
        $robot = (new Color('red', options: ['bold']))->apply('@');
        $wall = (new Color('bright-white'))->apply('#');

        $this->consoleOutputSection->overwrite(
            [
                "<info>$header:</info>",
                '',
                $map->plot(fn (string $value) => match ($value) {
                    '[', ']' => $box->apply($value),
                    '@' => $robot,
                    '#' => $wall,
                    default => $value,
                }),
            ]
        );

        usleep(200000);
    }

    private function transformGrid(Grid $map): Grid
    {
        $transformedLines = [];
        foreach ($map->getRows() as $originalLine) {
            $transformedLine = [];
            foreach ($originalLine as $value) {
                /** @var Point $point */
                $leftValue = $rightValue = $value;
                if ($value === '@') {
                    $rightValue = '.';
                } elseif ($value === 'O') {
                    $leftValue = '[';
                    $rightValue = ']';
                }

                $transformedLine[] = $leftValue;
                $transformedLine[] = $rightValue;
            }
            $transformedLines[] = $transformedLine;
        }
        return new Grid(...$transformedLines);
    }

    /**
     * @return Point[]|null The left coordinates of all boxes that needs to be moved
     */
    private function getBoxesToMove(Grid $map, Point $robotPosition, Direction $instruction): ?array
    {
        /** @noinspection PhpUncoveredEnumCasesInspection */
        return match ($instruction) {
            Direction::EAST, Direction::WEST => $this->getBoxesToMoveHorizontally($map, $robotPosition, $instruction),
            Direction::NORTH, Direction::SOUTH => $this->getBoxesToMoveVertically($map, $robotPosition, $instruction),
        };
    }

    /**
     * @return Point[]|null The left coordinates of all boxes that needs to be moved
     */
    private function getBoxesToMoveHorizontally(Grid $map, Point $robotPosition, Direction $instruction): ?array
    {
        // This is similar to part 1

        $boxesToMove = [];
        $testPosition = $robotPosition->moveDirection($instruction);
        while (true) {
            switch ($map->get($testPosition)) {
                case '.': return $boxesToMove; // Empty spot found
                case '#': return null; // Wall found, not possible to move

                case ']':
                    $box = $testPosition->moveX(-1);
                    $boxesToMove[(string)$box] = $box;
                    break;
                case '[':
                    $boxesToMove[(string)$testPosition] = $testPosition;
                    break;

                default:
                    throw new \UnexpectedValueException("Unexpected value " . $map->get($testPosition), 241215084818);
            }
            $testPosition = $testPosition->moveDirection($instruction);
        }
    }

    private function getBoxesToMoveVertically(Grid $map, Point $robotPosition, Direction $instruction): ?array
    {
        $boxesToMove = [];

        $movingLine = [$robotPosition];
        while (count($movingLine)) {
            $newMovingLine = [];

            foreach ($movingLine as $point) {
                $testPosition = $point->moveDirection($instruction);

                switch ($map->get($testPosition)) {
                    case '.': break; // Empty spot found, not blocking, also no special actions
                    case '#': return null; // Wall found, not possible to move

                    /** @noinspection PhpMissingBreakStatementInspection Fall through because we want the left coordinate of boxes */
                    case ']':
                        $testPosition = $testPosition->moveX(-1);
                        // And fall through
                    case '[':
                        // (Another) box found
                        $boxesToMove[(string)$testPosition] = $testPosition;
                        $newMovingLine[(string)$testPosition] = $testPosition;
                        // And right of it as well
                        $testPosition = $testPosition->moveX(1);
                        $newMovingLine[(string)$testPosition] = $testPosition;
                        break;
                    default:
                        throw new \UnexpectedValueException("Unexpected value " . $map->get($testPosition), 241215084818);
                }
            }

            $movingLine = $newMovingLine;
        }

        return $boxesToMove;
    }
}
