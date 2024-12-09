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

    #[Puzzle(2024, day: 9, part: 2)]
    #[TestWithDemoInput(self::DEMO_INPUT, expectedAnswer: 2858)]
    public function part2(PuzzleInput $input): int
    {
        $stringRepresentation = $input->isDemoInput();
        $input = (string)$input;

        $files = [];
        $freeBlocks = [];

        $isFile = true;
        $cursor = 0;
        for ($i = 0; $i < strlen($input); $i++) {
            $size = (int)$input[$i];
            if ($isFile) {
                $files[] = ['start' => $cursor, 'size' => $size];
            } else {
                $freeBlocks[] = ['start' => $cursor, 'size' => $size];
            }

            $cursor += $size;
            $isFile = !$isFile;
        }

        if ($stringRepresentation) {
            $diskDebugMap = str_repeat('.', $cursor);
        }

        $result = 0;
        for ($fileId = count($files) - 1; $fileId >= 0; $fileId--) {
            $file = &$files[$fileId];

            // By default, 'insert' the file on its original location (unless we find a better location below)
            $insertFileAt = $file['start'];

            // Try to move to first free block
            foreach ($freeBlocks as $index => &$freeBlock) {
                if ($freeBlock['start'] > $file['start']) {
                    // We reached the free blocks AFTER the file, stop iterating $freeBlocks
                    break;
                }

                if ($freeBlock['size'] >= $file['size']) {
                    // Insert file on free block location
                    $insertFileAt = $freeBlock['start'];

                    if ($freeBlock['size'] === $file['size']) {
                        // Remove free block
                        unset($freeBlocks[$index]);
                    } else {
                        // Update free block
                        $freeBlock['start'] += $file['size'];
                        $freeBlock['size'] -= $file['size'];
                    }

                    break;
                }
            }

            // Actually insert the file (as in: calculate the result)
            for ($i = 0; $i < $file['size']; $i++) {
                $result += $fileId * ($insertFileAt + $i);

                if ($stringRepresentation) {
                    $diskDebugMap[$insertFileAt + $i] = $fileId;
                }
            }
        }

        if ($stringRepresentation) {
            dump($diskDebugMap);
        }
        return $result;
    }
}
