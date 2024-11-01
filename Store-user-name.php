<?php
session_start(); // Start the session

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and store the name and profile image URL in session
    $_SESSION['userName'] = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
    $_SESSION['profileImage'] = isset($_POST['profileImageUrl']) ? htmlspecialchars($_POST['profileImageUrl']) : '';
}
?>