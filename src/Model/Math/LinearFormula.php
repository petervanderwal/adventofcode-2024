<?php

declare(strict_types=1);

namespace App\Model\Math;

class LinearFormula
{
    public function __construct(
        public readonly int|float $base,
        public readonly int|float $factor,
    ) {}

    public static function getXyFormulaFromTwoTimeFormulas(
        LinearFormula $timeXFormula,
        LinearFormula $timeYFormula,
    ): LinearFormula {
        if ($timeXFormula->factor === 0) {
            throw new \InvalidArgumentException('x value is always the same', 231224113849);
        }

        return new self(
            $timeYFormula($timeXFormula->whenEquals(0)),
            $timeYFormula->factor / $timeXFormula->factor
        );
    }

    public function calculate(int|float $x): int|float
    {
        return $this->base + $x * $this->factor;
    }

    public function __invoke(int|float $x): int|float
    {
        return $this->calculate($x);
    }

    /**
     * @return ($this->factor === 0 ? When : float)
     */
    public function whenEquals(int|float $equals): float|When
    {
        if ($this->factor === 0) {
            return $this->base === $equals ? When::ALWAYS : When::NEVER;
        }
        return ($equals - $this->base) / $this->factor;
    }

    /**
     * @return ($this->factor === $other->factor ? When : float)
     */
    public function whenCrossing(LinearFormula $other): float|When
    {
        if ($this->factor === $other->factor) {
            return $this->base === $other->base ? When::ALWAYS : When::NEVER;
        }

        // $this->base + $x * $this->factor = $other->base + $x * $other->factor
        // $x * $this->factor - $x * $other->factor = $other->base - $this->base
        // $x * ($this->factor - $other->factor) = $other->base - $this->base
        return ($other->base - $this->base) / ($this->factor - $other->factor);

    }
}
