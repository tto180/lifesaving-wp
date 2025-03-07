<?php
/**
 * @license MIT
 *
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace LearnDash\Reports\PhpOffice\PhpSpreadsheet\Chart;

use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PlotArea
{
    /**
     * PlotArea Layout.
     *
     * @var Layout
     */
    private $layout;

    /**
     * Plot Series.
     *
     * @var DataSeries[]
     */
    private $plotSeries = [];

    /**
     * Create a new PlotArea.
     *
     * @param DataSeries[] $plotSeries
     */
    public function __construct(?Layout $layout = null, array $plotSeries = [])
    {
        $this->layout = $layout;
        $this->plotSeries = $plotSeries;
    }

    /**
     * Get Layout.
     *
     * @return Layout
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Get Number of Plot Groups.
     */
    public function getPlotGroupCount(): int
    {
        return count($this->plotSeries);
    }

    /**
     * Get Number of Plot Series.
     *
     * @return int
     */
    public function getPlotSeriesCount()
    {
        $seriesCount = 0;
        foreach ($this->plotSeries as $plot) {
            $seriesCount += $plot->getPlotSeriesCount();
        }

        return $seriesCount;
    }

    /**
     * Get Plot Series.
     *
     * @return DataSeries[]
     */
    public function getPlotGroup()
    {
        return $this->plotSeries;
    }

    /**
     * Get Plot Series by Index.
     *
     * @param mixed $index
     *
     * @return DataSeries
     */
    public function getPlotGroupByIndex($index)
    {
        return $this->plotSeries[$index];
    }

    /**
     * Set Plot Series.
     *
     * @param DataSeries[] $plotSeries
     *
     * @return $this
     */
    public function setPlotSeries(array $plotSeries)
    {
        $this->plotSeries = $plotSeries;

        return $this;
    }

    public function refresh(Worksheet $worksheet): void
    {
        foreach ($this->plotSeries as $plotSeries) {
            $plotSeries->refresh($worksheet);
        }
    }
}
