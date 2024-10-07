<?php
// Include Firebase connection
include 'firebase_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $country = $_POST['country'];
    $city = $_POST['city'];
    $item = $_POST['item']; // This will be either 'hotel', 'vehicle', or 'itinerary'
    $itemName = $_POST['itemName']; // This will be used to identify which specific item to delete

    // Firebase reference for the specific city
    $cityReference = $database->getReference("Packages/$country/$city");

    // Initialize a success response
    $response = ['success' => false, 'message' => 'Item not found.'];

    try {
        // Get existing city data
        $cityDataSnapshot = $cityReference->getSnapshot();
        $cityData = $cityDataSnapshot->getValue();

        // Perform deletion based on item type
        switch ($item) {
            case 'hotel':
                if (isset($cityData['Hotels'])) {
                    foreach ($cityData['Hotels'] as $hotelIndex => $hotel) {
                        if ($hotel['Hotel'] === $itemName) {
                            unset($cityData['Hotels'][$hotelIndex]); // Delete the specific hotel
                            break; // Stop searching after deleting
                        }
                    }
                    $response['success'] = true;
                    $response['message'] = 'Hotel deleted successfully.';
                } else {
                    $response['message'] = 'No hotels found.';
                }
                break;

            case 'vehicle':
                if (isset($cityData['Vehicle'][$itemName])) {
                    unset($cityData['Vehicle'][$itemName]); // Delete the specific vehicle
                    $response['success'] = true;
                    $response['message'] = 'Vehicle deleted successfully.';
                } else {
                    $response['message'] = 'Vehicle not found.';
                }
                break;

            case 'itinerary':
                if (isset($cityData['Itinerary'])) {
                    foreach ($cityData['Itinerary'] as $itineraryIndex => $itinerary) {
                        if ($itinerary['Itinerary'] === $itemName) {
                            unset($cityData['Itinerary'][$itineraryIndex]); // Delete the specific itinerary
                            break; // Stop searching after deleting
                        }
                    }
                    $response['success'] = true;
                    $response['message'] = 'Itinerary deleted successfully.';
                } else {
                    $response['message'] = 'No itineraries found.';
                }
                break;

            default:
                $response['message'] = 'Invalid item type.';
                break;
        }

        // Update the city reference with the modified data
        $cityReference->set($cityData);
    } catch (Exception $e) {
        $response['message'] = 'Error deleting item: ' . $e->getMessage();
    }

    // Send the response back to the client
    echo json_encode($response);
}
?>
