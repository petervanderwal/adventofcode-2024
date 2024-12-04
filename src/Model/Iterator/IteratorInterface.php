<?php

declare(strict_types=1);

namespace App\Model\Iterator;

use Countable;
use IteratorAggregate;

/**
 * @template TKey
 * @template TValue
 * @extends IteratorAggregate<TKey, TValue>
 */
interface IteratorInterface extends IteratorAggregate, Countable
{
    public function isEmpty(): bool;

    /**
     * @return array<TKey, TValue>
     */
    public function toArray(): array;

    /**
     * @return ArrayIterator<TKey, TValue>
     */
    public function cacheIterator(): ArrayIterator;

    /**
     * @return TValue
     */
    public function first(): mixed;

    /**
     * @return KeyIterator<TKey>
     */
    public function keys(): KeyIterator;

    /**
     * @return AbstractIterator<TKey, TValue>
     */
    public function reverse(): AbstractIterator;

    /**
     * @param callable(TValue, TKey): bool $where
     * @return WhereIterator<TKey, TValue>
     */
    public function where(callable $where): WhereIterator;

    /**
     * @param TValue $search
     * @return WhereIterator<TKey, TValue>
     */
    public function whereEquals(mixed $search): WhereIterator;

    /**
     * @param callable(TValue): bool $selector
     */
    public function has(callable $selector): bool;

    /**
     * @param TValue $search
     */
    public function hasEquals(mixed $search): bool;

    /**
     * @template TMappedValue
     * @param callable(TValue, TKey): TMappedValue $callback $callback
     * @return MapIterator<TKey, TMappedValue>
     */
    public function map(callable $callback): MapIterator;

    /**
     * @param callable(TValue, TKey): void $callback
     * @return $this
     */
    public function each(callable $callback): static;

    /**
     * @return MergeIterator<TKey, TValue>
     */
    public function merge(): MergeIterator;

    /**
     * @return TValue
     */
    public function max(): mixed;

    /**
     * @return TValue
     */
    public function min(): mixed;

    /**
     * @param callable(KeyValue<TKey, TValue> $current, TValue $option, TKey $optionKey): bool $selector
     * @return KeyValue<TKey, TValue>|null
     */
    public function getBest(callable $selector): ?KeyValue;

    /**
     * @param callable(TValue, TKey): bool $selector
     */
    public function doesAllMatch(callable $selector): bool;

    public function sum(): int|float;

    public function implode(string $separator = ''): string;

    /**
     * @template TCarry
     * @param callable(TCarry, TValue, TKey): TCarry $callback
     * @param TCarry $initial
     * @return TCarry
     */
    public function reduce(callable $callback, mixed $initial): mixed;
}