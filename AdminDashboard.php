<?php

require 'vendor/autoload.php';

use Kreait\Firebase\Factory;

$serviceAccount = __DIR__ . '/prvkey.json';

$factory = (new Factory)
    ->withServiceAccount($serviceAccount)
    ->withDatabaseUri('https://traveltrail-39e23-default-rtdb.firebaseio.com/');

$database = $factory->createDatabase();
$usersRef = $database->getReference('users');
$users = $usersRef->getValue();
$newUsersCount = 0;

if (is_array($users)) {
    foreach ($users as $user) {
        if (!array_key_exists('status', $user)) {
            $newUsersCount++;
        }
    }
}

$paymentsRef = $database->getReference('Admin/newBookings');
$payments = $paymentsRef->getValue();
$newPaymentsCount = 0;
$totalEarnings = 0;
$cardEarnings = 0;
$fpxEarnings = 0;
$today = date('Y-m-d');

if (is_array($payments)) {
    foreach ($payments as $payment) {
        $totalEarnings += isset($payment['totalPrice']) ? floatval($payment['totalPrice']) : 0;

        if (isset($payment['cardDetails'])) {
            $cardEarnings += floatval($payment['totalPrice']);
        } else {
            $fpxEarnings += floatval($payment['totalPrice']);
        }

        if (isset($payment['date']) && $payment['date'] === $today) {
            $newPaymentsCount++;
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <h2>Dashboard / Home</h2>
                <p>Dashboard</p>
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
                    <p><?php echo $newUsersCount; ?></p>

                </div>
            </div>
            <div class="card-payment">
            <div class="icon-wrapper">
                <img src="images/payment.jpg" alt="Payment Icon">
            </div>
            <div class="card-info">
                <h3>New Payments</h3>
                <p><?php echo $newPaymentsCount; ?></p>
            </div>
        </div>
        </div>

        <div class="charts">
        <div class="chart pie">
            <h3>Total Earnings</h3>
            <div class="pie-chart">
                <canvas id="earningsChart"></canvas>
            </div>
            <div class="chart-legend">
                <div>
                    <div class="color-box" style="background-color: #FFCD56;"></div>
                    <span>Card</span>
                </div>
                <div>
                    <div class="color-box" style="background-color: #4BC0C0;"></div>
                    <span>Bank Transfer (FPX)</span>
                </div>
            </div>
        </div>
        <div class="chart bar inventory-chart">
            <h3>Inventory Status</h3>
            <div class="bar-chart">
                <canvas id="inventoryChart"></canvas>
            </div>
        </div>
    </div>


<script>
        const cardEarnings = <?php echo $cardEarnings; ?>;
        const fpxEarnings = <?php echo $fpxEarnings; ?>;
        const minDisplayValue = 0.01;
        const adjustedCardEarnings = cardEarnings < minDisplayValue ? minDisplayValue : cardEarnings;
        const adjustedFpxEarnings = fpxEarnings < minDisplayValue ? minDisplayValue : fpxEarnings;
        const ctx = document.getElementById('earningsChart').getContext('2d');
        const earningsChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Card', 'Bank Transfer (FPX)'],
                datasets: [{
                    label: 'Total Earnings',
                    data: [adjustedCardEarnings, adjustedFpxEarnings],
                    backgroundColor: ['#FFCD56', '#4BC0C0'],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false 
                    }
                }
            }
        });
</script>

</body>
</html>
