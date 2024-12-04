<?php

declare(strict_types=1);

namespace App\Model\Operation;

interface IntIntIntFormulaOperationInterface
{
    public function __invoke(
        int|VariableFormula $argument1,
        int|VariableFormula $argument2
    ): int|VariableFormula;
}