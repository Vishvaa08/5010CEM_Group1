<?php
include 'firebase_connection.php'; 
session_start();
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    header('Location: AdminLogin.php');
    exit();
}

$db = $factory->createDatabase(); 

$userSnapshot = $db->getReference('users/' . $userId)->getValue();
if (!$userSnapshot || $userSnapshot['role'] !== 'admin' || $userSnapshot['status'] !== 'approved') {
    header('Location: index.php');
    exit();
}

$adminName = $userSnapshot['name'] ?? 'Admin';

// Fetch user data
$usersRef = $db->getReference('users');
$users = $usersRef->getValue();
$newUsersCount = 0;
if (is_array($users)) {
    foreach ($users as $user) {
        if (!array_key_exists('status', $user)) {
            $newUsersCount++;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['userId'])) {
    $userIdToApprove = $_POST['userId'];

    $db->getReference('users/' . $userIdToApprove)
      ->update([
          'status' => 'approved',
          'role' => 'admin' 
      ]);

    header("Location: AdminDashboard.php");
    exit;
}

// Fetch payment data
$paymentsRef = $db->getReference('Admin/newBookings');
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

// Fetch inventory data by country
$countryInventoryStatus = [];
$countriesData = $db->getReference('Packages')->getValue() ?: [];

foreach ($countriesData as $countryName => $countryData) {
    $totalAvailableRooms = 0;
    $totalAvailableSeats = 0;

    foreach ($countryData as $cityName => $cityData) {
        if (isset($cityData['Hotels'])) {
            foreach ($cityData['Hotels'] as $hotelData) {
                if (isset($hotelData['Rooms'])) {
                    foreach ($hotelData['Rooms'] as $roomData) {
                        $availability = isset($roomData['Availability']) ? (int)$roomData['Availability'] : 0;
                        $totalAvailableRooms += $availability;
                    }
                }
            }
        }

        if (isset($cityData['Flights'])) {
            foreach ($cityData['Flights'] as $flightData) {
                $seats = isset($flightData['Seats']) ? (int)$flightData['Seats'] : 0;
                $totalAvailableSeats += $seats;
            }
        }
    }

    $countryInventoryStatus[$countryName] = [
        'totalAvailableRooms' => $totalAvailableRooms,
        'totalAvailableSeats' => $totalAvailableSeats
    ];
}

// Fetch notifications
$reference = $db->getReference('adminNotifications');
$messages = $reference->getValue();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/Dashboard.css">
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
                <a href="AdminInventory.php">Hotel/Flight Management</a>
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
            <div class="header-right d-flex align-items-center">
            <div class="icon-wrapper" id="adminApprovalIcon">
                <img src="images/admin_approval.png" alt="Admin Approval Icon" class="admin-approval-icon">
                </div>
                <div class="admin-approval-dropdown" id="adminApprovalDropdown">
                    <div class="admin-approval-header">
                        <h4>Pending Approvals</h4>
                    </div>
                    <div class="admin-approval-list">
                        <?php
                        $pendingUsers = $db->getReference('users')->orderByChild('status')->equalTo('pending')->getValue();
                        
                        if ($pendingUsers): ?>
                            <?php foreach ($pendingUsers as $userId => $user): ?>
                                <div class="admin-approval-item">
                                    <div class="approval-details">
                                        <p class="approval-name"><?php echo htmlspecialchars($user['name']); ?></p>
                                        <p class="approval-email"><?php echo htmlspecialchars($user['email']); ?></p>
                                        <p class="approval-phone"><?php echo htmlspecialchars($user['phone']); ?></p>
                                        <p class="approval-role"><?php echo htmlspecialchars($user['role']); ?></p>
                                    </div>
                                    <form method="POST" action="AdminDashboard.php" class="approval-form">
                                        <input type="hidden" name="userId" value="<?php echo $userId; ?>">
                                        <button type="submit" class="approve-button">Approve</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-approvals">No pending approvals.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="notification-container">
                    <img src="images/notifications.png" alt="Notifications Icon" class="notification-icon" id="notificationIcon">
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-header">
                            <h4>Notifications</h4>
                        </div>
                        <div class="notification-list">
                            <?php if ($messages): ?>
                                <?php foreach ($messages as $message): ?>
                                    <div class="notification-item">
                                        <div class="notification-details">
                                            <p class="notification-title"><?php echo htmlspecialchars($message['userName']); ?></p> 
                                            <p class="notification-email"><?php echo htmlspecialchars($message['userEmail']); ?></p> 
                                            <p class="notification-message"><?php echo htmlspecialchars($message['userMessage']); ?></p> 
                                            <p class="notification-timestamp"><?php echo htmlspecialchars($message['timestamp']); ?></p> 
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="no-notifications">No notifications available.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="user-wrapper">
                    <p>Hi, <?php echo htmlspecialchars($adminName); ?>!</p> 
                    <a href="AdminLogin.php">
                        <img src="images/logout.png" alt="Logout Icon" class="logout-icon">
                    </a>
                </div>
            </div>
        </header>

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
                <h3>Inventory Status by Country</h3>
                <div class="bar-chart">
                    <canvas id="inventoryChart"></canvas>
                </div>
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

        const countryLabels = <?php echo json_encode(array_keys($countryInventoryStatus)); ?>;
        const availableRoomsData = <?php echo json_encode(array_column($countryInventoryStatus, 'totalAvailableRooms')); ?>;
        const availableSeatsData = <?php echo json_encode(array_column($countryInventoryStatus, 'totalAvailableSeats')); ?>;

        const inventoryCtx = document.getElementById('inventoryChart').getContext('2d');
        const inventoryChart = new Chart(inventoryCtx, {
            type: 'bar',
            data: {
                labels: countryLabels,
                datasets: [
                    {
                        label: 'Available Hotel Rooms',
                        data: availableRoomsData,
                        backgroundColor: 'rgba(255, 205, 86, 0.7)', 
                    },
                    {
                        label: 'Available Flight Seats',
                        data: availableSeatsData,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)', 
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        const notificationIcon = document.getElementById('notificationIcon');
        const notificationDropdown = document.getElementById('notificationDropdown');

        notificationIcon.addEventListener('click', function() {
            notificationDropdown.classList.toggle('show');
        });

        window.addEventListener('click', function(event) {
            if (!notificationIcon.contains(event.target) && !notificationDropdown.contains(event.target)) {
                notificationDropdown.classList.remove('show');
            }
        });

        const adminApprovalIcon = document.getElementById('adminApprovalIcon');
        const adminApprovalDropdown = document.getElementById('adminApprovalDropdown');

        adminApprovalIcon.addEventListener('click', function() {
            adminApprovalDropdown.classList.toggle('show');
        });

        window.addEventListener('click', function(event) {
            if (!adminApprovalIcon.contains(event.target) && !adminApprovalDropdown.contains(event.target)) {
                adminApprovalDropdown.classList.remove('show');
            }
        });

   </script>
</body>
</html>
