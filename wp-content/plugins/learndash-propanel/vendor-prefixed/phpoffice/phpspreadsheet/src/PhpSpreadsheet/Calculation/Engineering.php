<?php
/**
 * @license MIT
 *
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace LearnDash\Reports\PhpOffice\PhpSpreadsheet\Calculation;

use LearnDash\Reports\Complex\Complex;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexOperations;

/**
 * @deprecated 1.18.0
 */
class Engineering
{
    /**
     * EULER.
     *
     * @deprecated 1.18.0
     * @see Use Engineering\Constants\EULER instead
     */
    public const EULER = 2.71828182845904523536;

    /**
     * parseComplex.
     *
     * Parses a complex number into its real and imaginary parts, and an I or J suffix
     *
     * @deprecated 1.12.0 No longer used by internal code. Please use the \Complex\Complex class instead
     *
     * @param string $complexNumber The complex number
     *
     * @return mixed[] Indexed on "real", "imaginary" and "suffix"
     */
    public static function parseComplex($complexNumber)
    {
        $complex = new Complex($complexNumber);

        return [
            'real' => $complex->getReal(),
            'imaginary' => $complex->getImaginary(),
            'suffix' => $complex->getSuffix(),
        ];
    }

    /**
     * BESSELI.
     *
     *    Returns the modified Bessel function In(x), which is equivalent to the Bessel function evaluated
     *        for purely imaginary arguments
     *
     *    Excel Function:
     *        BESSELI(x,ord)
     *
     * @Deprecated 1.17.0
     *
     * @see Use the BESSELI() method in the Engineering\BesselI class instead
     *
     * @param float $x The value at which to evaluate the function.
     *                                If x is nonnumeric, BESSELI returns the #VALUE! error value.
     * @param int $ord The order of the Bessel function.
     *                                If ord is not an integer, it is truncated.
     *                                If $ord is nonnumeric, BESSELI returns the #VALUE! error value.
     *                                If $ord < 0, BESSELI returns the #NUM! error value.
     *
     * @return float|string Result, or a string containing an error
     */
    public static function BESSELI($x, $ord)
    {
        return Engineering\BesselI::BESSELI($x, $ord);
    }

    /**
     * BESSELJ.
     *
     *    Returns the Bessel function
     *
     *    Excel Function:
     *        BESSELJ(x,ord)
     *
     * @Deprecated 1.17.0
     *
     * @see Use the BESSELJ() method in the Engineering\BesselJ class instead
     *
     * @param float $x The value at which to evaluate the function.
     *                                If x is nonnumeric, BESSELJ returns the #VALUE! error value.
     * @param int $ord The order of the Bessel function. If n is not an integer, it is truncated.
     *                                If $ord is nonnumeric, BESSELJ returns the #VALUE! error value.
     *                                If $ord < 0, BESSELJ returns the #NUM! error value.
     *
     * @return float|string Result, or a string containing an error
     */
    public static function BESSELJ($x, $ord)
    {
        return Engineering\BesselJ::BESSELJ($x, $ord);
    }

    /**
     * BESSELK.
     *
     *    Returns the modified Bessel function Kn(x), which is equivalent to the Bessel functions evaluated
     *        for purely imaginary arguments.
     *
     *    Excel Function:
     *        BESSELK(x,ord)
     *
     * @Deprecated 1.17.0
     *
     * @see Use the BESSELK() method in the Engineering\BesselK class instead
     *
     * @param float $x The value at which to evaluate the function.
     *                                If x is nonnumeric, BESSELK returns the #VALUE! error value.
     * @param int $ord The order of the Bessel function. If n is not an integer, it is truncated.
     *                                If $ord is nonnumeric, BESSELK returns the #VALUE! error value.
     *                                If $ord < 0, BESSELK returns the #NUM! error value.
     *
     * @return float|string Result, or a string containing an error
     */
    public static function BESSELK($x, $ord)
    {
        return Engineering\BesselK::BESSELK($x, $ord);
    }

    /**
     * BESSELY.
     *
     * Returns the Bessel function, which is also called the Weber function or the Neumann function.
     *
     *    Excel Function:
     *        BESSELY(x,ord)
     *
     * @Deprecated 1.17.0
     *
     * @see Use the BESSELY() method in the Engineering\BesselY class instead
     *
     * @param float $x The value at which to evaluate the function.
     *                                If x is nonnumeric, BESSELY returns the #VALUE! error value.
     * @param int $ord The order of the Bessel function. If n is not an integer, it is truncated.
     *                                If $ord is nonnumeric, BESSELY returns the #VALUE! error value.
     *                                If $ord < 0, BESSELY returns the #NUM! error value.
     *
     * @return float|string Result, or a string containing an error
     */
    public static function BESSELY($x, $ord)
    {
        return Engineering\BesselY::BESSELY($x, $ord);
    }

