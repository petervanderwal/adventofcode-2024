<?php

declare(strict_types=1);

namespace App\Model\Algorithm\ShortestPath;

class Graph implements GraphInterface
{
    /**
     * @var VertexInterface[]
     */
    private array $vertices = [];

    /**
     * @var EdgeInterface[][]
     */
    private array $edges;

    /**
     * @param VertexInterface[] $vertices
     * @param EdgeInterface[] $edges
     */
    public function __construct(
        array $vertices = [],
        array $edges = [],
    ) {
        foreach ($vertices as $vertex) {
            if (!$vertex instanceof VertexInterface) {
                throw new \InvalidArgumentException('Given vertex should implement VertexInterface', 221217195326);
            }
            $this->addVertex($vertex);
        }

        foreach ($edges as $edge) {
            if (!$edge instanceof EdgeInterface) {
                throw new \InvalidArgumentException('Given edge should implement EdgeInterface', 221217195528);
            }
            $this->addEdge($edge);
        }
    }

    /**
     * @return VertexInterface[]
     */
    public function getVertices(): array
    {
        return $this->vertices;
    }

    public function getVertex(string $identifier): VertexInterface
    {
        if (!isset($this->vertices[$identifier])) {
            throw new \OutOfBoundsException('No such vertex in graph: ' . $identifier, 231004212417);
        }
        return $this->vertices[$identifier];
    }

    public function hasVertex(string $identifier): bool
    {
        return isset($this->vertices[$identifier]);
    }

    public function addVertex(VertexInterface $vertex): static
    {
        $identifier = $vertex->getVertexIdentifier();
        if (isset($this->vertices[$identifier])) {
            throw new \InvalidArgumentException('Vertex already exists in graph: ' . $identifier, 231014131206);
        }
        $this->vertices[$identifier] = $vertex;
        return $this;
    }

    public function getOrAddVertex(VertexInterface $vertex): VertexInterface
    {
        $identifier = $vertex->getVertexIdentifier();
        if (isset($this->vertices[$identifier])) {
            return $this->vertices[$identifier];
        }
        return $this->vertices[$identifier] = $vertex;
    }

    /**
     * @return EdgeInterface[]
     */
    public function getEdges(VertexInterface $from): array
    {
        return $this->edges[$from->getVertexIdentifier()] ?? [];
    }

    /**
     * @return EdgeInterface
     */
    public function getAllEdges(): array
    {
        $result = [];
        foreach ($this->edges as $edges) {
            $result = [...$result, ...array_values($edges)];
        }
        return $result;
    }

    public function addEdge(EdgeInterface $edge, bool $addVertices = false): static
    {
        $fromId = $this->ensureVertex($edge->getFomVertex(), $addVertices, 'Edge-from');
        $toId = $this->ensureVertex($edge->getToVertex(), $addVertices, 'Edge-to');
        if (isset($this->edges[$fromId][$toId])) {
            throw new \InvalidArgumentException('Can\t redefine edge ' . $fromId . ' => ' . $toId . ' twice', 221217195636);
        }
        $this->edges[$fromId][$toId] = $edge;
        return $this;
    }

    public function removeEdgeByVertices(string|VertexInterface $from, string|VertexInterface $to): EdgeInterface
    {
        $fromId = $from instanceof VertexInterface ? $from->getVertexIdentifier() : $from;
        $toId = $to instanceof VertexInterface ? $to->getVertexIdentifier() : $to;
        if (!isset($this->edges[$fromId][$toId])) {
            throw new \InvalidArgumentException('No such vertex ' . $fromId . ' => ' . $toId, 231225123226);
        }
        $backup = $this->edges[$fromId][$toId];
        unset($this->edges[$fromId][$toId]);
        return $backup;
    }

    private function ensureVertex(VertexInterface $vertex, bool $addIfNonExisting, string $errorPrefix = 'Vertex'): string
    {
        $identifier = $vertex->getVertexIdentifier();

        if ($this->hasVertex($vertex->getVertexIdentifier())) {
            return $identifier;
        }

        if ($addIfNonExisting) {
            $this->addVertex($vertex);
            return $identifier;
        }

        throw new \InvalidArgumentException($errorPrefix . ' should exist in vertices', 231014132711);
    }
}
