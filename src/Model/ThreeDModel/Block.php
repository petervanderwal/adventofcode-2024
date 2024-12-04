<?php

declare(strict_types=1);

namespace App\Model\ThreeDModel;

class Block extends ThreeDCoordinate
{
    public function __construct(
        public readonly ThreeDModel $model,
        int $x,
        int $y,
        int $z,
        private mixed $value,
    ) {
        parent::__construct($x, $y, $z);
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): static
    {
        $this->model->set($this, $value);
        $this->value = $value;
        return $this;
    }
}
