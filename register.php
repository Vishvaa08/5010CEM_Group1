<?php
include 'firebase_connection.php'; // Include Firebase connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $role = $_POST['role'];
    $profileImage = $_FILES['profileImage'];

    // Create user with Firebase Auth
    $auth = $factory->createAuth();
    $storage = $factory->createStorage();
    $db = $factory->createDatabase();

    try {
        // Register the user
        $user = $auth->createUserWithEmailAndPassword($email, $password);
        $userId = $user->uid;

        // Upload profile image
        $storageRef = $storage->getBucket()->upload(file_get_contents($profileImage['tmp_name']), [
            'name' => 'profile_images/' . $userId
        ]);
        $profileImageUrl = $storageRef->signedUrl(new \DateTime('+1 year'));

        // Store user data in Firebase Realtime Database
        $db->getReference('users/' . $userId)->set([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'profileImageUrl' => $profileImageUrl,
            'role' => $role,
            'status' => ($role === 'admin') ? 'pending' : 'approved'
           
        ]);

        // Redirect based on role
        if ($role === 'admin') {
            header("Location: adminWait.php");
        } else {
            header("Location: login.php");
        }
        exit;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="css/userRegister.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Joti+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
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
            <a href="index.php">
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
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="user">User</option>
                        <option value="adminREQUEST">Admin</option>
                    </select>
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
            const confirmPassword = document.getElementById('confirmPassword').value;
            const name = document.getElementById('name').value;
            const phone = document.getElementById('phone').value;
            const address = document.getElementById('address').value;
            const profileImage = document.getElementById('profileImage').files[0];
            const role = document.getElementById('role').value;

            // Check if password and confirm password match
            if (password !== confirmPassword) {
                alert("Passwords do not match.");
                return;
            }

            // Create user with Firebase authentication
            auth.createUserWithEmailAndPassword(email, password)
                .then((userCredential) => {
                    const user = userCredential.user;
                    const storageRef = storage.ref('profile_images/' + user.uid);

                    // Upload profile image to Firebase storage
                    return storageRef.put(profileImage).then(() => {
                        return storageRef.getDownloadURL();
                    }).then((downloadURL) => {
                        // Save user data to Firebase database
                        return db.ref('users/' + user.uid).set({
                            name: name,
                            email: email,
                            phone: phone,
                            address: address,
                            profileImageUrl: downloadURL,
                            role: role,
                            status: (role === 'adminREQUEST') ? 'pending' : 'approved' 
                        });
                    }).then(() => {
                        console.log('User registered successfully');
                        alert("Registration successful. Please login.");
                        window.location.href = 'login.php'; 
                    });
                })
                .catch((error) => {
                    const errorMessage = error.message;
                    console.error('Error registering user:', errorMessage);
                    alert(errorMessage); 
                });

        });
    </script>
</body>

</html>
