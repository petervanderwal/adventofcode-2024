<?php

declare(strict_types=1);

namespace App\Model\Algorithm\ShortestPath;

interface VertexInterface
{
    public function getVertexIdentifier(): string;
}
