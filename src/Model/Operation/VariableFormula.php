<?php

declare(strict_types=1);

namespace App\Model\Operation;

class VariableFormula
{
    public function __construct(
        public readonly string $variableName,
        public readonly float $multiply = 1,
        public readonly float $add = 0,
    ) {
    }

    public function __toString(): string
    {
        return sprintf('%d * %s + %d', round($this->multiply), $this->variableName, round($this->add));
    }

    public function solveVariable(int $desiredFormulaResult): int
    {
        $desiredFormulaResult -= $this->add;
        return (int)round($desiredFormulaResult / $this->multiply);
    }
}