    /**
     * BINTODEC.
     *
     * Return a binary value as decimal.
     *
     * Excel Function:
     *        BIN2DEC(x)
     *
     * @Deprecated 1.17.0
     *
     * @see Use the toDecimal() method in the Engineering\ConvertBinary class instead
     *
     * @param mixed $x The binary number (as a string) that you want to convert. The number
     *                                cannot contain more than 10 characters (10 bits). The most significant
     *                                bit of number is the sign bit. The remaining 9 bits are magnitude bits.
     *                                Negative numbers are represented using two's-complement notation.
     *                                If number is not a valid binary number, or if number contains more than
     *                                10 characters (10 bits), BIN2DEC returns the #NUM! error value.
     *
     * @return string
     */
    public static function BINTODEC($x)
    {
        return Engineering\ConvertBinary::toDecimal($x);
    }

    /**
     * BINTOHEX.
     *
     * Return a binary value as hex.
     *
     * Excel Function:
     *        BIN2HEX(x[,places])
     *
     * @Deprecated 1.17.0
     *
     * @see Use the toHex() method in the Engineering\ConvertBinary class instead
     *
     * @param mixed $x The binary number (as a string) that you want to convert. The number
     *                                cannot contain more than 10 characters (10 bits). The most significant
     *                                bit of number is the sign bit. The remaining 9 bits are magnitude bits.
     *                                Negative numbers are represented using two's-complement notation.
     *                                If number is not a valid binary number, or if number contains more than
     *                                10 characters (10 bits), BIN2HEX returns the #NUM! error value.
     * @param mixed $places The number of characters to use. If places is omitted, BIN2HEX uses the
     *                                minimum number of characters necessary. Places is useful for padding the
     *                                return value with leading 0s (zeros).
     *                                If places is not an integer, it is truncated.
     *                                If places is nonnumeric, BIN2HEX returns the #VALUE! error value.
     *                                If places is negative, BIN2HEX returns the #NUM! error value.
     *
     * @return string
     */
    public static function BINTOHEX($x, $places = null)
    {
        return Engineering\ConvertBinary::toHex($x, $places);
    }

    /**
     * BINTOOCT.
     *
     * Return a binary value as octal.
     *
     * Excel Function:
     *        BIN2OCT(x[,places])
     *
     * @Deprecated 1.17.0
     *
     * @see Use the toOctal() method in the Engineering\ConvertBinary class instead
     *
     * @param mixed $x The binary number (as a string) that you want to convert. The number
     *                                cannot contain more than 10 characters (10 bits). The most significant
     *                                bit of number is the sign bit. The remaining 9 bits are magnitude bits.
     *                                Negative numbers are represented using two's-complement notation.
     *                                If number is not a valid binary number, or if number contains more than
     *                                10 characters (10 bits), BIN2OCT returns the #NUM! error value.
     * @param mixed $places The number of characters to use. If places is omitted, BIN2OCT uses the
     *                                minimum number of characters necessary. Places is useful for padding the
     *                                return value with leading 0s (zeros).
     *                                If places is not an integer, it is truncated.
     *                                If places is nonnumeric, BIN2OCT returns the #VALUE! error value.
     *                                If places is negative, BIN2OCT returns the #NUM! error value.
     *
     * @return string
     */
    public static function BINTOOCT($x, $places = null)
    {
        return Engineering\ConvertBinary::toOctal($x, $places);
    }

    /**
     * DECTOBIN.
     *
     * Return a decimal value as binary.
     *
     * Excel Function:
     *        DEC2BIN(x[,places])
     *
     * @Deprecated 1.17.0
     *
     * @see Use the toBinary() method in the Engineering\ConvertDecimal class instead
     *
     * @param mixed $x The decimal integer you want to convert. If number is negative,
     *                                valid place values are ignored and DEC2BIN returns a 10-character
     *                                (10-bit) binary number in which the most significant bit is the sign
     *                                bit. The remaining 9 bits are magnitude bits. Negative numbers are
     *                                represented using two's-complement notation.
     *                                If number < -512 or if number > 511, DEC2BIN returns the #NUM! error
     *                                value.
     *                                If number is nonnumeric, DEC2BIN returns the #VALUE! error value.
     *                                If DEC2BIN requires more than places characters, it returns the #NUM!
     *                                error value.
     * @param mixed $places The number of characters to use. If places is omitted, DEC2BIN uses
     *                                the minimum number of characters necessary. Places is useful for
     *                                padding the return value with leading 0s (zeros).
     *                                If places is not an integer, it is truncated.
     *                                If places is nonnumeric, DEC2BIN returns the #VALUE! error value.
     *                                If places is zero or negative, DEC2BIN returns the #NUM! error value.
     *
     * @return string
     */
    public static function DECTOBIN($x, $places = null)
    {
        return Engineering\ConvertDecimal::toBinary($x, $places);
    }

