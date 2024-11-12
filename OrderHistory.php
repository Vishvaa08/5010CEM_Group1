<?php
session_start();
include 'firebase_connection.php';

if (isset($_SESSION['userName'])) {
    $userName = $_SESSION['userName'];
    $pic = $_SESSION['profileImage'] ?? 'images/user.png';
} else {
    header("Location: login.php");
    exit();
}

$bookingsRef = $database->getReference('Admin/newBookings');
$bookingsData = $bookingsRef->getValue() ?: [];

$userBookings = [];
foreach ($bookingsData as $bookingId => $bookingDetails) {
    if (isset($bookingDetails['userName']) && $bookingDetails['userName'] === $userName) {
        $userBookings[$bookingId] = $bookingDetails;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet" href="css/reorder.css">
</head>
<body>

    <div id="header">
        <div id="left-nav">
            <a href="#home">
                <div class="logo-container">
                    <p style="color: white; font-size: 25px; font-family: 'Joti One', serif;">TT</p>
                </div>
            </a>
            <h1>TravelTrail</h1>
        </div>

            <div id="right-nav">
                <a class="nav-link" href="#home">Home</a>
                <a class="nav-link" href="#about">About</a>
                <a class="nav-link" href="#contact">Contact</a>
                <a href="php_functions/user_login_check.php" class="user-profile"><img src="<?php echo htmlspecialchars($pic, ENT_QUOTES, 'UTF-8'); ?>" style="width:75px; height:75px; border-radius:50%; object-fit:cover;"></a>
            </div>
        </div>

       
        <div id="orders">
        <h2>Your Orders <span class="order-count"><?= count($userBookings); ?></span></h2>

        <?php if (!empty($userBookings)) : ?>
            <?php foreach ($userBookings as $bookingId => $booking) : ?>
                <div class="order-item">
                    <div class="order-info">
                    <div class="order-details">
        <div class="order-row">
            <div class="order-column">
                <p class="label">Order Placed</p>
                <p class="value"><?= htmlspecialchars($booking['orderDate']); ?></p>
            </div>
            <div class="order-column">
                <p class="label">Total</p>
                <p class="value">RM<?= htmlspecialchars($booking['totalPrice']); ?></p>
            </div>
            <div class="order-column">
                <p class="label">Location</p>
                <p class="value"><?= htmlspecialchars($booking['country']); ?>, <?= htmlspecialchars($booking['city']); ?></p>
            </div>
        </div>
    </div>

        <div class="order-actions">
            <p>Order #<?= htmlspecialchars($bookingId); ?></p>
            <button class="action-link" onclick="showOrderDetails('<?= $bookingId; ?>')">View Details</button>
        </div>
    </div>

    <div id="order-details-<?= $bookingId; ?>" class="order-details-expanded">
    <img src="https://firebasestorage.googleapis.com/v0/b/traveltrail-39e23.appspot.com/o/<?= urlencode($booking['city']); ?>.jpg?alt=media&token=" alt="<?= htmlspecialchars($booking['city']); ?>" class="order-img">
    <div class="order-booking-info">
            <p>Hotel: <?= htmlspecialchars($booking['hotelID']); ?>, <?= htmlspecialchars($booking['roomType']); ?></p>
            <p>Flight: <?= htmlspecialchars($booking['flightType']); ?>, <?= htmlspecialchars($booking['numTickets']); ?> seats</p>
            <p>Vehicle: <?= htmlspecialchars($booking['vehicleType']); ?></p>
            <p>Date: <?= htmlspecialchars($booking['checkInDate']); ?></p>
        </div>
        <form action="citydetails.php" method="GET">
            <input type="hidden" name="city" value="<?= htmlspecialchars($booking['city']); ?>">
            <input type="hidden" name="country" value="<?= htmlspecialchars($booking['country']); ?>">
            <button type="submit" class="reorder-button">Order again</button>
        </form>
        </div>
    </div>
        <?php endforeach; ?>
        <?php else : ?>
        <p>No orders found.</p>
    <?php endif; ?>
    </div>



<script>
        function showOrderDetails(orderId) {
        var detailsDiv = document.getElementById('order-details-' + orderId);
        if (detailsDiv.style.display === "none" || detailsDiv.style.display === "") {
            detailsDiv.style.display = "flex";
        } else {
            detailsDiv.style.display = "none";
        }
        }
        
</script>

</body>
</html>
