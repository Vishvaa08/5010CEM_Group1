<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['userName'] = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
    $_SESSION['profileImage'] = isset($_POST['profileImageUrl']) ? htmlspecialchars($_POST['profileImageUrl']) : '';
}
?>