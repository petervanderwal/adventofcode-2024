<?php

declare(strict_types=1);

namespace App\Model\Operation;

abstract class AbstractIntIntFormulaOperation implements IntIntIntFormulaOperationInterface
{
    public function __invoke(
        int|VariableFormula $argument1,
        int|VariableFormula $argument2
    ): int|VariableFormula {
        if (is_int($argument1) && is_int($argument2)) {
            return $this->solveSimple($argument1, $argument2);
        }
        if (is_int($argument1) || is_int($argument2)) {
            return $this->solveFormula($argument1, $argument2);
        }
        throw new \InvalidArgumentException('Both arguments as formula isn\'t supported');
    }

    abstract protected function solveSimple(int $argument1, int $argument2): int;

    abstract protected function solveFormula(
        int|VariableFormula $argument1,
        int|VariableFormula $argument2
    ): VariableFormula;
}