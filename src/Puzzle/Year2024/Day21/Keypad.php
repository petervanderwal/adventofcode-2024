<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024\Day21;

use App\Model\Direction;
use App\Model\Grid;
use App\Model\Point;

class Keypad
{
    private array $coordinates = [];
    private array $legalPoints = [];

    public function __construct(string $keypad)
    {
        Grid::read($keypad, function (string $char, Point $coordinate) {
            if ($char !== ' ') {
                $this->coordinates[$char] = $coordinate;
                $this->legalPoints[] = (string)$coordinate;
            }
            return $char;
        });
    }

    public function getInstructions(string $cursorChar, string $pressChar): array
    {
        $cursorPoint = $this->coordinates[$cursorChar];
        $pressPoint = $this->coordinates[$pressChar];

        $diffX = $pressPoint->x - $cursorPoint->x;
        if ($diffX < 0) {
            $xPresses = array_fill(0, -$diffX, '<');
        } elseif ($diffX > 0) {
            $xPresses = array_fill(0, $diffX, '>');
        } else {
            $xPresses = [];
        }

        $diffY = $pressPoint->y - $cursorPoint->y;
        if ($diffY < 0) {
            $yPresses = array_fill(0, -$diffY, '^');
        } elseif ($diffY > 0) {
            $yPresses = array_fill(0, $diffY, 'v');
        } else {
            $yPresses = [];
        }

        // If we have <<< and ^^, then only return <<<^^ and ^^<<< as any <^^<<< will either cost more or the same
        // presses for the next robot
        $instructions = [
            [...$xPresses, ...$yPresses, 'A'],
            [...$yPresses, ...$xPresses, 'A'],
        ] ;

        // Filter out invalid instructions
        return array_filter($instructions, function ($instruction) use ($cursorPoint): bool {
            foreach ($instruction as $direction) {
                if ($direction === 'A') {
                    continue;
                }
                $cursorPoint = $cursorPoint->moveDirection(Direction::fromCharacter($direction));
                if (!in_array((string)$cursorPoint, $this->legalPoints)) {
                    return false;
                }
            }
            return true;
        });
    }
}
