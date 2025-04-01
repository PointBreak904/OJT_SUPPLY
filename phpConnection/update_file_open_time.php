<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['filePath'])) {
    $filePath = $conn->real_escape_string($_POST['filePath']);

    // Extract only the filename from the filePath
    $fileName = basename($filePath);

    // Update timestamp for the same file name
    $sql = "UPDATE uploaded_files 
            SET date_and_time_opened = DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s') 
            WHERE file_path LIKE CONCAT('%', '$fileName')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $conn->error]);
    }
}

$conn->close();
?>
