<?php
/**
 * @license MIT
 *
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace LearnDash\Reports\PhpOffice\PhpSpreadsheet\Writer\Ods\Cell;

use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Cell\Cell;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Shared\XMLWriter;

/**
 * @author     Alexander Pervakov <frost-nzcr4@jagmort.com>
 */
class Comment
{
    public static function write(XMLWriter $objWriter, Cell $cell): void
    {
        $comments = $cell->getWorksheet()->getComments();
        if (!isset($comments[$cell->getCoordinate()])) {
            return;
        }
        $comment = $comments[$cell->getCoordinate()];

        $objWriter->startElement('office:annotation');
        $objWriter->writeAttribute('svg:width', $comment->getWidth());
        $objWriter->writeAttribute('svg:height', $comment->getHeight());
        $objWriter->writeAttribute('svg:x', $comment->getMarginLeft());
        $objWriter->writeAttribute('svg:y', $comment->getMarginTop());
        $objWriter->writeElement('dc:creator', $comment->getAuthor());
        $objWriter->writeElement('text:p', $comment->getText()->getPlainText());
        $objWriter->endElement();
    }
}
