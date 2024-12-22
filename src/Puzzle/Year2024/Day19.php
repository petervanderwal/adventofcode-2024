<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;

class Day19
{
    private const string DEMO_INPUT = <<<EOF
        r, wr, b, g, bwu, rb, gb, br
        
        brwrr
        bggr
        gbbr
        rrbgbr
        ubwu
        bwurrg
        brgr
        bbrgwb
        EOF;

    /**
     * @var array<string, string[]>
     */
    private array $availableTowels = [];

    /**
     * @var array<string, array<string, int>>
     */
    private array $optionsStartingWithCache = [];

    #[Puzzle(2024, day: 19, part: 1)]
    #[TestWithDemoInput(self::DEMO_INPUT, expectedAnswer: 6)]
    public function part1(PuzzleInput $input): int
    {
        $desiredPatterns = $this->init($input);

        $result = 0;
        foreach ($desiredPatterns as $pattern) {
            if ($this->isAvailable($pattern)) {
                $result++;
            }
        }
        return $result;
    }

    #[Puzzle(2024, day: 19, part: 2)]
    #[TestWithDemoInput(self::DEMO_INPUT, expectedAnswer: 16)]
    public function part2(PuzzleInput $input): int
    {
        $desiredPatterns = $this->init($input);

        $result = 0;
        foreach ($desiredPatterns as $pattern) {
            $result += $this->getAllOptionsStartingWith($pattern)[''] ?? 0;
        }
        return $result;
    }

    /**
     * @return string[]
     */
    private function init(PuzzleInput $input): array
    {
        [$towels, $desiredPatterns] = $input->split("\n\n");
        $this->availableTowels = ['w' => [], 'u' => [], 'b' => [], 'r' => [], 'g' => []];
        foreach ($towels->split(", ") as $towel) {
            $towel = (string)$towel;
            $this->availableTowels[$towel[0]][] = $towel;
        }

        $this->optionsStartingWithCache = [];

        return $desiredPatterns->splitLines();
    }

    private function isAvailable(string $pattern): bool
    {
        if ($pattern === '') {
            return true;
        }
        foreach ($this->availableTowels[$pattern[0]] as $towel) {
            if (
                str_starts_with($pattern, $towel)
                && $this->isAvailable(substr($pattern, strlen($towel)))
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array<string, int> With on the key the overflowing characters (remaining characters from the towel we put
     *      on the end of the sequence) and on the value the amount of possibilities to reach this string.
     *
     * @example getAllOptionsStartingWith("gbb") will return (with self::DEMO_INPUT) the array [
     *          "" => 2,  // Indicating two options reaching "gbb" exactly ("gb"."b" and "g"."b"."b")
     *          "r" => 2, // Indicating two options reaching "gbbr" ("gb"."br" and "g."b"."br" -- note that
     *                       "gb"."b"."r" and "g"."b"."b"."r" are not counted here as it's not an overflow but a new
     *                       towel)
     *          "wu" => 2, // Indicating two options reaching "gbbwu" ("gb"."bwu" and "g."b"."bwu")
     *      ]
     */
    private function getAllOptionsStartingWith(string $startingWith): array
    {
        if (isset($this->optionsStartingWithCache[$startingWith])) {
            return $this->optionsStartingWithCache[$startingWith];
        }

        if (strlen($startingWith) === 1) {
            return $this->optionsStartingWithCache[$startingWith] = array_fill_keys(
                keys: array_map(
                    callback: fn(string $towel) => substr($towel, 1),
                    array: $this->availableTowels[$startingWith],
                ),
                value: 1,
            );
        }

        $previousOptions = $this->getAllOptionsStartingWith(substr($startingWith, 0, -1));
        $lastCharacter = substr($startingWith, -1);

        $options = [];
        foreach ($previousOptions as $overflow => $possibilities) {
            if (strlen($overflow) > 0) {
                if ($overflow[0] === $lastCharacter) {
                    $overflow = substr($overflow, 1);
                    $options[$overflow] = ($options[$overflow] ?? 0) + $possibilities;
                }
                continue;
            }

            // Else: overflow is empty, add overflows for all towels
            foreach ($this->availableTowels[$lastCharacter] as $towel) {
                $overflow = substr($towel, 1);
                $options[$overflow] = ($options[$overflow] ?? 0) + $possibilities;
            }
        }
        return $this->optionsStartingWithCache[$startingWith] = $options;
    }
}
