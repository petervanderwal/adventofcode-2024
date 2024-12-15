<?php

declare(strict_types=1);

namespace App\Utility;

use App\Model\Point;
use UnexpectedValueException;

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

    /**
     * For a formula
     *     x * $factor % $modulo = $remainder
     * solve x in the form x = first + repeat * i    (where i is integer)
     *
     * @return null|array{first: int, repeat: int}
     */
    public static function reverseModule(int $factor, int $modulo, int $remainder): ?array
    {
        $gcd = MathUtility::greatestCommonDivisor($factor, $modulo);
        if ($remainder % $gcd !== 0) {
            return null;
        }

        $factor /= $gcd;
        $modulo /= $gcd;
        $remainder /= $gcd;

        for ($first = 0; $first < $modulo; $first++) {
            if (($first * $factor) % $modulo === $remainder) {
                return ['first' => $first, 'repeat' => $modulo];
            }
        }
        throw new UnexpectedValueException('Failed to reverse module, although gcd indicates it should succeed', 241215193745);
    }
}
