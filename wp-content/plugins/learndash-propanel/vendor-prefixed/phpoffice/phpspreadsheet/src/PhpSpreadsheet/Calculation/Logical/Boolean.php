<?php
/**
 * @license MIT
 *
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace LearnDash\Reports\PhpOffice\PhpSpreadsheet\Calculation\Logical;

class Boolean
{
    /**
     * TRUE.
     *
     * Returns the boolean TRUE.
     *
     * Excel Function:
     *        =TRUE()
     *
     * @return bool True
     */
    public static function true(): bool
    {
        return true;
    }

    /**
     * FALSE.
     *
     * Returns the boolean FALSE.
     *
     * Excel Function:
     *        =FALSE()
     *
     * @return bool False
     */
    public static function false(): bool
    {
        return false;
    }
}
