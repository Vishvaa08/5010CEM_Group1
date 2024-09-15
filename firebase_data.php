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

?>