<?php

declare(strict_types=1);

namespace App\Model\Operation;

class DivideIntIntIntFormulaOperation extends AbstractIntIntFormulaOperation
{
    protected function solveSimple(int $argument1, int $argument2): int
    {
        return $argument1 / $argument2;
    }

    protected function solveFormula(VariableFormula|int $argument1, VariableFormula|int $argument2): VariableFormula
    {
        if ($argument1 instanceof VariableFormula) {
            return new VariableFormula(
                $argument1->variableName,
                $argument1->multiply / $argument2,
                $argument1->add / $argument2
            );
        }

        throw new \UnexpectedValueException('Divide by formula is not implemented, apparently I didn\'t need it for this test :)', 221223205229);
    }
}
