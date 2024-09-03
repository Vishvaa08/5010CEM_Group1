<?php

require 'vendor/autoload.php';

use Kreait\Firebase\Factory;

$serviceAccount = __DIR__ . '/prvKey.json';

$factory = (new Factory)
    ->withServiceAccount($serviceAccount)
    ->withDatabaseUri('https://traveltrail-39e23-default-rtdb.firebaseio.com/');

$database = $factory->createDatabase();

/*
$reference = $database->getReference('users');
$reference->push([
    'name' => 'Vishvaa',
    'age' => '24'
]);

echo "Data Stored";
*/

?>