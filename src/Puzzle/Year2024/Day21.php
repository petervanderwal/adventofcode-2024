<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;

class Day21
{

    private const string DEMO_INPUT = <<<EOF
        029A
        980A
        179A
        456A
        379A
        EOF;

    #[Puzzle(2024, day: 21, part: 1)]
    #[TestWithDemoInput('029A', 29 * 12, '029A with 1 robot')] // <A^A>^^AvvvA
    #[TestWithDemoInput('029A', 29 * 28, '029A with 2 robots')] // v<<A>>^A<A>AvA<^AA>A<vAAA>^A
    #[TestWithDemoInput('029A', 29 * 68, '029A with 3 robots')] // <vA<AA>>^AvAA<^A>A<v<A>>^AvA^A<vA>^A<v<A>^A>AAvA^A<v<A>A>^AAAvA<^A>A
    #[TestWithDemoInput('980A', 980 * 60, '980A with 3 robots')]
    #[TestWithDemoInput('179A', 179 * 68, '179A with 3 robots')]
    #[TestWithDemoInput('456A', 456 * 64, '456A with 3 robots')]
    #[TestWithDemoInput('379A', 379 * 64, '379A with 3 robots')]
    #[TestWithDemoInput(self::DEMO_INPUT, 126384)]
    public function part1(PuzzleInput $input): int
    {
        $amountOfRobots = match(true) {
            str_contains((string)$input->demoInputName, '1 robot') => 1,
            str_contains((string)$input->demoInputName, '2 robots') => 2,
            default => 3,
        };

        $numericKeypad = new Day21\Keypad(<<<EOF
            789
            456
            123
             0A
            EOF);
        $directionalKeypad = new Day21\Keypad(<<<EOF
             ^A
            <v>
            EOF);

        $previousButtonPresser = new Day21\Human();
        for ($i = 0; $i < $amountOfRobots - 1; $i++) {
            $previousButtonPresser = new Day21\Robot($directionalKeypad, $previousButtonPresser);
        }
        $robot = new Day21\Robot($numericKeypad, $previousButtonPresser);

        $result = 0;
        foreach ($input->splitLines() as $line) {
            $robot->resetCursor();
            $presses = '';

            foreach (str_split($line) as $button) {
                $presses .= $robot->getInstructionsToPress($button);
            }

            $pressesLength = strlen($presses);
            if ($input->isDemoInput()) {
                echo "$line has sequence $presses and costs $pressesLength presses with $amountOfRobots robots\n";
            }

            $result += ((int)$line) * $pressesLength;
        }
        return $result;
    }
}
