<?php
require '../vendor/autoload.php'; // Adjust path if needed

use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json');

try {
    // Get posted JSON data once
    $rawData = file_get_contents('php://input');
    $jsonData = json_decode($rawData, true);

    if (!isset($jsonData['data']) || !isset($jsonData['filePath'])) {
        throw new Exception("Missing data or filePath.");
    }

    $filePath = $jsonData['filePath'];

    if (!file_exists($filePath)) {
        throw new Exception("File does not exist: $filePath");
    }

    $spreadsheet = IOFactory::load($filePath);

    foreach ($jsonData['data'] as $sheetData) {
        $sheetName = $sheetData['sheetName'];
        $rows = $sheetData['rows'];

        $sheet = $spreadsheet->getSheetByName($sheetName);
        if (!$sheet) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($sheetName);
        }

        $sheet->fromArray($rows, null, 'A2'); // Starts at row 2 to skip header
    }

    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save($filePath); // Corrected

    echo json_encode(['success' => true, 'message' => 'Excel file saved.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}