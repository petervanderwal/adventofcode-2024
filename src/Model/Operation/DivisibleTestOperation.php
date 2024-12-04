<?php

declare(strict_types=1);

namespace App\Model\Operation;

class DivisibleTestOperation implements IntBoolOperationInterface
{
    public function __construct(
        private readonly int $testDivisibleBy,
    ) {}

    public function __invoke(int $input): bool
    {
        return $input % $this->testDivisibleBy === 0;
    }

    public function getTestDivisibleBy(): int
    {
        return $this->testDivisibleBy;
    }
}