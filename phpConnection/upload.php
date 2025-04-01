<?php
session_start();
$target_dir = "../drawables/file/"; // Folder where files will be stored

// Ensure the target directory exists
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
        $file_name = basename($_FILES["file"]["name"]);
        $target_file = $target_dir . $file_name; // Corrected path

        // Move uploaded file to the target directory
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $_SESSION['last_uploaded_file'] = $target_file;
            // Database connection
            $conn = new mysqli("localhost", "root", "", "ojt_db");

            if ($conn->connect_error) {
                die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
            }

            // Store file in database with the correct path
            $db_file_path = "drawables/file/" . $file_name; // Store relative path
            $sql = "INSERT INTO uploaded_files (file_name, file_path) VALUES ('$file_name', '$db_file_path')";

            if ($conn->query($sql) === TRUE) {
                echo json_encode(["success" => true, "file" => $db_file_path]); 
            } else {
                echo json_encode(["error" => "Database error: " . $conn->error]);
            }

            $conn->close();
        } else {
            echo json_encode(["error" => "File upload failed"]);
        }
    } else {
        echo json_encode(["error" => "No file uploaded or an error occurred"]);
    }
}
?>
