<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use App\Model\Direction;
use App\Model\Grid;
use App\Model\Point;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;
use Symfony\Component\String\UnicodeString;

class Day15a
{
    private const string SMALL_DEMO = <<<EOF
        ########
        #..O.O.#
        ##@.O..#
        #...O..#
        #.#.O..#
        #...O..#
        #......#
        ########
        
        <^^>>>vv<v>>v<<
        EOF;

    public const string LARGE_DEMO = <<<EOF
        ##########
        #..O..O.O#
        #......O.#
        #.OO..O.O#
        #..O@..O.#
        #O#..O...#
        #O..O..O.#
        #.OO.O.OO#
        #....O...#
        ##########
        
        <vv>^<v^>v>^vv^v>v<>v^v<v<^vv<<<^><<><>>v<vvv<>^v^>^<<<><<v<<<v^vv^v>^
        vvv<<^>^v^^><<>>><>^<<><^vv^^<>vvv<>><^^v>^>vv<>v<<<<v<^v>^<^^>>>^<v<v
        ><>vv>v^v^<>><>>>><^^>vv>v<^^^>>v^v^<^^>v^^>v^<^v>v<>>v^v^<v>v^^<^^vv<
        <<v<^>>^^^^>>>v^<>vvv^><v<<<>^^^vv^<vvv>^>v<^^^^v<>^>vvvv><>>v^<<^^^^^
        ^><^><>>><>^^<<^^v>>><^<v>^<vv>>v>>>^v><>^v><<<<v>>v<v<v>vvv>^<><<>^><
        ^>><>^v<><^vvv<^^<><v<<<<<><^v<<<><<<^^<v<^^^><^>>^<v^><<<^>>^v<v^v<v^
        >^>>^v>vv>^<<^v<>><<><<v<<v><>v<^vv<<<>^^v^>^^>>><<^v>>v^v><^^>>^<>vv^
        <><^^>^^^<><vvvvv^v<v<<>^v<v>v<<^><<><<><<<^^<<<^<<>><<><^^^>^^<>^>v<>
        ^^>vv<^v^v<vv>^<><v<^v>^^^>>>^^vvv^>vvv<>>>^<^>>>>>^<<^v>^vvv<>^<><<v>
        v^^>>><<^^<>>^v^<v^vv<>v^<<>^<^v^v><^<<<><<^<v><v<>vv>>v><v^<vv<>v^<<^
        EOF;

    #[Puzzle(2024, day: 15, part: 1)]
    #[TestWithDemoInput(self::SMALL_DEMO, expectedAnswer: 2028, name: 'Small demo')]
    #[TestWithDemoInput(self::LARGE_DEMO, expectedAnswer: 10092, name: 'Large demo')]
    public function solve(PuzzleInput $input): int
    {
        $blocks = $input->split("\n\n");
        $map = Grid::read($blocks[0]);
        $instructions = array_map(
            fn(UnicodeString $instruction) => Direction::fromCharacter((string)$instruction),
            $blocks[1]->replace("\n", '')->chunk()
        );

        /** @var Point $robotPosition */
        $robotPosition = $map->whereEquals('@')->keys()->first();
        foreach ($instructions as $instruction) {
            $boxesToMove = $this->getAmountOfBoxesToMove($map, $robotPosition, $instruction);
            if ($boxesToMove === null) {
                // Moving into wall
                continue;
            }

            // Clear current position
            $map->set($robotPosition, '.');
            // Actually moving robot
            $robotPosition = $robotPosition->moveDirection($instruction);
            $map->set($robotPosition, '@');
            // And (if we have boxes to move), set the last box position (all the others can remain untouched as they're
            // indicated as "O" already)
            if ($boxesToMove > 0) {
                $map->set($robotPosition->moveDirection($instruction, $boxesToMove), 'O');
            }
        }

        $result = 0;
        foreach ($map->whereEquals('O')->keys() as $boxPosition) {
            /** @var Point $boxPosition */
            $result += $boxPosition->y * 100 + $boxPosition->x;
        }
        return $result;
    }

    private function getAmountOfBoxesToMove(Grid $map, Point $robotPosition, Direction $instruction): ?int
    {
        $boxesToMove = 0;
        $testPosition = $robotPosition->moveDirection($instruction);
        while (true) {
            switch ($map->get($testPosition)) {
                case '.': return $boxesToMove; // Empty spot found
                case '#': return null; // Wall found, not possible to move
                case 'O': $boxesToMove++; break; // (Another) box found
                default:
                    throw new \UnexpectedValueException("Unexpected value " . $map->get($testPosition), 241215084818);
            }
            $testPosition = $testPosition->moveDirection($instruction);
        }
    }
}
