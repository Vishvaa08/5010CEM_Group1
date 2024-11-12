<?php
include '../firebase_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $country = $_POST['country'] ?? '';
    $city = $_POST['city'] ?? '';
    $hotelIndex = $_POST['hotelIndex'] ?? '';

    if (empty($country) || empty($city) || $hotelIndex === '') {
        echo json_encode(['success' => false, 'message' => 'Country, city, and hotel index must be specified.']);
        exit;
    }

    try {
        $database->getReference("Packages/$country/$city/Hotels/$hotelIndex")->remove();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
