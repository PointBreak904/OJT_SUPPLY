<?php
session_start();
if (isset($_POST['filePath'])) {
    $_SESSION['last_uploaded_file'] = $_POST['filePath'];
    echo json_encode(["success" => true]);
} else {
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../css/home.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
    <div class="header_div">
       <?php include './header/header.php'; ?> 
    </div>
    
    <div class="content">
        <div class="search_bar">
            <input type="text">
            <button>Search</button>
        </div>
        <div class="filter" style="visibility:hidden">
            <div>
                <button>filter 1</button>
                <button>filter 2</button>
                <button>filter 3</button>
                <button>filter 4</button>
            </div>
            <div class="filter2">
                <button class="filter_icon"><img src="../drawables/filter.png" alt="filter"></button>
            </div>
        </div>
    </div>

    <!-- New Upload Button -->
    <div class="upload_file">
        <button id="openUploadModal">Upload File</button>
        <button class="file_icon"><img src="../drawables/file_icon.png" alt="filter"></button>
    </div>
    
    <!-- Upload Container (Hidden by Default) -->
    <div class="uploadContainer" id="uploadModal">
        <span class="close-btn" onclick="closeUploadModal()">✖</span>
        <form id="uploadForm" action="../phpConnection/upload.php" method="POST" enctype="multipart/form-data">
            <input type="file" id="fileInput" name="file" accept=".xlsx, .xls" required>
            <button type="submit">Upload</button>
            <div class="progress">
            <progress id="uploadProgress" value="0" max="100" style="width: 100%; display: none;"></progress>
            <span id="uploadStatus"></span>
            </div>
            
        </form>  
    </div>

    <!-- Tables will be dynamically generated here -->
    <div class="table-container">
        <div id="tables-container"></div>
    </div>
    <div id="buttons-container"></div> <!-- Filter buttons will be added here -->

    <!-- Floating Modal for File List -->
    <div class="overlay" id="overlay"></div>
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
                    echo "<li><a href='#' class='loadFile' data-filepath='../" . $row['file_path'] . "'>" . $row['file_name'] . "</a></li>";
                }
            } else {
                echo "<li>No files uploaded yet.</li>";
            }

            $conn->close();
            ?>
        </ul>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>

    <script>
        document.getElementById("openUploadModal").addEventListener("click", function () {
            document.getElementById("uploadModal").style.display = "block";
            document.getElementById("overlay").style.display = "block";
        });

        function closeUploadModal() {
            document.getElementById("uploadModal").style.display = "none";
            document.getElementById("overlay").style.display = "none";
        }

    </script>
    
    <script>
       document.addEventListener("DOMContentLoaded", function () {
        let selectedFileData = null;
        let lastFile = localStorage.getItem("lastUploadedFile");
        if (lastFile) {
            loadExcelFile("../" + lastFile);
        }

        // Fetch the last opened file from the database
        fetch("../phpConnection/fetch_last_opened_file.php")
        .then(response => response.json())
        .then(data => {
            if (data.success && data.filePath) {
                loadExcelFile(data.filePath);
                console.log("Loaded last opened file:", data.filePath);
            } else {
                console.log("No previously opened file found.");
            }
        })
        .catch(error => console.error("Error fetching last opened file:", error));

        document.getElementById('fileInput').addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.readAsBinaryString(file);

            reader.onload = function (e) {
                selectedFileData = e.target.result; 
                localStorage.setItem("lastOpenedFile", file.name);
            };
        });

        document.getElementById("uploadForm").addEventListener('submit', function (event) {
            event.preventDefault();

            if (!selectedFileData) {
                alert("Please select a file first.");
                return;
            }

            let formData = new FormData(this);
            let progressBar = document.getElementById("uploadProgress");
            let statusMessage = document.getElementById("uploadStatus");

            progressBar.style.display = "block"; // Show progress bar
            statusMessage.textContent = "Uploading...";
            statusMessage.style.color = "blue";

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "../phpConnection/upload.php", true);

            xhr.upload.onprogress = function (event) {
                if (event.lengthComputable) {
                    let percentComplete = (event.loaded / event.total) * 100;
                    progressBar.value = percentComplete;
                }
            };

            xhr.onload = function () {

                if (xhr.status === 200) {
                    try {
                        let response = JSON.parse(xhr.responseText);
                        let submitButton = document.querySelector("#uploadForm button[type='submit']");

                        if (response.success) {
                            progressBar.value = 100;
                            statusMessage.textContent = "File uploaded successfully!";
                            statusMessage.style.color = "green";
                            localStorage.setItem("lastUploadedFile", response.file);
                            displayExcelData(selectedFileData);

                            // Disable the submit button after successful upload
                            submitButton.disabled = true;
                            submitButton.style.backgroundColor = "gray"; // Optional: change button style
                            submitButton.style.cursor = "not-allowed";
                        } else {
                            statusMessage.textContent = "Upload failed: " + response.error;
                            statusMessage.style.color = "red";
                        }
                    } catch (error) {
                        statusMessage.textContent = "Unexpected error occurred.";
                        statusMessage.style.color = "red";
                    }
                }
            };

            xhr.onerror = function () {
                statusMessage.textContent = "Upload error occurred.";
                statusMessage.style.color = "red";
            };

            xhr.send(formData);
        });
        
    // Event listener for saved files list
    document.querySelectorAll(".loadFile").forEach(fileLink => {
        fileLink.addEventListener("click", function (event) {
            event.preventDefault();
            let filePath = this.getAttribute("data-filepath");

            // Update session via AJAX
            fetch("../phpConnection/update_session.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `filePath=${encodeURIComponent(filePath)}`
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("Session updated successfully.");
                } else {
                    console.error("Session update failed:", data.error);
                }
            }).catch(error => console.error("Error updating session:", error));

            // Call PHP script to update file open timestamp
            fetch("../phpConnection/update_file_open_time.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `filePath=${encodeURIComponent(filePath)}`
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("File open time updated successfully.");
                } else {
                    console.error("Failed to update file open time:", data.error);
                }
            }).catch(error => console.error("Error updating file open time:", error));

            // Load file content
            loadExcelFile(filePath);
            localStorage.setItem("lastUploadedFile", filePath);
            closeModal();
        });
    });
});

