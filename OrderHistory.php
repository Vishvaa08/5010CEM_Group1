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
                <a class="nav-link" href="#home">Home</a>
                <a class="nav-link" href="#about">About</a>
                <a class="nav-link" href="#contact">Contact</a>
                <div class="user-profile"></div>
            </div>
        </div>

        <div id="orders">
            <h2>Your Orders <span class="order-count">2</span></h2>

            <div class="order-item">
                <div class="order-info">
                    <div class="order-details">
                        <p><strong>Order Placed:</strong> August 2, 2024</p>
                        <p><strong>Total:</strong> RM3050.00</p>
                        <p><strong>Location:</strong> Japan, Tokyo</p>
                    </div>
                    <div class="order-actions">
                        <p><strong>Order #:</strong> 002356</p>
                        <button onclick="toggleDetails(this)" class="action-link">View Details</button>
                    </div>
                </div>
                <div class="order-details-expanded" style="display: none;">
                    <img src="tokyo.jpg" alt="Tokyo" class="order-img">
                    <div class="order-booking-info">
                        <p><strong>Hotel:</strong> 3-Star, Double</p>
                        <p>2 rooms</p>
                        <p><strong>Flight:</strong> Economy, 1-way, 4 seats</p>
                        <p><strong>Vehicle:</strong> 4-seater, September 5, 2024</p>
                        <button class="reorder-button">Order again</button>
                    </div>
                </div>
            </div>

            <div class="order-item">
                <div class="order-info">
                    <div class="order-details">
                        <p><strong>Order Placed:</strong> November 12, 2024</p>
                        <p><strong>Total:</strong> RM5050.00</p>
                        <p><strong>Location:</strong> Korea, Seoul</p>
                    </div>
                    <div class="order-actions">
                        <p><strong>Order #:</strong> 004569</p>
                        <button onclick="toggleDetails(this)" class="action-link">View Details</button>
                    </div>
                </div>
                <div class="order-details-expanded" style="display: none;">
                    <img src="seoul.jpg" alt="Seoul" class="order-img">
                    <div class="order-booking-info">
                        <p><strong>Hotel:</strong> 4-Star, Double</p>
                        <p>1 room</p>
                        <p><strong>Flight:</strong> Business, 1-way, 2 seats</p>
                        <p><strong>Vehicle:</strong> 6-seater, November 20, 2024</p>
                        <button class="reorder-button">Order again</button>
                    </div>
                </div>
            </div>

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
