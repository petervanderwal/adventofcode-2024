<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use App\Utility\NumberUtility;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

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

    #[Puzzle(2024, day: 17, part: 2)]
    #[TestWithDemoInput(<<<EOF
        Register A: 2024
        Register B: 0
        Register C: 0
        
        Program: 0,3,5,4,3,0
        EOF, expectedAnswer: 117440)]
    public function part2(PuzzleInput $input): int
    {
        $this->program = NumberUtility::getNumbersFromLine($input);
        [, $b, $c] = array_splice($this->program, 0, 3);

        // Checking some program data (and assumptions)
        if (
            $this->program[count($this->program) - 2] !== 3
            || $this->program[count($this->program) - 1] !== 0
        ) {
            throw new \UnexpectedValueException('Last operation is not "jump back to 0"', 241221132610);
        }

        $aShifts = 0;
        $otherShifts = 0;
        $hasOut = false;
        for ($i = 0; $i < count($this->program) - 2; $i += 2) {
            $opcode = $this->program[$i];
            $operand = $this->program[$i + 1];
            switch ($opcode) {
                case 0:
                    if ($operand >= 4) {
                        throw new \UnexpectedValueException('Combo operand for adv operation', 241221132444);
                    }
                    $aShifts += $operand;
                    break;
                case 3:
                    throw new \UnexpectedValueException('Intermediate jnz operation', 241221132444);
                case 5:
                    if ($hasOut) {
                        throw new \UnexpectedValueException('Multiple out operations', 241221132824);
                    }
                    $hasOut = true;
                    break;
                case 6:
                case 7:
                    // Make assumption here that if a combo operand is used, the value of the register is % 8 first
                    $otherShifts += min(3, $operand);
            }
        }

        if ($aShifts === 0) {
            throw new \UnexpectedValueException('No adv operations', 241221132849);
        }

        // Get the absolute max for A -- if it was higher, the program will go another loop (and thus produce more output)
        $absoluteMaxA = pow(2, ($aShifts * count($this->program))) - 1;

        $output = new ConsoleOutput();
        $progressBar = new ProgressBar($output->section());
        $innerBar = new ProgressBar($output->section());
        $progressBar->start(count($this->program));

        // Get the solutions for the first digit. If we have those, the possible solutions of the 2nd digit can
        // be determined by a step size of 8 (if $aShift = 3), the possible solutions of the 3rd digit with a step size
        // of 64 and so on. Reason: on printing the 2nd digit, the last 3 bits of the answer doesn't matter anymore
        // as these are shifted of the A register already.
        $solutions = [0];
        for ($solveFirstChars = 1; $solveFirstChars <= count($this->program); $solveFirstChars++) {
            $newSolutions = [];

            $aMax = min(
                $absoluteMaxA,
                1 << ($aShifts * ($solveFirstChars + 1)) << $otherShifts
            );
            $aIncrement = 1 << ($aShifts * ($solveFirstChars - 1));

            $innerBar->start((int)ceil($aMax / $aIncrement));
            for ($a = 0; $a < $aMax; $a += $aIncrement) {
                foreach ($solutions as $solution) {
                    $aTry = $solution + $a;
                    if ($this->solvePart2With($aTry, $b, $c, $solveFirstChars)) {
                        $newSolutions[] = $aTry;
                    }
                }
                $innerBar->advance();
            }

            $innerBar->finish();
            $progressBar->advance();
            $progressBar->display();

            if (empty($newSolutions)) {
                throw new \UnexpectedValueException(
                    "No solution found for the {$solveFirstChars}th character",
                    241221145702
                );
            }
            $solutions = array_unique($newSolutions);
        }

        $progressBar->finish();
        return min($solutions);
    }

    private function solvePart2With(int $a, int $b, int $c, ?int $solveFirstChars = null): bool
    {
        $this->a = $a;
        $this->b = $b;
        $this->c = $c;

        $this->output = [];
        $verifiedOutput = 0;

        $this->instructionPointer = 0;
        while ($this->instructionPointer < count($this->program)) {
            $this->executeInstruction();
            if (count($this->output) > $verifiedOutput) {
                if ($this->output[$verifiedOutput] !== ($this->program[$verifiedOutput] ?? null)) {
                    // Early return on first invalid output
                    return false;
                }
                $verifiedOutput++;
                if ($verifiedOutput === $solveFirstChars) {
                    return true;
                }
            }
        }
        return $this->output === $this->program;
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
