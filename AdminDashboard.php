<?php

session_start();
include 'firebase_connection.php'; 

$pic = '';

if (isset($_SESSION['userName'])) {
    $pic = $_SESSION['profileImage'];
    $name = $_SESSION['userName'];
} else {
    $pic = 'images/user.png';
    $name = 'Admin';
}


$db = $database;
// Fetch total number of users
$usersSnapshot = $db->getReference('users')->getValue();
$totalUsers = is_array($usersSnapshot) ? count($usersSnapshot) : 0;

// Fetch total number of countries
$countriesSnapshot = $db->getReference('Packages')->getValue();
$totalCountries = is_array($countriesSnapshot) ? count($countriesSnapshot) : 0;

// Fetch payment data from Firebase
$paymentsRef = $db->getReference('Admin/newBookings');
$payments = $paymentsRef->getValue();
$totalEarnings = 0;
$cardEarnings = 0;
$fpxEarnings = 0;

if (is_array($payments)) {
    foreach ($payments as $payment) {
        // Add up the total earnings
        $totalEarnings += isset($payment['totalPrice']) ? floatval($payment['totalPrice']) : 0;

        // Check if payment was made via card
        if (isset($payment['cardDetails'])) {
            $cardEarnings += floatval($payment['totalPrice']);
        } else {
            // Otherwise, assume it's an FPX payment
            $fpxEarnings += floatval($payment['totalPrice']);
        }
    }
}

// Fetch report data for the selected month
if (isset($_GET['month'])) {
    $selectedMonth = $_GET['month'];

    // Format the start and end of the month
    $startOfMonth = $selectedMonth . '-01';
    $endOfMonth = date("Y-m-t", strtotime($startOfMonth));

    // Fetch booking data from Firebase (or SQL database)
    $bookingsRef = $db->getReference('Admin/newBookings');
    $bookings = $bookingsRef->getValue();

    $totalEarnings = 0;
    $cardEarnings = 0;
    $fpxEarnings = 0;

    // Filter bookings by the selected month
    if (is_array($bookings)) {
        foreach ($bookings as $booking) {
            $orderDate = isset($booking['orderDate']) ? $booking['orderDate'] : null;

            // Check if the booking was made in the selected month
            if ($orderDate >= $startOfMonth && $orderDate <= $endOfMonth) {
                $totalEarnings += floatval($booking['totalPrice']);

                // Check if payment was made via card
                if (isset($payment['cardDetails'])) {
                    $cardEarnings += floatval($payment['totalPrice']);
                } elseif (isset($payment['bankDetails'])) {
                    $fpxEarnings += floatval($payment['totalPrice']);
                }
            }
        }
    }

    // Output earnings for the selected month
    echo json_encode([
        'cardEarnings' => $cardEarnings,
        'fpxEarnings' => $fpxEarnings
    ]);

} else {
    // Handle case where 'month' is not set, you can return default values or an error message
    echo json_encode([
        'error' => 'Month parameter is missing',
        'cardEarnings' => 0,
        'fpxEarnings' => 0
    ]);
}

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>     

