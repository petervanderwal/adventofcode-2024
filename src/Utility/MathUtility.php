<?php

declare(strict_types=1);

namespace App\Utility;

use App\Model\Point;

class MathUtility
{
    /**
     * To solve: ax^2 + bx + c = 0
     * Calculate x = (-b +/- sqrt(b^2 - 4*a*c))/(2a)
     * @return array{0: float, 1: float} Where index 0 is always the lower number, and index 1 is always the higher number
     */
    public static function abcFormula(int|float $a, int|float $b, int|float $c): array
    {
        return [
            self::_abcFormula($a, $b, $c,  $a > 0 ? -1 : 1),
            self::_abcFormula($a, $b, $c,  $a > 0 ? 1 : -1)
        ];
    }

    /**
     * To solve: ax^2 + bx + c = 0
     * Calculate x = (-b +/- sqrt(b^2 - 4*a*c))/(2a)
     */
    private static function _abcFormula(int|float $a, int|float $b, int|float $c, int $sign): float
    {
        return (-$b + $sign * sqrt(pow($b, 2) - 4 * $a * $c)) / (2 * $a);
    }

    public static function greatestCommonDivisor(int $a, int $b, int ...$c): int
    {
        $result = static::_greatestCommonDivisor($a, $b);
        foreach ($c as $number) {
            $result = static::_greatestCommonDivisor($result, $number);
        }
        return $result;
    }

    private static function _greatestCommonDivisor(int $a, int $b): int
    {
        // Euclidean Algorithm: https://en.wikipedia.org/wiki/Greatest_common_divisor#Euclidean_algorithm
        $max = (int)max($a, $b);
        $min = (int)min($a, $b);
        return $min === 0 ? $max : static::_greatestCommonDivisor($min, $max % $min);
    }

    public static function shoelaceFormula(Point ...$points): float
    {
        // https://en.wikipedia.org/wiki/Shoelace_formula
        // and https://artofproblemsolving.com/wiki/index.php/Shoelace_Theorem

        $sum = 0;
        foreach ($points as $index => $leftPoint) {
            $rightPoint = $points[($index + 1) % count($points)];
            $sum += $leftPoint->x * $rightPoint->y - $leftPoint->y * $rightPoint->x;
        }
        return abs($sum) / 2;
    }
}
