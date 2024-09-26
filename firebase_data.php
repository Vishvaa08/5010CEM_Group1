<?php

$country = isset($_GET['country']) ? $_GET['country'] : '';
$city = isset($_GET['city']) ? $_GET['city'] : '';
$hotel = isset($_GET['hotel']) ? $_GET['hotel'] : '';

$single = isset($_GET['single']) ? $_GET['single'] : '';
$double = isset($_GET['double']) ? $_GET['double'] : '';
$suite = isset($_GET['suite']) ? $_GET['suite'] : '';

//data for all countries under Packages
$reference = $database->getReference('Packages');
$snapshot = $reference->getSnapshot();
$data = $snapshot->getValue();

//data for all cities under each country
$reference = $database->getReference('Packages/' . $country);  
$snapshot = $reference->getSnapshot();
$dataCity = $snapshot->getValue();

//data for all city images
$reference = $database->getReference('Packages/' . $country . '/' . $city . '/Images');  
$snapshot = $reference->getSnapshot();
$dataCityImages = $snapshot->getValue();

//data for all city itineraries
$reference = $database->getReference('Packages/' . $country . '/' . $city . '/Itinerary');  
$snapshot = $reference->getSnapshot();
$dataCityItinerary = $snapshot->getValue();

//data for all city hotels
$reference = $database->getReference('Packages/' . $country . '/' . $city . '/Hotels');  
$snapshot = $reference->getSnapshot();
$dataHotels = $snapshot->getValue();

//data for vehicles
$reference = $database->getReference('Packages/' . $country . '/' . $city . '/Vehicle');
$snapshot = $reference->getSnapshot();
$dataVehicle = $snapshot->getValue();

//data for city flights
$reference = $database->getReference('Packages/' . $country . '/' . $city . '/Flights');  
$snapshot = $reference->getSnapshot();
$dataFlights = $snapshot->getValue();

//assigning flight prices to variables
$economyPrice = isset($dataFlights['Economy']['Price']) ? $dataFlights['Economy']['Price'] : 0;
$businessPrice = isset($dataFlights['Business']['Price']) ? $dataFlights['Business']['Price'] : 0;
$firstPrice = isset($dataFlights['First Class']['Price']) ? $dataFlights['First Class']['Price'] : 0;

//assigning flights seats to variables
$economySeats = isset($dataFlights['Economy']['Seats']) ? $dataFlights['Economy']['Seats'] : 0;
$businessSeats = isset($dataFlights['Business']['Seats']) ? $dataFlights['Business']['Seats'] : 0;
$firstSeats = isset($dataFlights['First Class']['Seats']) ? $dataFlights['First Class']['Seats'] : 0;

//data for hotel name
$reference = $database->getReference('Packages/' . $country . '/' . $city . '/Hotels/' . $hotel);
$snapshot = $reference->getSnapshot();
$dataHotel = $snapshot->getValue();

//data for hotel room price
$reference = $database->getReference('Packages/' . $country . '/' . $city . '/Hotels/' . $hotel . '/Rooms/');
$snapshot = $reference->getSnapshot();
$dataRoomPrice = $snapshot->getValue();

//assigning room prices to variables
$singlePrice = isset($dataRoomPrice['Single']['Price']) ? $dataRoomPrice['Single']['Price'] : 0;
$doublePrice = isset($dataRoomPrice['Double']['Price']) ? $dataRoomPrice['Double']['Price'] : 0;
$suitePrice = isset($dataRoomPrice['Suite']['Price']) ? $dataRoomPrice['Suite']['Price'] : 0;

?>