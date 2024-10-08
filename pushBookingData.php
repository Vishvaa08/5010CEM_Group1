<?php

include 'firebase_connection.php';
include 'firebase_data.php';

$bookingData = json_decode(file_get_contents('php://input'), true);

$reference = $database->getReference('Admin/newBookings');
$newBookingRef = $reference->push($bookingData);

$newBookingKey = $newBookingRef->getKey();
echo json_encode(['bookingId' => $newBookingKey]);

$country = $bookingData['country'];
$city = $bookingData['city'];
$hotel = $bookingData['hotelID'];
$roomType = $bookingData['roomType'];

$roomReference = $database->getReference('Packages/' . $country . '/' . $city . '/Hotels/' . $hotel . '/Rooms/' . $roomType);
$snapshot = $roomReference->getSnapshot();
$dataRoomPrice = $snapshot->getValue();

if ($dataRoomPrice && isset($dataRoomPrice['Availability'])) {
    $currentRoomCount = $dataRoomPrice['Availability'];
    $newRoomCount = max(0, $currentRoomCount - 1);

    $roomReference->update(['Availability' => $newRoomCount]);
}

$flightType = $bookingData['flightType'];
$numTickets = $bookingData['numTickets'];

$flightReference = $database->getReference('Packages/' . $country . '/' . $city . '/Flights/' . $flightType);
$snapshot = $flightReference->getSnapshot();
$dataFlight = $snapshot->getValue();

if ($dataFlight && isset($dataFlight['Seats'])) {
    $currentSeatsCount = $dataFlight['Seats'];
    $newSeatsCount = max(0, $currentSeatsCount - $numTickets);

    $flightReference->update(['Seats' => $newSeatsCount]);
}
?>