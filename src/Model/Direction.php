<?php

namespace App\Model;

enum Direction
{
    case NORTH;
    case EAST;
    case SOUTH;
    case WEST;

    case NORTH_EAST;
    case NORTH_WEST;
    case SOUTH_EAST;
    case SOUTH_WEST;

    case UP;
    case DOWN;

    /**
     * @return Direction[]
     */
    public static function straightCases(): array
    {
        return [
            self::NORTH,
            self::EAST,
            self::SOUTH,
            self::WEST
        ];
    }

    /**
     * @return Direction[]
     */
    public static function diagonalCases(): array
    {
        return [
            self::NORTH_EAST,
            self::NORTH_WEST,
            self::SOUTH_WEST,
            self::SOUTH_EAST,
        ];
    }

    /**
     * @return Direction[]
     */
    public static function verticalCases(): array
    {
        return [
            self::UP,
            self::DOWN,
        ];
    }

    public static function fromCharacter(string $character): Direction
    {
        static $characters = [];
        if (empty($characters)) {
            foreach (self::straightCases() as $direction) {
                $characters[$direction->character()] = $direction;
            }
        }
        return $characters[$character];
    }

    public function turnRight(bool $halfStep = false): Direction
    {
        return match($this) {
            self::NORTH => $halfStep ? self::NORTH_EAST : self::EAST,
            self::EAST => $halfStep ? self::SOUTH_EAST : self::SOUTH,
            self::SOUTH => $halfStep ? self::SOUTH_WEST : self::WEST,
            self::WEST => $halfStep ? self::NORTH_WEST : self::NORTH,

            self::NORTH_EAST => $halfStep ? self::EAST : self::SOUTH_EAST,
            self::SOUTH_EAST => $halfStep ? self::SOUTH : self::SOUTH_WEST,
            self::SOUTH_WEST => $halfStep ? self::WEST : self::NORTH_WEST,
            self::NORTH_WEST => $halfStep ? self::NORTH : self::NORTH_EAST,
        };
    }

    public function turnLeft(bool $halfStep = false): Direction
    {
        return match($this) {
            self::NORTH => $halfStep ? self::NORTH_WEST : self::WEST,
            self::WEST => $halfStep ? self::SOUTH_WEST : self::SOUTH,
            self::SOUTH => $halfStep ? self::SOUTH_EAST : self::EAST,
            self::EAST => $halfStep ? self::NORTH_EAST : self::NORTH,

            self::NORTH_EAST => $halfStep ? self::NORTH : self::NORTH_WEST,
            self::SOUTH_EAST => $halfStep ? self::EAST : self::NORTH_EAST,
            self::SOUTH_WEST => $halfStep ? self::SOUTH : self::SOUTH_EAST,
            self::NORTH_WEST => $halfStep ? self::WEST : self::SOUTH_WEST,
        };
    }

    public function turnAround(): Direction
    {
        return match($this) {
            self::NORTH => self::SOUTH,
            self::WEST => self::EAST,
            self::SOUTH => self::NORTH,
            self::EAST => self::WEST,

            self::NORTH_EAST => self::SOUTH_WEST,
            self::SOUTH_EAST => self::NORTH_WEST,
            self::SOUTH_WEST => self::NORTH_EAST,
            self::NORTH_WEST => self::SOUTH_EAST,

            self::UP => self::DOWN,
            self::DOWN => self::UP,
        };
    }

    public function getXStep(): int
    {
        return match($this) {
            self::WEST, self::NORTH_WEST, self::SOUTH_WEST => -1,
            self::EAST, self::NORTH_EAST, self::SOUTH_EAST => 1,
            default => 0,
        };
    }

    public function getYStep(): int
    {
        return match($this) {
            self::NORTH, self::NORTH_EAST, self::NORTH_WEST => -1,
            self::SOUTH, self::SOUTH_EAST, self::SOUTH_WEST => 1,
            default => 0,
        };
    }

    public function getZStep(): int
    {
        return match($this) {
            self::UP => 1,
            self::DOWN => -1,
            default => 0,
        };
    }

    public function character(): string
    {
        return match($this) {
            self::NORTH => '^',
            self::WEST => '<',
            self::SOUTH => 'v',
            self::EAST => '>',
        };
    }

    public function prettyName(): string
    {
        return strtolower(str_replace('_', '-', $this->name));
    }
}
