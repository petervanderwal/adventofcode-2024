<?php

declare(strict_types=1);

namespace App\Utility;

class IterableUtility
{
    public static function removeKeys(iterable $iterable): iterable
    {
        foreach ($iterable as $value) {
            yield $value;
        }
    }
}
