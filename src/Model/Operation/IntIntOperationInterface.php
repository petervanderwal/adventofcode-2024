<?php

declare(strict_types=1);

namespace App\Model\Operation;

interface IntIntOperationInterface
{
    public function __invoke(int $input): int;
}
