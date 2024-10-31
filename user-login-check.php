<?php
session_start(); // Start the session

// Check if the user is logged in by checking if the session variable exists
if (isset($_SESSION['userName'])) {
    header("Location: user_profile.php"); // Redirect to user profile
    exit();
}else{
    header("Location: login.php");
}