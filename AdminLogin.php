<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $correct_username = "ADMIN";
    $correct_password = "Admin2306";

    if ($username !== $correct_username) {
        $error_message = "Invalid username.";
    }
  
    else if ($password !== $correct_password) {
        $error_message = "Invalid password.";
    }
   
    else {
        
        header("Location: AdminDashboard.php");
        exit;
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
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter Username" required>
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
                echo "<p style='color:red; text-align:center;'>" . $error_message . "</p>";
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
                icon.classList.add("fa-eye"); //open eye
            } else {
                passwordInput.type = "password";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash"); //close eye
            }
        }
    </script>


</body>
</html>
