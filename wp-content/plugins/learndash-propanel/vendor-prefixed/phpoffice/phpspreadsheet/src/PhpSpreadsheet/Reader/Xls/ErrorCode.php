<?php
/**
 * @license MIT
 *
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace LearnDash\Reports\PhpOffice\PhpSpreadsheet\Reader\Xls;

class ErrorCode
{
    protected static $map = [
        0x00 => '#NULL!',
        0x07 => '#DIV/0!',
        0x0F => '#VALUE!',
        0x17 => '#REF!',
        0x1D => '#NAME?',
        0x24 => '#NUM!',
        0x2A => '#N/A',
    ];

    /**
     * Map error code, e.g. '#N/A'.
     *
     * @param int $code
     *
     * @return bool|string
     */
    public static function lookup($code)
    {
        if (isset(self::$map[$code])) {
            return self::$map[$code];
        }

        return false;
    }
}
