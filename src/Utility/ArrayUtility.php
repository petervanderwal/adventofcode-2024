<?php

declare(strict_types=1);

namespace App\Utility;

class ArrayUtility
{
    public static function getSlidingChunks(iterable $input, int $chunkSize): array
    {
        if ($chunkSize <= 0) {
            throw new \InvalidArgumentException('$chunkSize should be > 0, ' . $chunkSize . ' given', 230921132317);
        }
        $result = [];

        $index = 0;
        foreach ($input as $value) {
            for ($chunk = 0; $chunk < $chunkSize && $index - $chunk >= 0; $chunk++) {
                $result[$index - $chunk][] = $value;
            }
            $index++;
        }

        // Delete uncompleted chunks
        for ($chunk = 1; $chunk < $chunkSize && $index - $chunk > 0; $chunk++) {
            unset($result[$index - $chunk]);
        }

        return $result;
    }

    public static function getMedian(int ...$numbers): int|float
    {
        sort($numbers);
        if (count($numbers) % 2 === 1) {
            return $numbers[floor(count($numbers) / 2)];
        }

        return ($numbers[count($numbers) / 2] + $numbers[count($numbers) / 2 - 1]) / 2;
    }

    public static function unique(array $values): array
    {
        $unique = [];
        foreach ($values as $value) {
            $key = self::getUniqueStringValue($value);
            if (!array_key_exists($key, $unique)) {
                $unique[$key] = $value;
            }
        }
        return array_values($unique);
    }

    public static function intersect(array $array1, array $array2): array
    {
        return self::compare($array1, $array2, true);
    }

    public static function diff(array $array1, array $array2): array
    {
        return self::compare($array1, $array2, false);
    }

    private static function compare(array $array1, array $array2, bool $shouldExistInArray2): array
    {
        $array2Values = array_map(fn ($value) => self::getUniqueStringValue($value), $array2);
        $result = [];
        foreach ($array1 as $key => $value) {
            if (in_array(self::getUniqueStringValue($value), $array2Values) === $shouldExistInArray2) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    private static function getUniqueStringValue(mixed $value): string
    {
        return $value instanceof \UnitEnum ? get_class($value) . '::' . $value->name : (string)$value;
    }

    public static function multiply(array $array): int|float
    {
        if (empty($array)) {
            throw new \InvalidArgumentException('Can\'t multiply an empty array', 231001185305);
        }

        $result = 1;
        foreach ($array as $value) {
            $result *= $value;
        }
        return $result;
    }

    public static function first(array $array): mixed
    {
        if (empty($array)) {
            throw new \OutOfBoundsException('Array is empty', 231208170512);
        }
        return $array[array_key_first($array)];
    }

    /**
     * @template T
     * @param array<array-key, T> $array
     * @return T
     */
    public static function last(array $array): mixed
    {
        if (empty($array)) {
            throw new \OutOfBoundsException('Array is empty', 231208170512);
        }
        return $array[array_key_last($array)];
    }

    /**
     * @param mixed|\Closure $search The search term, pass a \Closure (e.g. fn ($v) => ...) for a custom filter
     * @return array<int, int|string> The found keys
     */
    public static function searchAll(array $array, mixed $search): array
    {
        $filter = $search instanceof \Closure ? $search : fn (mixed $v) => $v === $search;
        return array_keys(array_filter($array, $filter));
    }

    /**
     * @template TValue
     * @param int $selectAmount
     * @param TValue ...$values
     * @return \Generator<TValue>
     */
    public static function getCombinations(int $selectAmount, mixed ...$values): \Generator
    {
        $values = array_values($values);
        if ($selectAmount < 1) {
            throw new \OutOfRangeException('Can\'t select < 1 options', 231225124106);
        }
        if ($selectAmount > count($values)) {
            throw new \OutOfRangeException('Can\'t select more than amount of values', 231225124316);
        }
        if ($selectAmount === 1) {
            foreach ($values as $value) {
                yield [$value];
            }
            return;
        }

        for ($i = 0; $i < count($values) - $selectAmount + 1; $i++) {
            foreach (static::getCombinations($selectAmount - 1, ...array_slice($values, $i + 1)) as $option) {
                yield [$values[$i], ...$option];
            }
        }
    }
}
