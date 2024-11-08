<?php
session_start();

$pic = 'images/user.png'; 
$isLoggedIn = false; 
$userName = 'Guest'; 
$userEmail = ''; 

if (isset($_SESSION['userName'])) {
    $pic = $_SESSION['profileImage'] ?? $pic; 
    $isLoggedIn = true;
    $userName = $_SESSION['userName']; 
    $userEmail = $_SESSION['userEmail'] ?? ''; 
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link rel="stylesheet" type="text/css" href="css/index.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Joti+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <!-- Splash screen for when website is first opened -->
    <div class="splash-screen">
        <h1 class="splash-title">
            <span class="splash">Travel</span><span class="splash">Trail.</span>
        </h1>
    </div>
    <!-- End of splash screen -->

    <!--Beginning of home section of homepage including navigation-->
    <div id="home">
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

                <!-- Notification Button -->
                <a href="userNotifications.php" class="notification-button">
                    <i class="fa fa-bell" aria-hidden="true"></i>
                </a>

                <a href="php_functions/user_login_check.php" class="user-profile">
                    <img src="<?php echo $pic; ?>" style="width:75px; height:75px; border-radius:50%; object-fit:cover;">
                </a>
            </div>
        </div>

        <div id="main-title">
            <p class="title">Let's Embark On</br>Your Journey</br>Together!</p>
        </div>

        <div id="sub-main-title">
            <p class="sub-title">visit various places, and customise</br>your own travel packages now</br>with
                TravelTrail...</h3>
        </div>

        <button type="button" class="explore-btn" onclick="window.location.href='packages.php'">Explore Now!</button>

        <div id="socials">
            <div class="social-icons">
                <a href="https://www.facebook.com/"><i class="fa fa-facebook"></i></a>
                <a href="https://www.instagram.com/accounts/login/?hl=en"><i class="fa fa-instagram"></i></a>
                <a href="https://www.twitter.com/"><i class="fa fa-twitter"></i></a>
            </div>
        </div>
    </div>
    <!--Ending of home section-->

    <!-- The rest of your page content -->

    <div class="footer">
        <p>TravelTrail</p>
    </div>

    <script src="js/index.js"></script>
</body>
</html>
