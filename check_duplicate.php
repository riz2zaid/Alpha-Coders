<?php
session_start();
if (!isset($_SESSION['client'])) {
    header("Location: index.php");
    exit();
}

include "client_connection.php";

$response = [
    'exists' => false, 
    'nic_exists' => false, 
    'passport_exists' => false,
    'error' => null
];

try {
    // Get POST data safely
    $nicNumber = isset($_POST['nic_number']) ? trim($_POST['nic_number']) : '';
    $passportNumber = isset($_POST['passport_number']) ? trim($_POST['passport_number']) : '';
    
    // Check if Database class has search method
    if (!method_exists('Database', 'search')) {
        throw new Exception("Database search method not available");
    }
    
    // Check NIC number if provided
    if (!empty($nicNumber)) {
        Database::setupConnection();
        $nicNumberEscaped = Database::$connection->real_escape_string($nicNumber);
        $stmt = Database::search("SELECT id FROM candidate WHERE nic_no = '$nicNumberEscaped'");
        if ($stmt === false) {
            throw new Exception("NIC check query failed");
        }
        $response['nic_exists'] = $stmt->num_rows > 0;
    }
    
    // Check Passport number if provided
    if (!empty($passportNumber)) {
        Database::setupConnection();
        $passportNumberEscaped = Database::$connection->real_escape_string($passportNumber);
        $stmt = Database::search("SELECT id FROM candidate WHERE passport_no = '$passportNumberEscaped'");
        if ($stmt === false) {
            throw new Exception("Passport check query failed");
        }
        $response['passport_exists'] = $stmt->num_rows > 0;
    }
    
    $response['exists'] = $response['nic_exists'] || $response['passport_exists'];
    
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    error_log("Duplicate check error: " . $e->getMessage());
}

header('Content-Type: application/json');
echo json_encode($response);
?>