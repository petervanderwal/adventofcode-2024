<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use App\Model\Algorithm\Dijkstra;
use App\Model\Algorithm\ShortestPath\Edge;
use App\Model\Algorithm\ShortestPath\Graph;
use App\Model\DirectedPoint;
use App\Model\Direction;
use App\Model\Grid;
use App\Model\Point;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;

class Day16
{
    private const string FIRST_DEMO_INPUT = <<<EOF
        ###############
        #.......#....E#
        #.#.###.#.###.#
        #.....#.#...#.#
        #.###.#####.#.#
        #.#.#.......#.#
        #.#.#####.###.#
        #...........#.#
        ###.#.#####.#.#
        #...#.....#.#.#
        #.#.#.###.#.#.#
        #.....#...#.#.#
        #.###.#.#.#.#.#
        #S..#.....#...#
        ###############
        EOF;

    private const string SECOND_DEMO_INPUT = <<<EOF
        #################
        #...#...#...#..E#
        #.#.#.#.#.#.#.#.#
        #.#.#.#...#...#.#
        #.#.#.#.###.#.#.#
        #...#.#.#.....#.#
        #.#.#.#.#.#####.#
        #.#...#.#.#.....#
        #.#.#####.#.###.#
        #.#.#.......#...#
        #.#.###.#####.###
        #.#.#...#.....#.#
        #.#.#.#####.###.#
        #.#.#.........#.#
        #.#.#.#########.#
        #S#.............#
        #################
        EOF;


    #[Puzzle(2024, day: 16, part: 1)]
    #[TestWithDemoInput(self::FIRST_DEMO_INPUT, expectedAnswer: 7036, name: 'first demo')]
    #[TestWithDemoInput(self::SECOND_DEMO_INPUT, expectedAnswer: 11048, name: 'second demo')]
    public function part1(PuzzleInput $input): int
    {
        // In the end this is a shortest-path problem which we can solve with a shortest-path algorithm. So let's model
        // it as that.
        // Let's note all our vertices (single point) within our problem, where each vertex is not just a position but a
        // combination of position + direction. And let's note all our edges (step between two points), where every turn
        // is also an edge (with cost 1000).
        // The only vertex that's just a plain point is our end point (as it doesn't matter at which direction you enter
        // there). We should keep that in mind.

        // First: read our input as a Grid. While reading, extract the start & end point immediately.
        $start = $end = null;
        $map = Grid::read($input, characterConverter: function (string $char, Point $position) use (&$start, &$end) {
            if ($char === 'S') {
                $start = $position->addDirection(Direction::EAST);
            } elseif ($char === 'E') {
                $end = $position;
            }
            return $char;
        });

        // Next: define our graph with start & end point initially. Also define a method to retrieve the vertex by
        // any given directed point
        $graph = new Graph(vertices: [$start, $end]);
        $getVertex = function (DirectedPoint $directedPoint) use ($graph, $end) {
            if ($directedPoint->equalsCoordinates($end)) {
                return $end;
            }
            return $graph->getOrAddVertex($directedPoint);
        };

        // Iterate our map to add all other vertices and edges to the graph
        foreach ($map as $position => $char) {
            /** @var Point $position */
            if ($char === '#') {
                continue; // We can't climb over walls
            }
            if ($char === 'E') {
                continue; // Don't model any next step from our end point
            }

            $exitPositions = [];
            foreach (Direction::straightCases() as $direction) {
                $exitPosition = $position->moveDirection($direction);
                if ($map->get($exitPosition) !== '#') {
                    $exitPositions[] = $exitPosition->addDirection($direction);
                }
            }
            if (count($exitPositions) <= 1 && $char !== 'S') {
                continue; // We ignore dead ends (except on the start position)
            }

            // Add every step we can take (and while doing so, also keep track of possible entrance directions)
            $entranceDirections = [];
            foreach ($exitPositions as $exit) {
                // Add normal step from this point (with the same facing direction) to the next point
                $graph->addEdge(new Edge(
                    $getVertex($position->addDirection($exit->direction)),
                    $getVertex($exit),
                    cost: 1,
                ));

                // If we can exit south, then we can also enter facing north
                $entranceDirections[] = $exit->direction->turnAround();
            }

            if ($position->equals($start)) {
                // We only have one entrance direction on the start
                $entranceDirections = [$start->direction];
            }

            // Finally also add the turns to our model
            foreach ($entranceDirections as $entranceDirection) {
                foreach ($exitPositions as $exit) {
                    if (
                        $entranceDirection === $exit->direction // Same direction, no turn
                        || $entranceDirection === $exit->direction->turnAround() // We don't model 180 turns, it's useless
                    ) {
                        continue;
                    }

                    $graph->addEdge(new Edge(
                        $getVertex($position->addDirection($entranceDirection)),
                        $getVertex($position->addDirection($exit->direction)),
                        cost: 1000,
                    ));
                }
            }
        }

        // Finally, now we've modeled our graph, lets the shortest path algorithm do its work
        $shortestPathAlgorithm = Dijkstra::calculate($graph, $start);
        return $shortestPathAlgorithm->getDistance($end);
    }
}