    /**
     * DECTOHEX.
     *
     * Return a decimal value as hex.
     *
     * Excel Function:
     *        DEC2HEX(x[,places])
     *
     * @Deprecated 1.17.0
     *
     * @see Use the toHex() method in the Engineering\ConvertDecimal class instead
     *
     * @param mixed $x The decimal integer you want to convert. If number is negative,
     *                                places is ignored and DEC2HEX returns a 10-character (40-bit)
     *                                hexadecimal number in which the most significant bit is the sign
     *                                bit. The remaining 39 bits are magnitude bits. Negative numbers
     *                                are represented using two's-complement notation.
     *                                If number < -549,755,813,888 or if number > 549,755,813,887,
     *                                DEC2HEX returns the #NUM! error value.
     *                                If number is nonnumeric, DEC2HEX returns the #VALUE! error value.
     *                                If DEC2HEX requires more than places characters, it returns the
     *                                #NUM! error value.
     * @param mixed $places The number of characters to use. If places is omitted, DEC2HEX uses
     *                                the minimum number of characters necessary. Places is useful for
     *                                padding the return value with leading 0s (zeros).
     *                                If places is not an integer, it is truncated.
     *                                If places is nonnumeric, DEC2HEX returns the #VALUE! error value.
     *                                If places is zero or negative, DEC2HEX returns the #NUM! error value.
     *
     * @return string
     */
    public static function DECTOHEX($x, $places = null)
    {
        return Engineering\ConvertDecimal::toHex($x, $places);
    }

    /**
     * DECTOOCT.
     *
     * Return an decimal value as octal.
     *
     * Excel Function:
     *        DEC2OCT(x[,places])
     *
     * @Deprecated 1.17.0
     *
     * @see Use the toOctal() method in the Engineering\ConvertDecimal class instead
     *
     * @param mixed $x The decimal integer you want to convert. If number is negative,
     *                                places is ignored and DEC2OCT returns a 10-character (30-bit)
     *                                octal number in which the most significant bit is the sign bit.
     *                                The remaining 29 bits are magnitude bits. Negative numbers are
     *                                represented using two's-complement notation.
     *                                If number < -536,870,912 or if number > 536,870,911, DEC2OCT
     *                                returns the #NUM! error value.
     *                                If number is nonnumeric, DEC2OCT returns the #VALUE! error value.
     *                                If DEC2OCT requires more than places characters, it returns the
     *                                #NUM! error value.
     * @param mixed $places The number of characters to use. If places is omitted, DEC2OCT uses
     *                                the minimum number of characters necessary. Places is useful for
     *                                padding the return value with leading 0s (zeros).
     *                                If places is not an integer, it is truncated.
     *                                If places is nonnumeric, DEC2OCT returns the #VALUE! error value.
     *                                If places is zero or negative, DEC2OCT returns the #NUM! error value.
     *
     * @return string
     */
    public static function DECTOOCT($x, $places = null)
    {
        return Engineering\ConvertDecimal::toOctal($x, $places);
    }

    /**
     * HEXTOBIN.
     *
     * Return a hex value as binary.
     *
     * Excel Function:
     *        HEX2BIN(x[,places])
     *
     * @Deprecated 1.17.0
     *
     * @see Use the toBinary() method in the Engineering\ConvertHex class instead
     *
     * @param mixed $x the hexadecimal number (as a string) that you want to convert.
     *                  Number cannot contain more than 10 characters.
     *                  The most significant bit of number is the sign bit (40th bit from the right).
     *                  The remaining 9 bits are magnitude bits.
     *                  Negative numbers are represented using two's-complement notation.
     *                  If number is negative, HEX2BIN ignores places and returns a 10-character binary number.
     *                  If number is negative, it cannot be less than FFFFFFFE00,
     *                      and if number is positive, it cannot be greater than 1FF.
     *                  If number is not a valid hexadecimal number, HEX2BIN returns the #NUM! error value.
     *                  If HEX2BIN requires more than places characters, it returns the #NUM! error value.
     * @param mixed $places The number of characters to use. If places is omitted,
     *                                    HEX2BIN uses the minimum number of characters necessary. Places
     *                                    is useful for padding the return value with leading 0s (zeros).
     *                                    If places is not an integer, it is truncated.
     *                                    If places is nonnumeric, HEX2BIN returns the #VALUE! error value.
     *                                    If places is negative, HEX2BIN returns the #NUM! error value.
     *
     * @return string
     */
    public static function HEXTOBIN($x, $places = null)
    {
        return Engineering\ConvertHex::toBinary($x, $places);
    }

    /**
     * HEXTODEC.
     *
     * Return a hex value as decimal.
     *
     * Excel Function:
     *        HEX2DEC(x)
     *
     * @Deprecated 1.17.0
     *
     * @see Use the toDecimal() method in the Engineering\ConvertHex class instead
     *
     * @param mixed $x The hexadecimal number (as a string) that you want to convert. This number cannot
     *                                contain more than 10 characters (40 bits). The most significant
     *                                bit of number is the sign bit. The remaining 39 bits are magnitude
     *                                bits. Negative numbers are represented using two's-complement
     *                                notation.
     *                                If number is not a valid hexadecimal number, HEX2DEC returns the
     *                                #NUM! error value.
     *
     * @return string
     */
    public static function HEXTODEC($x)
    {
        return Engineering\ConvertHex::toDecimal($x);
    }

