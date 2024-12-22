<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use App\Utility\NumberUtility;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;

class Day22
{
    #[Puzzle(2024, day: 22, part: 1)]
    #[TestWithDemoInput("123", expectedAnswer: 15887950, name: '1 round')]
    #[TestWithDemoInput("123", expectedAnswer: 16495136, name: '2 rounds')]
    #[TestWithDemoInput("123", expectedAnswer: 527345, name: '3 rounds')]
    #[TestWithDemoInput("1\n10\n100\n2024", expectedAnswer: 37327623)]
    public function part1(PuzzleInput $input): int
    {
        $rounds = 2000;
        if ($input->isDemoInput()) {
            $rounds = NumberUtility::getNumbersFromLine($input->demoInputName)[0] ?? $rounds;
        }

        $result = 0;
        foreach ($input->splitInt("\n") as $secret) {
            for ($i = 0; $i < $rounds; $i++) {
                $secret = $this->getNextSecretNumber($secret);
            }
            $result += $secret;
        }
        return $result;
    }

    #[Puzzle(2024, day: 22, part: 2)]
    #[TestWithDemoInput("123", expectedAnswer: 6, name: '10 round')]
    #[TestWithDemoInput("1\n2\n3\n2024", expectedAnswer: 23)]
    public function part2(PuzzleInput $input): int
    {
        $rounds = 2000;
        if ($input->isDemoInput()) {
            $rounds = NumberUtility::getNumbersFromLine($input->demoInputName)[0] ?? $rounds;
        }

        $monkeySequenceScores = [];
        foreach ($input->splitInt("\n") as $secret) {
            $prices = [$secret % 10];
            for ($i = 0; $i < $rounds; $i++) {
                $secret = $this->getNextSecretNumber($secret);
                $prices[] = $secret % 10;
            }

            foreach ($this->getSequenceScores(...$prices) as $sequence => $score) {
                $monkeySequenceScores[$sequence] = ($monkeySequenceScores[$sequence] ?? 0) + $score;
            }
        }

        return max($monkeySequenceScores);
    }

    private function getNextSecretNumber(int $secret): int
    {
        $secret = ($secret ^ ($secret * 64)) % 16777216;
        $secret = ($secret ^ (int)($secret / 32)) % 16777216;
        $secret = ($secret ^ ($secret * 2048)) % 16777216;
        return $secret;
    }

    private function getSequenceScores(int ...$prices): array
    {
        $result = [];
        for ($i = 4; $i < count($prices); $i++) {
            $sequence = $this->getSequence(...array_slice($prices, $i - 4, 5));
            if (!isset($result[$sequence])) {
                $result[$sequence] = $prices[$i];
            }
        }
        return $result;
    }

    private function getSequence(int ...$prices): string
    {
        $diff = [];
        $previous = $prices[0];
        for ($i = 1; $i < count($prices); $i++) {
            $diff[] = $prices[$i] - $previous;
            $previous = $prices[$i];
        }
        return implode(',', $diff);
    }
}
