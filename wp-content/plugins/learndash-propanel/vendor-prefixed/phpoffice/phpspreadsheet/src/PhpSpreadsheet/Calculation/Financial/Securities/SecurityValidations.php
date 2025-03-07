<?php
/**
 * @license MIT
 *
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace LearnDash\Reports\PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities;

use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Calculation\Exception;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Calculation\Financial\FinancialValidations;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Calculation\Functions;

class SecurityValidations extends FinancialValidations
{
    /**
     * @param mixed $issue
     */
    public static function validateIssueDate($issue): float
    {
        return self::validateDate($issue);
    }

    /**
     * @param mixed $settlement
     * @param mixed $maturity
     */
    public static function validateSecurityPeriod($settlement, $maturity): void
    {
        if ($settlement >= $maturity) {
            throw new Exception(Functions::NAN());
        }
    }

    /**
     * @param mixed $redemption
     */
    public static function validateRedemption($redemption): float
    {
        $redemption = self::validateFloat($redemption);
        if ($redemption <= 0.0) {
            throw new Exception(Functions::NAN());
        }

        return $redemption;
    }
}
