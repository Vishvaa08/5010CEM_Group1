<?php
include 'firebase_connection.php'; // Include Firebase connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $role = $_POST['role'];
    $profileImage = $_FILES['profileImage'];

    // Server-side validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    if ($password !== $confirmPassword) {
        die("Passwords do not match.");
    }
    if (strlen($password) < 6) {
        die("Password must be at least 6 characters.");
    }

    if (empty($name)) {
        die("Name is required.");
    }

    if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        die("Phone number must be between 10 to 15 digits.");
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($profileImage['type'], $allowedTypes) || $profileImage['size'] > 2 * 1024 * 1024) {
        die("Profile image must be JPEG, PNG, or GIF and less than 2MB.");
    }

    // Create user with Firebase Auth
    $auth = $factory->createAuth();
    $storage = $factory->createStorage();
    $db = $factory->createDatabase();

    try {
        $user = $auth->createUserWithEmailAndPassword($email, $password);
        $userId = $user->uid;

        $storageRef = $storage->getBucket()->upload(file_get_contents($profileImage['tmp_name']), [
            'name' => 'profile_images/' . $userId
        ]);
        $profileImageUrl = $storageRef->signedUrl(new \DateTime('+1 year'));

        $db->getReference('users/' . $userId)->set([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'profileImageUrl' => $profileImageUrl,
            'role' => $role,
            'status' => ($role === 'adminREQUEST') ? 'pending' : 'approved'
        ]);

        if ($role === 'adminREQUEST') {
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
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-database-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-storage-compat.js"></script>
</head>

<body>
    <div class="container-1">
        <div class="container">
            <h2>Register</h2>
            <form id="registerForm" method="POST" enctype="multipart/form-data">
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
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const name = document.getElementById('name').value;
            const phone = document.getElementById('phone').value;
            const profileImage = document.getElementById('profileImage').files[0];
            
            const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailPattern.test(email)) {
                alert("Please enter a valid email address.");
                return;
            }

            if (password !== confirmPassword) {
                alert("Passwords do not match.");
                return;
            }
            if (password.length < 6) {
                alert("Password must be at least 6 characters.");
                return;
            }

            const namePattern = /^[A-Za-z\s]+$/;
            if (!namePattern.test(name)) {
               alert("Name must contain only letters and spaces.");
               return;
           }

            const phonePattern = /^[0-9]{10,15}$/;
            if (!phonePattern.test(phone)) {
                alert("Please enter a valid phone number (10-15 digits).");
                return;
            }

            if (!profileImage) {
                alert("Please upload a profile image.");
                return;
            }
            if (profileImage.size > 2 * 1024 * 1024) {
                alert("Profile image must be less than 2MB.");
                return;
            }

            e.target.submit();
        });
    </script>
</body>

</html>
