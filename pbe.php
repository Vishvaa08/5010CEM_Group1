<?php
session_start();

if (isset($_SESSION['userName'])) {
    $name = $_SESSION['userName'];
} else {
    $name = 'Error:Name not found';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PBE</title>
    <link rel="stylesheet" type="text/css" href="css/pbe.css">
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
            background-color: rgba(255, 255, 255, 0.3);
            justify-content: center;
            align-items: center;
        }

        .popup-content {
            background: black;
            color: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            font-family: 'Joti One', sans-serif;
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
            <div class="user-profile"></div>
        </div>
    </div>

    <div id="pbe-container-bg">
        <div id="pbe-container">
            <div id="top">
                <div class="logo-pbe"><img src="images/pbe.jpg"></div>
                <div class="pbe-title">
                    <div class="text-text">Public Bank</div>
                </div>
                <div class="text-price">
                    <div class="text-text2"><?php echo 'RM ' . $calc ?></div>
                </div>
            </div>
            <div id="content">
                <div id="text-title2">
                    <div class="text-recipient">
                        <div class="recipient">Recipient</div>
                    </div>
                </div>
                <div id="text-title">
                    <input type="text" placeholder="1048-1468-3398  -> TravelTrail" class="recipient-input" readonly>
                </div>
                <div id="text-title2">
                    <div class="text-recipient">
                        <div class="recipient">Account Number</div>
                    </div>
                </div>
                <div id="text-title">
                    <input type="text" id="accNum" placeholder="Your Account Number" class="recipient-input" maxlength="12" minlength="12" oninput="this.value = this.value.replace(/\D/g, '')">
                </div>
                <div id="text-title2"></div>
                <div id="text-title">
                    <div id="bg"><input type="file" class="upload" accept=".png, .jpg, .jpeg"></div>
                </div>
                <div id="text-title">
                    <div id="btn-container">
                        <button class="confirm-btn">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="popup-overlay" id="popupOverlay">
        <div class="popup-content">
            <h2>Order ID</h2>
            <p><strong id="bookingId">1234</strong></p>
            <p>We will notify you once your payment has been processed.</p>
            <button class="close-popup">Close</button>
        </div>
    </div>

    <script type="module">
        document.querySelector('.confirm-btn').addEventListener('click', submit);
        document.querySelector('.close-popup').addEventListener('click', closePopup);

        import {
            initializeApp
        } from "https://www.gstatic.com/firebasejs/9.22.0/firebase-app.js";
        import {
            getStorage,
            ref,
            uploadBytes,
            getDownloadURL
        } from "https://www.gstatic.com/firebasejs/9.22.0/firebase-storage.js";

        const firebaseConfig = {
            apiKey: "AIzaSyAef9-sjwyQL-MAiUYLUgBO0p68QuRGRNI",
            projectId: "traveltrail-39e23",
            storageBucket: "gs://traveltrail-39e23.appspot.com",
            appId: "1:91519152452:web:422ee3957f7b21778fa711"
        };

        const app = initializeApp(firebaseConfig);
        const storage = getStorage(app);

        function submit() {
            const accNumber = document.getElementById('accNum').value;
            const fileInput = document.querySelector('.upload');
            const file = fileInput.files[0];

            const today = new Date();
            const dateToday = today.getFullYear() + '-' + (today.getMonth() + 1).toString().padStart(2, '0') + '-' + today.getDate().toString().padStart(2, '0');

            <?php
            $name = $_SESSION['userName'];
            ?>

            if (accNumber === '') {
                alert('Enter your Account Number!');
                return;
            }

            if (accNumber.length < 12) {
                alert('Account Number should be exactly 12 digits!');
                return;
            }

            if (!file) {
                alert('Please select an image file!');
                return;
            }

            const allowedExtensions = ['image/png', 'image/jpg', 'image/jpeg'];
            if (!allowedExtensions.includes(file.type)) {
                alert('Invalid file type. Please select a PNG, JPG, or JPEG image.');
                return;
            }

            const imageName = `FPXpayments/${Date.now()}_${file.name}`;
            const imageRef = ref(storage, imageName);

            <?php

            $pointsEarned = ($calc) / 100;

            ?>

            uploadBytes(imageRef, file)
                .then(snapshot => {
                    return getDownloadURL(imageRef);
                })
                .then(downloadURL => {
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
                        userName: '<?php echo $name; ?>',
                        orderDate: dateToday,
                        paymentProof: downloadURL,
                        bankType: 'Public Bank',
                        bankDetails: {
                            accNumber: accNumber
                        }
                    };

                    return fetch('pushBookingData.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(bookingData)
                    });
                })
                .then(response => response.json())
                .then(data => {
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