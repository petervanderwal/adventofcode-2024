<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;

class Day09
{
    private const string DEMO_INPUT = '2333133121414131402';

    #[Puzzle(2024, day: 9, part: 1)]
    #[TestWithDemoInput(self::DEMO_INPUT, expectedAnswer: 1928)]
    public function part1(PuzzleInput $input): int
    {
        $stringRepresentation = $input->isDemoInput();
        $diskMap = (string)$input;

        $leftCursorStringPosition = 0;
        $leftCursorFileId = 0;

        $rightCursorStringPosition = strlen($diskMap) - 1;
        if ($rightCursorStringPosition % 2 !== 0) {
            $rightCursorStringPosition--; // Last digit was indicating free space
        }
        $rightCursorFileId = $rightCursorStringPosition / 2;
        $rightCursorBytesLeft = (int)$diskMap[$rightCursorStringPosition];

        $diskCursor = 0;
        $result = 0;
        $diskDebugMap = '';

        while ($leftCursorStringPosition < $rightCursorStringPosition) {
            // Parse file
            $fileLength = (int)$diskMap[$leftCursorStringPosition];
            for ($i = 0; $i < $fileLength; $i++) {
                $result += $diskCursor++ * $leftCursorFileId;
                if ($stringRepresentation) {
                    $diskDebugMap .= $leftCursorFileId;
                }
            }
            $leftCursorFileId++;
            $leftCursorStringPosition++;

            // Parse free space
            $freeSpaceLength = (int)$diskMap[$leftCursorStringPosition];
            for ($i = 0; $i < $freeSpaceLength; $i++) {
                while ($rightCursorBytesLeft === 0) {
                    $rightCursorStringPosition -= 2;
                    if ($rightCursorStringPosition < $leftCursorStringPosition) {
                        throw new \UnexpectedValueException('I don\'t know how to handle this yet');
                    }
                    $rightCursorFileId--;
                    $rightCursorBytesLeft = (int)$diskMap[$rightCursorStringPosition];
                }

                $result += $diskCursor++ * $rightCursorFileId;
                if ($stringRepresentation) {
                    $diskDebugMap .= $rightCursorFileId;
                }
                $rightCursorBytesLeft--;
            }
            $leftCursorStringPosition++;
        }

        // Handle remaining bytes
        while ($rightCursorBytesLeft > 0) {
            $result += $diskCursor++ * $rightCursorFileId;
            if ($stringRepresentation) {
                $diskDebugMap .= $rightCursorFileId;
            }
            $rightCursorBytesLeft--;
        }

        if ($stringRepresentation) {
            dump($diskDebugMap);
        }
        return $result;
    }
}
