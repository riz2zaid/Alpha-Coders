<?php
session_start();
include "../DB/connection.php";

header('Content-Type: application/json');

try {
    // Validate input
    if (!isset($_POST["cn"]) || empty(trim($_POST["cn"]))) {
        echo json_encode(['status' => 'error', 'message' => 'Country name cannot be empty.']);
        exit;
    }

    if (!isset($_POST["request_id"]) || !is_numeric($_POST["request_id"])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request ID.']);
        exit;
    }

    $countryName = trim($_POST["cn"]);
    $requestId = (int)$_POST["request_id"];

    // Initialize database connection
    Database::setupConnection();
    $conn = Database::$connection;

    // Validate request_id exists in request table
    $query = "SELECT COUNT(*) as count FROM request WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $requestId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row['count'] == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request ID.']);
        exit;
    }

    // Check for duplicate country for this request_id
    $query = "SELECT COUNT(*) as count FROM country WHERE cname = ? AND request_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $countryName, $requestId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Country already exists for this client.']);
        exit;
    }

    // Insert new country
    $query = "INSERT INTO country (cname, request_id) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $countryName, $requestId);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Country added successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add country to database.']);
    }
} catch (Exception $e) {
    error_log("Error in addCountryProcess.php: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
}
?>