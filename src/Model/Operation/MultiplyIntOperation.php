<?php

declare(strict_types=1);

namespace App\Model\Operation;

class MultiplyIntOperation implements IntIntOperationInterface
{
    public function __construct(
        private readonly int $factor,
    ) {}

    public function __invoke(int $input): int
    {
        return $input * $this->factor;
    }
}