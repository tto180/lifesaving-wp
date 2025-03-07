<?php
/**
 * @license MIT
 *
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace LearnDash\Reports\PhpOffice\PhpSpreadsheet\Reader\Xml\Style;

use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Style\Font as FontUnderline;
use SimpleXMLElement;

class Font extends StyleBase
{
    protected const UNDERLINE_STYLES = [
        FontUnderline::UNDERLINE_NONE,
        FontUnderline::UNDERLINE_DOUBLE,
        FontUnderline::UNDERLINE_DOUBLEACCOUNTING,
        FontUnderline::UNDERLINE_SINGLE,
        FontUnderline::UNDERLINE_SINGLEACCOUNTING,
    ];

    protected function parseUnderline(array $style, string $styleAttributeValue): array
    {
        if (self::identifyFixedStyleValue(self::UNDERLINE_STYLES, $styleAttributeValue)) {
            $style['font']['underline'] = $styleAttributeValue;
        }

        return $style;
    }

    protected function parseVerticalAlign(array $style, string $styleAttributeValue): array
    {
        if ($styleAttributeValue == 'Superscript') {
            $style['font']['superscript'] = true;
        }
        if ($styleAttributeValue == 'Subscript') {
            $style['font']['subscript'] = true;
        }

        return $style;
    }

    public function parseStyle(SimpleXMLElement $styleAttributes): array
    {
        $style = [];

        foreach ($styleAttributes as $styleAttributeKey => $styleAttributeValue) {
            $styleAttributeValue = (string) $styleAttributeValue;
            switch ($styleAttributeKey) {
                case 'FontName':
                    $style['font']['name'] = $styleAttributeValue;

                    break;
                case 'Size':
                    $style['font']['size'] = $styleAttributeValue;

                    break;
                case 'Color':
                    $style['font']['color']['rgb'] = substr($styleAttributeValue, 1);

                    break;
                case 'Bold':
                    $style['font']['bold'] = true;

                    break;
                case 'Italic':
                    $style['font']['italic'] = true;

                    break;
                case 'Underline':
                    $style = $this->parseUnderline($style, $styleAttributeValue);

                    break;
                case 'VerticalAlign':
                    $style = $this->parseVerticalAlign($style, $styleAttributeValue);

                    break;
            }
        }

        return $style;
    }
}
