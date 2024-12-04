<?php

declare(strict_types=1);

namespace App\Model\Operation;

class AddIntOperation implements IntIntOperationInterface
{
    public function __construct(
        private readonly int $add,
    ) {}

    public function __invoke(int $input): int
    {
        return $input + $this->add;
    }
}