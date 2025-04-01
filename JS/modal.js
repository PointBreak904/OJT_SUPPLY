document.querySelector(".file_icon").addEventListener("click", function () {
    document.getElementById("fileModal").style.display = "block";
    document.getElementById("overlay").style.display = "block";
});

function closeModal() {
    document.getElementById("fileModal").style.display = "none";
    document.getElementById("overlay").style.display = "none";
}