    /**
     * HEXTOOCT.
     *
     * Return a hex value as octal.
     *
     * Excel Function:
     *        HEX2OCT(x[,places])
     *
     * @Deprecated 1.17.0
     *
     * @see Use the toOctal() method in the Engineering\ConvertHex class instead
     *
     * @param mixed $x The hexadecimal number (as a string) that you want to convert. Number cannot
     *                                    contain more than 10 characters. The most significant bit of
     *                                    number is the sign bit. The remaining 39 bits are magnitude
     *                                    bits. Negative numbers are represented using two's-complement
     *                                    notation.
     *                                    If number is negative, HEX2OCT ignores places and returns a
     *                                    10-character octal number.
     *                                    If number is negative, it cannot be less than FFE0000000, and
     *                                    if number is positive, it cannot be greater than 1FFFFFFF.
     *                                    If number is not a valid hexadecimal number, HEX2OCT returns
     *                                    the #NUM! error value.
     *                                    If HEX2OCT requires more than places characters, it returns
     *                                    the #NUM! error value.
     * @param mixed $places The number of characters to use. If places is omitted, HEX2OCT
     *                                    uses the minimum number of characters necessary. Places is
     *                                    useful for padding the return value with leading 0s (zeros).
     *                                    If places is not an integer, it is truncated.
     *                                    If places is nonnumeric, HEX2OCT returns the #VALUE! error
     *                                    value.
     *                                    If places is negative, HEX2OCT returns the #NUM! error value.
     *
     * @return string
     */
    public static function HEXTOOCT($x, $places = null)
    {
        return Engineering\ConvertHex::toOctal($x, $places);
    }

    /**
     * OCTTOBIN.
     *
     * Return an octal value as binary.
     *
     * Excel Function:
     *        OCT2BIN(x[,places])
     *
     * @Deprecated 1.17.0
     *
     * @see Use the toBinary() method in the Engineering\ConvertOctal class instead
     *
     * @param mixed $x The octal number you want to convert. Number may not
     *                                    contain more than 10 characters. The most significant
     *                                    bit of number is the sign bit. The remaining 29 bits
     *                                    are magnitude bits. Negative numbers are represented
     *                                    using two's-complement notation.
     *                                    If number is negative, OCT2BIN ignores places and returns
     *                                    a 10-character binary number.
     *                                    If number is negative, it cannot be less than 7777777000,
     *                                    and if number is positive, it cannot be greater than 777.
     *                                    If number is not a valid octal number, OCT2BIN returns
     *                                    the #NUM! error value.
     *                                    If OCT2BIN requires more than places characters, it
     *                                    returns the #NUM! error value.
     * @param mixed $places The number of characters to use. If places is omitted,
     *                                    OCT2BIN uses the minimum number of characters necessary.
     *                                    Places is useful for padding the return value with
     *                                    leading 0s (zeros).
     *                                    If places is not an integer, it is truncated.
     *                                    If places is nonnumeric, OCT2BIN returns the #VALUE!
     *                                    error value.
     *                                    If places is negative, OCT2BIN returns the #NUM! error
     *                                    value.
     *
     * @return string
     */
    public static function OCTTOBIN($x, $places = null)
    {
        return Engineering\ConvertOctal::toBinary($x, $places);
    }

    /**
     * OCTTODEC.
     *
     * Return an octal value as decimal.
     *
     * Excel Function:
     *        OCT2DEC(x)
     *
     * @Deprecated 1.17.0
     *
     * @see Use the toDecimal() method in the Engineering\ConvertOctal class instead
     *
     * @param mixed $x The octal number you want to convert. Number may not contain
     *                                more than 10 octal characters (30 bits). The most significant
     *                                bit of number is the sign bit. The remaining 29 bits are
     *                                magnitude bits. Negative numbers are represented using
     *                                two's-complement notation.
     *                                If number is not a valid octal number, OCT2DEC returns the
     *                                #NUM! error value.
     *
     * @return string
     */
    public static function OCTTODEC($x)
    {
        return Engineering\ConvertOctal::toDecimal($x);
    }

    /**
     * OCTTOHEX.
     *
     * Return an octal value as hex.
     *
     * Excel Function:
     *        OCT2HEX(x[,places])
     *
     * @Deprecated 1.17.0
     *
     * @see Use the toHex() method in the Engineering\ConvertOctal class instead
     *
     * @param mixed $x The octal number you want to convert. Number may not contain
     *                                    more than 10 octal characters (30 bits). The most significant
     *                                    bit of number is the sign bit. The remaining 29 bits are
     *                                    magnitude bits. Negative numbers are represented using
     *                                    two's-complement notation.
     *                                    If number is negative, OCT2HEX ignores places and returns a
     *                                    10-character hexadecimal number.
     *                                    If number is not a valid octal number, OCT2HEX returns the
     *                                    #NUM! error value.
     *                                    If OCT2HEX requires more than places characters, it returns
     *                                    the #NUM! error value.
     * @param mixed $places The number of characters to use. If places is omitted, OCT2HEX
     *                                    uses the minimum number of characters necessary. Places is useful
     *                                    for padding the return value with leading 0s (zeros).
     *                                    If places is not an integer, it is truncated.
     *                                    If places is nonnumeric, OCT2HEX returns the #VALUE! error value.
     *                                    If places is negative, OCT2HEX returns the #NUM! error value.
     *
     * @return string
     */
    public static function OCTTOHEX($x, $places = null)
    {
        return Engineering\ConvertOctal::toHex($x, $places);
    }

