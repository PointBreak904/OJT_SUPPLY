<!-- upload_modal.php -->
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
