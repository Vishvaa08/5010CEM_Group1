<?php

require 'vendor/autoload.php';

use Kreait\Firebase\Factory;

$serviceAccount = __DIR__ . '/prvkey.json';

$factory = (new Factory)
    ->withServiceAccount($serviceAccount)
    ->withDatabaseUri('https://traveltrail-39e23-default-rtdb.firebaseio.com/');

$database = $factory->createDatabase();

// Fetch booking data
$bookingsRef = $database->getReference('Admin/newBookings');
$bookings = $bookingsRef->getValue();

$totalEarnings = 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Report</title>
    <link rel="stylesheet" href="css/Report.css">
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
            <li>
                <img src="images/users.png" alt="User Icon">
                <a href="AdminUser.php">User Management</a>
            </li>
            <li>
                <img src="images/inventory.png" alt="Inventory Icon">
                <a href="AdminInventory.php">Inventory Status</a>
            </li>
            <li class="active">
                <img src="images/report_dark.jpg" alt="Report Icon">
                <a href="AdminReport.php">Report</a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <div class="header-left">
                <h2>Dashboard / Report</h2>
                <p>Report</p>
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

        <div class="report-content">
        <div class="report-table">
            <div class="scrollable-table">
                <table>
                    <thead>
                        <tr>
                            <th>Amount (RM)</th>
                            <th>FPX</th>
                            <th>Card</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (is_array($bookings) && !empty($bookings)): ?>
                            <?php foreach ($bookings as $booking): ?>
                                <?php 
                                    $amount = isset($booking['totalPrice']) ? floatval($booking['totalPrice']) : 0;
                                    $isCard = isset($booking['cardDetails']);
                                    $totalEarnings += $amount;
                                ?>
                                <tr>
                                    <td><?php echo number_format($amount, 2); ?></td>
                                    <td><?php echo !$isCard ? '●' : ''; ?></td>
                                    <td><?php echo $isCard ? '●' : ''; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">No bookings found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="total-earnings">
                Total Earnings: <strong>RM <?php echo number_format($totalEarnings, 2); ?></strong>
            </div>
        </div>

        <div class="user-details">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (is_array($bookings) && !empty($bookings)): ?>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo isset($booking['user_name']) ? htmlspecialchars($booking['user_name']) : 'N/A'; ?></td>
                                <td><?php echo isset($booking['checkInDate']) ? htmlspecialchars($booking['checkInDate']) : 'N/A'; ?></td>
                                <td>
                                    <?php if (!empty($booking['receipt_url'])): ?>
                                        <a href="<?php echo htmlspecialchars($booking['receipt_url']); ?>" target="_blank">Attachment</a>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No user payments found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