    /**
     * COMPLEX.
     *
     * Converts real and imaginary coefficients into a complex number of the form x +/- yi or x +/- yj.
     *
     * Excel Function:
     *        COMPLEX(realNumber,imaginary[,suffix])
     *
     * @Deprecated 1.18.0
     *
     * @see Use the COMPLEX() method in the Engineering\Complex class instead
     *
     * @param float $realNumber the real coefficient of the complex number
     * @param float $imaginary the imaginary coefficient of the complex number
     * @param string $suffix The suffix for the imaginary component of the complex number.
     *                                        If omitted, the suffix is assumed to be "i".
     *
     * @return string
     */
    public static function COMPLEX($realNumber = 0.0, $imaginary = 0.0, $suffix = 'i')
    {
        return Engineering\Complex::COMPLEX($realNumber, $imaginary, $suffix);
    }

    /**
     * IMAGINARY.
     *
     * Returns the imaginary coefficient of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMAGINARY(complexNumber)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the IMAGINARY() method in the Engineering\Complex class instead
     *
     * @param string $complexNumber the complex number for which you want the imaginary
     *                                         coefficient
     *
     * @return float|string
     */
    public static function IMAGINARY($complexNumber)
    {
        return Engineering\Complex::IMAGINARY($complexNumber);
    }

    /**
     * IMREAL.
     *
     * Returns the real coefficient of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMREAL(complexNumber)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the IMREAL() method in the Engineering\Complex class instead
     *
     * @param string $complexNumber the complex number for which you want the real coefficient
     *
     * @return float|string
     */
    public static function IMREAL($complexNumber)
    {
        return Engineering\Complex::IMREAL($complexNumber);
    }

    /**
     * IMABS.
     *
     * Returns the absolute value (modulus) of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMABS(complexNumber)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the IMABS() method in the Engineering\ComplexFunctions class instead
     *
     * @param string $complexNumber the complex number for which you want the absolute value
     *
     * @return float|string
     */
    public static function IMABS($complexNumber)
    {
        return ComplexFunctions::IMABS($complexNumber);
    }

    /**
     * IMARGUMENT.
     *
     * Returns the argument theta of a complex number, i.e. the angle in radians from the real
     * axis to the representation of the number in polar coordinates.
     *
     * Excel Function:
     *        IMARGUMENT(complexNumber)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the IMARGUMENT() method in the Engineering\ComplexFunctions class instead
     *
     * @param string $complexNumber the complex number for which you want the argument theta
     *
     * @return float|string
     */
    public static function IMARGUMENT($complexNumber)
    {
        return ComplexFunctions::IMARGUMENT($complexNumber);
    }

    /**
     * IMCONJUGATE.
     *
     * Returns the complex conjugate of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMCONJUGATE(complexNumber)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the IMARGUMENT() method in the Engineering\ComplexFunctions class instead
     *
     * @param string $complexNumber the complex number for which you want the conjugate
     *
     * @return string
     */
    public static function IMCONJUGATE($complexNumber)
    {
        return ComplexFunctions::IMCONJUGATE($complexNumber);
    }

    /**
     * IMCOS.
     *
     * Returns the cosine of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMCOS(complexNumber)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the IMCOS() method in the Engineering\ComplexFunctions class instead
     *
     * @param string $complexNumber the complex number for which you want the cosine
     *
     * @return float|string
     */
    public static function IMCOS($complexNumber)
    {
        return ComplexFunctions::IMCOS($complexNumber);
    }

    /**
     * IMCOSH.
     *
     * Returns the hyperbolic cosine of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMCOSH(complexNumber)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the IMCOSH() method in the Engineering\ComplexFunctions class instead
     *
     * @param string $complexNumber the complex number for which you want the hyperbolic cosine
     *
     * @return float|string
     */
    public static function IMCOSH($complexNumber)
    {
        return ComplexFunctions::IMCOSH($complexNumber);
    }

    /**
     * IMCOT.
     *
     * Returns the cotangent of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMCOT(complexNumber)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the IMCOT() method in the Engineering\ComplexFunctions class instead
     *
     * @param string $complexNumber the complex number for which you want the cotangent
     *
     * @return float|string
     */
    public static function IMCOT($complexNumber)
    {
        return ComplexFunctions::IMCOT($complexNumber);
    }

    /**
     * IMCSC.
     *
     * Returns the cosecant of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMCSC(complexNumber)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the IMCSC() method in the Engineering\ComplexFunctions class instead
     *
     * @param string $complexNumber the complex number for which you want the cosecant
     *
     * @return float|string
     */
    public static function IMCSC($complexNumber)
    {
        return ComplexFunctions::IMCSC($complexNumber);
    }

    /**
     * IMCSCH.
     *
     * Returns the hyperbolic cosecant of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMCSCH(complexNumber)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the IMCSCH() method in the Engineering\ComplexFunctions class instead
     *
     * @param string $complexNumber the complex number for which you want the hyperbolic cosecant
     *
     * @return float|string
     */
    public static function IMCSCH($complexNumber)
    {
        return ComplexFunctions::IMCSCH($complexNumber);
    }

    /**
     * IMSIN.
     *
     * Returns the sine of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMSIN(complexNumber)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the IMSIN() method in the Engineering\ComplexFunctions class instead
     *
     * @param string $complexNumber the complex number for which you want the sine
     *
     * @return float|string
     */
    public static function IMSIN($complexNumber)
    {
        return ComplexFunctions::IMSIN($complexNumber);
    }

