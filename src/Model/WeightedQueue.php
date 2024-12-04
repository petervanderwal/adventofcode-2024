<?php

declare(strict_types=1);

namespace App\Model;

use IteratorAggregate;
use Traversable;

class WeightedQueue implements IteratorAggregate
{
    private array $items = [];

    public function addWithPriority(mixed $item, int $priority): static
    {
        $this->removeItem($item);
        $this->items[$priority][] = $item;
        return $this;
    }

    public function removeItem(mixed $item): static
    {
        foreach ($this->items as $priority => $items) {
            $items = array_filter($items, fn (mixed $existing) => $existing !== $item);
            if (empty($items)) {
                unset($this->items[$priority]);
            } else {
                $this->items[$priority] = $items;
            }
        }
        return $this;
    }

    public function shiftLowestPriority(): mixed
    {
        if (empty($this->items)) {
            return null;
        }
        $priority = min(array_keys($this->items));
        $result = array_pop($this->items[$priority]);
        if (empty($this->items[$priority])) {
            unset($this->items[$priority]);
        }
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
}
