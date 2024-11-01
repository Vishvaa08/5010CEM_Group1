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
            <a href="php_functions/user_login_check.php" class="user-profile"><img src="<?php echo $pic; ?>" style="width:75px; height:75px; border-radius:50%; object-fit:cover;"></a>
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

    <!--Beginning of about us section-->
    <div id="about">
        <div id="about-details">
            <div id="left-about">
                <div class="founder-pic"></div>
                <div class="founder-name">Jane Doe</div>
                <div class="founder-title">Founder of TravelTrail</div>
            </div>

            <div id="right-about">
                <div class="about-title">About Us</div>
                <div class="about-description">Jane Doe has always been passionate about exploring the world,
                    discovering new places, and immersing herself in diverse cultures. However, during her travels, she
                    often found herself constrained by rigid, pre-packaged travel options that didn’t fully align with
                    her desires and interests. Frustrated by the lack of flexibility, she envisioned a solution that
                    would empower travelers like herself to take control of their journeys.

                    </br>
                    </br>

                    This vision led to the creation of TravelTrail—a platform that offers truly customizable travel
                    packages. With TravelTrail, Jane's mission is to give travelers the freedom to design trips that
                    reflect their unique preferences, allowing them to explore the world on their terms. Whether it’s
                    crafting a multi-destination adventure or simply selecting the perfect mix of activities,
                    TravelTrail is here to make every journey as unique as the traveler.</div>
            </div>
        </div>
    </div>


    <!-- Contact Form -->
    <div id="contact">
    <div class="contact-section">
        <h2>Get the Info you're looking for!</h2>

        <div class="info-item">
            <button class="info-btn" data-id="info1">
                Delays and Cancellations 
                <i class="arrow down"></i>
                <i class="arrow up" style="display: none;"></i>
            </button>
            <div class="info-details" id="info1">
                <p>As a third-party booking service, we are not responsible for delays or cancellations related to flights or hotels. For any issues with your tour guide, please contact us directly for assistance.</p>
            </div>
        </div>

        <div class="info-item">
            <button class="info-btn" data-id="info2">
                Refunds 
                <i class="arrow down"></i>
                <i class="arrow up" style="display: none;"></i>
            </button>
            <div class="info-details" id="info2">
                <p>Refunds are not provided through our service, as we act solely as a booking platform. Please reach out to the airline, hotel, or service provider directly for any refund inquiries.</p>
            </div>
        </div>

        <div class="info-item">
            <button class="info-btn" data-id="info3">
                No Change or Cancel Fees 
                <i class="arrow down"></i>
                <i class="arrow up" style="display: none;"></i>
            </button>
            <div class="info-details" id="info3">
                <p>We do not charge any fees for changing or cancelling your bookings. However, please refer to the policies of the respective airline, hotel, or service provider for any applicable charges.</p>
            </div>
        </div>

        <div class="contact">
            <p>Still Need HELP? <a href="#" class="contact-link" id="openModal">Contact Us</a></p>
            </div>
        </div>
    </div>

    <div id="contactModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Contact Us</h2>
        <p>Got any questions or suggestions? Fill out this form to reach out!</p>
        <form id="contactForm" method="POST">
            <?php if ($isLoggedIn): ?>
                <p>Hi, <?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?>!</p>
                <input type="hidden" id="userName" name="userName" value="<?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" id="userEmail" name="userEmail" value="<?php echo htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8'); ?>">
            <?php else: ?>
                <input type="text" id="userName" name="userName" placeholder="Enter your name" required>
                <input type="email" id="userEmail" name="userEmail" placeholder="Enter your email" required>
            <?php endif; ?>
            <textarea name="userMessage" placeholder="Enter your message" required></textarea>
            <button type="submit" class="submit-btn">Submit</button>
        </form>
        <div id="toast" class="toast">Message successfully sent!</div>
    </div>
</div>

    <script src="js/index.js"></script>

    <div class="footer">
        <p>TravelTrail</p>
    </div>
</body>
</html>