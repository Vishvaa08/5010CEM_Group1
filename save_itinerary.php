<?php
require 'vendor/autoload.php';
use Kreait\Firebase\Factory;
use Kreait\Firebase\Storage;

$serviceAccount = __DIR__ . '/prvkey.json';

$factory = (new Factory)
    ->withServiceAccount($serviceAccount)
    ->withDatabaseUri('https://traveltrail-39e23-default-rtdb.firebaseio.com/');

$database = $factory->createDatabase();
$storage = $factory->createStorage();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode($_POST['data'], true); // Get the data sent via AJAX

    // Validate inputs
    if (!isset($data['Itinerary']) || !isset($data['ItineraryPrice'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid itinerary data.']);
        exit;
    }

    $country = $_POST['country'] ?? null;
    $city = $_POST['city'] ?? null;

    // Validate country and city
    if (!$country || !$city) {
        echo json_encode(['success' => false, 'message' => 'Country or city not specified.']);
        exit;
    }

    // Handle image upload
    $imageUrl = ''; // Initialize the variable to hold image URL
    if (isset($_FILES['itineraryImage']) && $_FILES['itineraryImage']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['itineraryImage'];
        $filePath = 'itinerary_images/' . basename($file['name']); // Path to save image
        $storageBucket = $storage->getBucket();

        // Validate the image type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.']);
            exit;
        }

        // Upload file to Firebase Storage
        try {
            $storageBucket->upload(
                fopen($file['tmp_name'], 'r'),
                [
                    'name' => $filePath,
                    'predefinedAcl' => 'publicRead' // Make the image publicly readable
                ]
            );

            // Get the public URL of the uploaded image
            $imageUrl = "https://firebasestorage.googleapis.com/v0/b/traveltrail-39e23.appspot.com/o/" . urlencode($filePath) . "?alt=media";
        } catch (Exception $e) {
            error_log("Error uploading image: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to upload the itinerary image.']);
            exit;
        }
    }

    // Reference to the specific city in the database
    $cityReference = $database->getReference("Packages/$country/$city/Itinerary");
    
    // Fetch existing itineraries to determine the next ID
    $existingItineraries = $cityReference->getValue() ?? [];
    $nextId = count($existingItineraries) + 1; // Determine the next itinerary ID

    // Prepare the itinerary data
    $itineraryData = [
        'Itinerary' => $data['Itinerary'],
        'Image' => $imageUrl,
        'Price' => $data['ItineraryPrice']
    ];

    // Save itinerary data to the correct location in the database
    try {
        $cityReference->getChild($nextId)->set($itineraryData);
        echo json_encode(['success' => true, 'message' => 'Itinerary saved successfully!']);
    } catch (Exception $e) {
        error_log("Error saving itinerary data: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to save itinerary data.']);
    }
}
?>
