<?php

declare(strict_types=1);

namespace App\Model\Iterator;

use Override;

/**
 * @template TKey
 * @template TValue
 */
abstract class AbstractIterator implements IteratorInterface
{
    #[Override]
    public function count(): int
    {
        $result = 0;
        foreach ($this as $ignored) {
            $result++;
        }
        return $result;
    }

    #[Override]
    public function isEmpty(): bool
    {
        foreach ($this as $ignored) {
            return false;
        }
        return true;
    }

    #[Override]
    public function toArray(): array
    {
        return iterator_to_array($this);
    }

    #[Override]
    public function cacheIterator(): ArrayIterator
    {
        return new ArrayIterator($this->toArray());
    }

    #[Override]
    public function first(): mixed
    {
        foreach ($this as $item) {
            return $item;
        }
        return null;
    }

    #[Override]
    public function keys(): KeyIterator
    {
        return new KeyIterator($this);
    }

    #[Override]
    public function reverse(): AbstractIterator
    {
        return new ArrayIterator(array_reverse(iterator_to_array($this)));
    }

    #[Override]
    public function where(callable $where): WhereIterator
    {
        return new WhereIterator($this, $where);
    }

    #[Override]
    public function whereEquals(mixed $search): WhereIterator
    {
        return $this->where(fn (mixed $value) => $value === $search);
    }

    #[Override]
    public function has(callable $selector): bool
    {
        foreach ($this as $item) {
            if ($selector($item)) {
                return true;
            }
        }
        return false;
    }

    #[Override]
    public function hasEquals(mixed $search): bool
    {
        return $this->has(fn (mixed $value) => $value === $search);
    }

    #[Override]
    public function map(callable $callback): MapIterator
    {
        return new MapIterator($this, $callback);
    }

    #[Override]
    public function each(callable $callback): static
    {
        foreach ($this as $key => $value) {
            $callback($value, $key);
        }
        return $this;
    }

    #[Override]
    public function merge(): MergeIterator
    {
        return new MergeIterator($this);
    }

    #[Override]
    public function max(): mixed
    {
        return $this->getBest(fn (KeyValue $current, mixed $option) => $option > $current->value);
    }

    #[Override]
    public function min(): mixed
    {
        return $this->getBest(fn (KeyValue $current, mixed $option) => $option < $current->value);
    }

    #[Override]
    public function getBest(callable $selector): ?KeyValue
    {
        $result = null;
        foreach ($this as $key => $item) {
            if (
                $result === null
                || $selector($result, $item, $key)
            ) {
                $result = new KeyValue($key, $item);
            }
        }
        return $result;
    }

    #[Override]
    public function doesAllMatch(callable $selector): bool
    {
        foreach ($this as $item) {
            if (!$selector($item)) {
                return false;
            }
        }
        return true;
    }

    #[Override]
    public function sum(): int|float
    {
        return $this->reduce(fn (int|float $carry, int|float $item) => $carry + $item, 0);
    }

    #[Override]
    public function implode(string $separator = ''): string
    {
        return $this->reduce(
            fn (?string $carry, mixed $item) => $carry === null ? (string)$item : $carry . $separator . $item
        ) ?? '';
    }

    #[Override]
    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        foreach ($this as $key => $item) {
            $initial = $callback($initial, $item, $key);
        }
        return $initial;
    }
}