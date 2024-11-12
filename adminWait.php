<?php
session_start();
include 'firebase_connection.php'; 

$pic = '';
$name = '';

// Check if session variables are set
if (isset($_SESSION['userName'])) {
    $pic = $_SESSION['profileImage'];
    $name = $_SESSION['userName'];
} else {
    $pic = 'images/user.jpg';
    $name = 'Admin';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Pending Approval</title>
    <link rel="stylesheet" type="text/css" href="css/adminWait.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 50px;
        }
        h1 {
            color: #333;
        }
        p {
            font-size: 18px;
            color: #555;
        }
        .profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }
    </style>
</head>
<body>

    <h1>Account Pending Approval</h1>
    <img src="<?php echo $pic; ?>" alt="Profile Image" class="profile-pic">
    <p>Hello, <?php echo $name; ?>! Your account is currently awaiting admin approval.</p>
    <p>Please wait for the admin to approve your account.</p>

    <script>
        // Periodically check for approval every 10 seconds
        setInterval(function() {
            fetch('/php_functions/checkApproval.php')
                .then(response => response.json())
                .then(data => {
                    if (data.approved) {
                        // Redirect to AdminDashboard if approved
                        window.location.href = 'AdminDashboard.php';
                    }
                })
                .catch(error => console.error('Error checking approval:', error));
        }, 10000); // Check every 10 seconds
    </script>

</body>
</html>
