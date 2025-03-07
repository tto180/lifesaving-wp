<?php
/**
 * @license MIT
 *
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace LearnDash\Reports\PhpOffice\PhpSpreadsheet;

use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Reader\IReader;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Shared\File;
use LearnDash\Reports\PhpOffice\PhpSpreadsheet\Writer\IWriter;

/**
 * Factory to create readers and writers easily.
 *
 * It is not required to use this class, but it should make it easier to read and write files.
 * Especially for reading files with an unknown format.
 */
abstract class IOFactory
{
    private static $readers = [
        'Xlsx' => Reader\Xlsx::class,
        'Xls' => Reader\Xls::class,
        'Xml' => Reader\Xml::class,
        'Ods' => Reader\Ods::class,
        'Slk' => Reader\Slk::class,
        'Gnumeric' => Reader\Gnumeric::class,
        'Html' => Reader\Html::class,
        'Csv' => Reader\Csv::class,
    ];

    private static $writers = [
        'Xls' => Writer\Xls::class,
        'Xlsx' => Writer\Xlsx::class,
        'Ods' => Writer\Ods::class,
        'Csv' => Writer\Csv::class,
        'Html' => Writer\Html::class,
        'Tcpdf' => Writer\Pdf\Tcpdf::class,
        'Dompdf' => Writer\Pdf\Dompdf::class,
        'Mpdf' => Writer\Pdf\Mpdf::class,
    ];

    /**
     * Create Writer\IWriter.
     */
    public static function createWriter(Spreadsheet $spreadsheet, string $writerType): IWriter
    {
        if (!isset(self::$writers[$writerType])) {
            throw new Writer\Exception("No writer found for type $writerType");
        }

        // Instantiate writer
        $className = self::$writers[$writerType];

        return new $className($spreadsheet);
    }

    /**
     * Create IReader.
     */
    public static function createReader(string $readerType): IReader
    {
        if (!isset(self::$readers[$readerType])) {
            throw new Reader\Exception("No reader found for type $readerType");
        }

        // Instantiate reader
        $className = self::$readers[$readerType];

        return new $className();
    }

    /**
     * Loads Spreadsheet from file using automatic Reader\IReader resolution.
     *
     * @param string $filename The name of the spreadsheet file
     */
    public static function load(string $filename, int $flags = 0): Spreadsheet
    {
        $reader = self::createReaderForFile($filename);

        return $reader->load($filename, $flags);
    }

    /**
     * Identify file type using automatic IReader resolution.
     */
    public static function identify(string $filename): string
    {
        $reader = self::createReaderForFile($filename);
        $className = get_class($reader);
        $classType = explode('\\', $className);
        unset($reader);

        return array_pop($classType);
    }

    /**
     * Create Reader\IReader for file using automatic IReader resolution.
     */
    public static function createReaderForFile(string $filename): IReader
    {
        File::assertFile($filename);

        // First, lucky guess by inspecting file extension
        $guessedReader = self::getReaderTypeFromExtension($filename);
        if ($guessedReader !== null) {
            $reader = self::createReader($guessedReader);

            // Let's see if we are lucky
            if ($reader->canRead($filename)) {
                return $reader;
            }
        }

        // If we reach here then "lucky guess" didn't give any result
        // Try walking through all the options in self::$autoResolveClasses
        foreach (self::$readers as $type => $class) {
            //    Ignore our original guess, we know that won't work
            if ($type !== $guessedReader) {
                $reader = self::createReader($type);
                if ($reader->canRead($filename)) {
                    return $reader;
                }
            }
        }

        throw new Reader\Exception('Unable to identify a reader for this file');
    }

    /**
     * Guess a reader type from the file extension, if any.
     */
    private static function getReaderTypeFromExtension(string $filename): ?string
    {
        $pathinfo = pathinfo($filename);
        if (!isset($pathinfo['extension'])) {
            return null;
        }

        switch (strtolower($pathinfo['extension'])) {
            case 'xlsx': // Excel (OfficeOpenXML) Spreadsheet
            case 'xlsm': // Excel (OfficeOpenXML) Macro Spreadsheet (macros will be discarded)
            case 'xltx': // Excel (OfficeOpenXML) Template
            case 'xltm': // Excel (OfficeOpenXML) Macro Template (macros will be discarded)
                return 'Xlsx';
            case 'xls': // Excel (BIFF) Spreadsheet
            case 'xlt': // Excel (BIFF) Template
                return 'Xls';
            case 'ods': // Open/Libre Offic Calc
            case 'ots': // Open/Libre Offic Calc Template
                return 'Ods';
            case 'slk':
                return 'Slk';
            case 'xml': // Excel 2003 SpreadSheetML
                return 'Xml';
            case 'gnumeric':
                return 'Gnumeric';
            case 'htm':
            case 'html':
                return 'Html';
            case 'csv':
                // Do nothing
                // We must not try to use CSV reader since it loads
                // all files including Excel files etc.
                return null;
            default:
                return null;
        }
    }

    /**
     * Register a writer with its type and class name.
     */
    public static function registerWriter(string $writerType, string $writerClass): void
    {
        if (!is_a($writerClass, IWriter::class, true)) {
            throw new Writer\Exception('Registered writers must implement ' . IWriter::class);
        }

        self::$writers[$writerType] = $writerClass;
    }

    /**
     * Register a reader with its type and class name.
     */
    public static function registerReader(string $readerType, string $readerClass): void
    {
        if (!is_a($readerClass, IReader::class, true)) {
            throw new Reader\Exception('Registered readers must implement ' . IReader::class);
        }

        self::$readers[$readerType] = $readerClass;
    }
}
