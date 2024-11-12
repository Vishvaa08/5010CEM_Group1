<?php
include '../firebase_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $country = $_POST['country'] ?? '';
    $city = $_POST['city'] ?? '';

    if (empty($country) || empty($city)) {
        echo json_encode(['success' => false, 'message' => 'Country and city must be specified.']);
        exit;
    }

    try {
        $database->getReference("Packages/$country/$city")->remove(); 

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to delete city package: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
