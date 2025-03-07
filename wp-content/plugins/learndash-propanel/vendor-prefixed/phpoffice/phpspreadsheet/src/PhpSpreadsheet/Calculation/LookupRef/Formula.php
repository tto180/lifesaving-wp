<?php
/**
 * @license MIT
 *
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace LearnDash\Reports\PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Calculation\Functions;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Cell\Cell;

class Formula
{
    /**
     * FORMULATEXT.
     *
     * @param mixed $cellReference The cell to check
     * @param Cell $pCell The current cell (containing this formula)
     *
     * @return string
     */
    public static function text($cellReference = '', ?Cell $pCell = null)
    {
        if ($pCell === null) {
            return Functions::REF();
        }

        preg_match('/^' . Calculation::CALCULATION_REGEXP_CELLREF . '$/i', $cellReference, $matches);

        $cellReference = $matches[6] . $matches[7];
        $worksheetName = trim($matches[3], "'");
        $worksheet = (!empty($worksheetName))
            ? $pCell->getWorksheet()->getParent()->getSheetByName($worksheetName)
            : $pCell->getWorksheet();

        if (
            $worksheet === null ||
            !$worksheet->cellExists($cellReference) ||
            !$worksheet->getCell($cellReference)->isFormula()
        ) {
            return Functions::NA();
        }

        return $worksheet->getCell($cellReference)->getValue();
    }
}
