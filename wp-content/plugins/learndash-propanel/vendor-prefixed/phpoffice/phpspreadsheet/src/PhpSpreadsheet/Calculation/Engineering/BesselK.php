<?php
/**
 * @license MIT
 *
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace LearnDash\Reports\PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Calculation\Exception;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Calculation\Functions;

class BesselK
{
    /**
     * BESSELK.
     *
     *    Returns the modified Bessel function Kn(x), which is equivalent to the Bessel functions evaluated
     *        for purely imaginary arguments.
     *
     *    Excel Function:
     *        BESSELK(x,ord)
     *
     * @param mixed $x A float value at which to evaluate the function.
     *                                If x is nonnumeric, BESSELK returns the #VALUE! error value.
     * @param mixed $ord The integer order of the Bessel function.
     *                       If ord is not an integer, it is truncated.
     *                                If $ord is nonnumeric, BESSELK returns the #VALUE! error value.
     *                       If $ord < 0, BESSELKI returns the #NUM! error value.
     *
     * @return float|string Result, or a string containing an error
     */
    public static function BESSELK($x, $ord)
    {
        $x = Functions::flattenSingleValue($x);
        $ord = Functions::flattenSingleValue($ord);

        try {
            $x = EngineeringValidations::validateFloat($x);
            $ord = EngineeringValidations::validateInt($ord);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($ord < 0) || ($x <= 0.0)) {
            return Functions::NAN();
        }

        $fBk = self::calculate($x, $ord);

        return (is_nan($fBk)) ? Functions::NAN() : $fBk;
    }

    private static function calculate(float $x, int $ord): float
    {
        // special cases
        switch ($ord) {
            case 0:
                return self::besselK0($x);
            case 1:
                return self::besselK1($x);
        }

        return self::besselK2($x, $ord);
    }

    private static function besselK0(float $x): float
    {
        if ($x <= 2) {
            $fNum2 = $x * 0.5;
            $y = ($fNum2 * $fNum2);

            return -log($fNum2) * BesselI::BESSELI($x, 0) +
                (-0.57721566 + $y * (0.42278420 + $y * (0.23069756 + $y * (0.3488590e-1 + $y * (0.262698e-2 + $y *
                                    (0.10750e-3 + $y * 0.74e-5))))));
        }

        $y = 2 / $x;

        return exp(-$x) / sqrt($x) *
            (1.25331414 + $y * (-0.7832358e-1 + $y * (0.2189568e-1 + $y * (-0.1062446e-1 + $y *
                            (0.587872e-2 + $y * (-0.251540e-2 + $y * 0.53208e-3))))));
    }

    private static function besselK1(float $x): float
    {
        if ($x <= 2) {
            $fNum2 = $x * 0.5;
            $y = ($fNum2 * $fNum2);

            return log($fNum2) * BesselI::BESSELI($x, 1) +
                (1 + $y * (0.15443144 + $y * (-0.67278579 + $y * (-0.18156897 + $y * (-0.1919402e-1 + $y *
                                    (-0.110404e-2 + $y * (-0.4686e-4))))))) / $x;
        }

        $y = 2 / $x;

        return exp(-$x) / sqrt($x) *
            (1.25331414 + $y * (0.23498619 + $y * (-0.3655620e-1 + $y * (0.1504268e-1 + $y * (-0.780353e-2 + $y *
                                (0.325614e-2 + $y * (-0.68245e-3)))))));
    }

    private static function besselK2(float $x, int $ord)
    {
        $fTox = 2 / $x;
        $fBkm = self::besselK0($x);
        $fBk = self::besselK1($x);
        for ($n = 1; $n < $ord; ++$n) {
            $fBkp = $fBkm + $n * $fTox * $fBk;
            $fBkm = $fBk;
            $fBk = $fBkp;
        }

        return $fBk;
    }
}
