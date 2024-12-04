<?php

declare(strict_types=1);

namespace App\Model\Operation;

class SubtractIntIntIntFormulaOperation extends AbstractIntIntFormulaOperation
{
    protected function solveSimple(int $argument1, int $argument2): int
    {
        return $argument1 - $argument2;
    }

    protected function solveFormula(VariableFormula|int $argument1, VariableFormula|int $argument2): VariableFormula
    {
        if ($argument1 instanceof VariableFormula) {
            return new VariableFormula(
                $argument1->variableName,
                $argument1->multiply,
                $argument1->add - $argument2
            );
        }

        // 5 - (m * v + a) => (-1 * m) * v + (5 - a)
        return new VariableFormula(
            $argument2->variableName,
            -1 * $argument2->multiply,
            $argument1 - $argument2->add
        );
    }
}