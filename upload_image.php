<?php
require 'vendor/autoload.php';

use Kreait\Firebase\Factory;

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load the service account
$serviceAccount = __DIR__ . '/prvkey.json';

$factory = (new Factory)
    ->withServiceAccount($serviceAccount)
    ->withDatabaseUri('https://traveltrail-39e23-default-rtdb.firebaseio.com/');

$storage = $factory->createStorage();
$database = $factory->createDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image'])) {
        // Check for upload errors
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode([
                'success' => false,
                'message' => 'File upload error: ' . $_FILES['image']['error'],
            ]);
            exit;
        }

        $file = $_FILES['image']['tmp_name'];
        $fileType = $_FILES['image']['type'];
        $originalFileName = $_FILES['image']['name'];

        // Ensure the filename is safe
        $safeFileName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $originalFileName);
        $uniqueFileName = time() . '_' . $safeFileName; // Create unique name using timestamp

        // Set dynamic path for Firebase Storage
        $country = isset($_POST['country']) ? $_POST['country'] : ''; // Dynamically get country
        $city = isset($_POST['city']) ? $_POST['city'] : ''; // Dynamically get city

        if (empty($country) || empty($city)) {
            echo json_encode([
                'success' => false,
                'message' => 'Country and City must be provided.',
            ]);
            exit;
        }

        try {
            // Check file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($fileType, $allowedTypes)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid file type. Only JPG, PNG, and GIF allowed.',
                ]);
                exit;
            }

            // Upload the new image to Firebase Storage
            $bucket = $storage->getBucket();
            $bucket->upload(
                fopen($file, 'r'),
                [
                    'name' => $uniqueFileName,
                    'metadata' => ['contentType' => $fileType],
                ]
            );

            // Get the public URL of the new image
            $newImageUrl = "https://firebasestorage.googleapis.com/v0/b/traveltrail-39e23.appspot.com/o/" . urlencode($uniqueFileName) . "?alt=media";

            // Update the CityImage with the new image URL (overwriting the old one)
            $database->getReference('Packages/' . $country . '/' . $city . '/CityImage')
                ->set($newImageUrl);

            echo json_encode([
                'success' => true,
                'imageUrl' => $newImageUrl,
            ]);
        } catch (Exception $e) {
            error_log('Upload Error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Failed to upload new image: ' . $e->getMessage(),
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No image file was uploaded.',
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.',
    ]);
}

// Check if cityImage was uploaded and handle it
if (isset($_FILES['cityImage']) && $_FILES['cityImage']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['cityImage']['tmp_name'];
    $fileName = $_FILES['cityImage']['name'];
    $fileSize = $_FILES['cityImage']['size'];
    $fileType = $_FILES['cityImage']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    // Specify your upload directory
    $uploadFileDir = './uploaded_images/';
    $dest_path = $uploadFileDir . $fileName;

    // Move the uploaded file to the desired location
    if (move_uploaded_file($fileTmpPath, $dest_path)) {
        echo json_encode([
            'success' => true,
            'message' => 'File uploaded successfully!',
            'filePath' => $dest_path
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'There was an error moving the uploaded file.'
        ]);
    }
}
?>
