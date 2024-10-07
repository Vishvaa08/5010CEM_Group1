<?php
include 'firebase_connection.php';

$country = $_POST['country'];
$city = $_POST['city'];

$reference = $database->getReference('Packages/' . $country . '/' . $city);
$reference->remove();

echo json_encode(['message' => 'City package deleted successfully.']);
?>
