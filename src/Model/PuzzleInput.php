<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\String\UnicodeString;

class PuzzleInput extends UnicodeString
{
    public function __construct(
        string $string = '',
        private readonly bool $isDemoInput = false,
    ) {
        parent::__construct($string);
    }

    public function isDemoInput(): bool
    {
        return $this->isDemoInput;
    }

    public function __sleep(): array
    {
        return [...parent::__sleep(), 'isDemoInput'];
    }

    public function mapLines(callable $callback = null, bool $asObject = false): array
    {
        $callback ??= fn ($line) => $line;
        if (!$asObject) {
            $callback = fn ($line) => $callback((string)$line);
        }
        return array_map(
            $callback,
            $this->split("\n")
        );
    }

    /**
     * @return int[]
     */
    public function mapIntegers(string $separator = ','): array
    {
        return array_map(
            fn (UnicodeString $string): int => (int)(string)$string,
            $this->split($separator)
        );
    }
}