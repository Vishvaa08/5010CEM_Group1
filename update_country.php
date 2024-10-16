<?php
include 'firebase_connection.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['country']) && isset($data['availability'])) {
        $country = $data['country'];
        $availability = $data['availability'];

        $updateData = [
            'Availability' => $availability
        ];

        try {
            $database->getReference('Packages/' . $country)->update($updateData);

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
