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

$database = $factory->createDatabase();
$bookingsRef = $database->getReference('Admin/newBookings');
$bookings = $bookingsRef->getValue();

// Fetch user data
$usersRef = $database->getReference('users');
$users = $usersRef->getValue();

function getUserEmailByName($userName, $users) {
    foreach ($users as $user) {
        if (isset($user['name']) && $user['name'] === $userName) {
            return $user['email'];
        }
    }
    return 'N/A';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Report</title>
    <link rel="stylesheet" href="css/Report.css">
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<div class="sidebar">
    <div class="sidebar-header">
        <div class="admin-profile">
            <img src="<?php echo htmlspecialchars($pic); ?>" alt="Admin Profile Picture" class="profile-pic">
            <p><?php echo htmlspecialchars($name); ?></p>
        </div>
    </div>
    <ul>
            <li>
                <img src="images/home.webp" alt="Dashboard Icon">
                <a href="AdminDashboard.php">Dashboard</a>
            </li>
            <li>
                <img src="images/packages.png" alt="Packages Icon">
                <a href="AdminPackage.php">Travel Packages</a>
            </li>
            <li>
                <img src="images/users.webp" alt="User Icon">
                <a href="AdminUser.php">User Management</a>
            </li>
            <li>
                <img src="images/inventory.webp" alt="Inventory Icon">
                <a href="AdminInventory.php">Hotel/Flight Management</a>
            </li>
            <li  class="active">
                <img src="images/payments.webp" alt="Report Icon">
                <a href="AdminReport.php">Bookings</a>
            </li>
        </ul>
    <a href="php_functions/logout.php"  class="logout-link">
        <img src="images/logout.png" alt="Logout Icon" class="logout-icon">
        <span>Logout</span>
    </a>
</div>

<div class="main-content">
    <header>
        <div class="header-left">
            <h2>Dashboard / Bookings</h2>
            <p>Bookings</p>
        </div>
        <div class="header-right">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search by email/date/status" onkeyup="filterUsers()">
            </div>
        </div>
    </header>

    <div class="user-details" style="width: 90%; max-width: 1200px; margin: 30px auto;">
        <h1>User Payment Details</h1>
        <table id="userTable">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Date</th>
                    <th>Receipt</th>
                    <th>Status</th> 
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (is_array($bookings) && !empty($bookings)): ?>
                    <?php foreach ($bookings as $bookingId => $booking): ?>
                        <?php if (isset($booking['bankDetails'])): ?>
                            <tr>
                                <?php 
                                // Get email by userName cross-referencing the users data
                                $userEmail = isset($booking['userName']) ? getUserEmailByName($booking['userName'], $users) : 'N/A';
                                ?>
                                <td><?php echo htmlspecialchars($userEmail); ?></td>
                                <td><?php echo isset($booking['checkInDate']) ? htmlspecialchars($booking['checkInDate']) : 'N/A'; ?></td>
                                <td>
                                    <?php if (!empty($booking['paymentProof'])): ?>
                                        <a href="<?php echo htmlspecialchars($booking['paymentProof']); ?>" target="_blank">Attachment</a>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $status = isset($booking['status']) ? $booking['status'] : 'pending';
                                    echo $status === 'approved' ? "<span class='approved'>Approved</span>" : "<span class='unapproved'>Rejected</span>";
                                    ?>
                                </td>
                                <td>
                                    <?php if (!empty($booking['paymentProof'])): ?>
                                        <?php if ($status === 'pending'): ?>
                                            <button onclick="approvePayment('<?php echo $bookingId; ?>')">
                                                <i class="fa fa-check" style="color:green;"></i>
                                            </button>
                                            <button onclick="rejectPayment('<?php echo $bookingId; ?>')">
                                                <i class="fa fa-close" style="color:red;"></i>
                                            </button>
                                        <?php elseif ($status === 'approved'): ?>
                                            <span class="approved">Approved</span>
                                        <?php elseif ($status === 'rejected'): ?>
                                            <span class="rejected">Rejected</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span>N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No user payments found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-database-compat.js"></script>

<script>
    const firebaseConfig = {
        apiKey: "AIzaSyAef9-sjwyQL-MAiUYLUgBO0p68QuRGRNI",
        authDomain: "traveltrail-39e23.firebaseapp.com",
        databaseURL: "https://traveltrail-39e23-default-rtdb.firebaseio.com/",
        projectId: "traveltrail-39e23",
        storageBucket: "traveltrail-39e23.appspot.com",
        messagingSenderId: "91519152452",
        appId: "1:91519152452:web:422ee3957f7b21778fa711"
    };

    firebase.initializeApp(firebaseConfig);

    function approvePayment(bookingId) {
        const dbRef = firebase.database().ref('Admin/newBookings/' + bookingId);
        dbRef.update({
            status: 'approved' 
        }).then(() => {
            alert('Payment approved successfully');
            return firebase.database().ref('userNotifications/' + bookingId).set({
                message: 'Your payment has been approved.',
                timestamp: new Date().toISOString()
            });
        }).then(() => {
            location.reload(); 
        }).catch((error) => {
            console.error('Error approving payment:', error.message);
            alert('Error approving payment.');
        });
    }

    function rejectPayment(bookingId) {
        const dbRef = firebase.database().ref('Admin/newBookings/' + bookingId);
        dbRef.update({
            status: 'rejected' 
        }).then(() => {
            alert('Payment rejected successfully');
            return firebase.database().ref('userNotifications/' + bookingId).set({
                message: 'Your payment has been rejected.',
                timestamp: new Date().toISOString()
            });
        }).then(() => {
            location.reload(); 
        }).catch((error) => {
            console.error('Error rejecting payment:', error.message);
            alert('Error rejecting payment.');
        });
    }

    function filterUsers() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        const table = document.getElementById('userTable');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            let found = false;

            for (let j = 0; j < cells.length; j++) {
                if (cells[j]) {
                    const cellValue = cells[j].textContent.toLowerCase();
                    if (cellValue.indexOf(input) > -1) {
                        found = true;
                        break;
                    }
                }
            }

            if (found) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    }
</script>
</body>
</html>
