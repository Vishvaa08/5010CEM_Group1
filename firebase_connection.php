<?php

require 'vendor/autoload.php';

use Kreait\Firebase\Factory;

$serviceAccount = __DIR__ . '/prvkey.json';

$factory = (new Factory)
    ->withServiceAccount($serviceAccount)
    ->withDatabaseUri('https://traveltrail-39e23-default-rtdb.firebaseio.com/');
  

$database = $factory->createDatabase();
$storage = $factory->createStorage(); 

?>
