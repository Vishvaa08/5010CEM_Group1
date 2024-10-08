<?php
require 'vendor/autoload.php';
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\FirebaseException;

// Include your Firebase connection details
include 'firebase_connection.php';

// Handle the POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('POST request received.');

    // Get country and city from POST data
    $country = $_POST['country'] ?? null;
    $city = $_POST['city'] ?? null;

    // Check if country and city are provided
    if (!$country || !$city) {
        echo json_encode(['success' => false, 'message' => 'Country or city not specified.']);
        exit;
    }

    // Initialize variables for the city data
    $cityImageUrl = '';
    $data = json_decode($_POST['data'], true);

    // Validate JSON data
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON Error: ' . json_last_error_msg());
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data.']);
        exit;
    }

    // Extract city details
    $cityDetail = $data['CityDetail'] ?? '';
    $hotels = $data['Hotels'] ?? [];
    $flights = $data['Flights'] ?? [];
    $newVehicles = $data['Vehicle'] ?? [];

    // Get reference to the specific city data in Firebase
    $cityReference = $database->getReference("Packages/$country/$city");

    // Fetch existing data from Firebase
    $existingDataSnapshot = $cityReference->getSnapshot();
    $existingData = $existingDataSnapshot->getValue();
    error_log("Existing Data: " . print_r($existingData, true));

    // Check if a new image is uploaded for the city
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileType = $_FILES['image']['type'];
        $originalFileName = $_FILES['image']['name'];
        $safeFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $originalFileName);

        // Check file type (Allowing only certain types)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($fileType, $allowedTypes)) {
            try {
                // Upload the new image to Firebase Storage
                $bucket = $storage->getBucket();
                $bucket->upload(
                    fopen($fileTmpPath, 'r'),
                    ['name' => 'cities/' . $safeFileName, 'metadata' => ['contentType' => $fileType]]
                );
                // Generate new public URL for the image
                $cityImageUrl = "https://firebasestorage.googleapis.com/v0/b/traveltrail-39e23.appspot.com/o/cities%2F" . urlencode($safeFileName) . "?alt=media";
                error_log("Image uploaded successfully: " . $cityImageUrl);
            } catch (Exception $e) {
                error_log("Image upload failed: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Failed to upload the city image']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.']);
            exit;
        }
    }

    // Prepare the data structure for saving
    $cityData = [
        'City' => $city,
        'CityImage' => $cityImageUrl ?: ($existingData['CityImage'] ?? ''),
        'CityDetail' => $cityDetail ?: ($existingData['CityDetail'] ?? ''),
        'Flights' => $existingData['Flights'] ?? [],
        'Vehicle' => $existingData['Vehicle'] ?? [],
        'Hotels' => $existingData['Hotels'] ?? [], // Use existing hotels if any
        // Remove the itinerary part
    ];

    // Add or update flight data
    foreach ($flights as $class => $flight) {
        $cityData['Flights'][$class] = [
            'Price' => $flight['Price'] ?? 0,
            'Seats' => $flight['Seats'] ?? 0
        ];
    }

    // Add or update vehicle data
    foreach ($newVehicles as $vehicleType => $vehicle) {
        $cityData['Vehicle'][$vehicleType] = [
            'Type' => $vehicle['Type'] ?? $vehicleType,
            'Price' => $vehicle['Price'] ?? 0
        ];
    }

    // Handle hotel data
    foreach ($hotels as $hotel) {
        $hotelImageUrl = '';

        // Check if a new image is uploaded for the hotel
        if (isset($_FILES['hotelImage']) && $_FILES['hotelImage']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['hotelImage']['tmp_name'];
            $fileType = $_FILES['hotelImage']['type'];
            $originalFileName = $_FILES['hotelImage']['name'];
            $safeFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $originalFileName);

            // Check file type (Allowing only certain types)
            if (in_array($fileType, $allowedTypes)) {
                try {
                    // Upload the hotel image to Firebase Storage
                    $bucket->upload(
                        fopen($fileTmpPath, 'r'),
                        ['name' => 'hotels/' . $safeFileName, 'metadata' => ['contentType' => $fileType]]
                    );
                    // Generate new public URL for the hotel image
                    $hotelImageUrl = "https://firebasestorage.googleapis.com/v0/b/traveltrail-39e23.appspot.com/o/hotels%2F" . urlencode($safeFileName) . "?alt=media";
                } catch (Exception $e) {
                    error_log("Hotel image upload failed: " . $e->getMessage());
                    echo json_encode(['success' => false, 'message' => 'Failed to upload the hotel image']);
                    exit;
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid hotel file type. Only JPG, PNG, and GIF are allowed.']);
                exit;
            }
        }

        // Save hotel data
        $cityData['Hotels'][] = [
            'Hotel' => $hotel['Hotel'] ?? 'N/A',
            'Image' => $hotelImageUrl ?: $hotel['Image'] ?? '',
            'Description' => $hotel['Description'] ?? 'No description available',
            'Rooms' => $hotel['Rooms'] ?? []
        ];
    }

    // Log prepared data before saving
    error_log("City Data to Save: " . print_r($cityData, true));

    try {
        // Save the data back to Firebase
        $cityReference->set($cityData);
        echo json_encode(['success' => true, 'message' => 'City details saved successfully!']);
    } catch (FirebaseException $e) {
        error_log("Error saving to Firebase: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to save city details']);
    }
}
?>
