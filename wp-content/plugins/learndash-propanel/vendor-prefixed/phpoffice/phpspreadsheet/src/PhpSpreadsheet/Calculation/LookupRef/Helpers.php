<?php
/**
 * @license MIT
 *
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace LearnDash\Reports\PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Cell\AddressHelper;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Cell\Cell;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\DefinedName;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Helpers
{
    public const CELLADDRESS_USE_A1 = true;

    public const CELLADDRESS_USE_R1C1 = false;

    private static function convertR1C1(string &$cellAddress1, ?string &$cellAddress2, bool $a1): string
    {
        if ($a1 === self::CELLADDRESS_USE_R1C1) {
            $cellAddress1 = AddressHelper::convertToA1($cellAddress1);
            if ($cellAddress2) {
                $cellAddress2 = AddressHelper::convertToA1($cellAddress2);
            }
        }

        return $cellAddress1 . ($cellAddress2 ? ":$cellAddress2" : '');
    }

    private static function adjustSheetTitle(string &$sheetTitle, ?string $value): void
    {
        if ($sheetTitle) {
            $sheetTitle .= '!';
            if (stripos($value ?? '', $sheetTitle) === 0) {
                $sheetTitle = '';
            }
        }
    }

    public static function extractCellAddresses(string $cellAddress, bool $a1, Worksheet $sheet, string $sheetName = ''): array
    {
        $cellAddress1 = $cellAddress;
        $cellAddress2 = null;
        $namedRange = DefinedName::resolveName($cellAddress1, $sheet, $sheetName);
        if ($namedRange !== null) {
            $workSheet = $namedRange->getWorkSheet();
            $sheetTitle = ($workSheet === null) ? '' : $workSheet->getTitle();
            $value = preg_replace('/^=/', '', $namedRange->getValue());
            self::adjustSheetTitle($sheetTitle, $value);
            $cellAddress1 = $sheetTitle . $value;
            $cellAddress = $cellAddress1;
            $a1 = self::CELLADDRESS_USE_A1;
        }
        if (strpos($cellAddress, ':') !== false) {
            [$cellAddress1, $cellAddress2] = explode(':', $cellAddress);
        }
        $cellAddress = self::convertR1C1($cellAddress1, $cellAddress2, $a1);

        return [$cellAddress1, $cellAddress2, $cellAddress];
    }

    public static function extractWorksheet(string $cellAddress, Cell $pCell): array
    {
        $sheetName = '';
        if (strpos($cellAddress, '!') !== false) {
            [$sheetName, $cellAddress] = Worksheet::extractSheetTitle($cellAddress, true);
            $sheetName = trim($sheetName, "'");
        }

        $worksheet = ($sheetName !== '')
            ? $pCell->getWorksheet()->getParent()->getSheetByName($sheetName)
            : $pCell->getWorksheet();

        return [$cellAddress, $worksheet, $sheetName];
    }
}
