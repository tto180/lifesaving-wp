<?php
/**
 * @license MIT
 *
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace LearnDash\Reports\PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Calculation\Exception;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Calculation\Functions;

class SeriesSum
{
    /**
     * SERIESSUM.
     *
     * Returns the sum of a power series
     *
     * @param mixed $x Input value
     * @param mixed $n Initial power
     * @param mixed $m Step
     * @param mixed[] $args An array of coefficients for the Data Series
     *
     * @return float|string The result, or a string containing an error
     */
    public static function evaluate($x, $n, $m, ...$args)
    {
        try {
            $x = Helpers::validateNumericNullSubstitution($x, 0);
            $n = Helpers::validateNumericNullSubstitution($n, 0);
            $m = Helpers::validateNumericNullSubstitution($m, 0);

            // Loop through arguments
            $aArgs = Functions::flattenArray($args);
            $returnValue = 0;
            $i = 0;
            foreach ($aArgs as $argx) {
                if ($argx !== null) {
                    $arg = Helpers::validateNumericNullSubstitution($argx, 0);
                    $returnValue += $arg * $x ** ($n + ($m * $i));
                    ++$i;
                }
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $returnValue;
    }
}
