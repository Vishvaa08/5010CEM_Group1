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

    $city = isset($_GET['city']) ? $_GET['city'] : '';
    $country = isset($_GET['country']) ? $_GET['country'] : '';
    $hotel = isset($_GET['hotel']) ? $_GET['hotel'] : '';

    $reference = $database->getReference('Packages/' . $country . '/' . $city);
    $snapshot = $reference->getSnapshot();
    $data = $snapshot->getValue();

    $reference = $database->getReference('Packages/' . $country . '/' . $city . '/Itinerary');
    $snapshot = $reference->getSnapshot();
    $dataItinerary = $snapshot->getValue();

    $reference = $database->getReference('Packages/' . $country . '/' . $city . '/Vehicle');
    $snapshot = $reference->getSnapshot();
    $dataVehicle = $snapshot->getValue();

    $reference = $database->getReference('Packages/' . $country . '/' . $city . '/Hotels/' . $hotel);
    $snapshot = $reference->getSnapshot();
    $dataHotel = $snapshot->getValue();

    ?>

    <div id="header">
        <div id="left-nav">
            <a href="index.html">
                <div class="logo-container">
                    <p style="color: white; font-size: 25px; font-family: 'Joti One', serif;">TT</p>
                </div>
            </a>
            <h1>TravelTrail</h1>
        </div>

        <div id="right-nav">
            <a class="nav-link" href="index.html#home">Home</a>
            <a class="nav-link" href="index.html#about">About</a>
            <a class="nav-link" href="index.html#contact">Contact</a>
            <div class="user-profile"></div>
        </div>
    </div>

    <div id="form-container">

        <div class="container1">

            <p class="title"><?php echo $city ?></p>
            <p class="sub-title">Select your Itineraries :</p>

            <label class="labels">
                <input type="checkbox" name="Itinerary1" value="Itinerary1">
                <?php if (isset($dataItinerary['Itinerary1'])) {
                    echo $dataItinerary['Itinerary1'];
                } ?>
            </label>
            <label class="labels">
                <input type="checkbox" name="Itinerary2" value="Itinerary2">
                <?php if (isset($dataItinerary['Itinerary2'])) {
                    echo $dataItinerary['Itinerary2'];
                } ?>
            </label>
            <label class="labels">
                <input type="checkbox" name="Itinerary3" value="Itinerary3">
                <?php if (isset($dataItinerary['Itinerary3'])) {
                    echo $dataItinerary['Itinerary3'];
                } ?>
            </label>
            <label class="labels">
                <input type="checkbox" name="Itinerary4" value="Itinerary4">
                <?php if (isset($dataItinerary['Itinerary4'])) {
                    echo $dataItinerary['Itinerary4'];
                } ?>
            </label>

            <p class="vehicle">Vehicle</p>
            <p class="vehicle-select">Select your vehicle type :</p>

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

        </div>
        <div class="container2">

            <p class="hotel-name"><?php if (isset($dataHotel['Hotel'])) {
                                        echo $dataHotel['Hotel'];
                                    } ?></p>
            <p class="hotel-select">Select your room type :</p>

            <label class="hotels">
                <input type="radio" name="room" value="Single">Single
            </label>

            <label class="hotels">
                <input type="radio" name="room" value="Double">Double
            </label>

            <label class="hotels">
                <input type="radio" name="room" value="Suite">Suite
            </label>

            <br>
            <br>
            <br>

            <label for="check-in" class="date-picker">Check In :</label>
            <input type="date" class="date-picker">

            <br>

            <label for="check-in" class="date-picker">Check Out :</label>
            <input type="date" class="date-picker">

        </div>
        <div class="container3"></div>

    </div>

</body>

</html>