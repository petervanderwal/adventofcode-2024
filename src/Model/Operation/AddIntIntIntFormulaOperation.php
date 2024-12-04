<?php

declare(strict_types=1);

namespace App\Model\Operation;

class AddIntIntIntFormulaOperation extends AbstractUnorderedIntIntFormulaOperation
{
    protected function solveSimple(int $argument1, int $argument2): int
    {
        return $argument1 + $argument2;
    }

    protected function solveUnorderedFormula(VariableFormula $variableFormula, int $integer): VariableFormula
    {
        return new VariableFormula(
            $variableFormula->variableName,
            $variableFormula->multiply,
            $variableFormula->add + $integer
        );
    }
}
