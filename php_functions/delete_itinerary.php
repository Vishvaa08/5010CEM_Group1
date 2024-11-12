<?php
include '../firebase_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $country = $_POST['country'] ?? '';
    $city = $_POST['city'] ?? '';
    $index = $_POST['index'] ?? '';

    if (empty($country) || empty($city) || empty($index)) {
        echo json_encode(['success' => false, 'message' => 'Country, city, and index must be specified.']);
        exit;
    }

    try {
        $database->getReference("Packages/$country/$city/Itinerary/$index")->remove();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