    /**
     * IMSINH.
     *
     * Returns the hyperbolic sine of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMSINH(complexNumber)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the IMSINH() method in the Engineering\ComplexFunctions class instead
     *
     * @param string $complexNumber the complex number for which you want the hyperbolic sine
     *
     * @return float|string
     */
    public static function IMSINH($complexNumber)
    {
        return ComplexFunctions::IMSINH($complexNumber);
    }

    /**
     * IMSEC.
     *
     * Returns the secant of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMSEC(complexNumber)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the IMSEC() method in the Engineering\ComplexFunctions class instead
     *
     * @param string $complexNumber the complex number for which you want the secant
     *
     * @return float|string
     */
    public static function IMSEC($complexNumber)
    {
        return ComplexFunctions::IMSEC($complexNumber);
    }

    /**
     * IMSECH.
     *
     * Returns the hyperbolic secant of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMSECH(complexNumber)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the IMSECH() method in the Engineering\ComplexFunctions class instead
     *
     * @param string $complexNumber the complex number for which you want the hyperbolic secant
     *
     * @return float|string
     */
    public static function IMSECH($complexNumber)
    {
        return ComplexFunctions::IMSECH($complexNumber);
    }

    /**
     * IMTAN.
     *
     * Returns the tangent of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMTAN(complexNumber)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the IMTAN() method in the Engineering\ComplexFunctions class instead
     *
     * @param string $complexNumber the complex number for which you want the tangent
     *
     * @return float|string
     */
    public static function IMTAN($complexNumber)
    {
        return ComplexFunctions::IMTAN($complexNumber);
    }

    /**
     * IMSQRT.
     *
     * Returns the square root of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMSQRT(complexNumber)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the IMSQRT() method in the Engineering\ComplexFunctions class instead
     *
     * @param string $complexNumber the complex number for which you want the square root
     *
     * @return string
     */
    public static function IMSQRT($complexNumber)
    {
        return ComplexFunctions::IMSQRT($complexNumber);
    }

    /**
     * IMLN.
     *
     * Returns the natural logarithm of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMLN(complexNumber)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the IMLN() method in the Engineering\ComplexFunctions class instead
     *
     * @param string $complexNumber the complex number for which you want the natural logarithm
     *
     * @return string
     */
    public static function IMLN($complexNumber)
    {
        return ComplexFunctions::IMLN($complexNumber);
    }

    /**
     * IMLOG10.
     *
     * Returns the common logarithm (base 10) of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMLOG10(complexNumber)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the IMLOG10() method in the Engineering\ComplexFunctions class instead
     *
     * @param string $complexNumber the complex number for which you want the common logarithm
     *
     * @return string
     */
    public static function IMLOG10($complexNumber)
    {
        return ComplexFunctions::IMLOG10($complexNumber);
    }

    /**
     * IMLOG2.
     *
     * Returns the base-2 logarithm of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMLOG2(complexNumber)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the IMLOG2() method in the Engineering\ComplexFunctions class instead
     *
     * @param string $complexNumber the complex number for which you want the base-2 logarithm
     *
     * @return string
     */
    public static function IMLOG2($complexNumber)
    {
        return ComplexFunctions::IMLOG2($complexNumber);
    }

    /**
     * IMEXP.
     *
     * Returns the exponential of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMEXP(complexNumber)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the IMEXP() method in the Engineering\ComplexFunctions class instead
     *
     * @param string $complexNumber the complex number for which you want the exponential
     *
     * @return string
     */
    public static function IMEXP($complexNumber)
    {
        return ComplexFunctions::IMEXP($complexNumber);
    }

    /**
     * IMPOWER.
     *
     * Returns a complex number in x + yi or x + yj text format raised to a power.
     *
     * Excel Function:
     *        IMPOWER(complexNumber,realNumber)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the IMPOWER() method in the Engineering\ComplexFunctions class instead
     *
     * @param string $complexNumber the complex number you want to raise to a power
     * @param float $realNumber the power to which you want to raise the complex number
     *
     * @return string
     */
    public static function IMPOWER($complexNumber, $realNumber)
    {
        return ComplexFunctions::IMPOWER($complexNumber, $realNumber);
    }

    /**
     * IMDIV.
     *
     * Returns the quotient of two complex numbers in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMDIV(complexDividend,complexDivisor)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the IMDIV() method in the Engineering\ComplexOperations class instead
     *
     * @param string $complexDividend the complex numerator or dividend
     * @param string $complexDivisor the complex denominator or divisor
     *
     * @return string
     */
    public static function IMDIV($complexDividend, $complexDivisor)
    {
        return ComplexOperations::IMDIV($complexDividend, $complexDivisor);
    }

    /**
     * IMSUB.
     *
     * Returns the difference of two complex numbers in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMSUB(complexNumber1,complexNumber2)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the IMSUB() method in the Engineering\ComplexOperations class instead
     *
     * @param string $complexNumber1 the complex number from which to subtract complexNumber2
     * @param string $complexNumber2 the complex number to subtract from complexNumber1
     *
     * @return string
     */
    public static function IMSUB($complexNumber1, $complexNumber2)
    {
        return ComplexOperations::IMSUB($complexNumber1, $complexNumber2);
    }

