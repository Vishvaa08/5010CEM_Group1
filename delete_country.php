<?php
include 'firebase_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['country']) || empty($data['country'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid or missing country']);
        exit();
    }

    $country = $data['country'];

    try {
        $database->getReference('Packages/' . $country)->remove();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error deleting country: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
