<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use App\Utility\MathUtility;
use App\Utility\NumberUtility;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;
use Spatie\Async\Pool;

/**
 * Xa * A + Xb * B = Xprize
 * and
 * Ya * A + Yb * B = Yprize
 *
 * Which for the first puzzle equals to
 *
 * 94A + 22B = 8400                             | 94 = Xa, 22 = Xb, 8400 = Xprize
 * and                                          |
 * 34A + 67B = 5400                             | 34 = Ya, 67 = Yb, 5400 = Yprize
 *                                              |
 * 22B = 8400 - 94A                             | 22 = Xb, 8400 = Xprize, 94 = Xa
 * and                                          |
 * 67B = 5400 - 34A                             | 67 = Yb, 5400 = Yprize, 34 = Ya
 *                                               |
 * 8400 - 94A % 22 = 0                          | 8400 = Xprize, 94 = Xa, 22 = Xb
 * and                                          |
 * 5400 - 34A % 67 = 0                          | 5400 = Yprize, 34 = Ya, 67 = Yb
 *                                               |
 * (67 * (8400 - 94A)) % (22 * 67) = 0          | 67 = Yb, 8400 = Xprize, 94 = Xa, 22 = Xb
 * and                                          |
 * (22 * (5400 - 34A)) % (22 * 67) = 0          | 22 = Xb, 5400 = Yprize, 34 = Ya, 67 = Yb
 *                                              |
 * (67 * 8400 - 67 * 94 * A) % (22 * 67) = 0    | 67 = Yb, 8400 = Xprize, 94 = Xa, 22 = Xb
 * and                                          |
 * (22 * 5400 - 22 * 34 * A) % (22 * 67) = 0    | 22 = Xb, 5400 = Yprize, 34 = Ya, 67 = Yb
 *                                              |
 * (562800 - 6298A) % 1474 = 0                  | 562800 = (Yb * Xprize), 6298 = (Yb * Xa), 1474 = (Xb * Yb)
 * and                                          |
 * (118800 - 748A) % 1474 = 0                   | 118800 = (Xb * Yprize), 748 = (Xb * Ya), 1474 = (Xb * Yb)
 *                                              |
 * 6298A % 1474 = 562800 % 1474                 | 6298 = (Yb * Xa), 1474 = (Xb * Yb), 562800 = (Yb * Xprize)
 * and                                          |
 * 748A % 1474 = 118800 % 1474                  | 748 = (Xb * Ya), 1474 = (Xb * Yb), 118800 = (Xb * Yprize)
 *                                              |
 * 6298A % 1474 = 1206                          | 6298 = (Yb * Xa), 1474 = (Xb * Yb), 1206 = ((Yb * Xprize) % (Xb * Yb))
 * and                                          |
 * 748A % 1474 = 880                            | 748 = (Xb * Ya), 1474 = (Xb * Yb), 880 = ((Xb * Yprize) % (Xb * Yb))
 *                                               |
 * According to Wikipedia https://en.wikipedia.org/wiki/Modular_arithmetic#Basic_properties
 * if                                           |
 *   a1 ≡ a2  (mod m) and b1 ≡ b2  (mod m)      |
 * then                                         |
 *   (a1 + b1) ≡ (a2 + b2)  (mod m)             |
 *                                              |
 * Therefore:                                   |
 * (6298A + 748A) % 1474 = (1206 + 880) % 1474  | 6298 = (Yb * Xa), 748 = (Xb * Ya), 1474 = (Xb * Yb), 1206 = ((Yb * Xprize) % (Xb * Yb)), 880 = ((Xb * Yprize) % (Xb * Yb))
 * (6298 + 748)A % 1474 = 2086 % 1474           | 6298 = (Yb * Xa), 748 = (Xb * Ya), 1474 = (Xb * Yb), 2086 = (((Yb * Xprize) % (Xb * Yb)) + ((Xb * Yprize) % (Xb * Yb)))
 * 7046A % 1474 = 612                           | 7046 = ((Yb * Xa) + (Xb * Ya)), 1474 = (Xb * Yb), 612 = (((Yb * Xprize) + (Xb * Yprize)) % (Xb * Yb))
 * =>                                           |
 * 7046 % 1474 = 1150                           | 7046 = ((Yb * Xa) + (Xb * Ya)), 1474 = (Xb * Yb), 1150 = (((Yb * Xa) + (Xb * Ya)) % (Xb * Yb))
 * =>                                           |
 * 1150A % 1474 = 612                           | 1150 = (((Yb * Xa) + (Xb * Ya)) % (Xb * Yb)), 1474 = (Xb * Yb), 612 = (((Yb * Xprize) + (Xb * Yprize)) % (Xb * Yb))
 * =>                                           |
 * gcd(1150, 1474) = 2                          |
 * => Check if 612 can be divided by 2 (our gcd |
 *    here). If it can't, then there is no      |
 *    answer to the puzzle. Think about it, any |
 *    1150 * A % 1474 will be an even number    |
 *    so that can never be an uneven number.    |
 *                                              |
 * => Divide all values by our gcd (2 here)     |
 * 575A % 737 = 306                             |
 * => To solve A, try any number 0..736, to     |
 *    find the first (minimal) value for A.     |
 *    Within this example that is 80. Then any  |
 *    addition of 737 (so 80, 817, 1554) will   |
 *    be possible values for A                  |
 */
