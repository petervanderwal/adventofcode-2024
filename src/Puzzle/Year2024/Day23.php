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

    #[Puzzle(2024, day: 23, part: 2)]
    #[TestWithDemoInput(self::DEMO_INPUT, expectedAnswer: 'co,de,ka,ta')]
    public function part2(PuzzleInput $input): string
    {
        $network = [];
        foreach ($input->split("\n") as $pair) {
            $pair = $pair->split('-');
            $a = (string)$pair[0];
            $b = (string)$pair[1];
            $network[$a][] = $b;
            $network[$b][] = $a;
        }

        $connectedPCs = $this->getLargestNetwork($network);
        sort($connectedPCs);
        return implode(',', $connectedPCs);
    }

    /**
     * @param array<string, string[]> $network
     * @return string[]
     */
    private function getLargestNetwork(array $network): array
    {
        $result = [];

        // We're shrinking our network each iteration (see line `unset($network[$pc])`) so we've seen every pc when
        // the network is empty.
        while (!empty($network)) {

            // Start with the PC that has the most amount of connections, that one has the highest chance to be
            // included in the largest network
            ['pc' => $pc, 'amountOfConnections' => $amountOfConnections] = $this->getPCWithMostConnections($network);

            if ($amountOfConnections < count($result)) {
                // We've reached the point that a single PC is connected with fewer computers than our current best
                // match. With this PC we're never going to get a better result (nor with the remaining PCs as this
                // was the largest already in our network).
                return $result;
            }

            if ($amountOfConnections === 0) {
                // This only happens on the deepest level of recursion where we 'complete' our network
                return [$pc];
            }

            // Remove this PC from the network completely. We don't want to check it again in the next iteration. This
            // PC either produces the best result (and then that will be stored in $result a few lines below) or it
            // isn't included in the largest network at all.
            $connectedPCs = $network[$pc];
            unset($network[$pc]);
            foreach ($connectedPCs as $connectedPC) {
                $network[$connectedPC] = array_diff($network[$connectedPC], [$pc]);
            }

            // Get the subnetwork of all computers that are connected with $pc
            $subNetwork = $this->getSubNetwork($network, $connectedPCs);

            // And within that subnetwork, get the longest list of PCs that are all connected with each other (and thus
            // also connected with $pc)
            $connections = $this->getLargestNetwork($subNetwork);

            // If that list (plus one to include $pc) is longer than our current $result, then we found a better match
            if (count($connections) + 1 > count($result)) {
                $result = $connections;
                $result[] = $pc;
            }
        }

        return $result;
    }

    /**
     * @param array<string, string[]> $network
     * @return array{pc: string, amountOfConnections: int}
     */
    private function getPCWithMostConnections(array $network): array
    {
        $pcWithMostConnections = null;
        $largestAmountOfConnections = -1;
        foreach ($network as $pc => $connections) {
            if (count($connections) > $largestAmountOfConnections) {
                $pcWithMostConnections = $pc;
                $largestAmountOfConnections = count($connections);
            }
        }
        return ['pc' => $pcWithMostConnections, 'amountOfConnections' => $largestAmountOfConnections];
    }

    /**
     * Returns the subnetwork containing only the PCs mentioned
     *
     * @param array<string, string[]> $network
     * @param string[] $pcs
     * @return array<string, string[]>
     */
    private function getSubNetwork(array $network, array $pcs): array
    {
        $subNetwork = [];
        foreach ($pcs as $pc) {
            $subNetwork[$pc] = array_intersect($network[$pc], $pcs);
        }
        return $subNetwork;
    }
}
