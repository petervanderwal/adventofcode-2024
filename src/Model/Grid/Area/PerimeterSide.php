<?php

declare(strict_types=1);

namespace App\Model\Grid\Area;

use App\Model\DirectedPoint;
use App\Model\Iterator\AbstractArrayIterator;
use App\Model\Iterator\IteratorInterface;

/**
 * @extends IteratorInterface<string, DirectedPoint>
 */
class PerimeterSide extends AbstractArrayIterator
{
    private array $directedPoints = [];

    public function __construct(DirectedPoint ...$directPoints)
    {
        foreach ($directPoints as $directPoint) {
            $this->directedPoints[(string)$directPoint] = $directPoint;
        }
    }

    public function toArray(): array
    {
        return $this->directedPoints;
    }
}
