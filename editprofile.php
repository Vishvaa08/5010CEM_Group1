<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" type="text/css" href="editprofile.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Joti+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    <!-- Add Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-database-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-storage-compat.js"></script>
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
             <a href="userprofile.php" class="userprofile-btn">Back to Profile</a>
        </div>
    </div>
    <div class="container-1">
        <div class="container">
            <h2>Edit Profile</h2>
            <form id="editProfileForm">
                <div class="form-group">
                    <label for="profileImage">Profile Image</label>
                    <input type="file" id="profileImage" name="profileImage" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required readonly>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" required>
                </div>
                <div class="form-group">
                    <label for="passportId">Passport ID</label>
                    <input type="text" id="passportId" name="passportId">
                </div>
                <button type="submit">Update Profile</button>
            </form>
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

        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);

        // Get a reference to the auth, database, and storage services
        const auth = firebase.auth();
        const db = firebase.database();
        const storage = firebase.storage();

        let currentUser;

        // Check if user is logged in
        auth.onAuthStateChanged(function(user) {
            if (user) {
                // User is signed in, fetch and display user data
                currentUser = user;
                fetchUserData(user.uid);
            } else {
                // No user is signed in, redirect to login page
                window.location.href = 'login.html';
            }
        });

        // Fetch user data from Realtime Database
        function fetchUserData(userId) {
            const userRef = db.ref('users/' + userId);
            userRef.once('value').then((snapshot) => {
                const userData = snapshot.val();
                if (userData) {
                    document.getElementById('name').value = userData.name || '';
                    document.getElementById('email').value = userData.email || '';
                    document.getElementById('phone').value = userData.phone || '';
                    document.getElementById('address').value = userData.address || '';
                    document.getElementById('passportId').value = userData.passportId || '';
                } else {
                    console.log("No user data found!");
                }
            }).catch((error) => {
                console.log("Error fetching user data:", error);
            });
        }

        // Handle form submission
        document.getElementById('editProfileForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const name = document.getElementById('name').value;
            const phone = document.getElementById('phone').value;
            const address = document.getElementById('address').value;
            const passportId = document.getElementById('passportId').value;
            const profileImage = document.getElementById('profileImage').files[0];

            const userId = currentUser.uid;

            // Create an object with the updated user data
            const updatedData = {
                name: name,
                phone: phone,
                address: address,
                passportId: passportId
            };

            // If a new profile image is selected, upload it first
            if (profileImage) {
                const storageRef = storage.ref('profile_images/' + userId);
                storageRef.put(profileImage).then(() => {
                    return storageRef.getDownloadURL();
                }).then((downloadURL) => {
                    updatedData.profileImageUrl = downloadURL;
                    updateUserData(userId, updatedData);
                }).catch((error) => {
                    console.error("Error uploading image: ", error);
                });
            } else {
                updateUserData(userId, updatedData);
            }
        });

        // Update user data in Realtime Database
        function updateUserData(userId, updatedData) {
            const userRef = db.ref('users/' + userId);
            userRef.update(updatedData).then(() => {
                console.log("Profile updated successfully!");
                alert("Profile updated successfully!");
                window.location.href = 'userprofile.php';
            }).catch((error) => {
                console.error("Error updating profile: ", error);
                alert("An error occurred while updating your profile. Please try again.");
            });
        }
    </script>
</body>
</html>
