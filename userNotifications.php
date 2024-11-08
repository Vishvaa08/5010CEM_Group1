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
    <title>User Notifications</title>
    <link rel="stylesheet" type="text/css" href="css/usernotification.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Joti+One&display=swap" rel="stylesheet">
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-database-compat.js"></script>
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
            <a href="login.php" class="login-btn" id="logoutBtn">Logout</a>
            <a href="php_functions/user_login_check.php" class="user-profile">
            <img src="<?php echo $pic; ?>" style="width:75px; height:75px; border-radius:50%; object-fit:cover;"></a>
        </div>
        
    </div>
    <div class="container">
        <h2>User Notifications</h2>
        <div id="notificationsContainer">
            <!-- Notifications will be dynamically added here -->
        </div>
    </div>

    <script>
        // Your web app's Firebase configuration
        const firebaseConfig = {
            apiKey: "AIzaSyAef9-sjwyQL-MAiUYLUgBO0p68QuRGRNI",
            authDomain: "traveltrail-39e23.firebaseapp.com",
            databaseURL: "https://traveltrail-39e23-default-rtdb.firebaseio.com",
            projectId: "traveltrail-39e23",
            storageBucket: "traveltrail-39e23.appspot.com",
            messagingSenderId: "91519152452",
            appId: "1:91519152452:web:422ee3957f7b21778fa711"
        };

        firebase.initializeApp(firebaseConfig);

        const auth = firebase.auth();
        const db = firebase.database();

        auth.onAuthStateChanged(function(user) {
            if (user) {
                fetchUserNotifications(user.uid);
            } else {
                window.location.href = 'login.php';
            }
        });

        function fetchUserNotifications() {
    const user = auth.currentUser;
    const userEmail = user.email; // Get the logged-in user's email
    
    const notificationsRef = db.ref('userNotifications/');
    notificationsRef.once('value').then((snapshot) => {
        const notificationsData = snapshot.val();
        const notificationsContainer = document.getElementById('notificationsContainer');
        notificationsContainer.innerHTML = ''; // Clear any existing notifications

        if (notificationsData) {
            let foundNotifications = false; // Track if any notifications are found for the user

            Object.keys(notificationsData).forEach(key => {
                const notification = notificationsData[key];

                // Check if the notification is for the logged-in user (based on userEmail or userName)
                if (notification.userEmail === userEmail) {
                    foundNotifications = true;
                    const userName = notification.userName || 'Unknown User';
                    const adminReply = notification.adminReply || 'No reply';
                    const timestamp = notification.timestamp ? new Date(notification.timestamp).toLocaleString() : 'No date';

                    // Create a notification element to display the details
                    const notificationElement = document.createElement('div');
                    notificationElement.classList.add('notification');
                    notificationElement.innerHTML = `
                        <p><strong>${userName}</strong>: ${adminReply}</p>
                        <p><em>${timestamp}</em></p>
                    `;
                    notificationsContainer.appendChild(notificationElement);
                }
            });

            if (!foundNotifications) {
                notificationsContainer.innerHTML = '<p>No notifications available for you.</p>';
            }
        } else {
            notificationsContainer.innerHTML = '<p>No notifications available.</p>';
        }
    }).catch((error) => {
        console.error('Error fetching notifications:', error);
    });
}

        document.getElementById('logoutBtn').addEventListener('click', function(e) {
            e.preventDefault();
            auth.signOut().then(() => {
                window.location.href = 'login.php';
            }).catch((error) => {
                console.error('Logout Error:', error);
            });
        });
    </script>
</body>
</html>