    /**
     * IMSUM.
     *
     * Returns the sum of two or more complex numbers in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMSUM(complexNumber[,complexNumber[,...]])
     *
     * @Deprecated 1.18.0
     *
     * @see Use the IMSUM() method in the Engineering\ComplexOperations class instead
     *
     * @param string ...$complexNumbers Series of complex numbers to add
     *
     * @return string
     */
    public static function IMSUM(...$complexNumbers)
    {
        return ComplexOperations::IMSUM(...$complexNumbers);
    }

    /**
     * IMPRODUCT.
     *
     * Returns the product of two or more complex numbers in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMPRODUCT(complexNumber[,complexNumber[,...]])
     *
     * @Deprecated 1.18.0
     *
     * @see Use the IMPRODUCT() method in the Engineering\ComplexOperations class instead
     *
     * @param string ...$complexNumbers Series of complex numbers to multiply
     *
     * @return string
     */
    public static function IMPRODUCT(...$complexNumbers)
    {
        return ComplexOperations::IMPRODUCT(...$complexNumbers);
    }

    /**
     * DELTA.
     *
     * Tests whether two values are equal. Returns 1 if number1 = number2; returns 0 otherwise.
     * Use this function to filter a set of values. For example, by summing several DELTA
     *     functions you calculate the count of equal pairs. This function is also known as the
     *     Kronecker Delta function.
     *
     *    Excel Function:
     *        DELTA(a[,b])
     *
     * @Deprecated 1.17.0
     *
     * @see Use the DELTA() method in the Engineering\Compare class instead
     *
     * @param float $a the first number
     * @param float $b The second number. If omitted, b is assumed to be zero.
     *
     * @return int|string (string in the event of an error)
     */
    public static function DELTA($a, $b = 0)
    {
        return Engineering\Compare::DELTA($a, $b);
    }

    /**
     * GESTEP.
     *
     *    Excel Function:
     *        GESTEP(number[,step])
     *
     *    Returns 1 if number >= step; returns 0 (zero) otherwise
     *    Use this function to filter a set of values. For example, by summing several GESTEP
     *        functions you calculate the count of values that exceed a threshold.
     *
     * @Deprecated 1.17.0
     *
     * @see Use the GESTEP() method in the Engineering\Compare class instead
     *
     * @param float $number the value to test against step
     * @param float $step The threshold value. If you omit a value for step, GESTEP uses zero.
     *
     * @return int|string (string in the event of an error)
     */
    public static function GESTEP($number, $step = 0)
    {
        return Engineering\Compare::GESTEP($number, $step);
    }

    /**
     * BITAND.
     *
     * Returns the bitwise AND of two integer values.
     *
     * Excel Function:
     *        BITAND(number1, number2)
     *
     * @Deprecated 1.17.0
     *
     * @see Use the BITAND() method in the Engineering\BitWise class instead
     *
     * @param int $number1
     * @param int $number2
     *
     * @return int|string
     */
    public static function BITAND($number1, $number2)
    {
        return Engineering\BitWise::BITAND($number1, $number2);
    }

    /**
     * BITOR.
     *
     * Returns the bitwise OR of two integer values.
     *
     * Excel Function:
     *        BITOR(number1, number2)
     *
     * @Deprecated 1.17.0
     *
     * @see Use the BITOR() method in the Engineering\BitWise class instead
     *
     * @param int $number1
     * @param int $number2
     *
     * @return int|string
     */
    public static function BITOR($number1, $number2)
    {
        return Engineering\BitWise::BITOR($number1, $number2);
    }

    /**
     * BITXOR.
     *
     * Returns the bitwise XOR of two integer values.
     *
     * Excel Function:
     *        BITXOR(number1, number2)
     *
     * @Deprecated 1.17.0
     *
     * @see Use the BITXOR() method in the Engineering\BitWise class instead
     *
     * @param int $number1
     * @param int $number2
     *
     * @return int|string
     */
    public static function BITXOR($number1, $number2)
    {
        return Engineering\BitWise::BITXOR($number1, $number2);
    }

    /**
     * BITLSHIFT.
     *
     * Returns the number value shifted left by shift_amount bits.
     *
     * Excel Function:
     *        BITLSHIFT(number, shift_amount)
     *
     * @Deprecated 1.17.0
     *
     * @see Use the BITLSHIFT() method in the Engineering\BitWise class instead
     *
     * @param int $number
     * @param int $shiftAmount
     *
     * @return int|string
     */
    public static function BITLSHIFT($number, $shiftAmount)
    {
        return Engineering\BitWise::BITLSHIFT($number, $shiftAmount);
    }

    /**
     * BITRSHIFT.
     *
     * Returns the number value shifted right by shift_amount bits.
     *
     * Excel Function:
     *        BITRSHIFT(number, shift_amount)
     *
     * @Deprecated 1.17.0
     *
     * @see Use the BITRSHIFT() method in the Engineering\BitWise class instead
     *
     * @param int $number
     * @param int $shiftAmount
     *
     * @return int|string
     */
    public static function BITRSHIFT($number, $shiftAmount)
    {
        return Engineering\BitWise::BITRSHIFT($number, $shiftAmount);
    }

