<?php
session_start();

$pic = '';
$userLoggedIn = false;

if (isset($_SESSION['userName'])) {
    $pic = $_SESSION['profileImage'];
    $userLoggedIn = true;
} else {
    $pic = 'images/user.png';
    $userLoggedIn = false;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking</title>
    <link rel="stylesheet" type="text/css" href="css/booking.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Joti+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap"
        rel="stylesheet">
</head>

<body>

    <?php

    include 'firebase_connection.php';
    include 'firebase_data.php';

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

    <div id="form-background">
        <div id="form-container">
            <div id="banner">

                <?php

                if (isset($dataCityImages['Banner2'])) {
                    echo '<img src="' . ($dataCityImages['Banner2']) . ' Image" class="banner2-image">';
                } else {
                    echo '<img src="images/error.jpg" class="banner2-image">';
                }

                ?>

                <div id="opacity">

                    <?php
                    $caps = strtoupper($city);
                    echo '<h2 class="city-name">' . $caps . '</h2>';
                    ?>

                </div>

            </div>
            <form id="form" method="GET">
                <div id="content">

                    <h1>Itineraries :</h1>
                    <?php
                    echo '<div>';
                    foreach ($dataCityItinerary as $key => $itinerary) {
                        if (isset($itinerary['Itinerary']) && isset($itinerary['ItineraryPrice'])) {
                            echo '<label>';
                            echo '<input type="checkbox" class="itinerary-checkbox" name="itineraries[]" value="' . htmlspecialchars($itinerary['Itinerary']) . '|' . htmlspecialchars($itinerary['ItineraryPrice']) . '">';
                            echo htmlspecialchars($itinerary['Itinerary']) . ' - <span style="color: white;">RM' . htmlspecialchars($itinerary['ItineraryPrice']);
                            echo '</label>';
                        }
                    }
                    echo '</div>';
                    ?>

                    <hr>

                    <h1>Vehicle :</h1>
                    <?php
                    echo '<div>';
                    foreach ($dataVehicle as $key => $vehicle1) {
                        if (isset($vehicle1['Type']) && isset($vehicle1['Price'])) {
                            echo '<label class="vehicles">';
                            echo '<input type="radio" id="vehicle" name="vehicle" value="' . htmlspecialchars($vehicle1['Type']) . '|' . htmlspecialchars($vehicle1['Price']) . '">';
                            echo htmlspecialchars($vehicle1['Type']) . ' - <span style="color: white;">RM' . htmlspecialchars($vehicle1['Price']);
                            echo '</label>';
                        }
                    }
                    echo '</div>';
                    ?>

                    <hr>

                    <h1><?php if (isset($dataHotel['Hotel'])) {
                            echo $dataHotel['Hotel'];
                        } ?>
                    </h1>

                    <label>
                        <input type="radio" name="room" value="Single|<?php echo $singlePrice; ?>"
                            <?php echo ($single == 0) ? 'disabled' : ''; ?>>
                        Single Room - <span style="color: white;">RM<?php echo $singlePrice ?></span>
                    </label>
                    <label>
                        <input type="radio" name="room" value="Double|<?php echo $doublePrice; ?>"
                            <?php echo ($double == 0) ? 'disabled' : ''; ?>>
                        Double Room - <span style="color: white;">RM<?php echo $doublePrice ?></span>
                    </label>
                    <label>
                        <input type="radio" name="room" value="Suite|<?php echo $suitePrice; ?>"
                            <?php echo ($suite == 0) ? 'disabled' : ''; ?>>
                        Suite Room - <span style="color: white;">RM<?php echo $suitePrice ?></span>
                    </label>

                    <div id="calendar-top">
                        <div id="left">Check In</div>
                        <div id="right">Check Out</div>
                    </div>
                    <div id="calendar-bottom">
                        <div id="left"><input type="date" id="check-in-date" name="check-in-date" value="check-in-date" min="<?php echo date('Y-m-d'); ?>" required></div>
                        <div id="right"><input type="date" id="check-out-date" name="check-out-date" value="check-out-date" required></div>
                    </div>

                    <hr>

                    <h1>Flight :</h1>
                    <label>
                        <input type="radio" name="flight" value="Economy|<?php echo $economyPrice; ?>"
                            <?php echo ($economySeats == 0) ? 'disabled' : ''; ?>>
                        Economy : Seats Available : <?php echo $economySeats ?> - <span style="color: white;">RM<?php echo $economyPrice ?></span>
                    </label>
                    <label>
                        <input type="radio" name="flight" value="Business|<?php echo $businessPrice; ?>"
                            <?php echo ($businessSeats == 0) ? 'disabled' : ''; ?>>
                        Business : Seats Available : <?php echo $businessSeats ?> - <span style="color: white;">RM<?php echo $businessPrice ?></span>
                    </label>
                    <label>
                        <input type="radio" name="flight" value="First|<?php echo $firstPrice; ?>"
                            <?php echo ($firstSeats == 0) ? 'disabled' : ''; ?>>
                        First Class : Seats Available : <?php echo $firstSeats ?> - <span style="color: white;">RM<?php echo $firstPrice ?></span>
                    </label>

                    <h1>Tickets :</h1>
                    <label class="ticket">
                        <input type="radio" name="ticket-type" value="1">1-Way
                    </label>

                    <label class="ticket">
                        <input type="radio" name="ticket-type" value="2">2-Way <span style="color: white;">+RM150 per</span>
                    </label>

                    <p class="seats">Number of seats : <input type="number" id="tickets" name="tickets" min="1" max="10" value="1" class="ticket-input"></p>

                    <hr>

                    <h1>Payment Methods :</h1>
                    <div id="payments">
                        <div id="pay-left">
                            <button type="button" class="card-btn" id="card-btn">Card</button>
                        </div>
                        <div id="pay-right">
                            <button type="button" class="bank-btn" id="bank-btn">Bank</button>
                        </div>
                    </div>
                    <div id="bank-options" style="display: none;">
                        <h2>Select Bank:</h2>
                        <label class="bank-label">
                            <input type="radio" name="bank" value="bank1">
                            <img src="images/pbe.jpg" style="width: 3vh; height:3vh">
                            Public Bank
                        </label>
                        <label class="bank-label">
                            <input type="radio" name="bank" value="bank2">
                            <img src="images/m2u.jpg" style="width: 3vh; height:3vh">
                            Maybank2u
                        </label>

                        <div id="pay"><button class="pay" id="pay-btn">Payment</button></div>
                    </div>

                </div>
                <input type="hidden" name="city" value="<?php echo htmlspecialchars($city); ?>">
                <input type="hidden" name="country" value="<?php echo htmlspecialchars($country); ?>">
                <input type="hidden" name="hotel" value="<?php echo htmlspecialchars($hotel); ?>">
            </form>
        </div>
    </div>

    <div id="footer"></div>

    <script>
        const today = new Date();
        const dd = String(today.getDate()).padStart(2, '0');
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const yyyy = today.getFullYear();

        const formattedDate = yyyy + '-' + mm + '-' + dd;

        const checkinInput = document.getElementById('check-in-date');
        checkinInput.setAttribute('min', formattedDate);

        checkinInput.addEventListener('change', function() {
            const checkinValue = new Date(checkinInput.value);
            checkinValue.setDate(checkinValue.getDate() + 1);

            const checkoutMinDate = checkinValue.toISOString().split('T')[0];
            document.getElementById('check-out-date').setAttribute('min', checkoutMinDate);
        });

        document.getElementById('bank-btn').addEventListener('click', function() {
            var bankOptions = document.getElementById('bank-options');
            if (bankOptions.style.display === 'none') {
                bankOptions.style.display = 'block';
                bankOptions.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            } else {
                bankOptions.style.display = 'none';
            }
        });

        document.getElementById('pay-btn').addEventListener('click', function(event) {
            event.preventDefault();
            const selectedBank = document.querySelector('input[name="bank"]:checked');
            const selectedVehicle = document.querySelector('input[name="vehicle"]:checked');
            const selectedRoom = document.querySelector('input[name="room"]:checked');
            const selectedFlight = document.querySelector('input[name="flight"]:checked');
            const itineraries = document.querySelectorAll('input[name="itineraries[]"]:checked');
            const ticketType = document.querySelector('input[name="ticket-type"]:checked');

            const userLoggedIn = <?php echo json_encode($userLoggedIn); ?>;

            if (!userLoggedIn) {
                alert("Please log in before booking.");
                return;
            }

            if (selectedBank && selectedVehicle && selectedRoom && selectedFlight && itineraries.length > 0 && ticketType) {
                var form = document.getElementById('form');
                const bankValue = selectedBank.value;

                const city = document.querySelector('input[name="city"]').value;
                const country = document.querySelector('input[name="country"]').value;

                const checkinDate = document.getElementById('check-in-date').value;
                const checkoutDate = document.getElementById('check-out-date').value;

                if (!checkinDate || !checkoutDate) {
                    alert('Please select both check-in and check-out dates.');
                    return;
                }

                const itineraries = document.querySelectorAll('input[name="itineraries[]"]:checked');
                let itineraryParams = [];

                itineraries.forEach(function(itinerary) {
                    itineraryParams.push(itinerary.value);
                });

                if (bankValue === 'bank1') {
                    form.action = 'pbe.php?city=' + city + '&country=' + country;
                } else if (bankValue === 'bank2') {
                    form.action = 'm2u.php?city=' + city + '&country=' + country;
                }

                form.submit();
            } else {
                let alertMessage = 'Please select the following:\n';
                if (itineraries.length === 0) alertMessage += '- Itineraries\n';
                if (!selectedVehicle) alertMessage += '- Vehicle\n';
                if (!selectedRoom) alertMessage += '- Room\n';
                if (!selectedFlight) alertMessage += '- Flight\n';
                if (!ticketType) alertMessage += '- Ticket Type\n';
                if (!selectedBank) alertMessage += '- Bank\n';

                alert(alertMessage);
            }
        });

        document.getElementById('card-btn').addEventListener('click', function(event) {
            event.preventDefault();
            const selectedVehicle = document.querySelector('input[name="vehicle"]:checked');
            const selectedRoom = document.querySelector('input[name="room"]:checked');
            const selectedFlight = document.querySelector('input[name="flight"]:checked');
            const itineraries = document.querySelectorAll('input[name="itineraries[]"]:checked');
            const ticketType = document.querySelector('input[name="ticket-type"]:checked');

            const userLoggedIn = <?php echo json_encode($userLoggedIn); ?>;

            if (!userLoggedIn) {
                alert("Please log in before making a booking.");
                return;
            }

            if (selectedVehicle && selectedRoom && selectedFlight && itineraries.length > 0 && ticketType) {
                var form = document.getElementById('form');

                const city = document.querySelector('input[name="city"]').value;
                const country = document.querySelector('input[name="country"]').value;
                const hotel = document.querySelector('input[name="hotel"]').value;

                const itineraries = document.querySelectorAll('input[name="itineraries[]"]:checked');
                let itineraryParams = [];

                itineraries.forEach(function(itinerary) {
                    itineraryParams.push(itinerary.value);
                });

                const checkinDate = document.getElementById('check-in-date').value;
                const checkoutDate = document.getElementById('check-out-date').value;

                if (!checkinDate || !checkoutDate) {
                    alert('Please select both check-in and check-out dates.');
                    return;
                }

                form.action = 'card-payment.php?city=' + city + '&country=' + country + '&hotel' + hotel;

                form.submit();
            } else {
                let alertMessage = 'Please select the following:\n';
                if (itineraries.length === 0) alertMessage += '- Itineraries\n';
                if (!selectedVehicle) alertMessage += '- Vehicle\n';
                if (!selectedRoom) alertMessage += '- Room\n';
                if (!selectedFlight) alertMessage += '- Flight\n';
                if (!ticketType) alertMessage += '- Ticket Type\n';

                alert(alertMessage);
            }
        });
    </script>


</body>

</html>