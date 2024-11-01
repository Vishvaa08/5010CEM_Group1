<?php
include 'firebase_connection.php';

session_start();  

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userName = isset($_POST['userName']) ? $_POST['userName'] : 'Anonymous';
    $userMessage = isset($_POST['userMessage']) ? $_POST['userMessage'] : '';

    if (isset($_SESSION['userEmail'])) {
        $userEmail = $_SESSION['userEmail'];  
    } else {
        $userEmail = isset($_POST['userEmail']) ? $_POST['userEmail'] : 'No email provided';
    }

    $messageData = [
        'userName' => $userName,
        'userEmail' => $userEmail,  
        'userMessage' => $userMessage,
        'timestamp' => date('Y-m-d')  
    ];

    try {
        $database->getReference('adminNotifications')->push($messageData);

        echo "Message successfully sent!";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
