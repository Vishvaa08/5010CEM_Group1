<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PBE</title>
    <link rel="stylesheet" type="text/css" href="css/pbe.css">
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

    <div id="pbe-container-bg">
        <div id="pbe-container">
            <div id="top">
                <div class="logo-pbe"><img src="images/pbe.jpg"></div>
                <div class="pbe-title">
                    <div class="text-text">Public Bank</div>
                </div>
                <div class="text-price">
                    <div class="text-text2"><?php echo 'RM6969' ?></div>
                </div>
            </div>
            <div id="content">
                <div id="text-title2">
                    <div class="text-recipient">
                        <div class="recipient">Recipient</div>
                    </div>
                </div>
                <div id="text-title">
                    <input type="text" placeholder="1048-1468-3398  -> TravelTrail" class="recipient-input" readonly>
                </div>
                <div id="text-title2">
                    <div class="text-recipient">
                        <div class="recipient">Account Number</div>
                    </div>
                </div>
                <div id="text-title">
                <input type="text" placeholder="Your Account Number" class="recipient-input"></div>
                <div id="text-title2"></div>
                <div id="text-title">
                    <div id="bg"><input type="file" class="upload" accept=".png, .jpg, .jpeg"></div>
                </div>
                <div id="text-title">
                    <div id="btn-container">
                        <button class="confirm-btn">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>