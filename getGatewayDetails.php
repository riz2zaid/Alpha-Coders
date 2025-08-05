<?php
header('Content-Type: application/json');
include "connection_admin.php";

$gatewayId = $_GET['id'] ?? 0;

if ($gatewayId) {
    Database::setupConnection();
    $query = "SELECT * FROM payment_gatway WHERE id = " . (int)$gatewayId;
    $result = Database::search($query);
    
    if ($result && $result->num_rows > 0) {
        $gateway = $result->fetch_assoc();
        echo json_encode($gateway);
    } else {
        echo json_encode(["error" => "Gateway not found"]);
    }
} else {
    echo json_encode(["error" => "Invalid gateway ID"]);
}
?>