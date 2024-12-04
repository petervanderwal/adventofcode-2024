<?php

declare(strict_types=1);

namespace App\Utility;

use Symfony\Component\String\UnicodeString;

class NumberUtility
{
    public static function getSign(int|float $number): int
    {
        if ($number === 0) {
            return 0;
        }
        return $number > 0 ? 1 : -1;
    }

    /**
     * @return int[]
     */
    public static function getNumbersFromLine(string|UnicodeString $line): array
    {
        return RegexUtility::extractAll('/-?[0-9]+/', $line, parse: fn (string $val) => (int)$val);
    }

    public static function positiveModulo(int $number, int $divider): int
    {
        $result = $number % $divider;
        if ($result < 0) {
            $result += $divider;
        }
        return $result;
    }
}