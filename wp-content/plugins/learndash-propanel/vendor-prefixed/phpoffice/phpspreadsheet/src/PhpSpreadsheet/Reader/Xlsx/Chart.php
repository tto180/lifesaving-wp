<?php
/**
 * @license MIT
 *
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace LearnDash\Reports\PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Calculation\Functions;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Chart\Layout;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Chart\Legend;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Chart\Title;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\RichText\RichText;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Style\Color;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Style\Font;
use SimpleXMLElement;

class Chart
{
    /**
     * @param string $name
     * @param string $format
     *
     * @return null|bool|float|int|string
     */
    private static function getAttribute(SimpleXMLElement $component, $name, $format)
    {
        $attributes = $component->attributes();
        if (isset($attributes[$name])) {
            if ($format == 'string') {
                return (string) $attributes[$name];
            } elseif ($format == 'integer') {
                return (int) $attributes[$name];
            } elseif ($format == 'boolean') {
                $value = (string) $attributes[$name];

                return $value === 'true' || $value === '1';
            }

            return (float) $attributes[$name];
        }

        return null;
    }

    private static function readColor($color, $background = false)
    {
        if (isset($color['rgb'])) {
            return (string) $color['rgb'];
        } elseif (isset($color['indexed'])) {
            return Color::indexedColor($color['indexed'] - 7, $background)->getARGB();
        }
    }

    /**
     * @param string $chartName
     *
     * @return \LearnDash\Reports\PhpOffice\PhpSpreadsheet\Chart\Chart
     */
    public static function readChart(SimpleXMLElement $chartElements, $chartName)
    {
        $namespacesChartMeta = $chartElements->getNamespaces(true);
        $chartElementsC = $chartElements->children($namespacesChartMeta['c']);

        $XaxisLabel = $YaxisLabel = $legend = $title = null;
        $dispBlanksAs = $plotVisOnly = null;
        $plotArea = null;
        foreach ($chartElementsC as $chartElementKey => $chartElement) {
            switch ($chartElementKey) {
                case 'chart':
                    foreach ($chartElement as $chartDetailsKey => $chartDetails) {
                        $chartDetailsC = $chartDetails->children($namespacesChartMeta['c']);
                        switch ($chartDetailsKey) {
                            case 'plotArea':
                                $plotAreaLayout = $XaxisLable = $YaxisLable = null;
                                $plotSeries = $plotAttributes = [];
                                foreach ($chartDetails as $chartDetailKey => $chartDetail) {
                                    switch ($chartDetailKey) {
                                        case 'layout':
                                            $plotAreaLayout = self::chartLayoutDetails($chartDetail, $namespacesChartMeta);

                                            break;
                                        case 'catAx':
                                            if (isset($chartDetail->title)) {
                                                $XaxisLabel = self::chartTitle($chartDetail->title->children($namespacesChartMeta['c']), $namespacesChartMeta);
                                            }

                                            break;
                                        case 'dateAx':
                                            if (isset($chartDetail->title)) {
                                                $XaxisLabel = self::chartTitle($chartDetail->title->children($namespacesChartMeta['c']), $namespacesChartMeta);
                                            }

                                            break;
                                        case 'valAx':
                                            if (isset($chartDetail->title, $chartDetail->axPos)) {
                                                $axisLabel = self::chartTitle($chartDetail->title->children($namespacesChartMeta['c']), $namespacesChartMeta);
                                                $axPos = self::getAttribute($chartDetail->axPos, 'val', 'string');

                                                switch ($axPos) {
                                                    case 't':
                                                    case 'b':
                                                        $XaxisLabel = $axisLabel;

                                                        break;
                                                    case 'r':
                                                    case 'l':
                                                        $YaxisLabel = $axisLabel;

                                                        break;
                                                }
                                            }

                                            break;
                                        case 'barChart':
                                        case 'bar3DChart':
                                            $barDirection = self::getAttribute($chartDetail->barDir, 'val', 'string');
                                            $plotSer = self::chartDataSeries($chartDetail, $namespacesChartMeta, $chartDetailKey);
                                            $plotSer->setPlotDirection($barDirection);
                                            $plotSeries[] = $plotSer;
                                            $plotAttributes = self::readChartAttributes($chartDetail);

                                            break;
                                        case 'lineChart':
                                        case 'line3DChart':
                                            $plotSeries[] = self::chartDataSeries($chartDetail, $namespacesChartMeta, $chartDetailKey);
                                            $plotAttributes = self::readChartAttributes($chartDetail);

                                            break;
                                        case 'areaChart':
                                        case 'area3DChart':
                                            $plotSeries[] = self::chartDataSeries($chartDetail, $namespacesChartMeta, $chartDetailKey);
                                            $plotAttributes = self::readChartAttributes($chartDetail);

                                            break;
                                        case 'doughnutChart':
                                        case 'pieChart':
                                        case 'pie3DChart':
                                            $explosion = isset($chartDetail->ser->explosion);
                                            $plotSer = self::chartDataSeries($chartDetail, $namespacesChartMeta, $chartDetailKey);
                                            $plotSer->setPlotStyle($explosion);
                                            $plotSeries[] = $plotSer;
                                            $plotAttributes = self::readChartAttributes($chartDetail);

                                            break;
                                        case 'scatterChart':
                                            $scatterStyle = self::getAttribute($chartDetail->scatterStyle, 'val', 'string');
                                            $plotSer = self::chartDataSeries($chartDetail, $namespacesChartMeta, $chartDetailKey);
                                            $plotSer->setPlotStyle($scatterStyle);
                                            $plotSeries[] = $plotSer;
                                            $plotAttributes = self::readChartAttributes($chartDetail);

                                            break;
                                        case 'bubbleChart':
                                            $bubbleScale = self::getAttribute($chartDetail->bubbleScale, 'val', 'integer');
                                            $plotSer = self::chartDataSeries($chartDetail, $namespacesChartMeta, $chartDetailKey);
                                            $plotSer->setPlotStyle($bubbleScale);
                                            $plotSeries[] = $plotSer;
                                            $plotAttributes = self::readChartAttributes($chartDetail);

                                            break;
                                        case 'radarChart':
                                            $radarStyle = self::getAttribute($chartDetail->radarStyle, 'val', 'string');
                                            $plotSer = self::chartDataSeries($chartDetail, $namespacesChartMeta, $chartDetailKey);
                                            $plotSer->setPlotStyle($radarStyle);
                                            $plotSeries[] = $plotSer;
                                            $plotAttributes = self::readChartAttributes($chartDetail);

                                            break;
                                        case 'surfaceChart':
                                        case 'surface3DChart':
                                            $wireFrame = self::getAttribute($chartDetail->wireframe, 'val', 'boolean');
                                            $plotSer = self::chartDataSeries($chartDetail, $namespacesChartMeta, $chartDetailKey);
                                            $plotSer->setPlotStyle($wireFrame);
                                            $plotSeries[] = $plotSer;
                                            $plotAttributes = self::readChartAttributes($chartDetail);

                                            break;
                                        case 'stockChart':
                                            $plotSeries[] = self::chartDataSeries($chartDetail, $namespacesChartMeta, $chartDetailKey);
                                            $plotAttributes = self::readChartAttributes($plotAreaLayout);

                                            break;
                                    }
                                }
                                if ($plotAreaLayout == null) {
                                    $plotAreaLayout = new Layout();
                                }
                                $plotArea = new PlotArea($plotAreaLayout, $plotSeries);
                                self::setChartAttributes($plotAreaLayout, $plotAttributes);

                                break;
                            case 'plotVisOnly':
                                $plotVisOnly = self::getAttribute($chartDetails, 'val', 'string');

                                break;
                            case 'dispBlanksAs':
                                $dispBlanksAs = self::getAttribute($chartDetails, 'val', 'string');

                                break;
                            case 'title':
                                $title = self::chartTitle($chartDetails, $namespacesChartMeta);

                                break;
                            case 'legend':
                                $legendPos = 'r';
                                $legendLayout = null;
                                $legendOverlay = false;
                                foreach ($chartDetails as $chartDetailKey => $chartDetail) {
                                    switch ($chartDetailKey) {
                                        case 'legendPos':
                                            $legendPos = self::getAttribute($chartDetail, 'val', 'string');

                                            break;
                                        case 'overlay':
                                            $legendOverlay = self::getAttribute($chartDetail, 'val', 'boolean');

                                            break;
                                        case 'layout':
                                            $legendLayout = self::chartLayoutDetails($chartDetail, $namespacesChartMeta);

                                            break;
                                    }
                                }
                                $legend = new Legend($legendPos, $legendLayout, $legendOverlay);

                                break;
                        }
                    }
            }
        }
        $chart = new \LearnDash\Reports\PhpOffice\PhpSpreadsheet\Chart\Chart($chartName, $title, $legend, $plotArea, $plotVisOnly, $dispBlanksAs, $XaxisLabel, $YaxisLabel);

        return $chart;
    }

    private static function chartTitle(SimpleXMLElement $titleDetails, array $namespacesChartMeta)
    {
        $caption = [];
        $titleLayout = null;
        foreach ($titleDetails as $titleDetailKey => $chartDetail) {
            switch ($titleDetailKey) {
                case 'tx':
                    $titleDetails = $chartDetail->rich->children($namespacesChartMeta['a']);
                    foreach ($titleDetails as $titleKey => $titleDetail) {
                        switch ($titleKey) {
                            case 'p':
                                $titleDetailPart = $titleDetail->children($namespacesChartMeta['a']);
                                $caption[] = self::parseRichText($titleDetailPart);
                        }
                    }

                    break;
                case 'layout':
                    $titleLayout = self::chartLayoutDetails($chartDetail, $namespacesChartMeta);

                    break;
            }
        }

        return new Title($caption, $titleLayout);
    }

    private static function chartLayoutDetails($chartDetail, $namespacesChartMeta)
    {
        if (!isset($chartDetail->manualLayout)) {
            return null;
        }
        $details = $chartDetail->manualLayout->children($namespacesChartMeta['c']);
        if ($details === null) {
            return null;
        }
        $layout = [];
        foreach ($details as $detailKey => $detail) {
            $layout[$detailKey] = self::getAttribute($detail, 'val', 'string');
        }

        return new Layout($layout);
    }

    private static function chartDataSeries($chartDetail, $namespacesChartMeta, $plotType)
    {
        $multiSeriesType = null;
        $smoothLine = false;
        $seriesLabel = $seriesCategory = $seriesValues = $plotOrder = [];

        $seriesDetailSet = $chartDetail->children($namespacesChartMeta['c']);
        foreach ($seriesDetailSet as $seriesDetailKey => $seriesDetails) {
            switch ($seriesDetailKey) {
                case 'grouping':
                    $multiSeriesType = self::getAttribute($chartDetail->grouping, 'val', 'string');

                    break;
                case 'ser':
                    $marker = null;
                    $seriesIndex = '';
                    foreach ($seriesDetails as $seriesKey => $seriesDetail) {
                        switch ($seriesKey) {
                            case 'idx':
                                $seriesIndex = self::getAttribute($seriesDetail, 'val', 'integer');

                                break;
                            case 'order':
                                $seriesOrder = self::getAttribute($seriesDetail, 'val', 'integer');
                                $plotOrder[$seriesIndex] = $seriesOrder;

                                break;
                            case 'tx':
                                $seriesLabel[$seriesIndex] = self::chartDataSeriesValueSet($seriesDetail, $namespacesChartMeta);

                                break;
                            case 'marker':
                                $marker = self::getAttribute($seriesDetail->symbol, 'val', 'string');

                                break;
                            case 'smooth':
                                $smoothLine = self::getAttribute($seriesDetail, 'val', 'boolean');

                                break;
                            case 'cat':
                                $seriesCategory[$seriesIndex] = self::chartDataSeriesValueSet($seriesDetail, $namespacesChartMeta);

                                break;
                            case 'val':
                                $seriesValues[$seriesIndex] = self::chartDataSeriesValueSet($seriesDetail, $namespacesChartMeta, $marker);

                                break;
                            case 'xVal':
                                $seriesCategory[$seriesIndex] = self::chartDataSeriesValueSet($seriesDetail, $namespacesChartMeta, $marker);

                                break;
                            case 'yVal':
                                $seriesValues[$seriesIndex] = self::chartDataSeriesValueSet($seriesDetail, $namespacesChartMeta, $marker);

                                break;
                        }
                    }
            }
        }

        return new DataSeries($plotType, $multiSeriesType, $plotOrder, $seriesLabel, $seriesCategory, $seriesValues, $smoothLine);
    }

    private static function chartDataSeriesValueSet($seriesDetail, $namespacesChartMeta, $marker = null)
    {
        if (isset($seriesDetail->strRef)) {
            $seriesSource = (string) $seriesDetail->strRef->f;
            $seriesValues = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $seriesSource, null, null, null, $marker);

            if (isset($seriesDetail->strRef->strCache)) {
                $seriesData = self::chartDataSeriesValues($seriesDetail->strRef->strCache->children($namespacesChartMeta['c']), 's');
                $seriesValues
                    ->setFormatCode($seriesData['formatCode'])
                    ->setDataValues($seriesData['dataValues']);
            }

            return $seriesValues;
        } elseif (isset($seriesDetail->numRef)) {
            $seriesSource = (string) $seriesDetail->numRef->f;
            $seriesValues = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $seriesSource, null, null, null, $marker);
            if (isset($seriesDetail->numRef->numCache)) {
                $seriesData = self::chartDataSeriesValues($seriesDetail->numRef->numCache->children($namespacesChartMeta['c']));
                $seriesValues
                    ->setFormatCode($seriesData['formatCode'])
                    ->setDataValues($seriesData['dataValues']);
            }

            return $seriesValues;
        } elseif (isset($seriesDetail->multiLvlStrRef)) {
            $seriesSource = (string) $seriesDetail->multiLvlStrRef->f;
            $seriesValues = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $seriesSource, null, null, null, $marker);

            if (isset($seriesDetail->multiLvlStrRef->multiLvlStrCache)) {
                $seriesData = self::chartDataSeriesValuesMultiLevel($seriesDetail->multiLvlStrRef->multiLvlStrCache->children($namespacesChartMeta['c']), 's');
                $seriesValues
                    ->setFormatCode($seriesData['formatCode'])
                    ->setDataValues($seriesData['dataValues']);
            }

            return $seriesValues;
        } elseif (isset($seriesDetail->multiLvlNumRef)) {
            $seriesSource = (string) $seriesDetail->multiLvlNumRef->f;
            $seriesValues = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $seriesSource, null, null, null, $marker);

            if (isset($seriesDetail->multiLvlNumRef->multiLvlNumCache)) {
                $seriesData = self::chartDataSeriesValuesMultiLevel($seriesDetail->multiLvlNumRef->multiLvlNumCache->children($namespacesChartMeta['c']), 's');
                $seriesValues
                    ->setFormatCode($seriesData['formatCode'])
                    ->setDataValues($seriesData['dataValues']);
            }

            return $seriesValues;
        }

        return null;
    }

    private static function chartDataSeriesValues($seriesValueSet, $dataType = 'n')
    {
        $seriesVal = [];
        $formatCode = '';
        $pointCount = 0;

        foreach ($seriesValueSet as $seriesValueIdx => $seriesValue) {
            switch ($seriesValueIdx) {
                case 'ptCount':
                    $pointCount = self::getAttribute($seriesValue, 'val', 'integer');

                    break;
                case 'formatCode':
                    $formatCode = (string) $seriesValue;

                    break;
                case 'pt':
                    $pointVal = self::getAttribute($seriesValue, 'idx', 'integer');
                    if ($dataType == 's') {
                        $seriesVal[$pointVal] = (string) $seriesValue->v;
                    } elseif ($seriesValue->v === Functions::NA()) {
                        $seriesVal[$pointVal] = null;
                    } else {
                        $seriesVal[$pointVal] = (float) $seriesValue->v;
                    }

                    break;
            }
        }

        return [
            'formatCode' => $formatCode,
            'pointCount' => $pointCount,
            'dataValues' => $seriesVal,
        ];
    }

    private static function chartDataSeriesValuesMultiLevel($seriesValueSet, $dataType = 'n')
    {
        $seriesVal = [];
        $formatCode = '';
        $pointCount = 0;

        foreach ($seriesValueSet->lvl as $seriesLevelIdx => $seriesLevel) {
            foreach ($seriesLevel as $seriesValueIdx => $seriesValue) {
                switch ($seriesValueIdx) {
                    case 'ptCount':
                        $pointCount = self::getAttribute($seriesValue, 'val', 'integer');

                        break;
                    case 'formatCode':
                        $formatCode = (string) $seriesValue;

                        break;
                    case 'pt':
                        $pointVal = self::getAttribute($seriesValue, 'idx', 'integer');
                        if ($dataType == 's') {
                            $seriesVal[$pointVal][] = (string) $seriesValue->v;
                        } elseif ($seriesValue->v === Functions::NA()) {
                            $seriesVal[$pointVal] = null;
                        } else {
                            $seriesVal[$pointVal][] = (float) $seriesValue->v;
                        }

                        break;
                }
            }
        }

        return [
            'formatCode' => $formatCode,
            'pointCount' => $pointCount,
            'dataValues' => $seriesVal,
        ];
    }

    private static function parseRichText(SimpleXMLElement $titleDetailPart)
    {
        $value = new RichText();
        $objText = null;
        foreach ($titleDetailPart as $titleDetailElementKey => $titleDetailElement) {
            if (isset($titleDetailElement->t)) {
                $objText = $value->createTextRun((string) $titleDetailElement->t);
            }
            if (isset($titleDetailElement->rPr)) {
                if (isset($titleDetailElement->rPr->rFont['val'])) {
                    $objText->getFont()->setName((string) $titleDetailElement->rPr->rFont['val']);
                }

                $fontSize = (self::getAttribute($titleDetailElement->rPr, 'sz', 'integer'));
                if (is_int($fontSize)) {
                    $objText->getFont()->setSize(floor($fontSize / 100));
                }

                $fontColor = (self::getAttribute($titleDetailElement->rPr, 'color', 'string'));
                if ($fontColor !== null) {
                    $objText->getFont()->setColor(new Color(self::readColor($fontColor)));
                }

                $bold = self::getAttribute($titleDetailElement->rPr, 'b', 'boolean');
                if ($bold !== null) {
                    $objText->getFont()->setBold($bold);
                }

                $italic = self::getAttribute($titleDetailElement->rPr, 'i', 'boolean');
                if ($italic !== null) {
                    $objText->getFont()->setItalic($italic);
                }

                $baseline = self::getAttribute($titleDetailElement->rPr, 'baseline', 'integer');
                if ($baseline !== null) {
                    if ($baseline > 0) {
                        $objText->getFont()->setSuperscript(true);
                    } elseif ($baseline < 0) {
                        $objText->getFont()->setSubscript(true);
                    }
                }

                $underscore = (self::getAttribute($titleDetailElement->rPr, 'u', 'string'));
                if ($underscore !== null) {
                    if ($underscore == 'sng') {
                        $objText->getFont()->setUnderline(Font::UNDERLINE_SINGLE);
                    } elseif ($underscore == 'dbl') {
                        $objText->getFont()->setUnderline(Font::UNDERLINE_DOUBLE);
                    } else {
                        $objText->getFont()->setUnderline(Font::UNDERLINE_NONE);
                    }
                }

                $strikethrough = (self::getAttribute($titleDetailElement->rPr, 's', 'string'));
                if ($strikethrough !== null) {
                    if ($strikethrough == 'noStrike') {
                        $objText->getFont()->setStrikethrough(false);
                    } else {
                        $objText->getFont()->setStrikethrough(true);
                    }
                }
            }
        }

        return $value;
    }

    private static function readChartAttributes($chartDetail)
    {
        $plotAttributes = [];
        if (isset($chartDetail->dLbls)) {
            if (isset($chartDetail->dLbls->showLegendKey)) {
                $plotAttributes['showLegendKey'] = self::getAttribute($chartDetail->dLbls->showLegendKey, 'val', 'string');
            }
            if (isset($chartDetail->dLbls->showVal)) {
                $plotAttributes['showVal'] = self::getAttribute($chartDetail->dLbls->showVal, 'val', 'string');
            }
            if (isset($chartDetail->dLbls->showCatName)) {
                $plotAttributes['showCatName'] = self::getAttribute($chartDetail->dLbls->showCatName, 'val', 'string');
            }
            if (isset($chartDetail->dLbls->showSerName)) {
                $plotAttributes['showSerName'] = self::getAttribute($chartDetail->dLbls->showSerName, 'val', 'string');
            }
            if (isset($chartDetail->dLbls->showPercent)) {
                $plotAttributes['showPercent'] = self::getAttribute($chartDetail->dLbls->showPercent, 'val', 'string');
            }
            if (isset($chartDetail->dLbls->showBubbleSize)) {
                $plotAttributes['showBubbleSize'] = self::getAttribute($chartDetail->dLbls->showBubbleSize, 'val', 'string');
            }
            if (isset($chartDetail->dLbls->showLeaderLines)) {
                $plotAttributes['showLeaderLines'] = self::getAttribute($chartDetail->dLbls->showLeaderLines, 'val', 'string');
            }
        }

        return $plotAttributes;
    }

    /**
     * @param mixed $plotAttributes
     */
    private static function setChartAttributes(Layout $plotArea, $plotAttributes): void
    {
        foreach ($plotAttributes as $plotAttributeKey => $plotAttributeValue) {
            switch ($plotAttributeKey) {
                case 'showLegendKey':
                    $plotArea->setShowLegendKey($plotAttributeValue);

                    break;
                case 'showVal':
                    $plotArea->setShowVal($plotAttributeValue);

                    break;
                case 'showCatName':
                    $plotArea->setShowCatName($plotAttributeValue);

                    break;
                case 'showSerName':
                    $plotArea->setShowSerName($plotAttributeValue);

                    break;
                case 'showPercent':
                    $plotArea->setShowPercent($plotAttributeValue);

                    break;
                case 'showBubbleSize':
                    $plotArea->setShowBubbleSize($plotAttributeValue);

                    break;
                case 'showLeaderLines':
                    $plotArea->setShowLeaderLines($plotAttributeValue);

                    break;
            }
        }
    }
}
