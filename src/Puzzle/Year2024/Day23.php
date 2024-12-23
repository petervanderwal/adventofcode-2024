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

        $result = 0;
        foreach ($this->tuples as $index => $tuple) {
            [$a, $b] = $tuple;
            foreach ($this->getComputersConnectedWithAll($index + 1, $a, $b) as $c => $ignore) {
                if (str_starts_with($a, 't') || str_starts_with($b, 't') || str_starts_with($c, 't')) {
                    $result++;
                }
            }
        }
        return $result;
    }

    #[Puzzle(2024, day: 23, part: 2)]
    #[TestWithDemoInput(self::DEMO_INPUT, expectedAnswer: 'co,de,ka,ta')]
    public function part2(PuzzleInput $input): string
    {
        $this->init($input);

        $networks = [];
        foreach ($this->tuples as $index => $tuple) {
            $networks["{$tuple[0]},{$tuple[1]}"] = [
                'lowestIndex' => $index,
                'network' => $tuple,
            ];
        }

        while (true) {
            $newNetworks = [];
            foreach ($networks as $info) {
                foreach ($this->getComputersConnectedWithAll($info['lowestIndex'], ...$info['network']) as $next => $nextInfo) {
                    $network = [...$info['network'], $next];
                    sort($network);
                    $key = implode(',', $network);
                    if (isset($newNetworks[$key])) {
                        $newNetworks[$key]['lowestIndex'] = min($newNetworks[$key]['lowestIndex'], $nextInfo['lowestIndex']);
                    } else {
                        $newNetworks[$key] = [
                            'lowestIndex' => $nextInfo['lowestIndex'],
                            'network' => $network,
                        ];
                    }
                }
            }

            if (empty($newNetworks)) {
                if (current($networks)['lowestIndex'] === 0) {
                    // We couldn't expand, the previous iteration contains the largest
                    dump(array_keys($networks));
                    return array_keys($networks)[0];
                } else {
                    // We couldn't expand, retry once with the lowest index reset
                    // This shouldn't be necessary, just a try from my end to see if this solves my "I get 4 largest networks"
                    // result
                    foreach ($networks as &$info) {
                        $info['lowestIndex'] = 0;
                    }
                }
            } else {
                $networks = $newNetworks;
            }
        }
    }

    private function init(PuzzleInput $input): void
    {
        $this->tuples = $input->splitMap("\n", fn(string $line) => explode('-', $line));
    }

    /**
     * @return array<string, array{lowestIndex: int, connections: int}>
     */
    private function getComputersConnectedWithAll(int $startIndex, string ...$connectWithAll): array
    {
        $result = [];
        for ($i = $startIndex; $i < count($this->tuples); $i++) {
            $aInArray = in_array($this->tuples[$i][0], $connectWithAll, true);
            $bInArray = in_array($this->tuples[$i][1], $connectWithAll, true);
            if ($aInArray && !$bInArray) {
                $new = $this->tuples[$i][1];
            } elseif ($bInArray && !$aInArray) {
                $new = $this->tuples[$i][0];
            } else {
                continue;
            }

            if (!isset($result[$new])) {
                $result[$new] = ['lowestIndex' => $i, 'connections' => 1];
            } else {
                $result[$new]['connections']++;
            }
        }
        return array_filter($result, fn(array $data) => $data['connections'] === count($connectWithAll));
    }
}
