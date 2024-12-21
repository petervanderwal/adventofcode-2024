<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024\Day21;

class Human implements ICanPressAButton
{
    public function getInstructionsToPress(string $button): string
    {
        return $button;
    }

    public function getCostToPress(string $button): int
    {
        return 1;
    }

    public function resetCursor(): void
    {
        // No-op
    }
}
