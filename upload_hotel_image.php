<?php
require 'vendor/autoload.php';
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\FirebaseException;

include 'firebase_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hotelId = $_POST['hotelId'] ?? null;
    $country = $_POST['country'] ?? null;
    $city = $_POST['city'] ?? null;

    if (!$hotelId || !$country || !$city || !isset($_FILES['hotelImage'])) {
        echo json_encode(['success' => false, 'message' => 'Required parameters are missing.']);
        exit;
    }

    $fileTmpPath = $_FILES['hotelImage']['tmp_name'];
    $fileName = $_FILES['hotelImage']['name'];
    $fileType = $_FILES['hotelImage']['type'];
    $safeFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $fileName);

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (in_array($fileType, $allowedTypes)) {
        try {
            $bucket = $storage->getBucket();
            $bucket->upload(
                fopen($fileTmpPath, 'r'),
                ['name' => 'hotels/' . $safeFileName, 'metadata' => ['contentType' => $fileType]]
            );

            $imageUrl = "https://firebasestorage.googleapis.com/v0/b/traveltrail-39e23.appspot.com/o/hotels%2F" . urlencode($safeFileName) . "?alt=media";
            echo json_encode(['success' => true, 'imageUrl' => $imageUrl]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Image upload failed: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.']);
    }
}
?>
