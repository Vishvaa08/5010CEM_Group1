<?php
require 'vendor/autoload.php';

use Kreait\Firebase\Factory;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$serviceAccount = __DIR__ . '/prvkey.json';

$factory = (new Factory)
    ->withServiceAccount($serviceAccount)
    ->withDatabaseUri('https://traveltrail-39e23-default-rtdb.firebaseio.com/');

$storage = $factory->createStorage();
$database = $factory->createDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $country = $_POST['country'];
    $cityName = $_POST['cityName'];
    $cityDescription = $_POST['cityDescription'];

    if (isset($_FILES['cityImage']) && $_FILES['cityImage']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['cityImage']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode([
                'success' => false,
                'message' => 'File upload error: ' . $_FILES['cityImage']['error'],
            ]);
            exit;
        }

        $file = $_FILES['cityImage']['tmp_name'];
        $fileType = $_FILES['cityImage']['type'];
        $originalFileName = $_FILES['cityImage']['name']; 

        $safeFileName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $originalFileName);

        $firebaseFilePath = $safeFileName; 

        try {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($fileType, $allowedTypes)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid file type. Only JPG, PNG, and GIF allowed.',
                ]);
                exit;
            }

            // Upload to Firebase Storage
            $bucket = $storage->getBucket();
            $bucket->upload(
                fopen($file, 'r'),
                [
                    'name' => $firebaseFilePath,
                    'metadata' => ['contentType' => $fileType],
                ]
            );

            $newImageUrl = "https://firebasestorage.googleapis.com/v0/b/traveltrail-39e23.appspot.com/o/" . urlencode($firebaseFilePath) . "?alt=media";

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to upload new image: ' . $e->getMessage(),
            ]);
            exit;
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No image file was uploaded.',
        ]);
        exit;
    }

    $newCityData = [
        'City' => $cityName,
        'CityDetail' => $cityDescription,
        'CityImage' => $newImageUrl
    ];

    $reference = $database->getReference('Packages/' . $country . '/' . $cityName);

    try {
        $reference->set($newCityData);
        echo json_encode(['success' => true, 'message' => 'City added successfully']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to add city: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
