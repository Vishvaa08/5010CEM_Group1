<?php
require 'vendor/autoload.php';
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\FirebaseException;

include '../firebase_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('POST request received.');

    $country = $_POST['country'] ?? null;
    $city = $_POST['city'] ?? null;

    if (!$country || !$city) {
        echo json_encode(['success' => false, 'message' => 'Country or city not specified.']);
        exit;
    }

    $cityImageUrl = '';
    $data = json_decode($_POST['data'], true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON Error: ' . json_last_error_msg());
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data.']);
        exit;
    }

    $cityDetail = $data['CityDetail'] ?? '';
    $hotels = $data['Hotels'] ?? [];
    $flights = $data['Flights'] ?? [];
    $newVehicles = $data['Vehicle'] ?? [];
    $itineraries = $data['Itinerary'] ?? []; 
    $cityAvailable = $data['Availability'] ?? 'N/A'; 

    $cityReference = $database->getReference("Packages/$country/$city");
    $existingDataSnapshot = $cityReference->getSnapshot();
    $existingData = $existingDataSnapshot->getValue() ?? [];
    error_log("Existing Data: " . print_r($existingData, true));

    $existingHotels = $existingData['Hotels'] ?? [];
    $existingHotelIds = array_keys($existingHotels);
    $nextHotelId = count($existingHotelIds) > 0 ? max($existingHotelIds) + 1 : 1; 

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    }

    $cityData = [
        'City' => $city,
        'CityImage' => $cityImageUrl ?: ($existingData['CityImage'] ?? ''),
        'CityDetail' => $cityDetail ?: ($existingData['CityDetail'] ?? ''),
        'Availability' => $cityAvailable,
        'Flights' => $existingData['Flights'] ?? [],
        'Vehicle' => $existingData['Vehicle'] ?? [],
        'Hotels' => $existingData['Hotels'] ?? [],
        'Itinerary' => $existingData['Itinerary'] ?? []
    ];

    // Add or update flight data
    foreach ($flights as $class => $flight) {
        $cityData['Flights'][$class] = [
            'Price' => $flight['Price'] ?? 0,
            'Seats' => $flight['Seats'] ?? 0
        ];
    }

    // Add or update vehicle data
    foreach ($newVehicles as $vehicleKey => $vehicle) {
        $cityData['Vehicle'][$vehicleKey] = [
            'Type' => $vehicle['Type'],
            'Price' => $vehicle['Price'] ?? 0
        ];
    }    

    // Add or update hotel data
    foreach ($hotels as $hotel) {
        $cityData['Hotels'][$nextHotelId] = [
            'Hotel' => $hotel['Hotel'] ?? 'N/A',
            'Availability' => $hotel['Availability'] ?? 'N/A',
            'Image' => $hotel['Image'] ?? '',
            'Description' => $hotel['Description'] ?? 'No description available',
            'CheapestRoom' => $hotel['CheapestRoom'] ?? 0, 
            'Rooms' => $hotel['Rooms'] ?? []
            
        ];
        $nextHotelId++; 
    }

    $cityReference->update($cityData); 

    try {
        $cityReference->set($cityData);
        echo json_encode(['success' => true, 'message' => 'City details saved successfully!']);
    } catch (FirebaseException $e) {
        error_log("Error saving to Firebase: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to save city details']);
    }
}
?>
