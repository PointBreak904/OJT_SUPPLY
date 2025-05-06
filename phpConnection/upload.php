<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_clean();
header('Content-Type: application/json');

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$conn = new mysqli("localhost", "root", "", "ojt_db");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit;
}

$step = $_POST['step'] ?? '';

if ($step === 'upload') {
    if (!isset($_FILES['file'])) {
        echo json_encode(["success" => false, "error" => "File parameter is missing"]);
        exit;
    }

    $fileName = $_FILES['file']['name'];
    $fileTmpName = $_FILES['file']['tmp_name'];
    $filePath = "../drawables/file/" . basename($fileName);

    if (move_uploaded_file($fileTmpName, $filePath)) {
        $file_id = rand(10000, 99999);
        $sql = "INSERT INTO uploaded_files (file_id, file_name, file_path, date_and_time_opened) 
                VALUES ('$file_id', '$fileName', '$filePath', NOW())";

        if ($conn->query($sql)) {
            echo json_encode(["success" => true, "file_id" => $file_id, "file_path" => $filePath]);
        } else {
            echo json_encode(["success" => false, "error" => "Saving uploaded file failed"]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "File upload failed"]);
    }

    exit;
}

if ($step === 'save_headers') {
    $file_id = $_POST['file_id'] ?? '';
    $filePath = $_POST['file_path'] ?? '';

    if (empty($file_id) || empty($filePath) || !file_exists($filePath)) {
        echo json_encode(["success" => false, "error" => "File path or ID invalid"]);
        exit;
    }

    $reader = IOFactory::createReaderForFile($filePath);
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load($filePath);

    foreach ($spreadsheet->getSheetNames() as $sheetIndex => $sheetName) {
        $subfile_id = rand(10000, 99999);
        $stmt = $conn->prepare("INSERT INTO subfiles (subfile_id, file_id, subfile_name) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $subfile_id, $file_id, $sheetName);
        $stmt->execute();
        $stmt->close();
    }

    echo json_encode(["success" => true, "message" => "Headers saved."]);
    exit;
}

if ($step === 'save_po') {
    $file_id = $_POST['file_id'] ?? '';
    $filePath = $_POST['file_path'] ?? '';

    if (empty($file_id) || empty($filePath) || !file_exists($filePath)) {
        echo json_encode(["success" => false, "error" => "File path or ID invalid"]);
        exit;
    }

    $reader = IOFactory::createReaderForFile($filePath);
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load($filePath);

    $poNumbersBySubfile = [];  // Store PO numbers grouped by subfile_id

    foreach ($spreadsheet->getSheetNames() as $sheetIndex => $sheetName) {
        // Check if the sheet name contains "PO", regardless of case or spacing
        if (stripos($sheetName, "po") === false) continue;

        $sheet = $spreadsheet->getSheet($sheetIndex);
        $rows = $sheet->toArray(null, true, true, false);

        // Look for the PO column and SUPPLIERS column
        $poColumnIndex = null;
        $suppliersColumnIndex = null;

        // Loop through the header rows to find column indices for PO and SUPPLIERS
        foreach ($rows as $rowIndex => $row) {
            foreach ($row as $colIndex => $cell) {
                $normalizedCell = strtolower(trim($cell));  // Normalize column name

                if ($normalizedCell === 'suppliers') {
                    $suppliersColumnIndex = $colIndex;  // Find Suppliers column
                }

                // Look for variations of "PO NO" or "PO. NO"
                if (preg_match('/po\.(\s?no|no\s?)/', $normalizedCell)) {
                    $poColumnIndex = $colIndex;  // Found the PO column
                }
            }

            // If both "suppliers" and "PO" columns are found, break out of the loop
            if ($poColumnIndex !== null && $suppliersColumnIndex !== null) {
                break;
            }
        }

        // If we didn't find both the PO column and Suppliers column, skip this sheet
        if ($poColumnIndex === null || $suppliersColumnIndex === null) {
            continue;
        }

        // Get subfile_id from DB for the current sheet
        $stmt = $conn->prepare("SELECT subfile_id FROM subfiles WHERE file_id = ? AND subfile_name = ?");
        $stmt->bind_param("is", $file_id, $sheetName);
        $stmt->execute();
        $stmt->bind_result($subfile_id);
        $stmt->fetch();
        $stmt->close();

        // Check if we got the subfile_id
        if (!$subfile_id) {
            echo json_encode(["success" => false, "error" => "Subfile ID not found for sheet: $sheetName"]);
            exit;
        }

        // Collect PO numbers for this subfile
        foreach ($rows as $rowIndex => $row) {
            if ($rowIndex == 0) continue; // Skip the first header row

            $poValue = trim($row[$poColumnIndex] ?? '');

            // Ensure we found a valid PO value
            if (!empty($poValue)) {
                $poNumbersBySubfile[$subfile_id][] = $poValue;
            }
        }
    }

    // Insert PO numbers for each subfile
    foreach ($poNumbersBySubfile as $subfile_id => $poNumbers) {
        foreach ($poNumbers as $poValue) {
            $stmtPO = $conn->prepare("INSERT INTO file_items (subfile_id, po_no) VALUES (?, ?)");
            $stmtPO->bind_param("is", $subfile_id, $poValue);

            // Execute the insert for each PO number
            if (!$stmtPO->execute()) {
                echo json_encode(["success" => false, "error" => "Failed to insert PO number: $poValue"]);
                exit;
            }
            $stmtPO->close();
        }
    }

    echo json_encode(["success" => true, "extracted_po_numbers" => array_merge(...array_values($poNumbersBySubfile))]);
    exit;
}


echo json_encode(["success" => false, "error" => "Invalid or missing step"]);
exit;
?>
