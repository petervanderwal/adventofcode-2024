<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;

class Day05
{
    #[Puzzle(2024, day: 5, part: 1)]
    #[TestWithDemoInput(
        input: <<<EOF
            47|53
            97|13
            97|61
            97|47
            75|29
            61|13
            75|53
            29|13
            97|29
            53|29
            61|53
            97|53
            61|29
            47|13
            75|47
            97|75
            47|61
            75|61
            47|29
            75|13
            53|13
            
            75,47,61,53,29
            97,61,53,29,13
            75,29,13
            75,97,47,61,53
            61,13,29
            97,13,75,29,47
            EOF,
        expectedAnswer: 143,
    )]
    public function part1(PuzzleInput $input): int
    {
        $blocks = $input->split("\n\n");
        $orderRules = $this->getOrderRules($blocks[0]);

        $result = 0;
        foreach ($blocks[1]->split("\n") as $pageNumbers) {
            $pageNumbers = $pageNumbers->splitInt();
            if ($this->isOrderedCorrectly($orderRules, ...$pageNumbers)) {
                $result += $pageNumbers[(int)(count($pageNumbers) / 2)];
            }
        }
        return $result;
    }

    /**
     * @return array<int, int[]> With the before on the key and all the afters on the value
     */
    private function getOrderRules(PuzzleInput $input): array
    {
        $order = [];
        foreach ($input->split("\n") as $line) {
            [$before, $after] = $line->splitInt('|');
            $order[$before][] = (int)$after;
        }
        return $order;
    }

    /**
     * @param array<int, int[]> $orderRules With the before on the key and all the afters on the value
     */
    private function isOrderedCorrectly(array $orderRules, int ...$pageNumbers): bool
    {
        while (count($pageNumbers) > 1) {
            $lastNumber = array_pop($pageNumbers);
            if (count(array_intersect($pageNumbers, $orderRules[$lastNumber] ?? [])) > 0) {
                return false;
            }
        }

        return true;
    }

    #[Puzzle(2024, day: 5, part: 2)]
    #[TestWithDemoInput(
        input: <<<EOF
            47|53
            97|13
            97|61
            97|47
            75|29
            61|13
            75|53
            29|13
            97|29
            53|29
            61|53
            97|53
            61|29
            47|13
            75|47
            97|75
            47|61
            75|61
            47|29
            75|13
            53|13
            
            75,47,61,53,29
            97,61,53,29,13
            75,29,13
            75,97,47,61,53
            61,13,29
            97,13,75,29,47
            EOF,
        expectedAnswer: 123,
    )]
    public function part2(PuzzleInput $input): int
    {
        $blocks = $input->split("\n\n");
        $orderRules = $this->getOrderRules($blocks[0]);

        $result = 0;
        foreach ($blocks[1]->split("\n") as $pageNumbers) {
            $pageNumbers = $pageNumbers->splitInt();
            if ($this->isOrderedCorrectly($orderRules, ...$pageNumbers)) {
                continue;
            }

            $correctPageNumbers = $this->reorder($orderRules, ...$pageNumbers);
            $result += $correctPageNumbers[(int)(count($correctPageNumbers) / 2)];
        }
        return $result;
    }

    /**
     * @param array<int, int[]> $orderRules With the before on the key and all the afters on the value
     * @return int[]
     */
    private function reorder(array $orderRules, int ...$pageNumbers): array
    {
        $reversedResult = [];

        while (count($pageNumbers) > 0) {
            foreach ($pageNumbers as $pageNumber) {
                if (empty(array_intersect($orderRules[$pageNumber] ?? [], $pageNumbers))) {
                    // We have nothing prohibiting pushing this to the end
                    $reversedResult[] = $pageNumber;
                    $pageNumbers = array_diff($pageNumbers, [$pageNumber]);
                    break;
                }
            }
        }

        return array_reverse($reversedResult);
    }
}