    /**
     * ERF.
     *
     * Returns the error function integrated between the lower and upper bound arguments.
     *
     *    Note: In Excel 2007 or earlier, if you input a negative value for the upper or lower bound arguments,
     *            the function would return a #NUM! error. However, in Excel 2010, the function algorithm was
     *            improved, so that it can now calculate the function for both positive and negative ranges.
     *            PhpSpreadsheet follows Excel 2010 behaviour, and accepts negative arguments.
     *
     *    Excel Function:
     *        ERF(lower[,upper])
     *
     * @Deprecated 1.17.0
     *
     * @see Use the ERF() method in the Engineering\Erf class instead
     *
     * @param float $lower lower bound for integrating ERF
     * @param float $upper upper bound for integrating ERF.
     *                                If omitted, ERF integrates between zero and lower_limit
     *
     * @return float|string
     */
    public static function ERF($lower, $upper = null)
    {
        return Engineering\Erf::ERF($lower, $upper);
    }

    /**
     * ERFPRECISE.
     *
     * Returns the error function integrated between the lower and upper bound arguments.
     *
     *    Excel Function:
     *        ERF.PRECISE(limit)
     *
     * @Deprecated 1.17.0
     *
     * @see Use the ERFPRECISE() method in the Engineering\Erf class instead
     *
     * @param float $limit bound for integrating ERF
     *
     * @return float|string
     */
    public static function ERFPRECISE($limit)
    {
        return Engineering\Erf::ERFPRECISE($limit);
    }

    /**
     * ERFC.
     *
     *    Returns the complementary ERF function integrated between x and infinity
     *
     *    Note: In Excel 2007 or earlier, if you input a negative value for the lower bound argument,
     *        the function would return a #NUM! error. However, in Excel 2010, the function algorithm was
     *        improved, so that it can now calculate the function for both positive and negative x values.
     *            PhpSpreadsheet follows Excel 2010 behaviour, and accepts nagative arguments.
     *
     *    Excel Function:
     *        ERFC(x)
     *
     * @Deprecated 1.17.0
     *
     * @see Use the ERFC() method in the Engineering\ErfC class instead
     *
     * @param float $x The lower bound for integrating ERFC
     *
     * @return float|string
     */
    public static function ERFC($x)
    {
        return Engineering\ErfC::ERFC($x);
    }

    /**
     *    getConversionGroups
     * Returns a list of the different conversion groups for UOM conversions.
     *
     * @Deprecated 1.16.0
     *
     * @see Use the getConversionCategories() method in the Engineering\ConvertUOM class instead
     *
     * @return array
     */
    public static function getConversionGroups()
    {
        return Engineering\ConvertUOM::getConversionCategories();
    }

    /**
     *    getConversionGroupUnits
     * Returns an array of units of measure, for a specified conversion group, or for all groups.
     *
     * @Deprecated 1.16.0
     *
     * @see Use the getConversionCategoryUnits() method in the ConvertUOM class instead
     *
     * @param null|mixed $category
     *
     * @return array
     */
    public static function getConversionGroupUnits($category = null)
    {
        return Engineering\ConvertUOM::getConversionCategoryUnits($category);
    }

    /**
     * getConversionGroupUnitDetails.
     *
     * @Deprecated 1.16.0
     *
     * @see Use the getConversionCategoryUnitDetails() method in the ConvertUOM class instead
     *
     * @param null|mixed $category
     *
     * @return array
     */
    public static function getConversionGroupUnitDetails($category = null)
    {
        return Engineering\ConvertUOM::getConversionCategoryUnitDetails($category);
    }

    /**
     *    getConversionMultipliers
     * Returns an array of the Multiplier prefixes that can be used with Units of Measure in CONVERTUOM().
     *
     * @Deprecated 1.16.0
     *
     * @see Use the getConversionMultipliers() method in the ConvertUOM class instead
     *
     * @return mixed[]
     */
    public static function getConversionMultipliers()
    {
        return Engineering\ConvertUOM::getConversionMultipliers();
    }

    /**
     *    getBinaryConversionMultipliers.
     *
     * Returns an array of the additional Multiplier prefixes that can be used with Information Units of Measure
     *     in CONVERTUOM().
     *
     * @Deprecated 1.16.0
     *
     * @see Use the getBinaryConversionMultipliers() method in the ConvertUOM class instead
     *
     * @return mixed[]
     */
    public static function getBinaryConversionMultipliers()
    {
        return Engineering\ConvertUOM::getBinaryConversionMultipliers();
    }

    /**
     * CONVERTUOM.
     *
     * Converts a number from one measurement system to another.
     *    For example, CONVERT can translate a table of distances in miles to a table of distances
     * in kilometers.
     *
     *    Excel Function:
     *        CONVERT(value,fromUOM,toUOM)
     *
     * @Deprecated 1.16.0
     *
     * @see Use the CONVERT() method in the ConvertUOM class instead
     *
     * @param float|int $value the value in fromUOM to convert
     * @param string $fromUOM the units for value
     * @param string $toUOM the units for the result
     *
     * @return float|string
     */
    public static function CONVERTUOM($value, $fromUOM, $toUOM)
    {
        return Engineering\ConvertUOM::CONVERT($value, $fromUOM, $toUOM);
    }
}
