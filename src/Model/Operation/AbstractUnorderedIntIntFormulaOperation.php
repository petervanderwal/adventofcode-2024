<?php

declare(strict_types=1);

namespace App\Model\Operation;

abstract class AbstractUnorderedIntIntFormulaOperation extends AbstractIntIntFormulaOperation
{
    protected function solveFormula(VariableFormula|int $argument1, VariableFormula|int $argument2): VariableFormula
    {
        return $this->solveUnorderedFormula(
            is_int($argument1) ? $argument2 : $argument1,
            is_int($argument1) ? $argument1 : $argument2
        );
    }

    abstract protected function solveUnorderedFormula(VariableFormula $variableFormula, int $integer): VariableFormula;
}