// Function to load an Excel file given a file path (or server reference)
function loadExcelFile(filePath) {
    fetch(filePath)
        .then(response => response.blob())
        .then(blob => {
            let reader = new FileReader();
            reader.readAsBinaryString(blob);

            reader.onload = function (e) {
                const data = e.target.result;
                displayExcelData(data);
            };
        })
        .catch(error => console.error("Error loading file:", error));
}

// Function to display Excel data in tables
function displayExcelData(data) {
    const workbook = XLSX.read(data, { type: 'binary' });

    const tablesContainer = document.getElementById('tables-container');
    const buttonsContainer = document.getElementById('buttons-container');
    tablesContainer.innerHTML = ""; // Clear previous tables
    buttonsContainer.innerHTML = ""; // Clear previous buttons

    workbook.SheetNames.forEach(function (sheetName, index) {
        const sheet = workbook.Sheets[sheetName];
        const jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });

        if (jsonData.length === 0) return;

        let section = document.createElement("div");
        section.id = "table-" + index;
        section.style.display = index === 0 ? "block" : "none";

        let heading = document.createElement("h2");
        heading.textContent = sheetName;

        let table = document.createElement("table");
        table.border = "1";
        let thead = document.createElement("thead");
        let tbody = document.createElement("tbody");

        let headerRow = document.createElement("tr");
        jsonData[0].forEach(function (header) {
            let th = document.createElement("th");
            th.textContent = header;
            headerRow.appendChild(th);
        });
        thead.appendChild(headerRow);
        table.appendChild(thead);

        jsonData.slice(1).forEach(function (row) {
            let tr = document.createElement("tr");
            row.forEach(function (cell) {
                let td = document.createElement("td");
                td.textContent = cell || "";
                tr.appendChild(td);
            });
            tbody.appendChild(tr);
        });

        table.appendChild(tbody);
        section.appendChild(heading);
        section.appendChild(table);
        tablesContainer.appendChild(section);

        // Create filter button for this sheet (if desired)
        let button = document.createElement("button");
        button.textContent = sheetName;
        button.addEventListener("click", function () {
            document.querySelectorAll(".table-container div[id^='table-']").forEach(function (div) {
                div.style.display = "none";
            });
            document.getElementById("table-" + index).style.display = "block";
        });
        buttonsContainer.appendChild(button);
    });
}

    </script>

    <script>
        document.querySelector(".file_icon").addEventListener("click", function () {
            document.getElementById("fileModal").style.display = "block";
            document.getElementById("overlay").style.display = "block";
        });

        function closeModal() {
            document.getElementById("fileModal").style.display = "none";
            document.getElementById("overlay").style.display = "none";
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Add event listener to dynamically loaded file links
            document.querySelectorAll(".loadFile").forEach(fileLink => {
                fileLink.addEventListener("click", function (event) {
                    event.preventDefault();
                    let filePath = this.getAttribute("data-filepath");
                    loadExcelFile(filePath);
                    closeModal(); // Close modal after selecting a file
                });
            });
        });

        function loadExcelFile(filePath) {
            fetch(filePath)
                .then(response => response.blob())
                .then(blob => {
                    let reader = new FileReader();
                    reader.readAsBinaryString(blob);

                    reader.onload = function (e) {
                        const data = e.target.result;
                        const workbook = XLSX.read(data, { type: 'binary' });

                        const tablesContainer = document.getElementById('tables-container');
                        const buttonsContainer = document.getElementById('buttons-container');
                        tablesContainer.innerHTML = "";
                        buttonsContainer.innerHTML = "";

                        const currentYear = new Date().getFullYear();
                        let closestYear = null;
                        let closestYearIndex = null;
                        let sheetYears = [];
                        let hasYear = false;

                        // Identify all sheet names with years and find the closest to the current year
                        workbook.SheetNames.forEach((sheetName, index) => {
                            const yearMatch = sheetName.match(/(19|20)\d{2}/);
                            if (yearMatch) {
                                hasYear = true;
                                let year = parseInt(yearMatch[0]);
                                sheetYears.push({ year, index });

                                if (year === currentYear) {
                                    closestYear = year;
                                    closestYearIndex = index;
                                } else if (
                                    closestYear === null || 
                                    Math.abs(year - currentYear) < Math.abs(closestYear - currentYear) || 
                                    (Math.abs(year - currentYear) === Math.abs(closestYear - currentYear) && year > closestYear)
                                ) {
                                    closestYear = year;
                                    closestYearIndex = index;
                                }
                            }
                        });

                        console.log("Detected Sheet Years:", sheetYears);

                        // If no year is found, display the first sheet
                        if (!hasYear) {
                            closestYearIndex = 0;
                        }

                        // Display the selected sheet
                        workbook.SheetNames.forEach((sheetName, index) => {
                            const sheet = workbook.Sheets[sheetName];
                            const jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });

                            if (jsonData.length === 0) return;

                            let section = document.createElement("div");
                            section.id = "table-" + index;
                            section.style.display = (index === closestYearIndex) ? "block" : "none";

                            let heading = document.createElement("h2");
                            heading.textContent = sheetName;

                            let table = document.createElement("table");
                            table.border = "1";
                            let thead = document.createElement("thead");
                            let tbody = document.createElement("tbody");

                            let headerRow = document.createElement("tr");
                            jsonData[0].forEach(header => {
                                let th = document.createElement("th");
                                th.textContent = header;
                                headerRow.appendChild(th);
                            });
                            thead.appendChild(headerRow);
                            table.appendChild(thead);

                            jsonData.slice(1).forEach(row => {
                                let tr = document.createElement("tr");
                                row.forEach(cell => {
                                    let td = document.createElement("td");
                                    td.textContent = cell || "";
                                    tr.appendChild(td);
                                });
                                tbody.appendChild(tr);
                            });

                            table.appendChild(tbody);
                            section.appendChild(heading);
                            section.appendChild(table);
                            tablesContainer.appendChild(section);

                            let button = document.createElement("button");
                            button.textContent = sheetName;
                            button.addEventListener("click", function () {
                                document.querySelectorAll(".table-container div[id^='table-']").forEach(div => {
                                    div.style.display = "none";
                                });
                                document.getElementById("table-" + index).style.display = "block";
                            });
                            buttonsContainer.appendChild(button);
                        });

                        console.log("Automatically opened sheet: " + workbook.SheetNames[closestYearIndex]);
                    };
                })
                .catch(error => console.error("Error loading file:", error));
        }

// Function to filter table data based on header
function filterTableByHeader(header) {
    let tables = document.querySelectorAll(".table-container table");

    tables.forEach(table => {
        let headerIndex = -1;
        let headers = table.querySelectorAll("thead th");

        // Find the index of the clicked header
        headers.forEach((th, index) => {
            if (th.textContent === header) {
                headerIndex = index;
            }
        });

        if (headerIndex === -1) return; // Skip if header is not found

        table.querySelectorAll("tbody tr").forEach(row => {
            let cells = row.querySelectorAll("td");
            if (cells[headerIndex] && cells[headerIndex].textContent.trim() !== "") {
                row.style.display = ""; // Show matching rows
            } else {
                row.style.display = "none"; // Hide non-matching rows
            }
        });
    });
}

    </script>

</body>
</html>