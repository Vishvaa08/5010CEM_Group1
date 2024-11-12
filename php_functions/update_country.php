<?php
include '../firebase_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['country']) || empty($data['country']) || !isset($data['countryDetail']) || empty($data['countryDetail'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid or missing data']);
        exit();
    }

    $country = $data['country'];
    $countryDetail = $data['countryDetail'];
    $updateData = [
        'CountryDetail' => $countryDetail 
    ];

    try {
        $database->getReference('Packages/' . $country)->update($updateData);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error updating country: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

?>
