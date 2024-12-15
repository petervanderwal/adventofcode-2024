<?php

declare(strict_types=1);

namespace App\Puzzle\Year2024;

use App\Utility\MathUtility;
use App\Utility\NumberUtility;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\Puzzle;
use PeterVanDerWal\AdventOfCode\Cli\Attribute\TestWithDemoInput;
use PeterVanDerWal\AdventOfCode\Cli\Model\PuzzleInput;

/**
 * 1a.  Xa * A + Xb * B = Xprize
 *      and
 * 1b.  Ya * A + Yb * B = Yprize
 *
 *      Which for the first puzzle equals to
 *
 *      94A + 22B = 8400                             | 94 = Xa, 22 = Xb, 8400 = Xprize
 *      and                                          |
 *      34A + 67B = 5400                             | 34 = Ya, 67 = Yb, 5400 = Yprize
 *                                                   |
 *      22B = 8400 - 94A                             | 22 = Xb, 8400 = Xprize, 94 = Xa
 *      and                                          |
 *      67B = 5400 - 34A                             | 67 = Yb, 5400 = Yprize, 34 = Ya
 *                                                    |
 * 2a.  8400 - 94A % 22 = 0                          | 8400 = Xprize, 94 = Xa, 22 = Xb
 *      and                                          |
 * 2b.  5400 - 34A % 67 = 0                          | 5400 = Yprize, 34 = Ya, 67 = Yb
 *                                                    |
 * 3a.  (67 * (8400 - 94A)) % (22 * 67) = 0          | 67 = Yb, 8400 = Xprize, 94 = Xa, 22 = Xb
 *      and                                          |
 * 3b.  (22 * (5400 - 34A)) % (22 * 67) = 0          | 22 = Xb, 5400 = Yprize, 34 = Ya, 67 = Yb
 *                                                   |
 *      (67 * 8400 - 67 * 94 * A) % (22 * 67) = 0    | 67 = Yb, 8400 = Xprize, 94 = Xa, 22 = Xb
 *      and                                          |
 *      (22 * 5400 - 22 * 34 * A) % (22 * 67) = 0    | 22 = Xb, 5400 = Yprize, 34 = Ya, 67 = Yb
 *                                                   |
 * 4a.  (562800 - 6298A) % 1474 = 0                  | 562800 = (Yb * Xprize), 6298 = (Yb * Xa), 1474 = (Xb * Yb)
 *      and                                          |
 * 4b.  (118800 - 748A) % 1474 = 0                   | 118800 = (Xb * Yprize), 748 = (Xb * Ya), 1474 = (Xb * Yb)
 *                                                   |
 * 5a.  6298A % 1474 = 562800 % 1474                 | 6298 = (Yb * Xa), 1474 = (Xb * Yb), 562800 = (Yb * Xprize)
 *      and                                          |
 * 5b.  748A % 1474 = 118800 % 1474                  | 748 = (Xb * Ya), 1474 = (Xb * Yb), 118800 = (Xb * Yprize)
 *                                                   |
 * 6a.  6298A % 1474 = 1206                          | 6298 = (Yb * Xa), 1474 = (Xb * Yb), 1206 = ((Yb * Xprize) % (Xb * Yb))
 *      and                                          |
 * 6b.  748A % 1474 = 880                            | 748 = (Xb * Ya), 1474 = (Xb * Yb), 880 = ((Xb * Yprize) % (Xb * Yb))
 *                                                    |
 *      According to Wikipedia https://en.wikipedia.org/wiki/Modular_arithmetic#Basic_properties
 *      if                                           |
 *        a1 ≡ a2  (mod m) and b1 ≡ b2  (mod m)      |
 *      then                                         |
 *        (a1 + b1) ≡ (a2 + b2)  (mod m)             |
 *                                                   |
 *      Therefore:                                   |
 * 7.   (6298A + 748A) % 1474 = (1206 + 880) % 1474  | 6298 = (Yb * Xa), 748 = (Xb * Ya), 1474 = (Xb * Yb), 1206 = ((Yb * Xprize) % (Xb * Yb)), 880 = ((Xb * Yprize) % (Xb * Yb))
 *      (6298 + 748)A % 1474 = 2086 % 1474           | 6298 = (Yb * Xa), 748 = (Xb * Ya), 1474 = (Xb * Yb), 2086 = (((Yb * Xprize) % (Xb * Yb)) + ((Xb * Yprize) % (Xb * Yb)))
 * 8.   7046A % 1474 = 612                           | 7046 = ((Yb * Xa) + (Xb * Ya)), 1474 = (Xb * Yb), 612 = (((Yb * Xprize) + (Xb * Yprize)) % (Xb * Yb))
 *      =>                                           |
 * 9.   7046 % 1474 = 1150                           | 7046 = ((Yb * Xa) + (Xb * Ya)), 1474 = (Xb * Yb), 1150 = (((Yb * Xa) + (Xb * Ya)) % (Xb * Yb))
 *      =>                                           |
 * 10.  1150A % 1474 = 612                           | 1150 = (((Yb * Xa) + (Xb * Ya)) % (Xb * Yb)), 1474 = (Xb * Yb), 612 = (((Yb * Xprize) + (Xb * Yprize)) % (Xb * Yb))
 *      =>                                           |
 * 11.  gcd(1150, 1474) = 2                          | Note: this part (step 11 till 14) is now implemented in MathUtility::reverseModule()
 * 12.  => Check if 612 can be divided by 2 (our gcd |
 *         here). If it can't, then there is no      |
 *         answer to the puzzle. Think about it, any |
 *         1150 * A % 1474 will be an even number    |
 *         so that can never be an uneven number.    |
 *                                                   |
 * 13.  => Divide all values by our gcd (2 here)     |
 *      575A % 737 = 306                             |
 * 14.  => To solve A, try any number 0..736, to     |
 *         find the first (minimal) value for A.     | From here on, we store
 *         Within this example that is 80. Then any  | Afirst := 80
 *         addition of 737 (so 80, 817, 1554) will   | Arepeat := 737
 *         be possible values for A                  |
 *                                                   |
 *      Now that we know above, we can say that      |
 * 15.  A = 80 + 737i                                |
 *                                                   |
 *      We also know that                            |
 * 16.  B = (Xprize - (A * Xa)) / Xb                 |
 *                                                   |
 *      If we substitute A here with 80 + 737i, then |
 *                                                   |
 * 17.  B = (Xprize - (80 + 737i) * Xa) / Xb         |
 *      B = (8400 - (80 + 737i) * 94) / 22           | 8400 = Xprize, 80 = Afirst, 737 = Arepeat, 94 = Xa, 22 = Xb
 *      B = (8400 - (80 * 94) - (737*94)i) / 22      | 8400 = Xprize, 80 = Afirst, 737 = Arepeat, 94 = Xa, 22 = Xb
 *      B = (8400 - 7520 - 69278i) / 22              | 8400 = Xprize, 7520 = (Afirst * Xa), 69278 = (Arepeat * Xa), 22 = Xb
 * 18.  B = (880 - 69278i) / 22                      | 880 = (Xprize - (Afirst * Xa)), 69278 = (Arepeat * Xa), 22 = Xb
 *                                                   |
 *      Looking back at original formulas            |
 * 1a.     Xa * A + Xb * B = Xprize                  |
 *         and                                       |
 * 1b.     Ya * A + Yb * B = Yprize                  |
 *      And replacing A & B with our new formulas    |
 *                                                   |
 * 19a. 94 * (80 + 737i) +                           | 94 = Xa, 80 = Afirst, 737 = Arepeat
 *           22 * ((880 - 69278i) / 22) = 8400       |   22 = Xb, 880 = (Xprize - (Afirst * Xa)), 69278 = (Arepeat * Xa), 22 = Xb, 8400 = Xprize
 *        and                                        |
 * 19b. 34 * (80 + 737i) +                           | 34 = Ya, 80 = Afirst, 737 = Arepeat
 *           67 * ((880 - 69278i) / 22) = 5400       |   67 = Yb, 880 = (Xprize - (Afirst * Xa)), 69278 = (Arepeat * Xa), 22 = Xb, 5400 = Yprize
 *                                                   |
 *      Simplifying the first (19a) formula          |
 * 20a. (94 * 80) + (94 * 737)i +                    |
 *           (880 - 69278i) = 8400                   |
 *      7520 + 68278i + 880 - 69278i = 8400          |
 *      Which will always be true and thus helpless  |
 *                                                   |
 *      Simplifying the second formula               |
 * 20b. 34 * 80 + 34 * 737i +                        | 34 = Ya, 80 = Afirst, 737 = Arepeat
 *           ((67 * 880 - 67 * 69278i) / 22) = 5400  |   67 = Yb, 8226 = (Xprize - (Afirst * Xa)), 69278 = (Arepeat * Xa), 22 = Xb, 5400 = Yprize
 *                                                   |
 *      2720 + 25058i +                              | 2720 = (Ya * Afirst), 25058 = (Ya * Arepeat)
 *           ((58960 - 4641626i) / 22) = 5400        |   58960 = (Yb * (Xprize - (Afirst * Xa))), 4641626 = (Yb * Arepeat * Xa), 22 = Xb, 5400 = Yprize
 *                                                   |
 *                      58960 - 4641626i             | 58960 = (Yb * (Xprize - (Afirst * Xa))), 4641626 = (Yb * Arepeat * Xa)
 *      2720 + 25058i + ---------------- = 5400      | 2720 = (Ya * Afirst), 25058 = (Ya * Arepeat), 5400 = Yprize
 *                             22                    | 22 = Xb
 *                                                   |
 *               58960 - 4641626i                    | 58960 = (Yb * (Xprize - (Afirst * Xa))), 4641626 = (Yb * Arepeat * Xa)
 *      25058i + ---------------- = 5400 - 2720      | 2720 = (Ya * Afirst), 25058 = (Ya * Arepeat), 5400 = Yprize
 *                      22                           | 22 = Xb
 *                                                   |
 *               58960 - 4641626i                    | 58960 = (Yb * (Xprize - (Afirst * Xa))), 4641626 = (Yb * Arepeat * Xa)
 *      25058i + ---------------- = 2680             | 25058 = (Ya * Arepeat), 2680 = (Yprize - (Ya * Afirst))
 *                      22                           | 22 = Xb
 *                                                   |
 *      22 * 25058i   58960 - 4641626i               | 22 = Xb, 58960 = (Yb * (Xprize - (Afirst * Xa))), 4641626 = (Yb * Arepeat * Xa)
 *      ----------- + ---------------- = 2680        | 25058 = (Ya * Arepeat), 2680 = (Yprize - (Ya * Afirst))
 *           22              22                      | 22 = Xb
 *                                                   |
 *      22 * 25058i + 58960 - 4641626i               | 22 = Xb, 58960 = (Yb * (Xprize - (Afirst * Xa))), 4641626 = (Yb * Arepeat * Xa)
 *      ------------------------------ = 2680        | 25058 = (Ya * Arepeat), 2680 = (Yprize - (Ya * Afirst))
 *                   22                              | 22 = Xb
 *                                                   |
 *      22 * 25058i + 58960 - 4641626i = 2680 * 22   | 22 = Xb, 58960 = (Yb * (Xprize - (Afirst * Xa))), 4641626 = (Yb * Arepeat * Xa), 25058 = (Ya * Arepeat), 2680 = (Yprize - (Ya * Afirst))
 *      551276i - 4641626i + 58960 = 2680 * 22       | 551276 = (Xb * Ya * Arepeat), 58960 = (Yb * (Xprize - (Afirst * Xa))), 4641626 = (Yb * Arepeat * Xa), 2680 = (Yprize - (Ya * Afirst))
 *      (551276 - 4641626)i + 58960 = 2680 * 22      | 551276 = (Xb * Ya * Arepeat), 58960 = (Yb * (Xprize - (Afirst * Xa))), 4641626 = (Yb * Arepeat * Xa), 2680 = (Yprize - (Ya * Afirst))
 *      -4090350i + 58960 = 2680 * 22                | -4090350 = ((Xb * Ya * Arepeat) - (Yb * Arepeat * Xa)), 58960 = (Yb * (Xprize - (Afirst * Xa))), 2680 = (Yprize - (Ya * Afirst)), 22 = Xb
 *      -4090350i + 58960 = 58960                    | -4090350 = ((Xb * Ya * Arepeat) - (Yb * Arepeat * Xa)), 58960(first) = (Yb * (Xprize - (Afirst * Xa))), 58960(second) = (Xb * (Yprize - (Ya * Afirst)))
 *      -4090350i = 0                                | -4090350 = ((Xb * Ya * Arepeat) - (Yb * Arepeat * Xa)), 0 = ((Xb * (Yprize - (Ya * Afirst))) - (Yb * (Xprize - (Afirst * Xa))))
 * 21.  i = 0 / -4090350                             | i = ((Xb * (Yprize - (Ya * Afirst))) - (Yb * (Xprize - (Afirst * Xa)))) / ((Xb * Ya * Arepeat) - (Yb * Arepeat * Xa))
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
        return $this->solve($input, 100, 0);
    }

    /**
     * Note: the expected answer for the demo input is not denoted in the puzzle but was found by running an earlier
     * (slower) implementation and used to optimize the further calculation
     */
    #[Puzzle(2024, day: 13, part: 2)]
    #[TestWithDemoInput(self::DEMO_INPUT, expectedAnswer: 875318608908)]
    public function part2(PuzzleInput $input): int
    {
        return $this->solve($input, PHP_INT_MAX, 10000000000000);
    }

    private function solve(PuzzleInput $input, int $buttonLimit, int $offset): int
    {
        $cost = 0;

        foreach ($input->split("\n\n") as $block) {
            [$Xa, $Ya, $Xb, $Yb, $Xprize, $Yprize] = NumberUtility::getNumbersFromLine($block);
            $Xprize += $offset;
            $Yprize += $offset;

            // Perform formulas 9. and 10. in explanation above (factor = 1150, modulo = 1474, remainder = 612)
            $factor = (($Yb * $Xa) + ($Xb * $Ya)) % ($Xb * $Yb);
            $modulo = $Xb * $Yb;
            $remainder = (($Yb * $Xprize) + ($Xb * $Yprize)) % $modulo;

            // Perform formulas 11 till 14 in explanation above
            $reverseModulo = MathUtility::reverseModule($factor, $modulo, $remainder);
            if ($reverseModulo === null) {
                // No solution
                continue;
            }
            $Afirst = $reverseModulo['first'];
            $Arepeat = $reverseModulo['repeat'];

            // Perform formula 21 in explanation above
            $dividend = ($Xb * ($Yprize - ($Ya * $Afirst))) - ($Yb * ($Xprize - ($Afirst * $Xa)));
            $divisor = ($Xb * $Ya * $Arepeat) - ($Yb * $Arepeat * $Xa);
            if ($dividend % $divisor !== 0) {
                // Invalid amount of repeats, $dividend / $divisor should be an integer, no solution for this puzzle
                continue;
            }
            $i = $dividend / $divisor;

            // Formula 15
            $A = $Afirst + $Arepeat * $i;
            // Formula 16
            $B = ($Xprize - ($A * $Xa)) / $Xb;

            if ($A < 0 || $B < 0 || $A > $buttonLimit || $B > $buttonLimit) {
                // No solution within range
                continue;
            }

            $cost += 3 * $A + $B;
        }
        return $cost;
    }
}
