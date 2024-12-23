<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;

class Day23
{
    private const string DEMO_INPUT = <<<EOF
        kh-tc
        qp-kh
        de-cg
        ka-co
        yn-aq
        qp-ub
        cg-tb
        vc-aq
        tb-ka
        wh-tc
        yn-cg
        kh-ub
        ta-co
        de-co
        tc-td
        tb-wq
        wh-td
        ta-ka
        td-qp
        aq-cg
        wq-ub
        ub-vc
        de-ta
        wq-aq
        wq-vc
        wh-yn
        ka-de
        kh-ta
        co-tc
        wh-qp
        tb-vc
        td-yn
        EOF;

    /**
     * @var array<int, array{0: string, 1: string}>
     */
    private $tuples;

    #[Puzzle(2024, day: 23, part: 1)]
    #[TestWithDemoInput(self::DEMO_INPUT, expectedAnswer: 7)]
    public function part1(PuzzleInput $input): int
    {
        $this->init($input);

        $result = [];
        foreach ($this->tuples as $index => $tuple) {
            [$a, $b] = $tuple;
            foreach ($this->getComputersConnectedWithBoth($a, $b, $index + 1) as $c) {
                if (str_starts_with($a, 't') || str_starts_with($b, 't') || str_starts_with($c, 't')) {
                    $key = [$a, $b, $c];
                    sort($key);
                    $result[implode('-', $key)] = true;
                }
            }
        }
        return count($result);
    }

    private function init(PuzzleInput $input): void
    {
        $this->tuples = $input->splitMap("\n", fn(string $line) => explode('-', $line));
    }

    /**
     * @return string[]
     */
    private function getComputersConnectedWithBoth(string $a, string $b, int $startIndex): array
    {
        $result = [];
        for ($i = $startIndex; $i < count($this->tuples); $i++) {
            if (in_array($this->tuples[$i][0], [$a, $b], true)) {
                $result[$this->tuples[$i][1]] = ($result[$this->tuples[$i][1]] ?? 0) + 1;
            } elseif (in_array($this->tuples[$i][1], [$a, $b], true)) {
                $result[$this->tuples[$i][0]] = ($result[$this->tuples[$i][0]] ?? 0) + 1;
            }
        }
        return array_keys(array_filter($result, fn(int $connections) => $connections === 2));
    }
}
