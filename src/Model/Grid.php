<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\Iterator\AbstractIterator;
use App\Model\Iterator\GeneratedIterator;
use App\Model\Grid\Area;
use App\Model\Grid\BorderEntrance;
use App\Model\Grid\Column;
use App\Model\Grid\ColumnIterator;
use App\Model\Grid\MatchIterator;
use App\Model\Grid\Row;
use App\Model\Grid\RowIterator;
use App\Model\Iterator\IteratorInterface;
use App\Utility\IterableUtility;
use BadMethodCallException;
use Generator;
use InvalidArgumentException;
use OutOfRangeException;
use Symfony\Component\String\UnicodeString;
use Traversable;

/**
 * @template CellType
 * @extends IteratorInterface<Point, CellType>
 */
class Grid extends AbstractIterator
{
    /**
     * @var array[]
     */
    private array $rows = [];
    private int $numberOfColumns;
    private Point $sectionOffset;

    public function __construct(array|Row ...$rows)
    {
        $this->addRows(...$rows);
    }

    /**
     * @template TCallableCellType
     * @param callable(string, Point): TCallableCellType|null $characterConverter
     * @return Grid<TCallableCellType>
     */
    public static function read(UnicodeString $string, ?callable $characterConverter = null): static
    {
        $rows = [];
        foreach ($string->split("\n") as $row) {
            $column = [];
            foreach (str_split(((string)$row)) as $character) {
                if ($characterConverter !== null) {
                    $character = $characterConverter($character, new Point(count($column), count($rows)));
                }
                $column[] = $character;
            }
            $rows[] = $column;
        }
        return new static(...$rows);
    }

    public static function fill(int $numberOfRows, int $numberOfColumns, callable $initialValueGenerator): static
    {
        return (new static())->setNumberOfColumns($numberOfColumns)->addRowsFillWidth($numberOfRows, $initialValueGenerator);
    }

    public static function createFromPoints(
        bool $allowOffset,
        callable $initialValueGenerator,
        callable $pointValueGenerator,
        Point ...$points
    ): static {
        if (empty($points)) {
            throw new InvalidArgumentException('Points can\'t be empty', 231009195805);
        }

        $minRow = $minColumn = PHP_INT_MAX;
        $maxRow = $maxColumn = PHP_INT_MIN;
        foreach ($points as $point) {
            $minRow = min($minRow, $point->getRow());
            $minColumn = min($minColumn, $point->getColumn());
            $maxRow = max($maxRow, $point->getRow());
            $maxColumn = max($maxColumn, $point->getColumn());
        }

        if (!$allowOffset) {
            if ($minRow < 0 || $minColumn < 0) {
                throw new InvalidArgumentException(
                    'Can\'t create grid with points down to ' . $minRow . ',' . $minColumn,
                    231218172132
                );
            } else {
                $minRow = $minColumn = 0;
            }
        }

        $grid = static::fill(($maxRow - $minRow) + 1, ($maxColumn - $minColumn) + 1, $initialValueGenerator);
        foreach ($points as $index => $point) {
            $grid->set($point->moveXY(-$minColumn, -$minRow), $pointValueGenerator($point, $index));
        }
        return $grid;
    }

    public function getNumberOfRows(): int
    {
        return count($this->rows);
    }

    public function getNumberOfColumns(): int
    {
        return $this->numberOfColumns;
    }

    public function setNumberOfColumns(int $numberOfColumns): static
    {
        if (!empty($this->rows) && $numberOfColumns !== $this->numberOfColumns) {
            throw new InvalidArgumentException('Can\'t overwrite number of columns when rows have been added already', 221217110335);
        }
        $this->numberOfColumns = $numberOfColumns;
        return $this;
    }

    public function addRow(mixed ...$columnValues): static
    {
        if (!isset($this->numberOfColumns)) {
            $this->numberOfColumns = count($columnValues);
        } elseif (count($columnValues) !== $this->numberOfColumns) {
            throw new InvalidArgumentException(
                sprintf(
                    'Amount of added %d columns should be of same width as grid of %d',
                    count($columnValues),
                    $this->numberOfColumns
                ),
                221217105622
            );
        }

        $this->rows[] = $columnValues;
        return $this;
    }

    public function addRows(array|Row ...$rows): static
    {
        foreach ($rows as $row) {
            $this->addRow(...IterableUtility::removeKeys($row));
        }
        return $this;
    }

    public function addRowsFillWidth(int $numberOfRows, callable $initialValueGenerator): static
    {
        if (!isset($this->numberOfColumns)) {
            throw new BadMethodCallException('Can\'t call ' . __METHOD__ . ' when the number of columns is not set', 221217110639);
        }

        for ($row = 0; $row < $numberOfRows; $row++) {
            $line = [];
            for ($column = 0; $column < $this->numberOfColumns; $column++) {
                $line[] = $initialValueGenerator($row, $column);
            }
            $this->addRow(...$line);
        }

        return $this;
    }

