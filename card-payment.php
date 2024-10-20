<?php
session_start();

$pic = '';

if (isset($_SESSION['userName'])) {
    $name = $_SESSION['userName'];
    $pic = $_SESSION['profileImage'];
    $email = $_SESSION['userEmail'];
} else {
    $name = 'Error:Name not found';
    $pic = 'images/user.png';
    $email = 'N/A';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card</title>
    <link rel="stylesheet" type="text/css" href="css/card-payment.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Joti+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap"
        rel="stylesheet">

    <style>
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.7);
            justify-content: center;
            align-items: center;
        }

        .popup-overlay h2{
            margin-bottom: 3vh;
            font-size: 40px;
        }

        .orderID{
            font-size: 60px;
            margin-bottom: 0;
            margin-top: 5px;
        }

        .orderText{
            padding-right: 10px;
            padding-left: 10px;
        }

        .popup-content {
            background: black;
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            font-family: 'Joti One', sans-serif;
            font-size: 20px;
            height: 40vh;
        }
    </style>

</head>

<body>

    <?php

    include 'firebase_connection.php';
    include 'firebase_data.php';

    $itineraries = isset($_GET['itineraries']) ? $_GET['itineraries'] : [];
    $itineraryList = [];
    $totalItineraryPrice = 0;

    foreach ($itineraries as $itinerary) {
        $itineraryData = explode('|', $itinerary);
        if (isset($itineraryData[0]) && isset($itineraryData[1])) {
            $itineraryName = $itineraryData[0];
            $itineraryPrice = $itineraryData[1];
            $itineraryList[] = ['name' => $itineraryName, 'price' => $itineraryPrice];
            $totalItineraryPrice += $itineraryPrice;
        }
    }

    if (isset($_GET['vehicle'])) {
        $vehicleData = explode('|', $_GET['vehicle']);
        $vehicleType = isset($vehicleData[0]) ? $vehicleData[0] : '';
        $vehiclePrice = isset($vehicleData[1]) ? $vehicleData[1] : 0;
    }

    if (isset($_GET['room'])) {
        $roomData = explode('|', $_GET['room']);
        $roomType = isset($roomData[0]) ? $roomData[0] : '';
        $roomPrice = isset($roomData[1]) ? $roomData[1] : 0;
    }

    if (isset($_GET['check-in-date'])) {
        $checkInDate = $_GET['check-in-date'];
    }

    if (isset($_GET['check-out-date'])) {
        $checkOutDate = $_GET['check-out-date'];
    }

    if (isset($_GET['flight'])) {
        $dataFlights = explode('|', $_GET['flight']);
        $flightType = isset($dataFlights[0]) ? $dataFlights[0] : '';
        $flightPrice = isset($dataFlights[1]) ? $dataFlights[1] : 0;
    }

    if (isset($_GET['ticket-type'])) {
        $ticketType = $_GET['ticket-type'];
    }

    if (isset($_GET['tickets'])) {
        $numTickets = $_GET['tickets'];
    }

    if ($ticketType == 2) {
        $calc = ($roomPrice + $vehiclePrice + $totalItineraryPrice) + (($flightPrice + 150) * $numTickets);
    } else {
        $calc = ($roomPrice + $vehiclePrice + $totalItineraryPrice) + ($flightPrice * $numTickets);
    }

    ?>

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

    <div id="card-background">
        <div id="card-container">
            <div id="card-top">
                <div class="left">
                    <div class="text-title">
                        <?php
                        echo $city;
                        ?>
                    </div>
                </div>
                <div class="right">
                    <div class="text-price">
                        <?php
                        echo 'RM ' . $calc;
                        ?>
                    </div>
                </div>
            </div>
            <div id="hr"></div>
            <div id="card-num-container">
                <div class="card-num-text">
                    <div class="text-text">Card Number</div>
                </div>
                <div class="card-input">
                    <input type="text" id="card-number-input" minlength="19" maxlength="19"
                        oninput="this.value = this.value.replace(/\D/g, '').replace(/(.{4})(?=.)/g, '$1-').slice(0, 19);"
                        placeholder="xxxx-xxxx-xxxx-xxxx" class="card-num-input">
                </div>
            </div>
            <div id="middle">
                <div class="expiry-text">
                    <div class="text-text">Expiry</div>
                </div>
                <div class="cvv-text">
                    <div class="text-text">CVV</div>
                </div>
            </div>
            <div id="middle">
                <div class="expiry-input">
                    <input type="text" id="card-expiry-input" maxlength="5" oninput="this.value = this.value.replace(/\D/g, '').slice(0,2) + (this.value.length >= 2 ? '/' : '') + this.value.replace(/\D/g, '').slice(2,4);" placeholder="MM/YY" class="expiry-num-input" required>
                </div>
                <div class="cvv-input">
                    <input type="text" id="card-cvv-input" maxlength="3" oninput="this.value = this.value.replace(/\D/g, '').slice(0,3);" placeholder="XXX" class="cvv-num-input" required>
                </div>
            </div>
            <div id="button-container">
                <button class="confirm-btn">Confirm</button>
            </div>
        </div>
    </div>

    <div class="popup-overlay" id="popupOverlay">
        <div class="popup-content">
            <h2>Order ID</h2>
            <p class="orderID"><strong id="bookingId">1234</strong></p>
            <p class="orderText">We will notify you once your payment has been processed.</p>
            <button class="close-popup" onclick="closePopup()">Close</button>
        </div>
    </div>

    <script>
        document.querySelector('.confirm-btn').addEventListener('click', submit);

        function submit() {
            const cardNumber = document.getElementById('card-number-input').value.replace(/\D/g, '');
            const expiryNumber = document.getElementById('card-expiry-input').value;
            const cvvNumber = document.getElementById('card-cvv-input').value;

            <?php
            $name = $_SESSION['userName'];
            ?>

            const today = new Date();
            const dateToday = today.getFullYear() + '-' + (today.getMonth() + 1).toString().padStart(2, '0') + '-' + today.getDate().toString().padStart(2, '0');

            <?php

            $pointsEarned = ($calc) / 100;

            ?>

            if (cardNumber.length < 12) {
                alert('Card number must be at least 12 digits!');
                return;
            }

            if (expiryNumber === "" || expiryNumber.length !== 5 || !expiryNumber.includes('/')) {
                alert('Expiry number must be 4 digits and not empty!');
                return;
            }

            if (cvvNumber === "" || cvvNumber.length !== 3) {
                alert('CVV number must be 3 digits and not empty!');
                return;
            }

            const bookingData = {
                country: '<?php echo $country; ?>',
                city: '<?php echo $city; ?>',
                vehicleType: '<?php echo $vehicleType; ?>',
                vehiclePrice: '<?php echo $vehiclePrice; ?>',
                roomType: '<?php echo $roomType; ?>',
                roomPrice: '<?php echo $roomPrice; ?>',
                flightType: '<?php echo $flightType; ?>',
                flightPrice: '<?php echo $flightPrice; ?>',
                itineraries: <?php echo json_encode($itineraryList); ?>,
                totalPrice: '<?php echo $calc; ?>',
                checkInDate: '<?php echo $checkInDate; ?>',
                checkOutDate: '<?php echo $checkOutDate; ?>',
                numTickets: '<?php echo $numTickets; ?>',
                pointsEarned: '<?php echo $pointsEarned ?>',
                hotelID: '<?php echo $hotel; ?>',
                orderDate: dateToday,
                userName: '<?php echo $name; ?>',
                email: '<?php echo $email ?>',
                cardDetails: {
                    cardNumber: cardNumber,
                    expiry: expiryNumber,
                    cvv: cvvNumber
                }
            };

            fetch('pushBookingData.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(bookingData)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response from server:', data);
                    if (data.bookingId) {
                        openPopup(data.bookingId);
                    } else {
                        console.error('No booking ID returned:', data);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while processing your booking.');
                });
        }

        function openPopup(bookingId) {
            document.getElementById('bookingId').textContent = bookingId;
            document.getElementById('popupOverlay').style.display = 'flex';
        }

        function closePopup() {
            document.getElementById('popupOverlay').style.display = 'none';
            window.location.replace('index.php');
        }
    </script>


</body>

</html>