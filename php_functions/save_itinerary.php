<?php

include '../firebase_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode($_POST['data'], true);

    if (!isset($data['Itinerary']) || !isset($data['ItineraryPrice'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid itinerary data.']);
        exit;
    }

    $country = $_POST['country'] ?? null;
    $city = $_POST['city'] ?? null;

    if (!$country || !$city) {
        echo json_encode(['success' => false, 'message' => 'Country or city not specified.']);
        exit;
    }

    $imageUrl = ''; 
    if (isset($_FILES['itineraryImage']) && $_FILES['itineraryImage']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['itineraryImage'];
        $filePath = 'itinerary_images/' . time() . '_' . basename($file['name']); 
        $storageBucket = $storage->getBucket(); 
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    
        if (!in_array($file['type'], $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.']);
            exit;
        }
    
        try {
            $storageBucket->upload(
                fopen($file['tmp_name'], 'r'),
                [
                    'name' => $filePath, 
                    'predefinedAcl' => 'publicRead' 
                ]
            );
    
            $imageUrl = "https://firebasestorage.googleapis.com/v0/b/traveltrail-39e23.appspot.com/o/" . urlencode($filePath) . "?alt=media&token=";
            error_log("Image uploaded successfully: " . $imageUrl);
        } catch (Exception $e) {
            error_log("Error uploading image: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to upload the itinerary image.']);
            exit;
        }
    } else {
        if (isset($_FILES['itineraryImage']) && $_FILES['itineraryImage']['error'] !== UPLOAD_ERR_OK) {
            error_log("Image upload error: " . $_FILES['itineraryImage']['error']);
            echo json_encode(['success' => false, 'message' => 'Error during image upload.']);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'No image file uploaded.']);
            exit;
        }
    }

    $cityReference = $database->getReference("Packages/$country/$city/Itinerary");

    $existingItineraries = $cityReference->getValue() ?? [];

    if (!empty($existingItineraries)) {
        $existingIds = array_keys($existingItineraries); 
        $maxId = max($existingIds); 
        $nextId = $maxId + 1; 
    } else {
        $nextId = 1; 
    }

    $itineraryData = [
        'Itinerary' => $data['Itinerary'],
        'Image' => $imageUrl, 
        'Price' => $data['ItineraryPrice']
    ];

    try {
        $cityReference->getChild($nextId)->set($itineraryData); 
        echo json_encode(['success' => true, 'message' => 'Itinerary saved successfully!']);
    } catch (Exception $e) {
        error_log("Error saving itinerary data: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to save itinerary data.']);
    }
}
?>
