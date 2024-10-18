<?php
session_start();

if (isset($_SESSION['userName'])) {
    header("Location: ../user_profile.php");
    exit();
}else{
    header("Location: ../login.php");
}