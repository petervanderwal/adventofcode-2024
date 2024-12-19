<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;
use Spatie\Async\Pool;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

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

    #[Puzzle(2024, day: 19, part: 1)]
    #[TestWithDemoInput(self::DEMO_INPUT, expectedAnswer: 6)]
    public function part1(PuzzleInput $input): int
    {
        [$towels, $desiredPatterns] = $input->split("\n\n");
        $desiredPatterns = $desiredPatterns->splitLines();

        $this->availableTowels = ['w' => [], 'u' => [], 'b' => [], 'r' => [], 'g' => []];
        foreach ($towels->split(", ") as $towel) {
            $towel = (string)$towel;
            $this->availableTowels[$towel[0]][] = $towel;
        }

        $pool = Pool::create();
        $progressBar = new ProgressBar(new ConsoleOutput(), count($desiredPatterns));
        $progressBar->start();

        $result = 0;
        foreach ($desiredPatterns as $pattern) {
            $pool->add(function () use ($pattern): bool {
                    return $this->isAvailable($pattern);
                })
                ->then(function (bool $isAvailable) use ($progressBar, &$result) {
                    $progressBar->advance();
                    if ($isAvailable) {
                        $result++;
                    }
                });
        }

        $pool->wait();
        $progressBar->finish();
        echo "\n";
        return $result;
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
}
