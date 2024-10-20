<?php
require 'firebase_connection.php'; // Include Firebase connection

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $auth = $factory->createAuth();  // Firebase Auth
        $signInResult = $auth->signInWithEmailAndPassword($email, $password);
        $userId = $signInResult->firebaseUserId();

        // Fetch user role and status from Firebase Realtime Database
        $userSnapshot = $database->getReference('users/' . $userId)->getValue();
        $role = $userSnapshot['role'] ?? null;
        $status = $userSnapshot['status'] ?? null;

        // Store session
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_role'] = $role;

        // Redirect based on role and status
        if ($role === 'admin') {
            if ($status === 'pending') {
                header("Location: adminWait.php");
            } elseif ($status === 'approved') {
                header("Location: AdminDashboard.php");
            }
        } else {
            echo "You do not have admin access.";
        }
    } catch (Exception $e) {
        echo "Invalid email or password.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="css/Login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

   <div class="login-container">
        <h1>Admin Login</h1>
        <form id="loginForm" action="" method="POST">
            <div class="input-field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter Email" required>
            </div>
            <div class="input-field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter Password" required>
                <span class="eye-icon" onclick="togglePassword()">
                    <i class="fa fa-eye-slash"></i>
                </span>
            </div>
            <button type="submit" class="login-btn">Login</button>
        </form>

       <div id="error-message">
            <?php
            if (isset($error_message)) {
                echo "<p style='color:red; text-align:center;'>" . htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') . "</p>";
            }
            ?>
        </div>
    </div>

    <script>
        function togglePassword() {
            var passwordInput = document.getElementById("password");
            var icon = document.querySelector(".eye-icon i");

            if (passwordInput.type === "password") {
                passwordInput.type = "text"; 
                icon.classList.remove("fa-eye-slash"); 
                icon.classList.add("fa-eye"); // Open eye
            } else {
                passwordInput.type = "password";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash"); // Close eye
            }
        }
    </script>
</body>
</html>
