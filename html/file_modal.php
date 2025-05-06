<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/file_modal.css">
</head>
<body>
    <div class="modal" id="fileModal">
        <span class="close-btn" onclick="closeModal()">✖</span>
        <h2>Uploaded Files</h2>
        <ul id="fileList">
            <?php
            $conn = new mysqli("localhost", "root", "", "ojt_db");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "SELECT file_name, file_path FROM uploaded_files";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $fileName = htmlspecialchars($row['file_name']);
                    $filePath = "../" . $row['file_path'];
                    echo "
                        <li class='file-item'>
                            <div>
                                <img src='../drawables/excel_icon.png' alt='' style='vertical-align: middle;'>
                                <a href='#' class='loadFile' data-filepath='$filePath'>$fileName</a>
                            </div>
                            <div style='position: relative;'>
                                <button class='three-dot-btn' onclick='toggleActions(this)'>⋮</button>
                                <div class='file-actions'>
                                    <a style='vertical-align: middle;' href='#' class='loadFile' data-filepath='$filePath'> <img src='../drawables/open_icon.png' alt='' style='vertical-align: middle; width: 20px;'> Open</a>
                                    <a style='vertical-align: middle;' href='#'><img src='../drawables/edit_icon.png' alt='' style='vertical-align: middle; width: 14px; margin-left: 4px; margin-right: 5px;'> Rename</a>
                                    <a style='vertical-align: middle;' href='#'><img src='../drawables/copy_icon.png' alt='' style='vertical-align: middle; width: 16px; margin-left: 4px; margin-right: 6px;'>Copy</a>
                                    <a style='vertical-align: middle;' href='$filePath' download><img src='../drawables/download_icon.png' alt='' style=' width: 13px; margin-left: 4px; margin-right: 7px;'>Download</a>
                                    <a style='vertical-align: middle;' ef='#' onclick='deleteFile(\"$filePath\")'><img src='../drawables/delete_icon.png' alt='' style='=vertical-align: middle; width: 14px; margin-left: 4px; margin-right: 6px;'>Delete</a>
                                </div>
                            </div>
                        </li>
                    ";
                }
            } else {
                echo "<li>No files uploaded yet.</li>";
            }

            $conn->close();
            ?>
        </ul>
    </div>

    <script>
        function toggleActions(button) {
            const allMenus = document.querySelectorAll('.file-actions');
            allMenus.forEach(menu => {
                if (menu !== button.nextElementSibling) menu.style.display = 'none';
            });

            const menu = button.nextElementSibling;
            menu.style.display = (menu.style.display === 'flex') ? 'none' : 'flex';
        }

        function deleteFile(filePath) {
            if (confirm("Are you sure you want to delete this file?")) {
                fetch('../phpConnection/delete_file.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'file_path=' + encodeURIComponent(filePath)
                })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    // Reload or remove the file item from the list
                    location.reload(); // or remove the list item dynamically
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        }


        document.addEventListener('click', function (e) {
            if (!e.target.classList.contains('three-dot-btn')) {
                document.querySelectorAll('.file-actions').forEach(menu => menu.style.display = 'none');
            }
        });
    </script>
</body>
</html>
