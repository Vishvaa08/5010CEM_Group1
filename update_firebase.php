<?php
include 'firebase_connection.php';
use Kreait\Firebase\Factory;
use Kreait\Firebase\Storage;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $country = $_POST['country'];
    $city = $_POST['city'];
    $cityDetail = $_POST['cityDetail'];

    // Initialize Firebase Storage
    $storage = $factory->createStorage();
    $bucket = $storage->getBucket();

    // Check if the user uploaded a new image
    if (isset($_FILES['cityImage']) && $_FILES['cityImage']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['cityImage']['tmp_name'];
        $fileName = $_FILES['cityImage']['name'];

        // Upload the new file to Firebase Storage and replace the old one
        try {
            $firebaseStoragePath = 'cities/' . $fileName; // Path in Firebase Storage
            $bucket->upload(
                fopen($fileTmpPath, 'r'), [
                    'name' => $firebaseStoragePath,
                    'metadata' => [
                        'contentType' => $_FILES['cityImage']['type']
                    ]
                ]
            );

            // Get the new download URL
            $imageReference = $bucket->object($firebaseStoragePath);
            $cityImageUrl = $imageReference->signedUrl(new \DateTime('+1 years')); // Valid for 1 year

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Image upload failed: ' . $e->getMessage()]);
            exit();
        }
    } else {
        // No new image uploaded, keep the old one
        $cityImageUrl = null;
    }

    // Prepare data to update
    $updatedCityData = [
        'CityDetail' => $cityDetail
    ];

    if ($cityImageUrl) {
        $updatedCityData['CityImage'] = $cityImageUrl; // If a new image was uploaded, update the image URL
    }

    // Reference to the city's data in Firebase
    $reference = $database->getReference('Packages/' . $country . '/' . $city);

    // Update city data in Firebase
    try {
        $reference->update($updatedCityData);
        echo json_encode(['success' => true, 'message' => 'City updated successfully']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to update city: ' . $e->getMessage()]);
    }
}
?>
