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
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>

    <!--Beginning of home section of homepage including navigation-->
    <div id="home">

        <div id="header">
            <div id="left-nav">
                <a href="#home">
                    <div class="logo-container">
                        <p style="color: white; font-size: 25px; font-family: 'Joti One', serif;">TT</p>
                    </div>
                </a>
                <h1>TravelTrail</h1>
            </div>

            <div id="right-nav">
                <a class="nav-link" href="#home">Home</a>
                <a class="nav-link" href="#about">About</a>
                <a class="nav-link" href="#contact">Contact</a>
                <div class="user-profile"></div>
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

    <div id="contact">
    <div class="contact-section">
        <h2>Get the Info you're looking for!</h2>

        <div class="info-item">
            <button class="info-btn" data-id="info1">Delays and Cancellations</button>
            <div class="info-details" id="info1">
                <p>As a third-party booking service, we are not responsible for delays or cancellations related to flights or hotels. 
                For any issues with your tour guide, please contact us directly for assistance.</p>
            </div>
        </div>

        <div class="info-item">
            <button class="info-btn" data-id="info2">Refunds</button>
            <div class="info-details" id="info2">
                <p>Refunds are not provided through our service, as we act solely as a booking platform.
                Please reach out to the airline, hotel, or service provider directly for any refund inquiries.</p>
            </div>
        </div>

        <div class="info-item">
            <button class="info-btn" data-id="info3">No Change or Cancel Fees</button>
            <div class="info-details" id="info3">
                <p>We do not charge any fees for changing or cancelling your bookings. 
                However, please refer to the policies of the respective airline, hotel, or service provider for any applicable charges.</p>
            </div>
        </div>

        <div class="contact">
        <p>Still Need HELP? <a href="#" class="contact-link" id="openModal">Contact Us</a></p>
        </div>
    </div>
</div>

    <!-- Modal structure -->
        <div id="contactModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Contact Us</h2>
                <p>Got any questions or suggestions? Fill out this form to reach out!</p>
                <form id="contactForm">
                    
                    <div id="user-info" style="display: none;">
                        <input type="text" id="userName" placeholder="Enter your name" required>
                        <input type="email" id="userEmail" placeholder="Enter your email" required>
                    </div>
                    
                    <textarea placeholder="Enter your message" required></textarea>
                    <button type="submit" class="submit-btn">Submit</button>
                </form>
            </div>
        </div>


<script>
        const buttons = document.querySelectorAll('.info-btn');

        buttons.forEach(button => {
            button.addEventListener('click', function() {
                const infoId = this.getAttribute('data-id');
                const infoDetails = document.getElementById(infoId);

                if (infoDetails.style.display === 'block') {
                    infoDetails.style.display = 'none';
                } else {
                    document.querySelectorAll('.info-details').forEach(detail => {
                        detail.style.display = 'none';
                    });
                    infoDetails.style.display = 'block';
                }
            });
        });

        const modal = document.getElementById("contactModal");
        const openModal = document.getElementById("openModal");
        const closeBtn = document.querySelector(".close");

        openModal.addEventListener("click", function(event) {
            event.preventDefault();
            modal.style.display = "flex"; 
        });

        closeBtn.addEventListener("click", function() {
            modal.style.display = "none";
        });

        window.addEventListener("click", function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });

</script>





</body>

</html>