    public function hasCoordinate(int $rowIndex, int $columnIndex): bool
    {
        return array_key_exists($rowIndex, $this->rows) && array_key_exists($columnIndex, $this->rows[$rowIndex]);
    }

    public function hasPoint(Point $point): bool
    {
        return $this->hasCoordinate($point->getRow(), $point->getColumn());
    }

    /**
     * @return CellType
     */
    public function getCoordinate(int $rowIndex, int $columnIndex): mixed
    {
        if (!$this->hasCoordinate($rowIndex, $columnIndex)) {
            throw new OutOfRangeException(sprintf('No such row, column: %d, %d', $rowIndex, $columnIndex), 221212071206);
        }
        return $this->rows[$rowIndex][$columnIndex];
    }

    /**
     * @return CellType
     */
    public function get(Point $point): mixed
    {
        return $this->getCoordinate($point->getRow(), $point->getColumn());
    }

    public function getCornerPoint(Direction $direction): Point
    {
        return match($direction) {
            Direction::NORTH_WEST => new Point(0, 0),
            Direction::NORTH_EAST => new Point($this->getNumberOfColumns() - 1, 0),
            Direction::SOUTH_EAST => new Point($this->getNumberOfColumns() - 1, $this->getNumberOfRows() - 1),
            Direction::SOUTH_WEST => new Point(0, $this->getNumberOfRows() - 1),
            default => throw new InvalidArgumentException('Only diagonal directions direct to a corner', 231014134419),
        };
    }

    public function isBorderPoint(Point $point): bool
    {
        return $point->x === 0 || $point->y === 0
            || $point->x === $this->getNumberOfColumns() - 1
            || $point->y === $this->getNumberOfRows() - 1;
    }

    /**
     * @param CellType $value
     * @return $this
     */
    public function setCoordinate(int $rowIndex, int $columnIndex, mixed $value): static
    {
        if (!$this->hasCoordinate($rowIndex, $columnIndex)) {
            throw new OutOfRangeException(sprintf('No such row, column: %d, %d', $rowIndex, $columnIndex), 221214072615);
        }
        $this->rows[$rowIndex][$columnIndex] = $value;
        return $this;
    }

    /**
     * @param CellType $value
     */
    public function set(Point $point, mixed $value): static
    {
        return $this->setCoordinate($point->getRow(), $point->getColumn(), $value);
    }

    public function setFromGrid(Grid $grid, Point $offset = new Point(0, 0)): static
    {
        foreach ($grid as $point => $value) {
            /** @var Point $point */
            $this->set($point->offset($offset), $value);
        }
        return $this;
    }
    
    public function drawPath(Point $start, Point $destination, callable $valueGenerator): static
    {
        if ($destination->getColumn() === $start->getColumn()) {
            for (
                $row = min($start->getRow(), $destination->getRow());
                $row <= max($start->getRow(), $destination->getRow());
                $row++
            ) {
                $this->setCoordinate($row, $start->getColumn(), $valueGenerator($row, $start->getColumn()));
            }
            return $this;
        }
        
        if ($destination->getRow() === $start->getRow()) {
            for (
                $column = min($start->getColumn(), $destination->getColumn());
                $column <= max($start->getColumn(), $destination->getColumn());
                $column++
            ) {
                $this->setCoordinate($start->getRow(), $column, $valueGenerator($start->getRow(), $column));
            }
            return $this;
        }

        throw new InvalidArgumentException(
            sprintf(
                'Can\t draw diagonal path from %s to %s - only horizontal and vertical paths are supported',
                $start,
                $destination
            ),
            221214073708
        );
    }

    public function getRows(): RowIterator
    {
        return new RowIterator($this);
    }

    public function getRow(int $index): Row
    {
        if ($index < 0 || $index > $this->getNumberOfRows()) {
            throw new OutOfRangeException('$index should be >= 0 and <= $grid->getNumberOfRows()', 230921192835);
        }
        return new Row($this, $index);
    }

    public function getColumns(): ColumnIterator
    {
        return new ColumnIterator($this);
    }

    public function getColumn(int $index): Column
    {
        if ($index < 0 || $index > $this->getNumberOfColumns()) {
            throw new OutOfRangeException('$index should be >= 0 and <= $grid->getNumberOfColumns()', 230921191903);
        }
        return new Column($this, $index);
    }

    public function count(): int
    {
        return $this->getNumberOfRows() * $this->getNumberOfColumns();
    }

