<?php
include 'firebase_connection.php';

$country = $_POST['country'];
$city = $_POST['city'];

$reference = $database->getReference('Packages/' . $country . '/' . $city);
$data = $reference->getValue();
$data['visible'] = !$data['visible']; // Toggle visibility
$reference->set($data);

echo json_encode(['message' => $data['visible'] ? 'City package is now visible.' : 'City package is now hidden.']);
?>
