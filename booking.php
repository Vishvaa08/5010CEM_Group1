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
            <div class="user-profile"></div>
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
            <div id="content">

                <h1>Itineraries :</h1>
                <?php
                echo '<div class="checkbox-container">';
                foreach ($dataCityItinerary as $key => $itinerary) {
                    if (isset($itinerary['Itinerary']) && isset($itinerary['ItineraryPrice'])) {
                        echo '<label>';
                        echo '<input type="checkbox" class="itinerary-checkbox" data-price="' . ($itinerary['ItineraryPrice']) . '" value="' . ($itinerary['Itinerary']) . '">';
                        echo ($itinerary['Itinerary']) . ' - RM' . ($itinerary['ItineraryPrice']);
                        echo '</label>';
                    }
                }
                echo '</div>';
                ?>

                <hr>

                <h1>Vehicle :</h1>
                <label class="vehicles">
                    <input type="radio" name="vehicle" value="4-Seater">
                    <?php if (isset($dataVehicle['Type1'])) {
                        echo $dataVehicle['Type1'];
                    } ?>
                </label>

                <label class="vehicles">
                    <input type="radio" name="vehicle" value="7-Seater">
                    <?php if (isset($dataVehicle['Type2'])) {
                        echo $dataVehicle['Type2'];
                    } ?>
                </label>

                <label class="vehicles">
                    <input type="radio" name="vehicle" value="Van">
                    <?php if (isset($dataVehicle['Type3'])) {
                        echo $dataVehicle['Type3'];
                    } ?>
                </label>

                <hr>

                <h1><?php if (isset($dataHotel['Hotel'])) {
                        echo $dataHotel['Hotel'];
                    } ?></p>
                </h1>

                <label>
                    <input type="radio" name="room" value="Single"
                        <?php echo ($single == 0) ? 'disabled' : ''; ?>>
                    Single Room
                </label>
                <label>
                    <input type="radio" name="room" value="Double"
                        <?php echo ($double == 0) ? 'disabled' : ''; ?>>
                    Double Room
                </label>
                <label>
                    <input type="radio" name="room" value="Suite"
                        <?php echo ($suite == 0) ? 'disabled' : ''; ?>>
                    Suite Room
                </label>

                <div id="calendar-top">
                    <div id="left">Check In</div>
                    <div id="right">Check Out</div>
                </div>
                <div id="calendar-bottom">
                    <div id="left"><input type="date" value="check-in-date"></div>
                    <div id="right"><input type="date" value="check-in-date"></div>
                </div>

                <hr>

                <h1>Flight :</h1>
                <?php
                $flightClasses = ['Economy', 'Business', 'First Class'];

                foreach ($flightClasses as $class) {
                    $seatsAvailable = isset($dataFlights[$class]['Seats']) ? $dataFlights[$class]['Seats'] : 0;
                    $disabled = $seatsAvailable == 0 ? 'disabled' : '';
                    echo '<label>';
                    echo '<input type="radio" name="flight" value="' . $class . '" ' . $disabled . '>';
                    echo $class . ' - Seats Available: ' . $seatsAvailable;
                    echo '</label>';
                }
                ?>

                <h1>Tickets :</h1>
                <label class="ticket">
                    <input type="radio" name="ticket" value="1">1-Way
                </label>

                <label class="ticket">
                    <input type="radio" name="ticket" value="2">2-Way
                </label>

                <p class="seats">Number of seats : <input type="number" id="tickets" name="tickets" min="1" max="10" value="1" class="ticket-input"></p>

                <hr>

                <h1>Payment Methods :</h1>
                <div id="payments">
                    <div id="pay-left">
                        <?php
                        echo '<a href="card-payment.php?city=' . urlencode(ucfirst(strtolower($city))) .'" class="card">Card</a>';
                        ?>
                    </div>
                    <div id="pay-right">
                        <button class="bank-btn" id="bank-btn">Bank</button>
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
        </div>
    </div>

    <div id="footer"></div>

    <script>
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

        document.getElementById('pay-btn').addEventListener('click', function() {
        const selectedBank = document.querySelector('input[name="bank"]:checked');

        if (selectedBank) {
            const bankValue = selectedBank.value;

            if (bankValue === 'bank1') {
                window.location.href = 'pbe.php';
            } else if (bankValue === 'bank2') {
                window.location.href = 'm2u.php';
            }
        } else {
            alert('Please select a bank first!');
        }
    });
    </script>

</body>

</html>