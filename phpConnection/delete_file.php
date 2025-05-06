<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file_path'])) {
    $filePath = str_replace('..', '', $_POST['file_path']); // Basic security
    $relativePath = ltrim($filePath, '/'); // Remove leading slash if any

    $conn = new mysqli("localhost", "root", "", "ojt_db");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $file_path = "../" . $relativePath; // Remove leading slash if any
    // Prepare the statement to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM uploaded_files WHERE file_path = ?");
    $stmt->bind_param("s", $file_path);

    if ($stmt->execute()) {
        // Optionally delete the actual file from the server
        if (file_exists("../" . $relativePath)) {
            unlink("../" . $relativePath);
        }
        echo "File successfully deleted.";
    } else {
        echo "Failed to delete file from database.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
