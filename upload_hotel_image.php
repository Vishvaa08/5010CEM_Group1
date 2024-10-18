<?php
require 'vendor/autoload.php';
use Kreait\Firebase\Factory;
use Kreait\Firebase\Storage;
use Kreait\Firebase\Exception\FirebaseException;

$serviceAccount = __DIR__ . '/prvkey.json';

$factory = (new Factory)
    ->withServiceAccount($serviceAccount)
    ->withDatabaseUri('https://traveltrail-39e23-default-rtdb.firebaseio.com/');

$database = $factory->createDatabase();
$storage = $factory->createStorage();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hotelId = $_POST['hotelId'] ?? null;
    $country = $_POST['country'] ?? null;
    $city = $_POST['city'] ?? null;
    
    if (!$hotelId || !$country || !$city) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters.']);
        exit;
    }

    $imageUrl = '';

    if (isset($_FILES['hotelImage']) && $_FILES['hotelImage']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['hotelImage'];
        $filePath = 'hotels/' . basename($file['name']);
        $storageBucket = $storage->getBucket();
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        if (!in_array($file['type'], $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.']);
            exit;
        }

        try {
            $storageBucket->upload(
                fopen($file['tmp_name'], 'r'),
                ['name' => $filePath, 'predefinedAcl' => 'publicRead']
            );
            $imageUrl = "https://firebasestorage.googleapis.com/v0/b/traveltrail-39e23.appspot.com/o/" . urlencode($filePath) . "?alt=media";
        } catch (Exception $e) {
            error_log('Error uploading hotel image: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to upload the hotel image.']);
            exit;
        }
    }

    $hotelData = [
        'Hotel' => $_POST['hotelName'] ?? 'N/A',
        'Description' => $_POST['description'] ?? '',
        'Image' => $imageUrl
    ];

    try {
        $database->getReference("Hotels/$hotelId")->set($hotelData);
        echo json_encode(['success' => true, 'imageUrl' => $imageUrl]);
    } catch (FirebaseException $e) {
        error_log('Error saving hotel data: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to save hotel data.']);
    }
}
?>
