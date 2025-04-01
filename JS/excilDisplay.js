document.getElementById('fileInput').addEventListener('change', function (event) {
    const file = event.target.files[0];

    if (!file) return;

    const reader = new FileReader();
    reader.readAsBinaryString(file);

    reader.onload = function (e) {
        const data = e.target.result;
        const workbook = XLSX.read(data, { type: 'binary' });

        const tablesContainer = document.getElementById('tables-container');
        const buttonsContainer = document.getElementById('buttons-container');
        tablesContainer.innerHTML = ""; // Clear previous tables
        buttonsContainer.innerHTML = ""; // Clear previous buttons

        workbook.SheetNames.forEach((sheetName, index) => {
            const sheet = workbook.Sheets[sheetName];
            const jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });

            if (jsonData.length === 0) return; // Skip empty sheets

            // Create a new section for each sheet
            let section = document.createElement("div");
            section.id = `table-${index}`;
            section.style.display = index === 0 ? "block" : "none"; // Show first table, hide others

            let heading = document.createElement("h2");
            heading.textContent = sheetName;

            // Create table
            let table = document.createElement("table");
            table.border = "1";
            let thead = document.createElement("thead");
            let tbody = document.createElement("tbody");

            // Add headers from first row
            let headerRow = document.createElement("tr");
            jsonData[0].forEach(header => {
                let th = document.createElement("th");
                th.textContent = header;
                headerRow.appendChild(th);
            });
            thead.appendChild(headerRow);
            table.appendChild(thead);

            // Add table rows
            jsonData.slice(1).forEach(row => {
                let tr = document.createElement("tr");
                row.forEach(cell => {
                    let td = document.createElement("td");
                    td.textContent = cell || ""; // Handle empty cells
                    tr.appendChild(td);
                });
                tbody.appendChild(tr);
            });

            table.appendChild(tbody);
            section.appendChild(heading);
            section.appendChild(table);
            tablesContainer.appendChild(section);

            // Create filter button
            let button = document.createElement("button");
            button.textContent = sheetName;
            button.addEventListener("click", function () {
                // Hide all tables
                document.querySelectorAll(".table-container div[id^='table-']").forEach(div => {
                    div.style.display = "none";
                });
                // Show the selected table
                document.getElementById(`table-${index}`).style.display = "block";
            });
            buttonsContainer.appendChild(button);
        });
    };
});