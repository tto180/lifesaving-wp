<?php
/**
 * @license MIT
 *
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace LearnDash\Reports\PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

use DateInterval;
use DateTime;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Calculation\Exception;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Calculation\Functions;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Shared\Date as SharedDateHelper;

class Difference
{
    /**
     * DATEDIF.
     *
     * @param mixed $startDate Excel date serial value, PHP date/time stamp, PHP DateTime object
     *                                    or a standard date string
     * @param mixed $endDate Excel date serial value, PHP date/time stamp, PHP DateTime object
     *                                    or a standard date string
     * @param string $unit
     *
     * @return int|string Interval between the dates
     */
    public static function interval($startDate, $endDate, $unit = 'D')
    {
        try {
            $startDate = Helpers::getDateValue($startDate);
            $endDate = Helpers::getDateValue($endDate);
            $difference = self::initialDiff($startDate, $endDate);
            $unit = strtoupper(Functions::flattenSingleValue($unit));
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Execute function
        $PHPStartDateObject = SharedDateHelper::excelToDateTimeObject($startDate);
        $startDays = (int) $PHPStartDateObject->format('j');
        $startMonths = (int) $PHPStartDateObject->format('n');
        $startYears = (int) $PHPStartDateObject->format('Y');

        $PHPEndDateObject = SharedDateHelper::excelToDateTimeObject($endDate);
        $endDays = (int) $PHPEndDateObject->format('j');
        $endMonths = (int) $PHPEndDateObject->format('n');
        $endYears = (int) $PHPEndDateObject->format('Y');

        $PHPDiffDateObject = $PHPEndDateObject->diff($PHPStartDateObject);

        $retVal = false;
        $retVal = self::replaceRetValue($retVal, $unit, 'D') ?? self::datedifD($difference);
        $retVal = self::replaceRetValue($retVal, $unit, 'M') ?? self::datedifM($PHPDiffDateObject);
        $retVal = self::replaceRetValue($retVal, $unit, 'MD') ?? self::datedifMD($startDays, $endDays, $PHPEndDateObject, $PHPDiffDateObject);
        $retVal = self::replaceRetValue($retVal, $unit, 'Y') ?? self::datedifY($PHPDiffDateObject);
        $retVal = self::replaceRetValue($retVal, $unit, 'YD') ?? self::datedifYD($difference, $startYears, $endYears, $PHPStartDateObject, $PHPEndDateObject);
        $retVal = self::replaceRetValue($retVal, $unit, 'YM') ?? self::datedifYM($PHPDiffDateObject);

        return is_bool($retVal) ? Functions::VALUE() : $retVal;
    }

    private static function initialDiff(float $startDate, float $endDate): float
    {
        // Validate parameters
        if ($startDate > $endDate) {
            throw new Exception(Functions::NAN());
        }

        return $endDate - $startDate;
    }

    /**
     * Decide whether it's time to set retVal.
     *
     * @param bool|int $retVal
     *
     * @return null|bool|int
     */
    private static function replaceRetValue($retVal, string $unit, string $compare)
    {
        if ($retVal !== false || $unit !== $compare) {
            return $retVal;
        }

        return null;
    }

    private static function datedifD(float $difference): int
    {
        return (int) $difference;
    }

    private static function datedifM(DateInterval $PHPDiffDateObject): int
    {
        return 12 * (int) $PHPDiffDateObject->format('%y') + (int) $PHPDiffDateObject->format('%m');
    }

    private static function datedifMD(int $startDays, int $endDays, DateTime $PHPEndDateObject, DateInterval $PHPDiffDateObject): int
    {
        if ($endDays < $startDays) {
            $retVal = $endDays;
            $PHPEndDateObject->modify('-' . $endDays . ' days');
            $adjustDays = (int) $PHPEndDateObject->format('j');
            $retVal += ($adjustDays - $startDays);
        } else {
            $retVal = (int) $PHPDiffDateObject->format('%d');
        }

        return $retVal;
    }

    private static function datedifY(DateInterval $PHPDiffDateObject): int
    {
        return (int) $PHPDiffDateObject->format('%y');
    }

    private static function datedifYD(float $difference, int $startYears, int $endYears, DateTime $PHPStartDateObject, DateTime $PHPEndDateObject): int
    {
        $retVal = (int) $difference;
        if ($endYears > $startYears) {
            $isLeapStartYear = $PHPStartDateObject->format('L');
            $wasLeapEndYear = $PHPEndDateObject->format('L');

            // Adjust end year to be as close as possible as start year
            while ($PHPEndDateObject >= $PHPStartDateObject) {
                $PHPEndDateObject->modify('-1 year');
                $endYears = $PHPEndDateObject->format('Y');
            }
            $PHPEndDateObject->modify('+1 year');

            // Get the result
            $retVal = $PHPEndDateObject->diff($PHPStartDateObject)->days;

            // Adjust for leap years cases
            $isLeapEndYear = $PHPEndDateObject->format('L');
            $limit = new DateTime($PHPEndDateObject->format('Y-02-29'));
            if (!$isLeapStartYear && !$wasLeapEndYear && $isLeapEndYear && $PHPEndDateObject >= $limit) {
                --$retVal;
            }
        }

        return (int) $retVal;
    }

    private static function datedifYM(DateInterval $PHPDiffDateObject): int
    {
        return (int) $PHPDiffDateObject->format('%m');
    }
}
