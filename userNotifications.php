<?php
session_start();
include 'firebase_connection.php';

$pic = 'images/user.png'; 
$isLoggedIn = false; 
$userName = 'Guest'; 
$userEmail = ''; 

// Check if the user is logged in
if (isset($_SESSION['userName'])) {
    $pic = $_SESSION['profileImage'] ?? $pic; 
    $isLoggedIn = true;
    $userName = $_SESSION['userName']; 
    $userEmail = $_SESSION['userEmail'] ?? ''; 
}

$notifications = [];
if ($isLoggedIn) {
    $notificationsRef = $database->getReference('userNotifications');
    $snapshot = $notificationsRef->getValue();

    if ($snapshot) {
        foreach ($snapshot as $key => $notification) {
            // Check if the notification is for the logged-in user (by email or name)
            if ((isset($notification['userEmail']) && $notification['userEmail'] === $userEmail) || 
                (isset($notification['userName']) && $notification['userName'] === $userName)) {
                $notifications[] = $notification;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Notifications</title>
    <link rel="stylesheet" type="text/css" href="css/usernotification.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Joti+One&display=swap" rel="stylesheet">
</head>
<body>
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
            <a href="php_functions/logout.php" class="login-btn">Logout</a> 
            <a href="php_functions/user_login_check.php" class="user-profile">
            <img src="<?php echo htmlspecialchars($pic); ?>" style="width:75px; height:75px; border-radius:50%; object-fit:cover;"></a>
        </div>
    </div>

    <div class="container">
        <h2>User Notifications</h2>
        <div id="notificationsContainer">
            <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $notification): ?>
                    <div class="notification">
                        <p><strong><?php echo htmlspecialchars($notification['userName'] ?? 'Unknown User'); ?></strong>: <?php echo htmlspecialchars($notification['adminReply'] ?? 'No reply'); ?></p>
                        <p><em><?php echo isset($notification['timestamp']) ? date('Y-m-d H:i:s', strtotime($notification['timestamp'])) : 'No date'; ?></em></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No notifications available for you.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
