<?php
session_start();

if (isset($_SESSION['userName'])) {
    header("Location: ../userprofile.php");
    exit();
}else{
    header("Location: ../login.php");
}