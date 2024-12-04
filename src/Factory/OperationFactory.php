<?php

declare(strict_types=1);

namespace App\Factory;

use App\Model\Operation\AddIntIntIntFormulaOperation;
use App\Model\Operation\AddIntOperation;
use App\Model\Operation\DivideIntIntIntFormulaOperation;
use App\Model\Operation\IntIntIntFormulaOperationInterface;
use App\Model\Operation\IntIntOperationInterface;
use App\Model\Operation\MultiplyIntIntIntFormulaOperation;
use App\Model\Operation\MultiplyIntOperation;
use App\Model\Operation\PowerIntOperation;
use App\Model\Operation\SubtractIntIntIntFormulaOperation;
use Symfony\Component\String\UnicodeString;

class OperationFactory
{
    public function getIntIntOperation(string|UnicodeString $input, string $varName): IntIntOperationInterface
    {
        $argument = '\d+|' . preg_quote($varName, '/');
        preg_match(
            '/^(?P<a>' . $argument . ')\s*(?P<operation>[+*])\s*(?P<b>' . $argument . ')$/',
            (string)$input,
            $matches
        );

        if (empty($matches)) {
            throw new \InvalidArgumentException('Unknown operation: ' . $input, 221211094049);
        }

        if ($matches['a'] === $varName) {
            $number = $matches['b'];
        } elseif ($matches['b'] === $varName) {
            $number = $matches['a'];
        } else {
            throw new \InvalidArgumentException('Variable name "' . $varName . '" not found in operation: ' . $input, 221211101750);
        }

        if ($number === $varName) {
            return match($matches['operation']) {
                '+' => new MultiplyIntOperation(2),
                '*' => new PowerIntOperation(2),
            };
        }

        $number = (int)$number;
        return match($matches['operation']) {
            '+' => new AddIntOperation($number),
            '*' => new MultiplyIntOperation($number),
        };
    }

    public function getIntIntIntFormulaOperation(string $operation): IntIntIntFormulaOperationInterface
    {
        return match($operation) {
            '+' => new AddIntIntIntFormulaOperation(),
            '-' => new SubtractIntIntIntFormulaOperation(),
            '*' => new MultiplyIntIntIntFormulaOperation(),
            '/' => new DivideIntIntIntFormulaOperation(),
        };
    }
}
