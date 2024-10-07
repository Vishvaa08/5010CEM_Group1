<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin User Management</title>
    <link rel="stylesheet" href="css/Users.css">
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">

    </head>
    <body>

    <div class="sidebar">
        <div class="sidebar-header">
            <h2>TravelTrail</h2>
        </div>
        <ul>
            <li>
                <img src="images/home dark.jpg" alt="Dashboard Icon">
                    <a href="AdminDashboard.php">Dashboard</a>
            </li>
            <li>
                <img src="images/package.png" alt="Packages Icon">
                    <a href="AdminPackage.php">Travel Packages</a>
            </li>
            <li class="active">
                <img src="images/users dark.jpg" alt="User Icon">
                    <a href="AdminUser.php">User Management</a>
            </li>
            <li>
                <img src="images/inventory.png" alt="Inventory Icon">
                    <a href="AdminInventory.php">Inventory Status</a>
            </li>
            <li>
                <img src="images/report.png" alt="Report Icon">
                    <a href="AdminReport.php">Report</a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <div class="header-left">
                <h2>Dashboard / User Management</h2>
                <p>User Management</p>
            </div>
            <div class="header-right">
                <div class="search-box">
                    <input type="text" placeholder="Search">
                </div>
                <div class="user-wrapper">
                    <p>Hi, Admin</p>
                    <a href="AdminLogin.php">
                        <img src="images/logout.png" alt="Logout Icon" class="logout-icon">
                    </a>
                </div>
            </div>
        </header>


    </div>

    </body>
</html>
