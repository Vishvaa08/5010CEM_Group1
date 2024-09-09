<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cities</title>
    <link rel="stylesheet" type="text/css" href="css/cities.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Joti+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap"
        rel="stylesheet">
</head>

<body>

    <?php

    include 'firebase_connection.php';

    $country = isset($_GET['country']) ? $_GET['country'] : '';

    $reference = $database->getReference('Packages/')
                            ->orderByChild('Country')
                            ->equalTo($country);  
    $snapshot = $reference->getSnapshot();
    $data = $snapshot->getValue();

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
    </div>

    <div id="search-container">
        <input type="text" name="search-bar" id="search-bar" placeholder="Search..." />
    </div>

    <div id="packages" class="packages-container">

        <?php
        //
        foreach ($data as $country => $cities) {
            echo '<div class="card">';

            if (isset($cities['image'])) {
                echo '<img src="' . ($cities['image']) . ' Image" class="card-image">';
            }

            echo '<h2>' . ucfirst($country) . '</h2>';

            if (isset($cities['PackageDetail'])) {
                echo '<p>' . ($cities['PackageDetail']) . '</p>';
            } else {
                echo '<p>No details available</p>';
            }

            echo '<a href="cities.php?country=' . urlencode($country) . '" class="explore-btn">View More</a>';
            /*
            echo '<a href="' . strtolower($country) . '.php" class="explore-btn">Explore Now' . '</a>';
            */

            echo '</div>';
        }
        ?>

    </div>

</body>

</html>