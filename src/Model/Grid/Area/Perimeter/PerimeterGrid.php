<?php

declare(strict_types=1);

namespace App\Model\Grid\Area\Perimeter;

use App\Model\Grid;
use App\Model\Grid\Area\Perimeter;
use App\Model\Point;

class PerimeterGrid
{
    private readonly Grid $grid;

    public function __construct(Grid $originalGrid)
    {
        $this->grid = Grid::fill(
            $originalGrid->getNumberOfRows() * 3,
            $originalGrid->getNumberOfColumns() * 3,
            fn(int $row, int $column) =>
                null !== ($originalPoint = $this->translateToOriginalGridPoint($row, $column))
                ? new PerimeterGridOriginalValue($originalPoint, $originalGrid->get($originalPoint))
                : new PerimeterGridBlankValue()
        );
    }

    public function addPerimeter(Perimeter $perimeter): static
    {
        $cornerPoints = [];
        foreach ($perimeter->getSides() as $side) {
            $first = $last = null;
            foreach ($side as $directedPoint) {
                if ($first === null) {
                    $cornerPoints[] = $first = $directedPoint;
                } else {
                    $last = $directedPoint;
                }

                $combinationsOnSamePoint[(string)$directedPoint->removeDirection()][] = $directedPoint->direction;

                $point = $this->translateToPerimeterGridPoint($directedPoint)->moveDirection($directedPoint->direction);
                $this->grid->set($point, new PerimeterGridBorderValue($directedPoint));
                $this->grid->set(
                    $point->moveDirection($directedPoint->direction->turnLeft()),
                    new PerimeterGridBorderValue($directedPoint)
                );
                $this->grid->set(
                    $point->moveDirection($directedPoint->direction->turnRight()),
                    new PerimeterGridBorderValue($directedPoint)
                );
            }

            if ($last !== null) {
                $cornerPoints[] = $last;
            }
        }

        foreach ($cornerPoints as $indexA => $pointA) {
            for ($indexB = $indexA + 1; $indexB < count($cornerPoints); $indexB++) {
                $pointB = $cornerPoints[$indexB];

                if ($pointA->direction === $pointB->direction->turnAround()) {
                    // Opposite borders, not a corner
                    continue;
                }

                // Check normal (outer) corners
                if ($pointA->equalsCoordinates($pointB)) {
                    $cornerDirection = $pointA->direction->combine($pointB->direction);
                    $this->grid->set(
                        $this->translateToPerimeterGridPoint($pointA)->moveDirection($cornerDirection),
                        new PerimeterGridBorderValue($pointB->addDirection($cornerDirection))
                    );
                }

                // Check inner corner
                if ($pointA->moveCurrentDirection()->equalsCoordinates($pointB->moveCurrentDirection())) {
                    $cornerDirection = $pointA->direction->combine($pointB->direction);
                    $innerCornerPoint = $pointA->moveDirection($pointB->direction->turnAround());

                    $this->grid->set(
                        $this->translateToPerimeterGridPoint($innerCornerPoint)->moveDirection($cornerDirection),
                        new PerimeterGridBorderValue($pointB->addDirection($cornerDirection), isInnerCorner: true)
                    );
                }
            }
        }
        return $this;
    }

    /**
     * @param null|callable(mixed $char, Point $point): string $characterPlotter
     * @return string
     */
    public function plot(?callable $characterPlotter = null): string
    {
        return $this->grid->plot($characterPlotter);
    }

    private function translateToOriginalGridPoint(Point|int $pointOrRow, ?int $column = null): ?Point
    {
        if ($pointOrRow instanceof Point) {
            $row = $pointOrRow->getRow();
            $column = $pointOrRow->getColumn();
        } else {
            $row = $pointOrRow;
        }

        return (($row - 1) % 3 === 0 && ($column - 1) % 3 === 0)
            ? new Point(x: ($column - 1) / 3, y: ($row - 1) / 3)
            : null;
    }

    private function translateToPerimeterGridPoint(Point $point): Point
    {
        return $point->multiply(3)->moveXY(1, 1);
    }
}
