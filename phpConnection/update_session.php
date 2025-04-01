<?php
session_start();
if (isset($_POST['filePath'])) {
    $_SESSION['last_uploaded_file'] = $_POST['filePath'];
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "No file path provided"]);
}
?>
