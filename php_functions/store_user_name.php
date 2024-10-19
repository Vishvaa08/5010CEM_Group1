<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['userName'] = isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : 'Guest';
    $_SESSION['profileImage'] = isset($_POST['profileImageUrl']) ? htmlspecialchars($_POST['profileImageUrl'], ENT_QUOTES, 'UTF-8') : 'images/user.png';
    $_SESSION['userEmail'] = isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : ''; 
}
?>
