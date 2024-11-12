<?php
session_start();
include 'firebase_connection.php';

// Fetch user details
$pic = isset($_SESSION['profileImage']) ? $_SESSION['profileImage'] : 'images/user.png';

// Check for user session
if (!isset($_SESSION['userName'])) {
    header("Location: login.php");
    exit();
}

$bookingsRef = $database->getReference('Admin/newBookings');
$bookingsData = $bookingsRef->getValue() ?: [];
$userBookings = [];
foreach ($bookingsData as $bookingId => $bookingDetails) {
    if (isset($bookingDetails['userName']) && $bookingDetails['userName'] === $_SESSION['userName']) {
        $userBookings[$bookingId] = $bookingDetails;
    }
}

function fetchCityImage($country, $city) {
    global $database;
    $cityImageRef = $database->getReference("Packages/{$country}/{$city}/CityImage");
    $cityImageUrl = $cityImageRef->getValue();

    return $cityImageUrl ?: 'images/error.jpg';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet" href="css/reorder.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Joti+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
</head>
<body>

    <div id="header">
        <div id="left-nav">
            <a href="index.php">
                <div class="logo-container">
                    <p style="color: white; font-size: 25px; font-family: 'Joti One', serif;">TT</p>
                </div>
            </a>
            <h1>TravelTrail</h1>
        </div>

        <div id="right-nav">
            <a class="nav-link" href="index.php#home">Home</a>
            <a class="nav-link" href="index.php#about">About</a>
            <a class="nav-link" href="index.php#contact">Contact</a>
            <a href="php_functions/user_login_check.php" class="user-profile"><img src="<?php echo $pic; ?>" style="width:75px; height:75px; border-radius:50%; object-fit:cover;"></a>
        </div>
    </div>
    </div>

       
        <div id="orders">
    <h2>Your Orders <span class="order-count"><?= count($userBookings); ?></span></h2>

    <?php if (!empty($userBookings)) : ?>
        <?php foreach ($userBookings as $bookingId => $booking) : ?>
            <?php 
                $cityImage = fetchCityImage($booking['country'], $booking['city']);
            ?>
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

                <!-- Order Details Section -->
                <div id="order-details-<?= $bookingId; ?>" class="order-details-expanded" style="display: none;">
                    <img src="<?= htmlspecialchars($cityImage); ?>" 
                         alt="<?= htmlspecialchars($booking['city']); ?>" class="order-img">
                    <div class="order-booking-info">
                        <p>Hotel: <?= htmlspecialchars($booking['hotelID']); ?>, <?= htmlspecialchars($booking['roomType']); ?></p>
                        <p>Flight: <?= htmlspecialchars($booking['flightType']); ?>, <?= htmlspecialchars($booking['numTickets']); ?> seats</p>
                        <p>Vehicle: <?= htmlspecialchars($booking['vehicleType']); ?></p>
                        <p>Date: <?= htmlspecialchars($booking['checkInDate']); ?></p>
                    </div>
                    <form action="booking.php" method="GET">
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
