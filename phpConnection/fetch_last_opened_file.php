<?php
include 'db_connection.php';

// Fetch the most recently opened file
$sql = "SELECT file_path FROM uploaded_files ORDER BY date_and_time_opened DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(["success" => true, "filePath" => "../" . $row['file_path']]); 
} else {
    echo json_encode(["success" => false, "message" => "No files found"]);
}

$conn->close();
?>
