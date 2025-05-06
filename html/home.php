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
       <div id="color-summary" style="display: none;"></div>
    </div>
    
    
    
    <!-- Upload Container (Hidden by Default) -->
    <?php include './upload_modal.php'; ?>

    <div class="page-wrapper">
    <div class="content">
        <!-- New Upload Button -->
    <div class="upload_file">
        <button id="openUploadModal"><img src='../drawables/upload_icon.png' alt='' style='vertical-align: middle; width: 16px; margin-left: 4px; margin-right: 6px;'>Upload File</button>
        <div class="search_bar">
            <input type="text">
            <button>Search</button>
        </div>
        <button class="file_icon"><img src="../drawables/file_icon.png" alt="filter"></button>
    </div>

    <div class="filter_form">
        <div id="filter-buttons">
            <button id="show-all">Show All</button>
            <button id="filter-green">🟩 Show Green Only</button>
            <button id="filter-red">🟥 Show Red Only</button>
            <button id="filter-blue">🟦 Show Blue Only</button>
        </div>
        <div id="save-spinner" class="saving">
            <div class="spin">
             <div class="savespin "></div>
              Saving...     
            </div>
            
        </div>
    </div>

    <div id="tables-container" class="table-container"> 
        <div id="loading-overlay">
            <div class="loader"></div>
            <p>Loading...</p>
        </div>
    </div>
    <div id="buttons-container"></div> <!-- Filter buttons will be added here -->
    

    <!-- Floating Modal for File List -->
    <div class="overlay" id="overlay"></div>
    <?php include './file_modal.php'; ?>

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
    formData.append("step", "upload"); // Explicitly add the step parameter

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

                    // Send request to save headers
                    saveHeaders(response.file_id, response.file_path);

                    // Send request to save PO numbers
                    savePO(response.file_id, response.file_path);

                    // Refresh the page after file upload
                    setTimeout(function() {
                        location.reload(); // Refresh the page
                    }, 1000); // 1-second delay before reload (adjust if needed)
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

// Function to save headers
function saveHeaders(fileId, filePath) {
    let xhr = new XMLHttpRequest();
    let formData = new FormData();
    formData.append("step", "save_headers");
    formData.append("file_id", fileId);
    formData.append("file_path", filePath);

    xhr.open("POST", "../phpConnection/upload.php", true);

    xhr.onload = function () {
        if (xhr.status === 200) {
            let response = JSON.parse(xhr.responseText);
            if (response.success) {
                console.log("Headers saved successfully!");
            } else {
                console.error("Error saving headers: " + response.error);
            }
        }
    };

    xhr.send(formData);
}

// Function to save PO numbers
function savePO(fileId, filePath) {
    let xhr = new XMLHttpRequest();
    let formData = new FormData();
    formData.append("step", "save_po");
    formData.append("file_id", fileId);
    formData.append("file_path", filePath);

    xhr.open("POST", "../phpConnection/upload.php", true);

    xhr.onload = function () {
        if (xhr.status === 200) {
            let response = JSON.parse(xhr.responseText);
            if (response.success) {
                console.log("PO numbers saved successfully!");
            } else {
                console.error("Error saving PO numbers: " + response.error);
            }
        }
    };

    xhr.send(formData);
}



        document.getElementById("fileList").addEventListener("click", function (event) {
    if (event.target.classList.contains("fileList")) {
        event.preventDefault();
        let filePath = event.target.getAttribute("data-filepath");

        if (!filePath || filePath.trim() === "") {
            console.error("File path is empty or undefined.");
            return;
        }

        // Proceed with AJAX request
        fetch("../phpConnection/update_session.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `filePath=${encodeURIComponent(filePath)}`
        }).then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error("Session update failed:", data.error);
            }
        });
    }
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
                     setTimeout(function() {
                                location.reload(); // Refresh the page
                            }, 1000); // 1-second delay before reload (adjust if needed)
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

