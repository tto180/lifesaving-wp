<?php
/**
 * @license MIT
 *
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace LearnDash\Reports\PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Calculation\Functions;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Cell\Cell;

class Hyperlink
{
    /**
     * HYPERLINK.
     *
     * Excel Function:
     *        =HYPERLINK(linkURL, [displayName])
     *
     * @param mixed $linkURL Expect string. Value to check, is also the value returned when no error
     * @param mixed $displayName Expect string. Value to return when testValue is an error condition
     * @param Cell $pCell The cell to set the hyperlink in
     *
     * @return mixed The value of $displayName (or $linkURL if $displayName was blank)
     */
    public static function set($linkURL = '', $displayName = null, ?Cell $pCell = null)
    {
        $linkURL = ($linkURL === null) ? '' : Functions::flattenSingleValue($linkURL);
        $displayName = ($displayName === null) ? '' : Functions::flattenSingleValue($displayName);

        if ((!is_object($pCell)) || (trim($linkURL) == '')) {
            return Functions::REF();
        }

        if ((is_object($displayName)) || trim($displayName) == '') {
            $displayName = $linkURL;
        }

        $pCell->getHyperlink()->setUrl($linkURL);
        $pCell->getHyperlink()->setTooltip($displayName);

        return $displayName;
    }
}
