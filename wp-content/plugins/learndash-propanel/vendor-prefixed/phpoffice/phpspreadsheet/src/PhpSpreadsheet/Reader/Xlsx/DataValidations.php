<?php
/**
 * @license MIT
 *
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace LearnDash\Reports\PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SimpleXMLElement;

class DataValidations
{
    private $worksheet;

    private $worksheetXml;

    public function __construct(Worksheet $workSheet, SimpleXMLElement $worksheetXml)
    {
        $this->worksheet = $workSheet;
        $this->worksheetXml = $worksheetXml;
    }

    public function load(): void
    {
        foreach ($this->worksheetXml->dataValidations->dataValidation as $dataValidation) {
            // Uppercase coordinate
            $range = strtoupper($dataValidation['sqref']);
            $rangeSet = explode(' ', $range);
            foreach ($rangeSet as $range) {
                $stRange = $this->worksheet->shrinkRangeToFit($range);

                // Extract all cell references in $range
                foreach (Coordinate::extractAllCellReferencesInRange($stRange) as $reference) {
                    // Create validation
                    $docValidation = $this->worksheet->getCell($reference)->getDataValidation();
                    $docValidation->setType((string) $dataValidation['type']);
                    $docValidation->setErrorStyle((string) $dataValidation['errorStyle']);
                    $docValidation->setOperator((string) $dataValidation['operator']);
                    $docValidation->setAllowBlank(filter_var($dataValidation['allowBlank'], FILTER_VALIDATE_BOOLEAN));
                    // showDropDown is inverted (works as hideDropDown if true)
                    $docValidation->setShowDropDown(!filter_var($dataValidation['showDropDown'], FILTER_VALIDATE_BOOLEAN));
                    $docValidation->setShowInputMessage(filter_var($dataValidation['showInputMessage'], FILTER_VALIDATE_BOOLEAN));
                    $docValidation->setShowErrorMessage(filter_var($dataValidation['showErrorMessage'], FILTER_VALIDATE_BOOLEAN));
                    $docValidation->setErrorTitle((string) $dataValidation['errorTitle']);
                    $docValidation->setError((string) $dataValidation['error']);
                    $docValidation->setPromptTitle((string) $dataValidation['promptTitle']);
                    $docValidation->setPrompt((string) $dataValidation['prompt']);
                    $docValidation->setFormula1((string) $dataValidation->formula1);
                    $docValidation->setFormula2((string) $dataValidation->formula2);
                    $docValidation->setSqref($range);
                }
            }
        }
    }
}
