<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/Dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">

    </head>
    <body>

    <div class="sidebar">
        <div class="sidebar-header">
            <h2>TravelTrail</h2>
        </div>
        <ul>
            <li class="active">
                <img src="images/home dark.jpg" alt="Dashboard Icon">
                    <a href="AdminDashboard.php">Dashboard</a>
            </li>
            <li>
                <img src="images/package.png" alt="Packages Icon">
                    <a href="AdminPackage.php">Travel Packages</a>
            </li>
            <li>
                <img src="images/users.png" alt="User Icon">
                    <a href="#">User Management</a>
            </li>
            <li>
                <img src="images/inventory.png" alt="Inventory Icon">
                    <a href="#">Inventory Status</a>
            </li>
            <li>
                <img src="images/report.png" alt="Report Icon">
                    <a href="#">Report</a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <div class="header-left">
                <h2>Dashboard / Home</h2>
                <p>Dashboard</p>
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


        <div class="cards">
            <div class="card">
                <div class="icon-wrapper1">
                    <img src="images/new user.jpg" alt="User Icon">
                </div>
                <div class="card-info">
                    <h3>Unverified Users</h3>
                    <p>3</p>
                </div>
            </div>
            <div class="card">
                <div class="icon-wrapper">
                    <img src="images/payment.jpg" alt="Payment Icon">
                </div>
                <div class="card-info">
                    <h3>New Payments</h3>
                    <p>3</p>
                </div>
            </div>
        </div>


        <div class="charts">
            <div class="chart pie">
                <h3>Total Earnings</h3>
                <div class="pie-chart">
                    <span>12.6k</span>
                </div>
            </div>
            <div class="chart bar">
                <h3>Inventory Status</h3>
                <div class="bar-chart">

                </div>
            </div>
        </div>

    </div>

    </body>
</html>
