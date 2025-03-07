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

class PageSetup extends BaseParserClass
{
    private $worksheet;

    private $worksheetXml;

    public function __construct(Worksheet $workSheet, ?SimpleXMLElement $worksheetXml = null)
    {
        $this->worksheet = $workSheet;
        $this->worksheetXml = $worksheetXml;
    }

    public function load(array $unparsedLoadedData)
    {
        if (!$this->worksheetXml) {
            return $unparsedLoadedData;
        }

        $this->margins($this->worksheetXml, $this->worksheet);
        $unparsedLoadedData = $this->pageSetup($this->worksheetXml, $this->worksheet, $unparsedLoadedData);
        $this->headerFooter($this->worksheetXml, $this->worksheet);
        $this->pageBreaks($this->worksheetXml, $this->worksheet);

        return $unparsedLoadedData;
    }

    private function margins(SimpleXMLElement $xmlSheet, Worksheet $worksheet): void
    {
        if ($xmlSheet->pageMargins) {
            $docPageMargins = $worksheet->getPageMargins();
            $docPageMargins->setLeft((float) ($xmlSheet->pageMargins['left']));
            $docPageMargins->setRight((float) ($xmlSheet->pageMargins['right']));
            $docPageMargins->setTop((float) ($xmlSheet->pageMargins['top']));
            $docPageMargins->setBottom((float) ($xmlSheet->pageMargins['bottom']));
            $docPageMargins->setHeader((float) ($xmlSheet->pageMargins['header']));
            $docPageMargins->setFooter((float) ($xmlSheet->pageMargins['footer']));
        }
    }

    private function pageSetup(SimpleXMLElement $xmlSheet, Worksheet $worksheet, array $unparsedLoadedData)
    {
        if ($xmlSheet->pageSetup) {
            $docPageSetup = $worksheet->getPageSetup();

            if (isset($xmlSheet->pageSetup['orientation'])) {
                $docPageSetup->setOrientation((string) $xmlSheet->pageSetup['orientation']);
            }
            if (isset($xmlSheet->pageSetup['paperSize'])) {
                $docPageSetup->setPaperSize((int) ($xmlSheet->pageSetup['paperSize']));
            }
            if (isset($xmlSheet->pageSetup['scale'])) {
                $docPageSetup->setScale((int) ($xmlSheet->pageSetup['scale']), false);
            }
            if (isset($xmlSheet->pageSetup['fitToHeight']) && (int) ($xmlSheet->pageSetup['fitToHeight']) >= 0) {
                $docPageSetup->setFitToHeight((int) ($xmlSheet->pageSetup['fitToHeight']), false);
            }
            if (isset($xmlSheet->pageSetup['fitToWidth']) && (int) ($xmlSheet->pageSetup['fitToWidth']) >= 0) {
                $docPageSetup->setFitToWidth((int) ($xmlSheet->pageSetup['fitToWidth']), false);
            }
            if (
                isset($xmlSheet->pageSetup['firstPageNumber'], $xmlSheet->pageSetup['useFirstPageNumber']) &&
                self::boolean((string) $xmlSheet->pageSetup['useFirstPageNumber'])
            ) {
                $docPageSetup->setFirstPageNumber((int) ($xmlSheet->pageSetup['firstPageNumber']));
            }
            if (isset($xmlSheet->pageSetup['pageOrder'])) {
                $docPageSetup->setPageOrder((string) $xmlSheet->pageSetup['pageOrder']);
            }

            $relAttributes = $xmlSheet->pageSetup->attributes(Namespaces::SCHEMA_OFFICE_DOCUMENT);
            if (isset($relAttributes['id'])) {
                $unparsedLoadedData['sheets'][$worksheet->getCodeName()]['pageSetupRelId'] = (string) $relAttributes['id'];
            }
        }

        return $unparsedLoadedData;
    }

    private function headerFooter(SimpleXMLElement $xmlSheet, Worksheet $worksheet): void
    {
        if ($xmlSheet->headerFooter) {
            $docHeaderFooter = $worksheet->getHeaderFooter();

            if (
                isset($xmlSheet->headerFooter['differentOddEven']) &&
                self::boolean((string) $xmlSheet->headerFooter['differentOddEven'])
            ) {
                $docHeaderFooter->setDifferentOddEven(true);
            } else {
                $docHeaderFooter->setDifferentOddEven(false);
            }
            if (
                isset($xmlSheet->headerFooter['differentFirst']) &&
                self::boolean((string) $xmlSheet->headerFooter['differentFirst'])
            ) {
                $docHeaderFooter->setDifferentFirst(true);
            } else {
                $docHeaderFooter->setDifferentFirst(false);
            }
            if (
                isset($xmlSheet->headerFooter['scaleWithDoc']) &&
                !self::boolean((string) $xmlSheet->headerFooter['scaleWithDoc'])
            ) {
                $docHeaderFooter->setScaleWithDocument(false);
            } else {
                $docHeaderFooter->setScaleWithDocument(true);
            }
            if (
                isset($xmlSheet->headerFooter['alignWithMargins']) &&
                !self::boolean((string) $xmlSheet->headerFooter['alignWithMargins'])
            ) {
                $docHeaderFooter->setAlignWithMargins(false);
            } else {
                $docHeaderFooter->setAlignWithMargins(true);
            }

            $docHeaderFooter->setOddHeader((string) $xmlSheet->headerFooter->oddHeader);
            $docHeaderFooter->setOddFooter((string) $xmlSheet->headerFooter->oddFooter);
            $docHeaderFooter->setEvenHeader((string) $xmlSheet->headerFooter->evenHeader);
            $docHeaderFooter->setEvenFooter((string) $xmlSheet->headerFooter->evenFooter);
            $docHeaderFooter->setFirstHeader((string) $xmlSheet->headerFooter->firstHeader);
            $docHeaderFooter->setFirstFooter((string) $xmlSheet->headerFooter->firstFooter);
        }
    }

    private function pageBreaks(SimpleXMLElement $xmlSheet, Worksheet $worksheet): void
    {
        if ($xmlSheet->rowBreaks && $xmlSheet->rowBreaks->brk) {
            $this->rowBreaks($xmlSheet, $worksheet);
        }
        if ($xmlSheet->colBreaks && $xmlSheet->colBreaks->brk) {
            $this->columnBreaks($xmlSheet, $worksheet);
        }
    }

    private function rowBreaks(SimpleXMLElement $xmlSheet, Worksheet $worksheet): void
    {
        foreach ($xmlSheet->rowBreaks->brk as $brk) {
            if ($brk['man']) {
                $worksheet->setBreak("A{$brk['id']}", Worksheet::BREAK_ROW);
            }
        }
    }

    private function columnBreaks(SimpleXMLElement $xmlSheet, Worksheet $worksheet): void
    {
        foreach ($xmlSheet->colBreaks->brk as $brk) {
            if ($brk['man']) {
                $worksheet->setBreak(
                    Coordinate::stringFromColumnIndex(((int) $brk['id']) + 1) . '1',
                    Worksheet::BREAK_COLUMN
                );
            }
        }
    }
}