// Function to display Excel data in tables
function displayExcelData(data) {
    const workbook = XLSX.read(data, { type: 'binary' });
    const tablesContainer = document.getElementById('tables-container');
    const buttonsContainer = document.getElementById('buttons-container');
    
    // Clear previous tables and buttons
    tablesContainer.innerHTML = "";
    buttonsContainer.innerHTML = "";
    
    const currentYear = new Date().getFullYear();
    let closestYear = null;
    let closestYearIndex = null;
    let sheetYears = [];
    let hasYear = false;

    // Loop through sheets and find the closest year to the current year
    workbook.SheetNames.forEach(function (sheetName, index) {
        const sheet = workbook.Sheets[sheetName];
        const yearMatch = sheetName.match(/(19|20)\d{2}/);

        if (yearMatch) {
            hasYear = true;
            let year = parseInt(yearMatch[0]);
            sheetYears.push({ year, index });

            // Find closest year
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

    if (!hasYear) {
        closestYearIndex = 0;  // Default to first sheet if no year is found
    }

    // Create table and buttons for each sheet
    workbook.SheetNames.forEach(function (sheetName, index) {
        const sheet = workbook.Sheets[sheetName];
        const jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });

        if (jsonData.length === 0) return;

        // Create section for table
        let section = document.createElement("div");
        section.id = "table-" + index;
        section.style.display = index === closestYearIndex ? "block" : "none";

        let heading = document.createElement("h2");
        heading.textContent = sheetName;

        let table = document.createElement("table");
        table.border = "1";
        let thead = document.createElement("thead");
        let tbody = document.createElement("tbody");

        // Create table header row
        let headerRow = document.createElement("tr");
        jsonData[0].forEach(function (header) {
            let th = document.createElement("th");
            th.textContent = header;
            headerRow.appendChild(th);
        });
        thead.appendChild(headerRow);
        table.appendChild(thead);

        // Create table body rows
        jsonData.slice(1).forEach(function (row, rowIndex) {
            let tr = document.createElement("tr");
            row.forEach(function (cell, colIndex) {
                let td = document.createElement("td");
                td.textContent = cell || "";

                // Apply formatting
                const cellAddress = XLSX.utils.encode_cell({ r: rowIndex + 1, c: colIndex });
                const cellStyle = sheet[cellAddress] ? sheet[cellAddress].s : null;

                if (cellStyle) {
                    // Apply font styles
                    if (cellStyle.font) {
                        if (cellStyle.font.bold) td.style.fontWeight = "bold";
                        if (cellStyle.font.italic) td.style.fontStyle = "italic";
                        if (cellStyle.font.color) td.style.color = `#${cellStyle.font.color.rgb}`;
                        if (cellStyle.font.size) td.style.fontSize = `${cellStyle.font.size}px`;
                    }
                    // Apply background color
                    if (cellStyle.fill && cellStyle.fill.fgColor) {
                        td.style.backgroundColor = `#${cellStyle.fill.fgColor.rgb}`;
                    }
                }

                tr.appendChild(td);
            });
            tbody.appendChild(tr);
        });

        table.appendChild(tbody);
        section.appendChild(heading);
        section.appendChild(table);
        tablesContainer.appendChild(section);

        // Create filter button for each sheet
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
        const colorFilteredRowsPerSheet = {};

        function recountColorsForSheet(sheet, index) {
            let green = new Set();
            let red = new Set();
            let blue = new Set();

            sheet.data.forEach((row, rowIndex) => {
                let hasGreen = false, hasRed = false, hasBlue = false;

                row.forEach(cell => {
                    if (!cell.bgColor) return;
                    const hex = `#${cell.bgColor.substring(2)}`.toLowerCase();

                    if (hex.includes("00b050") || hex.includes("00ff00") || hex.includes("c6efce")) {
                        hasGreen = true;
                    } else if (hex.includes("ff0000") || hex.includes("ffc7ce") || hex.includes("e26b0a")) {
                        hasRed = true;
                    } else if (hex.includes("00b0f0") || hex.includes("9dc3e6") || hex.includes("bdd7ee")) {
                        hasBlue = true;
                    }
                });

                if (hasGreen) green.add(rowIndex);
                if (hasRed) red.add(rowIndex);
                if (hasBlue) blue.add(rowIndex);
            });

            colorFilteredRowsPerSheet[index] = {
                green,
                red,
                blue
            };

            document.getElementById("color-summary").innerHTML =
                `🟩 Green rows: ${green.size} &nbsp;&nbsp; 🟥 Red rows: ${red.size} &nbsp;&nbsp; 🟦 Blue rows: ${blue.size}`;
        }

        let currentFilePath = "";

        function loadExcelFile(filePath) {
            document.getElementById("loading-overlay").style.display = "block";
            currentFilePath = filePath;

            fetch(`../phpConnection/read_excel.php?file=${encodeURIComponent(filePath)}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        console.error("Error loading file:", data.error);
                        return;
                    }

                    const tablesContainer = document.getElementById('tables-container');
                    const buttonsContainer = document.getElementById('buttons-container');
                    tablesContainer.innerHTML = "";
                    buttonsContainer.innerHTML = "";

                    const currentYear = new Date().getFullYear();
                    let closestYear = null;
                    let closestYearIndex = null;
                    let sheetYears = [];
                    let hasYear = false;

                    let colorRowCounts = {
                        green: new Set(),
                        red: new Set(),
                        blue: new Set()
                    };

                    // Identify sheets with years and find the closest to the current year
                    data.sheets.forEach((sheet, index) => {
                        const yearMatch = sheet.sheetName.match(/(19|20)\d{2}/);
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

                    if (!hasYear) {
                        closestYearIndex = 0;
                    }

                    // Display each sheet
                    data.sheets.forEach((sheet, index) => {
                        let section = document.createElement("div");
                        section.id = "table-" + index;
                        section.style.display = (index === closestYearIndex) ? "block" : "none";

                        let heading = document.createElement("h2");
                        heading.textContent = sheet.sheetName;

                        let table = document.createElement("table");
                        table.border = "1";
                        let thead = document.createElement("thead");
                        let tbody = document.createElement("tbody");

                        sheet.data.forEach((row, rowIndex) => {
                            let tr = document.createElement("tr");

                            row.forEach(cell => {
                                let cellElement = rowIndex === 0 ? document.createElement("th") : document.createElement("td");
                                cellElement.textContent = cell.value || "";

                                // Apply styles from backend
                                if (cell.bold) cellElement.style.fontWeight = "bold";
                                if (cell.italic) cellElement.style.fontStyle = "italic";
                                if (cell.fontColor) cellElement.style.color = `#${cell.fontColor.substring(2)}`;
                                if (cell.bgColor) {
                                    let hex = `#${cell.bgColor.substring(2)}`.toLowerCase();
                                    cellElement.style.backgroundColor = hex;

                                    // Track row index if it has a matching color
                                    if (hex.includes("00b050") || hex.includes("00ff00") || hex.includes("c6efce")) {
                                        colorRowCounts.green.add(rowIndex);
                                    } else if (hex.includes("ff0000") || hex.includes("ffc7ce") || hex.includes("e26b0a")) {
                                        colorRowCounts.red.add(rowIndex);
                                    } else if (hex.includes("00b0f0") || hex.includes("9dc3e6") || hex.includes("bdd7ee")) {
                                        colorRowCounts.blue.add(rowIndex);
                                    }
                                }

                                // Make <td> editable and add autosave trigger
                                if (rowIndex !== 0) {
                                    cellElement.contentEditable = true;

                                    cellElement.addEventListener('input', () => {
                                        clearTimeout(saveTimeout);
                                        saveTimeout = setTimeout(saveExcelChanges, 1000);
                                    });
                                }

                                tr.appendChild(cellElement);
                            });

                            rowIndex === 0 ? thead.appendChild(tr) : tbody.appendChild(tr);
                        });

                        table.appendChild(thead);
                        table.appendChild(tbody);
                        section.appendChild(heading);
                        section.appendChild(table);
                        tablesContainer.appendChild(section);

                        // Button for switching between sheets
                        let button = document.createElement("button");
                        button.textContent = sheet.sheetName;
                        button.addEventListener("click", function () {
                            document.querySelectorAll(".table-container div[id^='table-']").forEach(div => {
                                div.style.display = "none";
                            });
                            document.getElementById("table-" + index).style.display = "block";

                            // Recalculate color rows for this sheet
                            recountColorsForSheet(data.sheets[index], index);
                        });

                        buttonsContainer.appendChild(button);
                    });

                    document.getElementById("color-summary").innerHTML =
                        `🟩 Green rows: ${colorRowCounts.green.size} &nbsp;&nbsp; 🟥 Red rows: ${colorRowCounts.red.size} &nbsp;&nbsp; 🟦 Blue rows: ${colorRowCounts.blue.size}`;
                    recountColorsForSheet(data.sheets[closestYearIndex], closestYearIndex);

                    console.log("Automatically opened sheet: " + data.sheets[closestYearIndex].sheetName);
                })
                .catch(error => console.error("Error fetching Excel data:", error));

            document.getElementById("show-all").addEventListener("click", () => filterRowsByColor("all"));
            document.getElementById("filter-green").addEventListener("click", () => filterRowsByColor("green"));
            document.getElementById("filter-red").addEventListener("click", () => filterRowsByColor("red"));
            document.getElementById("filter-blue").addEventListener("click", () => filterRowsByColor("blue"));
        }

        let saveTimeout;

        function saveExcelChanges() {
            document.getElementById("save-spinner").style.display = "block"; // Show saving indicator

            const tables = document.querySelectorAll('#tables-container table');
            const data = [];

            tables.forEach(table => {
                const tableData = [];
                const rows = table.querySelectorAll('tr');

                rows.forEach(row => {
                    const rowData = [];
                    row.querySelectorAll('td').forEach(cell => {
                        rowData.push(cell.innerText);
                    });
                    if (rowData.length > 0) tableData.push(rowData);
                });

                const sheetName = table.previousSibling.textContent;
                data.push({ sheetName: sheetName, rows: tableData });
            });

            // 🔧 Fix the path before sending it
            if (currentFilePath.startsWith("../../")) {
                currentFilePath = currentFilePath.replace("../", ""); // Remove one "../"
            }

            fetch('../phpConnection/save_excel.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ filePath: currentFilePath, data })
            })
                .then(res => res.text())
                .then(result => {
                    console.log('Save result:', result);
                })
                .catch(error => {
                    console.error('Save failed:', error);
                })
                .finally(() => {
                    setTimeout(() => {
                        document.getElementById("save-spinner").style.display = "none";
                    }, 500);
                });
        }

        function filterRowsByColor(color) {
            const visibleTable = document.querySelector(".table-container > div[id^='table-']:not([style*='display: none'])");
            if (!visibleTable) return;

            const index = parseInt(visibleTable.id.split("-")[1]);
            const rowSet = colorFilteredRowsPerSheet[index];
            if (!rowSet) return;

            const rows = visibleTable.querySelectorAll("tbody tr");

            if (color === "all") {
                rows.forEach(row => row.style.display = "");
                return;
            }

            const matchSet = rowSet[color];

            rows.forEach((row, i) => {
                // Always show first row (index 0)
                if (i === 0) {
                    row.style.display = "";
                } else {
                    row.style.display = matchSet.has(i + 1) ? "" : "none";
                }
            });
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