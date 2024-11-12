<?php
include '../firebase_connection.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is logged in
    if (isset($_SESSION['userEmail'])) {
        // If logged in, get name and email from session
        $userName = isset($_SESSION['userName']) ? $_SESSION['userName'] : 'Anonymous';
        $userEmail = $_SESSION['userEmail'];
    } else {
        // If not logged in, get name and email from form inputs
        $userName = isset($_POST['userName']) ? htmlspecialchars($_POST['userName'], ENT_QUOTES, 'UTF-8') : 'Anonymous';
        $userEmail = isset($_POST['userEmail']) ? htmlspecialchars($_POST['userEmail'], ENT_QUOTES, 'UTF-8') : 'No email provided';
    }

    // Get message from form input
    $userMessage = isset($_POST['userMessage']) ? htmlspecialchars($_POST['userMessage'], ENT_QUOTES, 'UTF-8') : '';

    // Prepare data to be stored in Firebase
    $messageData = [
        'userName' => $userName,
        'userEmail' => $userEmail,
        'userMessage' => $userMessage,
        'timestamp' => date('Y-m-d')
    ];

    // Attempt to store data in Firebase
    try {
        $database->getReference('adminNotifications')->push($messageData);
        echo "<script>document.getElementById('toast').classList.add('show');</script>";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>