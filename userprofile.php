<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" type="text/css" href="userprofile.css">
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
            <a href="index.php">
                <div class="logo-container">
                    <p style="color: white; font-size: 25px; font-family: 'Joti One', serif;">TT</p>
                </div>
            </a>
            <h1>TravelTrail</h1>
        </div>
        <div id="right-nav">
            <a href="login.php" class="login-btn" id="logoutBtn">Logout</a>
        </div>
    </div>
    <div class="container-1">
        <div class="container">
            <h2>User Profile</h2>
            <div class="profile-section">
                <img id="profileImage" src="" alt="Profile Image" style="width: 150px; height: 150px; object-fit: cover;">
                <div class="profile-details">
                    <p><strong>Name:</strong> <span id="userName"></span></p>
                    <p><strong>Email:</strong> <span id="userEmail"></span></p>
                    <p><strong>Phone:</strong> <span id="userPhone"></span></p>
                    <p><strong>Address:</strong> <span id="userAddress"></span></p>
                    <p><strong>Passport ID:</strong> <span id="userPassport"></span></p>
                    <p><strong>Points Earned:</strong> <span id="userPoints"></span></p>
                </div>
            </div>
            <a href="editprofile.php" class="edit-profile-btn">Edit Profile</a>
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
                
                fetchUserData(user.uid);
            } else {
                
                window.location.href = 'login.html';
            }
        });

       
        function fetchUserData(userId) {
            const userRef = db.ref('users/' + userId);
            userRef.once('value').then((snapshot) => {
                const userData = snapshot.val();
                if (userData) {
                    document.getElementById('profileImage').src = userData.profileImageUrl || 'placeholder.jpg';
                    document.getElementById('userName').textContent = userData.name || 'Not set';
                    document.getElementById('userEmail').textContent = userData.email || 'Not set';
                    document.getElementById('userPhone').textContent = userData.phone || 'Not set';
                    document.getElementById('userAddress').textContent = userData.address || 'Not set';
                    document.getElementById('userPassport').textContent = userData.passportId || 'Not set';
                    document.getElementById('userPoints').textContent = userData.points || '0';
                } else {
                    console.log("No user data found!");
                }
            }).catch((error) => {
                console.log("Error fetching user data:", error);
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
