<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packages</title>
    <link rel="stylesheet" type="text/css" href="css/packages.css">
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

    <div id="loading-animation"></div>

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
    </div>

    <div id="search-container">
        <input type="text" name="search-bar" id="search-bar" onkeyup="filterCountry()" placeholder="Search..." />
    </div>

    <div id="packages" class="packages-container">

        <?php
        //
        foreach ($data as $country => $cities) {

            if (isset($cities['Availability']) && $cities['Availability'] === 'Available') {
                echo '<div class="card">';

                if (isset($cities['CountryImage'])) {
                    echo '<img src="' . ($cities['CountryImage']) . ' Image" class="card-image">';
                } else {
                    echo '<img src="images/error.jpg" class="card-image">';
                }

                echo '<h2 class="search-name">' . ucfirst($country) . '</h2>';

                if (isset($cities['CountryDetail'])) {
                    echo '<p>' . ($cities['CountryDetail']) . '</p>';
                } else {
                    echo '<p>No country details available...</p>';
                }

                echo '<a href="cities.php?country=' . urlencode($country) . '" class="explore-btn">Explore Now</a>';

                echo '</div>';
            }
        }
        ?>

    </div>

    <script>
        var loader = document.getElementById('loading-animation');

        window.addEventListener('load', function() {
            loader.style.display = 'none';
        });

        function filterCountry() {
            const userInput = document.getElementById('search-bar').value.toLowerCase();
            const cards = document.querySelectorAll('.card');

            cards.forEach(card => {
                const countryName = card.querySelector('.search-name').textContent.toLowerCase();
                card.style.display = countryName.includes(userInput) ? 'block' : 'none';
            });
        }
    </script>

</body>

</html>