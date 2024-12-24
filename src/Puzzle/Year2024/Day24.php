<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;

class Day24
{
    private const string DEMO_INPUT = <<<EOF
        x00: 1
        x01: 0
        x02: 1
        x03: 1
        x04: 0
        y00: 1
        y01: 1
        y02: 1
        y03: 1
        y04: 1
        
        ntg XOR fgs -> mjb
        y02 OR x01 -> tnw
        kwq OR kpj -> z05
        x00 OR x03 -> fst
        tgd XOR rvg -> z01
        vdt OR tnw -> bfw
        bfw AND frj -> z10
        ffh OR nrd -> bqk
        y00 AND y03 -> djm
        y03 OR y00 -> psh
        bqk OR frj -> z08
        tnw OR fst -> frj
        gnj AND tgd -> z11
        bfw XOR mjb -> z00
        x03 OR x00 -> vdt
        gnj AND wpb -> z02
        x04 AND y00 -> kjc
        djm OR pbm -> qhw
        nrd AND vdt -> hwm
        kjc AND fst -> rvg
        y04 OR y02 -> fgs
        y01 AND x02 -> pbm
        ntg OR kjc -> kwq
        psh XOR fgs -> tgd
        qhw XOR tgd -> z09
        pbm OR djm -> kpj
        x03 XOR y03 -> ffh
        x00 XOR y04 -> ntg
        bfw OR bqk -> z06
        nrd XOR fgs -> wpb
        frj XOR qhw -> z04
        bqk OR frj -> z07
        y03 OR x01 -> nrd
        hwm AND bqk -> z03
        tgd XOR rvg -> z12
        tnw OR pbm -> gnj
        EOF;

    #[Puzzle(2024, day: 24, part: 1)]
    #[TestWithDemoInput(self::DEMO_INPUT, expectedAnswer: 2024)]
    public function part1(PuzzleInput $input): int
    {
        $register = [];
        foreach ($input->splitLines() as $line) {
            if (preg_match('/^(.*): (0|1)$/', $line, $matches)) {
                $register[$matches[1]] = (bool)$matches[2];
            } elseif (preg_match('/^(.*) (AND|OR|XOR) (.*) -> (.*)$/', $line, $matches)) {
                $register[$matches[4]] = [$matches[2], $matches[1], $matches[3]];
            } elseif ($line !== '') {
                throw new \UnexpectedValueException("Unexpected line: $line", 241224094718);
            }
        }

        $result = 0;
        foreach (array_keys($register) as $variable) {
            if ($variable[0] === 'z') {
                $shift = (int)substr($variable, 1);
                $result += ((int)$this->get($variable, $register)) << $shift;
            }
        }
        return $result;
    }

    private function get(string $variable, array &$register): bool
    {
        if (is_bool($register[$variable])) {
            return $register[$variable];
        }

        [$operation, $a, $b] = $register[$variable];
        $a = $this->get($a, $register);
        $b = $this->get($b, $register);
        return $register[$variable] = match($operation) {
            'AND' => $a && $b,
            'OR' => $a || $b,
            'XOR' => ($a && !$b) || (!$a && $b),
        };
    }
}
