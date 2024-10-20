<?php
session_start();
include 'firebase_connection.php'; 

$db = $factory->createDatabase();
$user = $db->getReference('users/' . $_SESSION['user_id'])->getValue();

if (!$user || $user['status'] !== 'pending') {
    header("Location: index.php"); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pending Approval</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 50px;
        }
        h1 {
            color: #333;
        }
        p {
            font-size: 18px;
            color: #555;
        }
    </style>
</head>
<body>
    <h1>Account Pending Approval</h1>
    <p>Please wait for admin to approve your admin account.</p>
    <script>
        setInterval(function() {
            fetch('checkApproval.php')
                .then(response => response.json())
                .then(data => {
                    if (data.approved) {
                        window.location.href = 'AdminDashboard.php'; 
                    }
                })
                .catch(error => console.error('Error checking approval:', error));
        }, 10000); 
    </script>
</body>
</html>

