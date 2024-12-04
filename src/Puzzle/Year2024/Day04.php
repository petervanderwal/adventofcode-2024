<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;

class Day04
{
    #[Puzzle(2024, day: 4, part: 1)]
    #[TestWithDemoInput(
        input: <<<EOF
            ..X...
            .SAMX.
            .A..A.
            XMAS.S
            .X....
            EOF,
        expectedAnswer: 4,
        name: 'demo-small',
    )]
    #[TestWithDemoInput(
        input: <<<EOF
            MMMSXXMASM
            MSAMXMSMSA
            AMXSXMAAMM
            MSAMASMSMX
            XMASAMXAMM
            XXAMMXXAMA
            SMSMSASXSS
            SAXAMASAAA
            MAMMMXMMMM
            MXMXAXMASX
            EOF,
        expectedAnswer: 18,
    )]
    public function part1(PuzzleInput $input): int
    {
        $count = 0;
        foreach ($this->getLines((string)$input) as $line) {
            $count += substr_count($line, 'XMAS');
        }
        return $count;
    }

    /**
     * @return string[]
     */
    private function getLines(string $input): array
    {
        $result = [];

        // Horizontal lines
        $horizontalLines = [];
        foreach (explode("\n", $input) as $row => $line) {
            $horizontalLines[] = $line;
            $this->appendLine($result, $line);
        }

        // Vertical lines
        for ($x = 0; $x < strlen($horizontalLines[0]); $x++) {
            $verticalLine = '';
            for ($y = 0; $y < count($horizontalLines); $y++) {
                $verticalLine .= $horizontalLines[$y][$x];
            }
            $this->appendLine($result, $verticalLine);
        }

        // Diagonal \ lines
        for ($startY = count($horizontalLines) - 1; $startY >= 0; $startY--) {
            $diagonalLine = '';
            for ($x = 0; $x < count($horizontalLines) - $startY; $x++) {
                $diagonalLine .= $horizontalLines[$startY + $x][$x];
            }
            $this->appendLine($result, $diagonalLine);
        }
        for ($startX = 1; $startX < strlen($horizontalLines[0]); $startX++) {
            $diagonalLine = '';
            for ($y = 0; $y < strlen($horizontalLines[0]) - $startX; $y++) {
                $diagonalLine .= $horizontalLines[$y][$startX + $y];
            }
            $this->appendLine($result, $diagonalLine);
        }

        // Diagonal / lines
        for ($startY = 0; $startY < count($horizontalLines); $startY++) {
            $diagonalLine = '';
            for ($x = 0; $x <= $startY; $x++) {
                $diagonalLine .= $horizontalLines[$startY - $x][$x];
            }
            $this->appendLine($result, $diagonalLine);
        }
        for ($startX = 1; $startX < strlen($horizontalLines[0]); $startX++) {
            $diagonalLine = '';
            for ($y = count($horizontalLines) - 1; $y >= $startX; $y--) {
                $diagonalLine .= $horizontalLines[$y][$startX + (count($horizontalLines) - 1 - $y)];
            }
            $this->appendLine($result, $diagonalLine);
        }

        return $result;
    }

    private function appendLine(array &$lines, string $line): void
    {
        $lines[] = $line;
        $lines[] = strrev($line);
    }

    #[Puzzle(2024, day: 4, part: 2)]
    #[TestWithDemoInput(
        input: <<<EOF
            M.S
            .A.
            M.S
            EOF,
        expectedAnswer: 1,
        name: 'demo-small',
    )]
    #[TestWithDemoInput(
        input: <<<EOF
            MMMSXXMASM
            MSAMXMSMSA
            AMXSXMAAMM
            MSAMASMSMX
            XMASAMXAMM
            XXAMMXXAMA
            SMSMSASXSS
            SAXAMASAAA
            MAMMMXMMMM
            MXMXAXMASX
            EOF,
        expectedAnswer: 9,
    )]
    public function part2(PuzzleInput $input): int
    {
        $horizontalLines = explode("\n", (string)$input);

        $count = 0;
        for ($x = 1; $x < strlen($horizontalLines[0]) - 1; $x++) {
            for ($y = 1; $y < count($horizontalLines) - 1; $y++) {
                if ($horizontalLines[$y][$x] !== 'A') {
                    continue;
                }

                $diagonalForward = $horizontalLines[$y + 1][$x - 1] . $horizontalLines[$y][$x] . $horizontalLines[$y - 1][$x + 1];
                $diagonalBackward = $horizontalLines[$y - 1][$x - 1] . $horizontalLines[$y][$x] . $horizontalLines[$y + 1][$x + 1];
                if (
                    ($diagonalForward === 'MAS' || $diagonalForward === 'SAM')
                    && ($diagonalBackward === 'MAS' || $diagonalBackward === 'SAM')
                ) {
                    $count++;
                }
            }
        }
        return $count;
    }
}
