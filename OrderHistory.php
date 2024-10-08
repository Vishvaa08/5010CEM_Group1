<?php
require 'vendor/autoload.php'; 

use Kreait\Firebase\Factory;

$serviceAccount = __DIR__ . '/prvkey.json';

$factory = (new Factory)
    ->withServiceAccount($serviceAccount)
    ->withDatabaseUri('https://traveltrail-39e23-default-rtdb.firebaseio.com/');

$database = $factory->createDatabase();
session_start();
$userId = $_SESSION['user_id']; 
$ordersRef = $database->getReference('orders/' . $userId); 
$orders = $ordersRef->getValue();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet" type="text/css" href="css/reorder.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Joti+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@100;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <div id="home">
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
                <div class="user-profile"></div>
            </div>
        </div>

        <div id="orders">
            <h2>Your Orders <span class="order-count"><?php echo is_array($orders) ? count($orders) : 0; ?></span></h2>

            <?php if (is_array($orders) && count($orders) > 0): ?>
                <?php foreach ($orders as $orderId => $order): ?>
                    <div class="order-item">
                        <div class="order-info">
                            <div class="order-details">
                                <p><strong>Order Placed:</strong> <?php echo $order['orderPlaced']; ?></p>
                                <p><strong>Total:</strong> RM<?php echo number_format($order['total'], 2); ?></p>
                                <p><strong>Location:</strong> <?php echo $order['location']; ?></p>
                            </div>
                            <div class="order-actions">
                                <p><strong>Order #:</strong> <?php echo $orderId; ?></p>
                                <button onclick="toggleDetails(this)" class="action-link">View Details</button>
                            </div>
                        </div>
                        <div class="order-details-expanded" style="display: none;">
                            <img src="<?php echo $order['image']; ?>" alt="Order Image" class="order-img">
                            <div class="order-booking-info">
                                <p><strong>Hotel:</strong> <?php echo $order['hotel']; ?></p>
                                <p><?php echo $order['rooms']; ?> rooms</p>
                                <p><strong>Flight:</strong> <?php echo $order['flight']; ?></p>
                                <p><strong>Vehicle:</strong> <?php echo $order['vehicle']; ?></p>
                                <a href="booking.php" class="reorder-button">Order again</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No orders found.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleDetails(button) {
            const orderItem = button.closest('.order-item');
            const details = orderItem.querySelector('.order-details-expanded');
            if (details.style.display === 'none') {
                details.style.display = 'flex';
                button.textContent = 'Hide Details';
            } else {
                details.style.display = 'none';
                button.textContent = 'View Details';
            }
        }
    </script>
</body>
</html>
