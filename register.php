<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="register.css">
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
    
    <?php 
    include 'firebase_connection.php';
    ?>
    
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
            <a class="nav-link" href="login.php">Login</a>
        </div>
    </div>
    
    <div class="container-1">
        <div class="container">
            <h2>Register</h2>
            <form id="registerForm">
                <div class="form-group">
                    <label for="profileImage">Profile Image</label>
                    <input type="file" id="profileImage" name="profileImage" accept="image/*" required>
                </div>
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter your name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" placeholder="Enter your address" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
                </div>
                <button type="submit">Register</button>
            </form>
        </div>
    </div>

    <script>
       
        const firebaseConfig = {
            // Add your Firebase configuration here
            apiKey: "AIzaSyAef9-sjwyQL-MAiUYLUgBO0p68QuRGRNI",
            authDomain: "traveltrail-39e23.firebaseapp.com",
            databaseURL: "https://traveltrail-39e23-default-rtdb.firebaseio.com/",
            projectId: "traveltrail-39e23",
            storageBucket: "gs://traveltrail-39e23.appspot.com",
            messagingSenderId: "91519152452",
            appId: "1:91519152452:web:422ee3957f7b21778fa711"
        };

        
        firebase.initializeApp(firebaseConfig);

        
        const auth = firebase.auth();

       
        const db = firebase.database();

       
        const storage = firebase.storage();

        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const name = document.getElementById('name').value;
            const phone = document.getElementById('phone').value;
            const address = document.getElementById('address').value;
            const profileImage = document.getElementById('profileImage').files[0];

            
            auth.createUserWithEmailAndPassword(email, password)
                .then((userCredential) => {
                  
                    const user = userCredential.user;

                   
                    const storageRef = storage.ref('profile_images/' + user.uid);
                    return storageRef.put(profileImage).then(() => {
                        
                        return storageRef.getDownloadURL();
                    }).then((downloadURL) => {
                        
                        return db.ref('users/' + user.uid).set({
                            name: name,
                            email: email,
                            phone: phone,
                            address: address,
                            profileImageUrl: downloadURL
                        });
                    }).then(() => {
                        console.log('User registered successfully');
                        
                        window.location.href = 'login.php';
                    });
                })
                .catch((error) => {
                    const errorCode = error.code;
                    const errorMessage = error.message;
                    console.error('Error registering user:', errorMessage);
                });
        });
    </script>
</body>
</html>
