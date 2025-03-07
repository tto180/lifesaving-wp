<?php
/**
 * @license MIT
 *
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace LearnDash\Reports\PhpOffice\PhpSpreadsheet\Worksheet;

use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Helper\Dimension as CssDimension;

class RowDimension extends Dimension
{
    /**
     * Row index.
     *
     * @var int
     */
    private $rowIndex;

    /**
     * Row height (in pt).
     *
     * When this is set to a negative value, the row height should be ignored by IWriter
     *
     * @var float
     */
    private $height = -1;

    /**
     * ZeroHeight for Row?
     *
     * @var bool
     */
    private $zeroHeight = false;

    /**
     * Create a new RowDimension.
     *
     * @param int $pIndex Numeric row index
     */
    public function __construct($pIndex = 0)
    {
        // Initialise values
        $this->rowIndex = $pIndex;

        // set dimension as unformatted by default
        parent::__construct(null);
    }

    /**
     * Get Row Index.
     */
    public function getRowIndex(): int
    {
        return $this->rowIndex;
    }

    /**
     * Set Row Index.
     *
     * @return $this
     */
    public function setRowIndex(int $index)
    {
        $this->rowIndex = $index;

        return $this;
    }

    /**
     * Get Row Height.
     * By default, this will be in points; but this method accepts a unit of measure
     *    argument, and will convert the value to the specified UoM.
     *
     * @return float
     */
    public function getRowHeight(?string $unitOfMeasure = null)
    {
        return ($unitOfMeasure === null || $this->height < 0)
            ? $this->height
            : (new CssDimension($this->height . CssDimension::UOM_POINTS))->toUnit($unitOfMeasure);
    }

    /**
     * Set Row Height.
     *
     * @param float $height in points
     * By default, this will be the passed argument value; but this method accepts a unit of measure
     *    argument, and will convert the passed argument value to points from the specified UoM
     *
     * @return $this
     */
    public function setRowHeight($height, ?string $unitOfMeasure = null)
    {
        $this->height = ($unitOfMeasure === null || $height < 0)
            ? $height
            : (new CssDimension("{$height}{$unitOfMeasure}"))->height();

        return $this;
    }

    /**
     * Get ZeroHeight.
     */
    public function getZeroHeight(): bool
    {
        return $this->zeroHeight;
    }

    /**
     * Set ZeroHeight.
     *
     * @return $this
     */
    public function setZeroHeight(bool $pValue)
    {
        $this->zeroHeight = $pValue;

        return $this;
    }
}
