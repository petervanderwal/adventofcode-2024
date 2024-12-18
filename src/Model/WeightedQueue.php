<?php

declare(strict_types=1);

namespace App\Model;

use IteratorAggregate;
use Traversable;

class WeightedQueue implements IteratorAggregate
{
    /**
     * @var array<string, array{0: int|string|object, 1: int}>
     */
    private array $items = [];

    /**
     * @var array<int, array<string, true>>
     */
    private array $itemsByPriority = [];

    public function addWithPriority(int|string|object $item, int $priority): static
    {
        $key = $this->getKey($item);
        $this->removeKey($key);

        $this->items[$key] = [$item, $priority];
        $this->itemsByPriority[$priority][$key] = true;
        return $this;
    }

    public function removeItem(int|string|object $item): static
    {
        $key = $this->getKey($item);
        return $this->removeKey($this->getKey($item));
    }

    private function removeKey(string $key): static
    {
        if (isset($this->items[$key])) {
            [,$priority] = $this->items[$key];
            unset($this->itemsByPriority[$priority][$key]);
            if (empty($this->itemsByPriority[$priority])) {
                unset($this->itemsByPriority[$priority]);
            }
        }
        unset($this->items[$key]);
        return $this;
    }

    public function shiftLowestPriority(): int|string|object|null
    {
        if (empty($this->items)) {
            return null;
        }
        $priority = min(array_keys($this->itemsByPriority));
        $key = array_keys($this->itemsByPriority[$priority])[0];
        [$result] = $this->items[$key];
        $this->removeKey($key);
        return $result;
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function getIterator(): Traversable
    {
        while (!$this->isEmpty()) {
            yield $this->shiftLowestPriority();
        }
    }

    private function getKey(int|string|object $item): string
    {
        return match (true) {
            is_object($item) => 'object:' . spl_object_hash($item),
            is_string($item) => "string:$item",
            is_int($item) => "int:$item",
        };
    }
}
