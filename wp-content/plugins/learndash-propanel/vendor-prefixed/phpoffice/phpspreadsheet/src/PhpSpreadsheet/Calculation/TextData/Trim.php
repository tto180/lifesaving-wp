<?php
/**
 * @license MIT
 *
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace LearnDash\Reports\PhpOffice\PhpSpreadsheet\Calculation\TextData;

class Trim
{
    /**
     * CLEAN.
     *
     * @param mixed $stringValue String Value to check
     *
     * @return null|string
     */
    public static function nonPrintable($stringValue = '')
    {
        $stringValue = Helpers::extractString($stringValue);

        return preg_replace('/[\\x00-\\x1f]/', '', "$stringValue");
    }

    /**
     * TRIM.
     *
     * @param mixed $stringValue String Value to check
     *
     * @return string
     */
    public static function spaces($stringValue = '')
    {
        $stringValue = Helpers::extractString($stringValue);

        return trim(preg_replace('/ +/', ' ', trim("$stringValue", ' ')) ?? '', ' ');
    }
}
