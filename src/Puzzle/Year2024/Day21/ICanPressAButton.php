<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024\Day21;

interface ICanPressAButton
{
    public function getInstructionsToPress(string $button): string;
    public function getCostToPress(string $button): int;
    public function resetCursor(): void;
}
