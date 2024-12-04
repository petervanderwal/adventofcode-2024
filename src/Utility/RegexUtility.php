<?php

declare(strict_types=1);

namespace App\Utility;

use Symfony\Component\String\UnicodeString;

class RegexUtility
{
    public static function extractAll(string $regex, string|UnicodeString $subject, int $group = 0, ?callable $parse = null): array
    {
        preg_match_all($regex, (string)$subject, $matches);
        $result = $matches[$group];
        return $parse == null ? $result : array_map($parse, $result);
    }
}