class Day13
{
    private const string DEMO_INPUT = <<<EOF
        Button A: X+94, Y+34
        Button B: X+22, Y+67
        Prize: X=8400, Y=5400
        
        Button A: X+26, Y+66
        Button B: X+67, Y+21
        Prize: X=12748, Y=12176
        
        Button A: X+17, Y+86
        Button B: X+84, Y+37
        Prize: X=7870, Y=6450
        
        Button A: X+69, Y+23
        Button B: X+27, Y+71
        Prize: X=18641, Y=10279
        EOF;

    #[Puzzle(2024, day: 13, part: 1)]
    #[TestWithDemoInput(self::DEMO_INPUT, expectedAnswer: 480)]
    public function part1(PuzzleInput $input): int
    {
        $blocks = $input->split("\n\n");
        $cost = 0;
        foreach ($blocks as $block) {
            $cost += $this->solveBlock((string)$block, 100, 0);
        }
        return $cost;
    }

    #[Puzzle(2024, day: 13, part: 2)]
    public function part2(PuzzleInput $input): int
    {
        $pool = Pool::create();
        $pool->timeout(3600);

        $cost = 0;
        $done = 0;

        $blocks = $input->split("\n\n");
        $count = count($blocks);
        foreach ($blocks as $index => $block) {
            $block = (string)$block;

            $pool->add(function () use ($block) {
                return $this->solveBlock($block, PHP_INT_MAX, 10000000000000);
            })->then(function (int $blockCost) use (&$cost, &$done, $count) {
                $cost += $blockCost;
                $done++;
                echo sprintf(
                    "[%s] %d of %d done (%.1f%%), current cost: %d\n",
                    date('H:i:s'),
                    $done,
                    $count,
                    $done / $count * 100,
                    $cost
                );
            })->catch(function (\Throwable $exception) use ($block, $index) {
                throw new \Exception("Couldn\'t solve $index: $block\n" . $exception->getMessage(), 241215083228);
            });
        }

        $pool->wait();
        return $cost;
    }

    private function solveBlock(string $block, int $buttonLimit, int $offset): int
    {
        [$Xa, $Ya, $Xb, $Yb, $Xprize, $Yprize] = NumberUtility::getNumbersFromLine($block);
        $Xprize += $offset;
        $Yprize += $offset;

        $factor = (($Yb * $Xa) + ($Xb * $Ya)) % ($Xb * $Yb);            // 1150 in example
        $modulo = $Xb * $Yb;                                            // 1474 in example
        $remainder = (($Yb * $Xprize) + ($Xb * $Yprize)) % $modulo;     // 612  in example

        $gcd = MathUtility::greatestCommonDivisor($factor, $modulo);
        if ($remainder % $gcd !== 0) {
            // No answer
            return 0;
        }

        $factor /= $gcd;
        $modulo /= $gcd;
        $remainder /= $gcd;

        for ($aPressesBase = 0; $aPressesBase < $modulo; $aPressesBase++) {
            if (($aPressesBase * $factor) % $modulo !== $remainder) {
                continue;
            }

            for ($multiplier = 0; ($aPresses = $aPressesBase + $multiplier * $modulo) <= $buttonLimit; $multiplier++) {
                if (
                    $aPresses * $Xa > $Xprize
                    || $aPresses * $Ya > $Yprize
                ) {
                    break;
                }

                // $bPresses = ($Xprize - ($aPresses * $Xa)) / $Xb;
                if (($Xprize - ($aPresses * $Xa)) % $Xb !== 0) {
                    continue;
                }

                $bPresses = ($Xprize - ($aPresses * $Xa)) / $Xb;
                if (
                    $aPresses * $Xa + $bPresses * $Xb === $Xprize
                    && $aPresses * $Ya + $bPresses * $Yb === $Yprize
                    && $bPresses <= $buttonLimit
                ) {
                    return 3 * $aPresses + $bPresses;
                    break 2;
                }
            }

            break;
        }

        return 0;
    }
}
