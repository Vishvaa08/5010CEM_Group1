<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Details</title>
    <link rel="stylesheet" type="text/css" href="css/citydetails.css">
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

    <div id="header-image">

        <?php

        if (isset($dataCityImages['Banner'])) {
            echo '<img src="' . ($dataCityImages['Banner']) . ' Image" class="banner-image">';
        } else {
            echo '<img src="images/error.jpg" class="banner-image">';
        }

        ?>

        <div id="city-name-container">
            <?php echo $city ?>
        </div>
    </div>

    <div id="itineraries-container">
        <h3>Things To Do :</h3>

        <div id="itineraries-list">

            <?php

            foreach ($dataCityItinerary as $city => $cities) {

                if (empty($cities)) {
                    continue;
                }

                echo '<div class="card">';

                if (isset($cities['ItineraryImage'])) {
                    echo '<img src="' . ($cities['ItineraryImage']) . ' Image" class="card-image">';
                } else {
                    echo '<img src="images/error.jpg" class="card-image">';
                }

                if (isset($cities['Itinerary'])) {
                    echo '<h2 class="title">' . ($cities['Itinerary']) . '</h2>';
                } else {
                    echo '<h2 class="title">No country details available...</h2>';
                }

                if (isset($cities['ItineraryPrice'])) {
                    echo '<h2 class="price"> RM' . ($cities['ItineraryPrice']) . '</h2>';
                } else {
                    echo '<h2 class="price">XX</h2>';
                }

                echo '</div>';
            }
            ?>

        </div>

    </div>

    <div id="hotel-container">

        <div id="left">
            <div id="name-card">

                <?php

                if (isset($dataCityImages['Banner2'])) {
                    echo '<img src="' . ($dataCityImages['Banner2']) . ' Image" class="banner2-image">';
                } else {
                    echo '<img src="images/error.jpg" class="banner2-image">';
                }

                ?>

                <div id="city-name-container2">

                    <?php
                    $city = htmlspecialchars($_GET['city']);
                    $city = strtoupper($city);
                    $cityArray = str_split($city);
                    ?>

                    <?php foreach ($cityArray as $letter): ?>
                        <div class="city-letter"><?php echo $letter; ?></div>
                    <?php endforeach; ?>

                </div>

            </div>
        </div>

        <div id="right">
            <div id="icon-card">

                <div id="icon1">
                    <div class="icon1-img"><img src="images/cheap.png"></div>
                    <div id="icon-title">Cheap</div>
                </div>
                <div id="icon2">
                    <div class="icon1-img"><img src="images/quick.png"></div>
                    <div id="icon-title">Quick</div>
                </div>
                <div id="icon3">
                    <div class="icon1-img"><img src="images/cozy.png"></div>
                    <div id="icon-title">Cozy</div>
                </div>
                <div id="icon4">
                    <div class="icon1-img"><img src="images/modern.png"></div>
                    <div id="icon-title">Modern</div>
                </div>

            </div>
            <div id="hotel-list">

                <?php

                foreach ($dataHotels as $hotel => $hotelData) {
                    if (isset($hotelData['Hotel'])) {
                        echo '<div class="card-hotel">';
                        echo '<div class="hotel-image-container">';

                        if (isset($hotelData['Image'])) {
                            echo '<img src="' . $hotelData['Image'] . '" class="card-image-hotel">';
                        }

                        echo '</div>';
                        echo '<div class="hotel-content">';

                        echo '<div class="hotel-name">';

                        if (isset($hotelData['Hotel'])) {
                            echo '<h2>' . $hotelData['Hotel'] . '</h2>';
                        }

                        echo '</div>';
                        echo '<div class="hotel-description">';

                        if (isset($hotelData['Description'])) {
                            echo '<h2>' . $hotelData['Description'] . '</h2>';
                        }

                        echo '</div>';
                        echo '<div class="room-remaining"><h2>Rooms Remaining :</h2></div>';
                        echo '<div class="rooms-container">';

                        echo '<div class="single">';
                        if (isset($hotelData['Rooms']['Single'])) {
                            $singleRoomAvailability = $hotelData['Rooms']['Single']['Availability'];
                            echo '<div class="single-text">Single</div>';
                            echo '<div>' . $singleRoomAvailability . '</div>';
                        }
                        echo '</div>';

                        echo '<div class="double">';
                        if (isset($hotelData['Rooms']['Double'])) {
                            $doubleRoomAvailability = $hotelData['Rooms']['Double']['Availability'];
                            echo '<div class="double-text">Double</div>';
                            echo '<div>' . $doubleRoomAvailability . '</div>';
                        }
                        echo '</div>';

                        echo '<div class="suite">';
                        if (isset($hotelData['Rooms']['Suite'])) {
                            $suiteRoomAvailability = $hotelData['Rooms']['Suite']['Availability'];
                            echo '<div class="suite-text">Suite</div>';
                            echo '<div>' . $suiteRoomAvailability . '</div>';
                        }
                        echo '</div>';

                        echo '</div>';

                        echo '<div class="price">RM' . $hotelData['CheapestRoom'] . '</div>';

                        echo '<a href="booking.php?city=' . urlencode($city) . '&country=' . urlencode($country) . '&hotel=' . urlencode($hotel) . '" class="btn">Book</a>';

                        echo '</div>';
                        echo '</div>';
                    }
                }
                ?>

            </div>
        </div>

    </div>

</body>

</html>