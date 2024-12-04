<?php

declare(strict_types=1);

namespace App\Model\Operation;

interface IntBoolOperationInterface
{
    public function __invoke(int $input): bool;
}