</head>
<body>
<div class="sidebar">
    <div class="sidebar-header">
        <div class="admin-profile">
        <div class="admin-profile">
        <img src="<?php echo htmlspecialchars($pic); ?>" alt="Admin Profile Picture" class="profile-pic">
        <p><?php echo htmlspecialchars($name); ?></p>
        </div>
    </div>
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
                <img src="images/payments.png" alt="Report Icon">
                <a href="AdminReport.php">Bookings</a>
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
                <div class="notification-container">
                    <!-- Notification Icon -->
                    <img src="images/notifications.png" alt="Notifications Icon" class="notification-icon" id="notificationIcon">
                </div>
                <div class="header-right d-flex align-items-center">
                    <a href="login.php" class="logout-link">
                        <img src="images/logout.png" alt="Logout Icon" class="logout-icon">
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </header>

        <div class="info-box-container">
        <!-- Total Users Box -->
        <div class="info-box" id="totalUsersBox">
            <div class="info-box-row">
                <div class="info-box-icon">
                    <img src="images/total_users.png" alt="Total Users Icon">
                </div>
                <div class="info-box-content">
                    <h3>Total Users</h3>
                    <p id="totalUsersCount">10</p>
                </div>
            </div>
        </div>

        <!-- Total Countries Box -->
        <div class="info-box" id="totalCountriesBox">
            <div class="info-box-row">
                <div class="info-box-icon">
                    <img src="images/countries.png" alt="Total Countries Icon">
                </div>
                <div class="info-box-content">
                    <h3>Total Countries</h3>
                    <p id="totalCountriesCount">6</p>
                </div>
            </div>
        </div>
    </div>
    <div class="charts">
    <div class="chart pie">
        <h3>Total Earnings</h3>
        <div class="pie-chart">
            <canvas id="earningsChart"></canvas>
        </div>
        <!-- Legend for Chart Colors -->
        <div class="chart-legend">
            <div class="legend-item">
                <div class="color-box" style="background-color: #FFCD56;"></div>
                <span>Card</span>
            </div>
            <div class="legend-item">
                <div class="color-box" style="background-color: #4BC0C0;"></div>
                <span>Bank Transfer (FPX)</span>
            </div>
        </div>
    </div>

    <div class="chart bar inventory-chart">
        <h3>Total Payments</h3>
        <label for="monthPicker">Select Month: </label>
        <input type="month" id="monthPicker">
        <div class="bar-chart">
            <canvas id="paymentsChart"></canvas>
        </div>
        <button id="generateReport" class="generate-btn">Generate Report</button>
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

    let paymentsChart = null; // Declare the chart variable globally

function updateChartForMonth(month) {
    return fetch(`<?php echo $_SERVER['PHP_SELF']; ?>?month=${month}`)
        .then(response => response.json())
        .then(data => {
            const paymentData = [data.cardEarnings, data.fpxEarnings];

            if (paymentsChart) {
                paymentsChart.destroy(); // Destroy previous instance
            }

            const paymentCtx = document.getElementById('paymentsChart').getContext('2d');
            paymentsChart = new Chart(paymentCtx, {
                type: 'bar',
                data: {
                    labels: paymentLabels,
                    datasets: [{
                        label: 'Total Payments',
                        data: paymentData,
                        backgroundColor: ['rgba(255, 205, 86, 0.7)', 'rgba(54, 162, 235, 0.7)']
                    }]
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

            return paymentData;
        });
}

        

        document.getElementById('generateReport').addEventListener('click', function () {
            const selectedMonth = document.getElementById('monthPicker').value;

            if (!selectedMonth) {
                alert('Please select a month.');
                return;
            }

            updateChartForMonth(selectedMonth).then(paymentData => {
                html2canvas(document.getElementById('paymentsChart')).then(function (canvas) {
                    const imgData = canvas.toDataURL('image/png');
                    const { jsPDF } = window.jspdf;
                    const pdf = new jsPDF();

                    pdf.addImage(imgData, 'PNG', 10, 10, 180, 100);

                    pdf.setFontSize(18);
                    pdf.text(`Total Payments Report for ${selectedMonth}`, 10, 130);
                    pdf.setFontSize(12);
                    pdf.text(`Card: RM${paymentData[0]}`, 10, 140);
                    pdf.text(`Bank Transfer (FPX): RM${paymentData[1]}`, 10, 150);

                    pdf.save(`Payments_Report_${selectedMonth}.pdf`);
                });
            });
        });

            document.getElementById('monthPicker').addEventListener('change', function () {
            const selectedMonth = this.value;
            updateChartForMonth(selectedMonth);
        });
            document.getElementById('totalUsersCount').textContent = '<?php echo $totalUsers; ?>';
            document.getElementById('totalCountriesCount').textContent = '<?php echo $totalCountries; ?>';

        
            document.getElementById('notificationIcon').addEventListener('click', function() {
            window.location.href = 'messages.php';  
        });

    </script>

</body>
</html>
