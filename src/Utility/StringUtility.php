<?php

declare(strict_types=1);

namespace App\Utility;

class StringUtility
{
    public static function repeat(string $string, int $times): string
    {
        return $times === 0 ? '' : str_repeat($string, $times);
    }
}
