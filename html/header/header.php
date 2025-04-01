<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
<header class="header">
    <div class="title">
        <h1>
            Supply
        </h1>
    </div>
    <div class="nav">
        <button class="notification-btn" onclick="toggleNotification()">
            <img src="../../drawables/notification.png" alt="Notifications">
        </button>
        <div class="notification-dropdown" id="notificationDropdown">
            <p>No new notifications</p>
        </div>

        <button class="settings-btn" onclick="toggleMenu()">
            <img src="../../drawables/setting.png" alt="Profile">
        </button>
    </div>


    <div class="side-menu" id="sideMenu">
    <button class="close-btn" onclick="toggleMenu()">×</button>
    <ul>
        <li><a href="#">User</a></li>
        <li><a href="#">Settings</a></li>
        <li><a href="#">Notifications</a></li>
        <li><a href="#">Logout</a></li>
    </ul>
</div>
</header>


<script>
    function toggleMenu() {
        var menu = document.getElementById("sideMenu");
        var tableContainer = document.getElementById("table-container");

        if (menu.style.right === "0px") {
            menu.style.right = "-250px";
            tableContainer.style.marginRight = "0"; 
        } else {
            menu.style.right = "0px";
            tableContainer.style.marginRight = "250px"; 
        }
    }

    function toggleNotification() {
        var dropdown = document.getElementById("notificationDropdown");
        var menu = document.getElementById("sideMenu");
        if (menu.style.right === "0px") {
            menu.style.right = "-250px";
        }
        dropdown.classList.toggle("show");
    }
    
    window.onclick = function(event) {
        if (!event.target.closest('.notification-btn') && !event.target.closest('.notification-dropdown')) {
            document.getElementById("notificationDropdown").classList.remove("show");
        }
    };


</script>

</body>
</html>
