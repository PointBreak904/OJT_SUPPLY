<?php
require '../vendor/autoload.php'; // Load PHPSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

if (isset($_GET['file'])) {
    $filePath = "../drawables/file/" . basename($_GET['file']);

    if (!file_exists($filePath)) {
        echo json_encode(["success" => false, "error" => "File not found"]);
        exit;
    }

    $spreadsheet = IOFactory::load($filePath);
    $sheetsData = [];

    foreach ($spreadsheet->getSheetNames() as $sheetIndex => $sheetName) {
        $sheet = $spreadsheet->getSheet($sheetIndex);
        $rows = $sheet->toArray(null, true, true, false);
        $formattedData = [];

        foreach ($rows as $rowIndex => $row) {
            $formattedRow = [];

            foreach ($row as $colIndex => $cellValue) {
                // Convert column index to Excel-style letters (A, B, C...)
                $cellCoordinate = Coordinate::stringFromColumnIndex($colIndex + 1) . ($rowIndex + 1);
                $cell = $sheet->getCell($cellCoordinate);
                $style = $sheet->getStyle($cellCoordinate);

                $font = $style->getFont();
                $fill = $style->getFill();

                $formattedRow[] = [
                    "value" => $cellValue,
                    "bold" => $font->getBold(),
                    "italic" => $font->getItalic(),
                    "fontColor" => $font->getColor()->getARGB(),
                    "bgColor" => ($fill->getFillType() === 'solid') ? $fill->getStartColor()->getARGB() : null
                ];
            }
            $formattedData[] = $formattedRow;
        }
        $sheetsData[] = ["sheetName" => $sheetName, "data" => $formattedData];
    }

    echo json_encode(["success" => true, "sheets" => $sheetsData]);
    exit;
}
?>
