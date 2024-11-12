<?php
include '../firebase_connection.php';

$data = json_decode(file_get_contents("php://input"), true);

$referencePath = $data['reference'];
$replyData = $data['data'];
$reference = $db->getReference($referencePath);
$reference->set($replyData); 

echo json_encode(["status" => "success", "message" => "Reply saved successfully"]);
?>
