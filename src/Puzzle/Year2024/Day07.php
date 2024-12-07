<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;

class Day07
{
    private const string DEMO_INPUT = <<<EOF
        190: 10 19
        3267: 81 40 27
        83: 17 5
        156: 15 6
        7290: 6 8 6 15
        161011: 16 10 13
        192: 17 8 14
        21037: 9 7 18 13
        292: 11 6 16 20
        EOF;

    #[Puzzle(2024, day: 7, part: 1)]
    #[TestWithDemoInput(input: self::DEMO_INPUT, expectedAnswer: 3749)]
    public function part1(PuzzleInput $input): int
    {
        return $this->solve($input, false);
    }

    #[Puzzle(2024, day: 7, part: 2)]
    #[TestWithDemoInput(input: self::DEMO_INPUT, expectedAnswer: 11387)]
    public function part2(PuzzleInput $input): int
    {
        return $this->solve($input, true);
    }

    private function solve(PuzzleInput $input, bool $allowConcatenate): int
    {
        $result = 0;
        foreach ($input->split("\n") as $line) {
            [$answer, $numbers] = $line->split(': ');
            $answer = (int)(string)$answer;
            if ($this->hasASolution($answer, $allowConcatenate, ...$numbers->splitInt(' '))) {
                $result += $answer;
            }
        }
        return $result;
    }

    private function hasASolution(int $answer, bool $allowConcatenate, int ...$numbers): bool
    {
        if (count($numbers) === 1) {
            return $answer === $numbers[0];
        }

        // The normal operation is left-to-right, so our reverse should be right-to-left
        $lastNumber = array_pop($numbers);

        // Try (reverse of) + operation
        $remaining = $answer - $lastNumber;
        if ($remaining > 0 && $this->hasASolution($remaining, $allowConcatenate, ...$numbers)) {
            return true;
        }

        // Try (reverse of) * operation
        $remaining = $answer / $lastNumber;
        // The is_int() check works in PHP? No rounding needed? -- Apparently it does
        if (is_int($remaining) && $this->hasASolution($remaining, $allowConcatenate, ...$numbers)) {
            return true;
        }

        if ($allowConcatenate) {
            $remaining = $this->reverseConcatenate($answer, $lastNumber);
            if ($remaining !== null && $this->hasASolution($remaining, true, ...$numbers)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $answer
     * @param int $rightHand
     * @return int|null $leftHand
     */
    private function reverseConcatenate(int $answer, int $rightHand): ?int
    {
        // Add a + 1: if $rightHand === 10, then we want a power of 2, not a power of 1
        $power = (int)ceil(log10($rightHand + 1));
        $divisor = pow(10, $power);

        if ($answer % $divisor !== $rightHand) {
            // The left most digits doesn't match the rightHand number
            return null;
        }
        return (int)($answer / $divisor);
    }
}
