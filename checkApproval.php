<?php
session_start();
include 'firebase_connection.php'; 

$userId = $_SESSION['user_id'];
$db = $factory->createDatabase();
$user = $db->getReference('users/' . $userId)->getValue();

if ($user) {
    echo json_encode(['approved' => $user['status'] === 'approved']);
} else {
    echo json_encode(['approved' => false]);
}
?>
