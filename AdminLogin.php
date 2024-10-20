<?php
require 'firebase_connection.php'; 

session_start();
$error_message = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $auth = $factory->createAuth();  
        $signInResult = $auth->signInWithEmailAndPassword($email, $password);
        $userId = $signInResult->firebaseUserId();

        $userSnapshot = $database->getReference('users/' . $userId)->getValue();
        $role = $userSnapshot['role'] ?? null;
        $status = $userSnapshot['status'] ?? null;

        $_SESSION['user_id'] = $userId;
        $_SESSION['user_role'] = $role;

        if ($role === 'admin') {
            header("Location: AdminDashboard.php");
            exit();
        } elseif ($role === 'adminREQUEST') {
            if ($status === 'pending') {
                header("Location: adminWait.php");
                exit();
            } elseif ($status === 'approved') {
                header("Location: AdminDashboard.php");
                exit();
            }
        } else {
            echo "You do not have admin access.";
        }
        
    } catch (Exception $e) {
        $error_message = "Invalid email or password."; 
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
            <div id="error-message" style="color:red; text-align:center;">
                <?php
                if ($error_message) {
                    echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); 
                }
                ?>
            </div>
            <button type="submit" class="login-btn">Login</button>
        </form>
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
