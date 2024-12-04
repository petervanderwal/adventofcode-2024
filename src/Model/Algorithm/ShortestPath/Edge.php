<?php

declare(strict_types=1);

namespace App\Model\Algorithm\ShortestPath;

class Edge implements EdgeInterface
{
    public function __construct(
        private readonly VertexInterface $from,
        private readonly VertexInterface $to,
        private readonly int $cost = 1,
    ) {}

    public function getFomVertex(): VertexInterface
    {
        return $this->from;
    }

    public function getToVertex(): VertexInterface
    {
        return $this->to;
    }

    public function getEdgeCost(): int
    {
        return $this->cost;
    }
}
