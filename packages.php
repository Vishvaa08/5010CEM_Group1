<?php
session_start();

$pic = '';

//retrieving image url saved in session during login
if (isset($_SESSION['userName'])) {
    $pic = $_SESSION['profileImage'];
} else {
    $pic = 'images/user.png';
}
?>

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
    //including files with firebase connections and data
    include 'firebase_connection.php';
    include 'firebase_data.php';

    ?>
    <!-- div to hold loading animation while page loads fully -->
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
            <a href="php_functions/user_login_check.php" class="user-profile"><img src="<?php echo $pic; ?>" style="width:75px; height:75px; border-radius:50%; object-fit:cover;"></a>
        </div>
    </div>
    </div>
    <!-- search container div -->
    <div id="search-container">
        <input type="text" name="search-bar" id="search-bar" onkeyup="filterCountry()" placeholder="Search..." />
    </div>

    <div id="packages" class="packages-container">

        <?php
        //foreach loop that loops through every child under $country reference = all countries
        foreach ($data as $country => $cities) {
            //only displays countries that have child Availability and set to Available
            if (isset($cities['Availability']) && $cities['Availability'] === 'Available') {
                echo '<div class="card">';
                //display child CountryImage (image of the country)
                if (isset($cities['CountryImage'])) {
                    echo '<img src="' . ($cities['CountryImage']) . ' Image" class="card-image">';
                } else {
                    //default image if no image url is found or any error retrieving url
                    echo '<img src="images/error.jpg" class="card-image">';
                }

                echo '<h2 class="search-name">' . ucfirst($country) . '</h2>';
                //display child CountryDetail (description of country)
                if (isset($cities['CountryDetail'])) {
                    echo '<p>' . ($cities['CountryDetail']) . '</p>';
                } else {
                    //default output if no data found
                    echo '<p>No country details available...</p>';
                }
                //dynamic link : adds country name into the link based on country clicked on
                echo '<a href="cities.php?country=' . urlencode($country) . '" class="explore-btn">Explore Now</a>';

                echo '</div>';
            }
        }
        ?>

    </div>

    <script>

        //loading animation function
        var loader = document.getElementById('loading-animation');
        //once page is fully loaded, set the display of the div to none
        window.addEventListener('load', function() {
            loader.style.display = 'none';
        });

        //search bar function
        function filterCountry() {
            //initialise variables for user's input and country card
            const userInput = document.getElementById('search-bar').value.toLowerCase();
            const cards = document.querySelectorAll('.card');

            //matches user input with country name in country card
            cards.forEach(card => {
                const countryName = card.querySelector('.search-name').textContent.toLowerCase();
                //set display of other cards to none except for the one that matches user's input
                card.style.display = countryName.includes(userInput) ? 'block' : 'none';
            });
        }
    </script>

</body>

</html>