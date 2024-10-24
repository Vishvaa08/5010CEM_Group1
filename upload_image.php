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
    if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['imageFile']['tmp_name'];
        $fileType = $_FILES['imageFile']['type'];
        $originalFileName = $_FILES['imageFile']['name'];

        $safeFileName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $originalFileName);
        $uniqueFileName = time() . '_' . $safeFileName;

        $country = $_POST['country'] ?? '';
        $city = $_POST['city'] ?? '';
        $imageType = $_POST['imageType'] ?? 'City';

        if (empty($country) || empty($city)) {
            echo json_encode([
                'success' => false,
                'message' => 'Country and City must be provided.',
            ]);
            exit;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($fileType, $allowedTypes)) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid file type. Only JPG, PNG, and GIF allowed.',
            ]);
            exit;
        }

        try {
            $bucket = $storage->getBucket();
            $bucket->upload(
                fopen($file, 'r'),
                [
                    'name' => 'cities/' . $uniqueFileName,
                    'metadata' => ['contentType' => $fileType],
                ]
            );

            $newImageUrl = "https://firebasestorage.googleapis.com/v0/b/traveltrail-39e23.appspot.com/o/" . urlencode('cities/' . $uniqueFileName) . "?alt=media&token=";

            if ($imageType === 'City') {
                $database->getReference("Packages/$country/$city/CityImage")->set($newImageUrl);
            } elseif ($imageType === 'Banner') {
                $database->getReference("Packages/$country/$city/Images/Banner")->set($newImageUrl);
            } elseif ($imageType === 'Banner2') {
                $database->getReference("Packages/$country/$city/Images/Banner2")->set($newImageUrl);
            }

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
?>
