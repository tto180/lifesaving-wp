<?php
/**
 * @license MIT
 *
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace LearnDash\Reports\PhpOffice\PhpSpreadsheet\Worksheet;

use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

class SheetView
{
    // Sheet View types
    const SHEETVIEW_NORMAL = 'normal';
    const SHEETVIEW_PAGE_LAYOUT = 'pageLayout';
    const SHEETVIEW_PAGE_BREAK_PREVIEW = 'pageBreakPreview';

    private static $sheetViewTypes = [
        self::SHEETVIEW_NORMAL,
        self::SHEETVIEW_PAGE_LAYOUT,
        self::SHEETVIEW_PAGE_BREAK_PREVIEW,
    ];

    /**
     * ZoomScale.
     *
     * Valid values range from 10 to 400.
     *
     * @var int
     */
    private $zoomScale = 100;

    /**
     * ZoomScaleNormal.
     *
     * Valid values range from 10 to 400.
     *
     * @var int
     */
    private $zoomScaleNormal = 100;

    /**
     * ShowZeros.
     *
     * If true, "null" values from a calculation will be shown as "0". This is the default Excel behaviour and can be changed
     * with the advanced worksheet option "Show a zero in cells that have zero value"
     *
     * @var bool
     */
    private $showZeros = true;

    /**
     * View.
     *
     * Valid values range from 10 to 400.
     *
     * @var string
     */
    private $sheetviewType = self::SHEETVIEW_NORMAL;

    /**
     * Create a new SheetView.
     */
    public function __construct()
    {
    }

    /**
     * Get ZoomScale.
     *
     * @return int
     */
    public function getZoomScale()
    {
        return $this->zoomScale;
    }

    /**
     * Set ZoomScale.
     * Valid values range from 10 to 400.
     *
     * @param int $pValue
     *
     * @return $this
     */
    public function setZoomScale($pValue)
    {
        // Microsoft Office Excel 2007 only allows setting a scale between 10 and 400 via the user interface,
        // but it is apparently still able to handle any scale >= 1
        if (($pValue >= 1) || $pValue === null) {
            $this->zoomScale = $pValue;
        } else {
            throw new PhpSpreadsheetException('Scale must be greater than or equal to 1.');
        }

        return $this;
    }

    /**
     * Get ZoomScaleNormal.
     *
     * @return int
     */
    public function getZoomScaleNormal()
    {
        return $this->zoomScaleNormal;
    }

    /**
     * Set ZoomScale.
     * Valid values range from 10 to 400.
     *
     * @param int $pValue
     *
     * @return $this
     */
    public function setZoomScaleNormal($pValue)
    {
        if (($pValue >= 1) || $pValue === null) {
            $this->zoomScaleNormal = $pValue;
        } else {
            throw new PhpSpreadsheetException('Scale must be greater than or equal to 1.');
        }

        return $this;
    }

    /**
     * Set ShowZeroes setting.
     *
     * @param bool $pValue
     */
    public function setShowZeros($pValue): void
    {
        $this->showZeros = $pValue;
    }

    /**
     * @return bool
     */
    public function getShowZeros()
    {
        return $this->showZeros;
    }

    /**
     * Get View.
     *
     * @return string
     */
    public function getView()
    {
        return $this->sheetviewType;
    }

    /**
     * Set View.
     *
     * Valid values are
     *        'normal'            self::SHEETVIEW_NORMAL
     *        'pageLayout'        self::SHEETVIEW_PAGE_LAYOUT
     *        'pageBreakPreview'  self::SHEETVIEW_PAGE_BREAK_PREVIEW
     *
     * @param string $pValue
     *
     * @return $this
     */
    public function setView($pValue)
    {
        // MS Excel 2007 allows setting the view to 'normal', 'pageLayout' or 'pageBreakPreview' via the user interface
        if ($pValue === null) {
            $pValue = self::SHEETVIEW_NORMAL;
        }
        if (in_array($pValue, self::$sheetViewTypes)) {
            $this->sheetviewType = $pValue;
        } else {
            throw new PhpSpreadsheetException('Invalid sheetview layout type.');
        }

        return $this;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }
}
