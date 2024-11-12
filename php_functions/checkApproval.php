<?php
session_start();
include '../firebase_connection.php';

// Ensure the user is logged in
if (!isset($_SESSION['uid'])) {
    echo json_encode(['approved' => false]);
    exit();
}

$uid = $_SESSION['uid'];

// Fetch the user's data from Firebase
$userRef = $db->getReference('users/' . $uid);
$userData = $userRef->getValue();

// Check if the user's status is now approved
if (isset($userData['status']) && $userData['status'] === 'approved') {
    echo json_encode(['approved' => true]);
} else {
    echo json_encode(['approved' => false]);
}
?>
