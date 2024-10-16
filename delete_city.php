<?php
require 'vendor/autoload.php';

use Kreait\Firebase\Factory;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$serviceAccount = __DIR__ . '/prvkey.json';
$factory = (new Factory)
    ->withServiceAccount($serviceAccount)
    ->withDatabaseUri('https://traveltrail-39e23-default-rtdb.firebaseio.com/');

$database = $factory->createDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $country = $_POST['country'] ?? '';
    $city = $_POST['city'] ?? '';

    if (empty($country) || empty($city)) {
        echo json_encode(['success' => false, 'message' => 'Country and city must be specified.']);
        exit;
    }

    try {
        $database->getReference("Packages/$country/$city")->remove(); 

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to delete city package: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
