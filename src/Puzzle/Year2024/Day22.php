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

    private function getNextSecretNumber(int $secret): int
    {
        $secret = ($secret ^ ($secret * 64)) % 16777216;
        $secret = ($secret ^ (int)($secret / 32)) % 16777216;
        $secret = ($secret ^ ($secret * 2048)) % 16777216;
        return $secret;
    }
}