    /**
     * @return Traversable<Point, mixed>
     */
    public function getIterator(): Traversable
    {
        foreach ($this->getRows() as $row) {
            foreach ($row as $point => $item) {
                yield $point => $item;
            }
        }
    }

    public function matches(string $pattern): MatchIterator
    {
        return new MatchIterator($this, $pattern);
    }

    public function plot(?callable $characterPlotter = null): string
    {
        $lines = [];
        foreach ($this->getRows() as $row) {
            $lines[] = $row->toString($characterPlotter);
        }
        return implode(PHP_EOL, $lines);
    }

    public function extractSection(int $rowStart, int $columnStart, int $rowEnd, int $columnEnd): static
    {
        if (!$this->hasCoordinate($rowStart, $columnStart)) {
            throw new OutOfRangeException(
                sprintf('No such start row, column: %d, %d', $rowStart, $columnStart),
                221214070727
            );
        }
        if (!$this->hasCoordinate($rowEnd, $columnEnd)) {
            throw new OutOfRangeException(
                sprintf('No such end row, column: %d, %d', $rowEnd, $columnEnd),
                221214070757
            );
        }
        if ($rowEnd < $rowStart) {
            throw new InvalidArgumentException('$rowEnd should be => $rowStart', 221214070918);
        }
        if ($columnEnd < $columnStart) {
            throw new InvalidArgumentException('$columnEnd should be => $columnStart', 221214070948);
        }

        $lines = [];
        for ($row = $rowStart; $row <= $rowEnd; $row++) {
            $line = [];
            for ($column = $columnStart; $column <= $columnEnd; $column++) {
                $line[] = $this->rows[$row][$column];
            }
            $lines[] = $line;
        }

        $section = new static(...$lines);
        $section->sectionOffset = $this->getSectionOffset()->moveXY($columnStart, $rowStart);
        return $section;
    }

    /**
     * @return Point
     */
    public function getSectionOffset(): Point
    {
        if (!isset($this->sectionOffset)) {
            return new Point(0, 0);
        }
        return $this->sectionOffset;
    }

    /**
     * @param callable(mixed $pointValue, Point $coordinate, Area $area): bool $belongsToArea Method to define whether
     *                  a given neighbouring point belongs to an area
     * @param null|callable(mixed $pointValue, Point $coordinate, Grid $grid): bool $canBeStartOfNewArea Method to
     *                  define whether a given point can be a start of a new area
     * @return GeneratedIterator<int, Grid\Area>
     */
    public function getAreas(callable $belongsToArea, callable $canBeStartOfNewArea = null): GeneratedIterator
    {
        if ($canBeStartOfNewArea === null) {
            $canBeStartOfNewArea = fn () => true;
        }

        return new GeneratedIterator(function () use ($belongsToArea, $canBeStartOfNewArea) {
            $visited = Grid::fill($this->getNumberOfRows(), $this->getNumberOfColumns(), fn() => false);
            foreach ($this as $point => $value) {
                /** @var Point $point */
                if ($visited->get($point) || !$canBeStartOfNewArea($value, $point, $this)) {
                    continue;
                }

                yield $this->populateArea($point, $belongsToArea, $visited);
            }
        });
    }

    private function populateArea(Point $startingPoint, callable $belongsToArea, Grid $visited): Area
    {
        $area = new Area($this, $startingPoint);
        $visited->set($startingPoint, true);

        $pointsToCheck = [];
        do {
            foreach (Direction::straightCases() as $direction) {
                $neighbour = $startingPoint->moveDirection($direction);
                if (
                    $this->hasPoint($neighbour)
                    && !$visited->get($neighbour)
                    && $belongsToArea($this->get($neighbour), $neighbour, $area)
                ) {
                    $area->addPoint($neighbour);
                    $visited->set($neighbour, true);
                    $pointsToCheck[] = $neighbour;
                }
            }
        } while (null !== $startingPoint = array_pop($pointsToCheck));

        return $area;
    }

    /**
     * @return Generator&iterable<int, BorderEntrance>
     */
    public function getBorderEntrances(): Generator
    {
        foreach ($this->getRow(0)->keys() as $point) {
            yield new BorderEntrance($point, Direction::SOUTH);
        }
        foreach ($this->getColumn($this->getNumberOfColumns() - 1)->keys() as $point) {
            yield new BorderEntrance($point, Direction::WEST);
        }
        foreach ($this->getRow($this->getNumberOfRows() - 1)->keys() as $point) {
            yield new BorderEntrance($point, Direction::NORTH);
        }
        foreach ($this->getColumn(0)->keys() as $point) {
            yield new BorderEntrance($point, Direction::EAST);
        }
    }
}
