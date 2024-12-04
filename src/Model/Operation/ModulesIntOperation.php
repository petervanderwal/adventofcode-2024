<?php

declare(strict_types=1);

namespace App\Model\Operation;

class ModulesIntOperation implements IntIntOperationInterface
{
    public function __construct(
        private readonly int $divider,
    ) {}

    public function __invoke(int $input): int
    {
        return $input % $this->divider;
    }
}