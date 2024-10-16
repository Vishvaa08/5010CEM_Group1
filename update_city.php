<?php
include 'firebase_connection.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $country = $_POST['country'];
    $city = $_POST['city'];
    $data = json_decode($_POST['data'], true);

    if (!empty($country) && !empty($city) && isset($data['Availability'])) {
        try {
            $reference = $database->getReference('Packages/' . $country . '/' . $city);

            $reference->update([
                'Availability' => $data['Availability']
            ]);

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
