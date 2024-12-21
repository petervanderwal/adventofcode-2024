<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024\Day21;

class Robot implements ICanPressAButton
{
    private string $cursor = 'A';
    private array $cache = [];

    public function __construct(
        private readonly Keypad $keypad,
        private readonly ICanPressAButton $operator,
    ) {
    }

    public function getInstructionsToPress(string $button): string
    {
        $bestOption = null;
        foreach ($this->keypad->getInstructions($this->cursor, $button) as $instruction) {
            $option = '';
            foreach ($instruction as $direction) {
                $option .= $this->operator->getInstructionsToPress($direction);
            }
            if ($bestOption === null || strlen($option) < strlen($bestOption)) {
                $bestOption = $option;
            }
        }

        $this->cursor = $button;
        return $bestOption;
    }

    public function getCostToPress(string $button): int
    {
        $cacheKey = $this->cursor . $button;
        if (isset($this->cache[$cacheKey])) {
            $this->cursor = $button;
            return $this->cache[$cacheKey];
        }

        $bestOption = null;
        foreach ($this->keypad->getInstructions($this->cursor, $button) as $instruction) {
            $option = 0;
            foreach ($instruction as $direction) {
                $option += $this->operator->getCostToPress($direction);
            }
            if ($bestOption === null || $option < $bestOption) {
                $bestOption = $option;
            }
        }

        $this->cursor = $button;
        return $this->cache[$cacheKey] = $bestOption;
    }

    public function resetCursor(): void
    {
        $this->cursor = 'A';
        $this->operator->resetCursor();
    }
}
