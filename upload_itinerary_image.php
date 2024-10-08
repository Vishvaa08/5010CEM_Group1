<?php
require 'vendor/autoload.php';
use Kreait\Firebase\Factory;

// Include your Firebase connection details
include 'firebase_connection.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if an image file was uploaded
    if (isset($_FILES['itineraryImage']) && is_array($_FILES['itineraryImage'])) {
        $imageUrls = []; // Array to hold URLs of uploaded images

        foreach ($_FILES['itineraryImage']['tmp_name'] as $index => $tmpName) {
            if ($_FILES['itineraryImage']['error'][$index] === UPLOAD_ERR_OK) {
                $fileTmpPath = $tmpName;
                $fileType = $_FILES['itineraryImage']['type'][$index];
                $originalFileName = $_FILES['itineraryImage']['name'][$index];
                $safeFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $originalFileName);

                // Check file type (Allowing only certain types)
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (in_array($fileType, $allowedTypes)) {
                    try {
                        // Upload the image to Firebase Storage
                        $bucket = $storage->getBucket();
                        $bucket->upload(
                            fopen($fileTmpPath, 'r'),
                            ['name' => 'itineraries/' . $safeFileName, 'metadata' => ['contentType' => $fileType]]
                        );

                        // Generate new public URL for the image
                        $imageUrl = "https://firebasestorage.googleapis.com/v0/b/traveltrail-39e23.appspot.com/o/itineraries%2F" . urlencode($safeFileName) . "?alt=media";
                        $imageUrls[] = $imageUrl; // Add URL to the array

                    } catch (Exception $e) {
                        echo json_encode(['success' => false, 'message' => 'Failed to upload the itinerary image: ' . $e->getMessage()]);
                        exit; // Exit on first error
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.']);
                    exit;
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Upload error for file index: ' . $index]);
                exit; // Exit on error
            }
        }

        // Return the array of image URLs if all uploads were successful
        echo json_encode(['success' => true, 'imageUrls' => $imageUrls]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error.']);
    }
}
?>
