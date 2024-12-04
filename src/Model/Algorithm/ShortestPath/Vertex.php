<?php

declare(strict_types=1);

namespace App\Model\Algorithm\ShortestPath;

class Vertex implements VertexInterface
{
    public function __construct(
        private readonly string $vertexIdentifier,
    ) {
    }

    public function getVertexIdentifier(): string
    {
        return $this->vertexIdentifier;
    }
}
