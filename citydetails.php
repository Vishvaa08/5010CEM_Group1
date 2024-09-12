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

    $city = isset($_GET['city']) ? $_GET['city'] : '';
    $country = isset($_GET['country']) ? $_GET['country'] : '';

    $reference = $database->getReference('Packages/' . $country . '/' . $city . '/Images');
    $snapshot = $reference->getSnapshot();
    $data = $snapshot->getValue();

    ?>

    <div id="itinerary-details">

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

        <div id="city-name">

            <div class="name"><?php echo $city; ?></div>

        </div>

        <div id="container">

            <div class="slider">

                <?php

                if (isset($data['Image1']) && isset($data['Image2']) && isset($data['Image3'])) {
                    echo '<div class="slides">';
                    echo '<img src="' . $data['Image1'] . '" class="slide">';
                    echo '<img src="' . $data['Image2'] . '" class="slide">';
                    echo '<img src="' . $data['Image3'] . '" class="slide">';
                    echo '</div>';
                }
                ?>

            </div>
            <div class="content">

                <h2>Itinerary</h2>
                <p>Day 1 :</p>
                <p>
                    <?php

                    $reference = $database->getReference('Packages/' . $country . '/' . $city . '/Itinerary');
                    $snapshot = $reference->getSnapshot();
                    $dataNew = $snapshot->getValue();

                    if (isset($dataNew['Itinerary1'])) {
                        echo '<p class="itinerary">- ' . $dataNew['Itinerary1'] . '</p>';
                    }
                    if (isset($dataNew['Itinerary2'])) {
                        echo '<p class="itinerary">- ' . $dataNew['Itinerary2'] . '</p><br>';
                    }
                    ?>
                </p>

                <p>Day 2 :</p>
                <p>
                    <?php
                    if (isset($dataNew['Itinerary3'])) {
                        echo '<p class="itinerary">- ' . $dataNew['Itinerary3'] . '</p>';
                    }
                    if (isset($dataNew['Itinerary4'])) {
                        echo '<p class="itinerary">- ' . $dataNew['Itinerary4'] . '</p>';
                    }
                    ?>
                </p>

            </div>

        </div>

        <div id="btn">
            <button class="hotel-btn" onclick="htl()">Hotels</button>
        </div>

    </div>

    <div id="hotels">

        <div id="container2">

                <?php

                $reference = $database->getReference('Packages/' . $country . '/' . $city . '/Hotels');
                $snapshot = $reference->getSnapshot();
                $dataHotels = $snapshot->getValue();

                foreach ($dataHotels as $hotel => $hotelData) {
                    if (isset($hotelData['Hotel'])) {
                        echo '<div class="card">';

                        if (isset($hotelData['Image'])) {
                            echo '<div class="image-container">';
                            echo '<img src="' . $hotelData['Image'] . '" class="card-image">';
                            echo '</div>';
                        }

                        if (isset($hotelData['Hotel'])) {
                            echo '<div class="content-container">';
                            echo '<h2>' . ($hotelData['Hotel']) . '</h2>';
                            echo '<p>' . ($hotelData['Description']) . '</p>';
                            echo '</div>';
                        }

                        echo '<a href="booking.php?city=' . urlencode($city) . '&country=' . urlencode($country) . '&hotel=' . urlencode($hotel) . '" class="explore-btn">Book</a>';

                        echo '</div>';
                    }
                }
                ?>

        </div>

    </div>

    <script>
        function htl() {
            document.getElementById('hotels').scrollIntoView({
                behavior: 'smooth'
            });
        }
    </script>

</body>

</html>