<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use App\Utility\NumberUtility;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;

class Day17
{
    private const string OUTPUT_DEMO = <<<EOF
        Register A: 10
        Register B: 0
        Register C: 0
        
        Program: 5,0,5,1,5,4
        EOF;


    private const string DEMO_INPUT = <<<EOF
        Register A: 729
        Register B: 0
        Register C: 0
        
        Program: 0,1,5,4,3,0
        EOF;

    /**
     * @var int[]
     */
    private array $program;
    private int $instructionPointer;
    private int $a;
    private int $b;
    private int $c;

    /**
     * @var int[]
     */
    private array $output = [];

    #[Puzzle(2024, day: 17, part: 1)]
    #[TestWithDemoInput(self::OUTPUT_DEMO, expectedAnswer: '0,1,2')]
    #[TestWithDemoInput(self::DEMO_INPUT, expectedAnswer: '4,6,3,5,6,3,5,2,1,0')]
    public function part1(PuzzleInput $input): string
    {
        $this->program = NumberUtility::getNumbersFromLine($input);

        [$this->a, $this->b, $this->c] = array_splice($this->program, 0, 3);

        $this->output = [];

        $this->instructionPointer = 0;
        while ($this->instructionPointer < count($this->program)) {
             $this->executeInstruction();
        }
        return implode(',', $this->output);
    }

    private function executeInstruction(): void
    {
        $opcode = $this->program[$this->instructionPointer];
        $literalOperand = $this->program[$this->instructionPointer + 1];

        match ($opcode) {
            0 => $this->executeDivision('a', $literalOperand),
            1 => $this->executeBitwiseXor($literalOperand),
            2 => $this->executeSetModulo8($literalOperand),
            3 => $this->executeJump($literalOperand),
            4 => $this->executeBitwiseXorBC(),
            5 => $this->executeOutput($literalOperand),
            6 => $this->executeDivision('b', $literalOperand),
            7 => $this->executeDivision('c', $literalOperand),
            default => throw new \UnexpectedValueException(
                "Opcode $opcode is not expected to happen",
                241217200645
            ),
        };

        if ($opcode !== 3) {
            $this->instructionPointer += 2;
        }
    }

    private function comboOperand(int $literalOperand): int
    {
        return match ($literalOperand) {
            0, 1, 2, 3 => $literalOperand,
            4 => $this->a,
            5 => $this->b,
            6 => $this->c,
            default => throw new \UnexpectedValueException(
                "Combo operand $literalOperand is not expected to happen",
                241217200450
            ),
        };
    }

    private function executeDivision(string $register, int $literalOperand): void
    {
        $this->{$register} = (int)($this->a / pow(2, $this->comboOperand($literalOperand)));
    }

    private function executeBitwiseXor(int $literalOperand): void
    {
        $this->b = $this->b ^ $literalOperand;
    }

    private function executeSetModulo8(int $literalOperand): void
    {
        $this->b = $this->comboOperand($literalOperand) % 8;
    }

    private function executeJump(int $literalOperand): void
    {
        if ($this->a === 0) {
            // Normal step
            $this->instructionPointer += 2;
        } else {
            $this->instructionPointer = $literalOperand;
        }
    }

    private function executeBitwiseXorBC(): void
    {
        $this->b = $this->b ^ $this->c;
    }

    private function executeOutput(int $literalOperand): void
    {
        $this->output[] = $this->comboOperand($literalOperand) % 8;
    }
}
