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

// Fetch payment data
$paymentsRef = $db->getReference('Admin/newBookings');
$payments = $paymentsRef->getValue();
$totalEarnings = 0;
$cardEarnings = 0;
$fpxEarnings = 0;

if (is_array($payments)) {
    foreach ($payments as $payment) {
        $totalEarnings += isset($payment['totalPrice']) ? floatval($payment['totalPrice']) : 0;

        if (isset($payment['cardDetails'])) {
            $cardEarnings += floatval($payment['totalPrice']);
        } else {
            $fpxEarnings += floatval($payment['totalPrice']);
        }
    }
}

// Fetch payment data grouped by month for the entire year
if (isset($_GET['action']) && $_GET['action'] === 'fetchMonthlyEarnings') {
    $year = date('Y'); 
    $monthlyEarnings = [
        'card' => array_fill(1, 12, 0),
        'fpx' => array_fill(1, 12, 0)
    ];

    // Fetch all bookings from Firebase again for monthly calculation
    $payments = $db->getReference('Admin/newBookings')->getValue();

    if (is_array($payments)) {
        foreach ($payments as $paymentID => $payment) {
            $totalPrice = 0;
            $orderDate = null;

            // Check for card details
            if (isset($payment['cardDetails'])) {
                $totalPrice = floatval($payment['totalPrice']);
                $orderDate = $payment['orderDate'] ?? null;
            } 
            // Check for bank details (FPX)
            elseif (isset($payment['bankDetails'])) {
                $totalPrice = floatval($payment['totalPrice']);
                $orderDate = $payment['orderDate'] ?? null;
            }

            // Process the payment if orderDate is set
            if ($orderDate) {
                $dateTime = strtotime($orderDate);
                $month = date('n', $dateTime); // Numeric representation of month (1-12)
                $yearOfPayment = date('Y', $dateTime);

                // Only accumulate earnings for the current year
                if ($yearOfPayment == $year) {
                    if (isset($payment['cardDetails'])) {
                        $monthlyEarnings['card'][$month] += $totalPrice;
                    } elseif (isset($payment['bankDetails'])) {
                        $monthlyEarnings['fpx'][$month] += $totalPrice;
                    }
                }
            }
        }
    }

    // Return the monthly earnings data as JSON
    header('Content-Type: application/json');
    echo json_encode($monthlyEarnings);
    exit; // Stop further execution
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
                    <a href= "messages.php">
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
    <h3>Monthly Earnings Report for <?php echo date('Y'); ?></h3>
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
                    display: false // We manually create the legend
                }
            }
        }
    });

    // Declare paymentsChart in the global scope
let paymentsChart;

function updateChartWithMonthlyData() {
    fetch('<?php echo $_SERVER['PHP_SELF']; ?>?action=fetchMonthlyEarnings') // AJAX request
        .then(response => response.json())
        .then(data => {
            console.log(data); // Debugging: log the data received

            const months = Array.from({ length: 12 }, (_, i) => i + 1); 
            const cardEarnings = months.map(month => data.card[month] || 0); 
            const fpxEarnings = months.map(month => data.fpx[month] || 0);   

            const totalCardEarnings = cardEarnings.reduce((acc, curr) => acc + curr, 0);
            const totalFpxEarnings = fpxEarnings.reduce((acc, curr) => acc + curr, 0);

            // Check totals before rendering
            console.log("Card Earnings:", totalCardEarnings);
            console.log("FPX Earnings:", totalFpxEarnings);

            if (typeof paymentsChart !== 'undefined') {
                paymentsChart.destroy(); 
            }

            const paymentCtx = document.getElementById('paymentsChart').getContext('2d');
            paymentsChart = new Chart(paymentCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [
                        {
                            label: 'Card Payments',
                            data: cardEarnings,
                            backgroundColor: 'rgba(255, 205, 86, 0.7)' 
                        },
                        {
                            label: 'Bank Transfer (FPX)',
                            data: fpxEarnings,
                            backgroundColor: 'rgba(54, 162, 235, 0.7)' 
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
        


            // Event listener for generating the report
            document.getElementById('generateReport').addEventListener('click', function () {
                html2canvas(document.getElementById('paymentsChart')).then(function (canvas) {
                    const imgData = canvas.toDataURL('image/png');
                    const { jsPDF } = window.jspdf;
                    const pdf = new jsPDF();

                    pdf.addImage(imgData, 'PNG', 10, 10, 180, 100);
                    pdf.setFontSize(18);
                    pdf.text('Total Earnings Report', 10, 130);
                    pdf.setFontSize(12);
                    pdf.text(`Card Total: RM${totalCardEarnings.toFixed(2)}`, 10, 140);
                    pdf.text(`Bank Transfer (FPX) Total: RM${totalFpxEarnings.toFixed(2)}`, 10, 150);

                    pdf.save('Payments_Report.pdf');
                });
            });
        });
}

// Call the function to load the data on page load
updateChartWithMonthlyData();



            document.getElementById('totalUsersCount').textContent = '<?php echo $totalUsers; ?>';
            document.getElementById('totalCountriesCount').textContent = '<?php echo $totalCountries; ?>';

        
            document.getElementById('notificationIcon').addEventListener('click', function() {
            window.location.href = 'messages.php';  
        });

    </script>

</body>
</html>
