<?php
session_start();
require 'firebase_connection.php';

if (isset($_SESSION['uid'])) {
    header("Location: user_profile.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $idToken = $data['idToken'];

    try {
        $verifiedIdToken = $auth->verifyIdToken($idToken);
        $uid = $verifiedIdToken->getClaim('sub');

        $_SESSION['uid'] = $uid;
        $_SESSION['idToken'] = $idToken;

        echo json_encode(['success' => true, 'message' => 'Logged in successfully']);
        exit();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="css/userLogin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Joti+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    <!-- Add Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-database-compat.js"></script>
</head>
<body>
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
            <div class="user-profile" id="userProfile"></div>
        </div>
    </div>
    <div class="login-container-1">
        <div class="login-container">
            <h2>Login Form</h2>
            <form id="loginForm">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit">Login</button>
            </form>
            <div class="register-section">
                <p>Don't have an account?</p>
                <button onclick="window.location.href='register.php'">Register</button>
                <a href="php_functions/logout.php">Logout</a>
            </div>
        </div>
    </div>

    <script>
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

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            // Inside your login function
            auth.signInWithEmailAndPassword(email, password)
                .then((userCredential) => {
                    const user = userCredential.user;

                    const userRef = db.ref('users/' + user.uid);
                    userRef.once('value').then((snapshot) => {
                        const userData = snapshot.val();

                        // Store the user's name in the session
                        fetch('php_functions/store_user_name.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: 'name=' + encodeURIComponent(userData.name) + '&profileImageUrl=' + encodeURIComponent(userData.profileImageUrl) + '&email=' + encodeURIComponent(userData.email)
                        });

                        const userProfileDiv = document.getElementById('userProfile');
                        userProfileDiv.innerHTML = `
                <p>Welcome, ${userData.name}</p>
                <img src="${userData.profileImageUrl}" alt="Profile Image" style="width:50px; height:50px; border-radius:50%;">
            `;
                        if (userData.role === 'admin') {
                            window.location.href = 'AdminDashboard.php';
                        } else if (userData.role === 'adminREQUEST') {
                            window.location.href = 'adminWait.php';
                        } else {
                            window.location.href = 'index.php';
                        }
                    });
                })
                .catch((error) => {
                    const errorCode = error.code;
                    const errorMessage = error.message;
                    console.error('Error logging in:', errorMessage);

                    alert('Login failed. Please check your email and password.');
                });

        });
    </script>
</body>
</html>