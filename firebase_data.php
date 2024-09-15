<?php
//data for all countries under Packages
$reference = $database->getReference('Packages');
$snapshot = $reference->getSnapshot();
$data = $snapshot->getValue();

//data for all cities under each country
$country = isset($_GET['country']) ? $_GET['country'] : '';

$reference = $database->getReference('Packages/' . $country);  
$snapshot = $reference->getSnapshot();
$dataCity = $snapshot->getValue();

//data for all city images
$city = isset($_GET['city']) ? $_GET['city'] : '';

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
?>