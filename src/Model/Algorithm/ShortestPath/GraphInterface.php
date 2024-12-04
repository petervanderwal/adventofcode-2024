<?php

declare(strict_types=1);

namespace App\Model\Algorithm\ShortestPath;

interface GraphInterface
{
    /**
     * @return iterable|VertexInterface[]
     */
    public function getVertices(): iterable;

    /**
     * @return iterable|EdgeInterface[]
     */
    public function getEdges(VertexInterface $from): iterable;
}
