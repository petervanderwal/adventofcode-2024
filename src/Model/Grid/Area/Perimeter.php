<?php

declare(strict_types=1);

namespace App\Model\Grid\Area;

use App\Model\DirectedPoint;
use App\Model\Iterator\AbstractArrayIterator;
use App\Model\Iterator\IteratorInterface;

/**
 * @extends IteratorInterface<string, DirectedPoint>
 */
class Perimeter extends AbstractArrayIterator
{
    private array $directedPoints = [];
    private array $sides;

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

    /**
     * @return PerimeterSide[]
     */
    public function getSides(): array
    {
        if (isset($this->sides)) {
            return $this->sides;
        }

        $seen = [];
        $sides = [];
        foreach ($this->directedPoints as $key => $directedPoint) {
            if (in_array($key, $seen)) {
                continue;
            }

            $seen[] = (string)$directedPoint;
            $side = [$directedPoint];

            $moveDirection = $directedPoint->direction->turnLeft();
            $movedBorder = $directedPoint->moveDirection($moveDirection);
            while (isset($this->directedPoints[(string)$movedBorder])) {
                $seen[] = (string)$movedBorder;
                array_unshift($side, $movedBorder);
                $movedBorder = $movedBorder->moveDirection($moveDirection);
            }

            $moveDirection = $directedPoint->direction->turnRight();
            $movedBorder = $directedPoint->moveDirection($moveDirection);
            while (isset($this->directedPoints[(string)$movedBorder])) {
                $seen[] = (string)$movedBorder;
                $side[] = $movedBorder;
                $movedBorder = $movedBorder->moveDirection($moveDirection);
            }

            $sides[] = new PerimeterSide(...$side);
        }

        return $this->sides = $sides;
    }
}
