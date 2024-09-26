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

    if(isset($_GET['check-in-date'])){
        $checkInDate = $_GET['check-in-date'];
    }

    if(isset($_GET['check-out-date'])){
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

    if(isset($_GET['tickets'])){
        $numTickets = $_GET['tickets'];
    }

    if($ticketType == 2){
        $calc = ($roomPrice + $vehiclePrice + $totalItineraryPrice) + (($flightPrice + 150) * $numTickets);
    }else{
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
                    <input type="text" id="card-number-input" maxlength="14"
                        oninput="this.value = this.value.replace(/\D/g, '').replace(/(.{4})(?=.)/g, '$1-').slice(0, 14);"
                        placeholder="xxxx-xxxx-xxxx" class="card-num-input">
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
                    <input type="text" id="card-expiry-input" maxlength="5" oninput="this.value = this.value.replace(/\D/g, '').slice(0,2) + (this.value.length >= 2 ? '/' : '') + this.value.replace(/\D/g, '').slice(2,4);" placeholder="MM/YY" class="expiry-num-input">
                </div>
                <div class="cvv-input">
                    <input type="text" id="card-cvv-input" maxlength="3" oninput="this.value = this.value.replace(/\D/g, '').slice(0,3);" placeholder="XXX" class="cvv-num-input">
                </div>
            </div>
            <div id="button-container">
                <button class="confirm-btn">Confirm</button>
            </div>
        </div>
    </div>


</body>

</html>