<?php

declare(strict_types=1);

namespace App\Model\Operation;

class DivideRoundDownIntOperation implements IntIntOperationInterface
{
    public function __construct(
        private readonly int $divider,
    ) {}

    public function __invoke(int $input): int
    {
        return (int)($input / $this->divider);
    }
}