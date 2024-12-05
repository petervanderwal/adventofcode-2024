<?php

declare(strict_types=1);

namespace App\Model\Grid;

use App\Model\Grid;
use App\Model\Point;

abstract class AbstractGridRowColumn extends AbstractGridIterator
{
    public function __construct(
        Grid $grid,
        protected int $index,
        bool $reverse = false,
    ) {
        parent::__construct($grid, $reverse);
    }

    public function reverse(): static
    {
        return new static($this->grid, $this->index, !$this->reverse);
    }

    abstract protected function getCoordinate(int $index): Point;

    protected function getIndex(int $index): int|Point
    {
        return $this->getCoordinate($index);
    }

    protected function getItem(int $index): mixed
    {
        return $this->grid->get($this->getCoordinate($index));
    }

    public function toString(?callable $characterPlotter = null): string
    {
        $characterPlotter ??= fn (mixed $char) => $char instanceof \BackedEnum ? (string)$char->value : (string)$char;

        $result = '';
        foreach ($this as $character) {
            $result .= $characterPlotter($character);
        }
        return $result;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}