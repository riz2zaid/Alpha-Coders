<?php
session_start();
include "../DB/connection.php";

header('Content-Type: application/json');

try {
    // Validate input
    if (!isset($_POST["job_position"]) || !isset($_POST["country"]) || 
        empty(trim($_POST["job_position"])) || empty(trim($_POST["country"]))) {
        echo json_encode(['status' => 'error', 'message' => 'Job position and country are required.']);
        exit;
    }

    if (!isset($_POST["request_id"]) || !is_numeric($_POST["request_id"])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request ID.']);
        exit;
    }

    $jobPosition = trim($_POST["job_position"]);
    $countryInput = trim($_POST["country"]);
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

    // Get country_id from country name and verify request_id
    $query = "SELECT id FROM country WHERE cname = ? AND request_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $countryInput, $requestId);
    $stmt->execute();
    $result = $stmt->get_result();
    $countryRow = $result->fetch_assoc();
    if (!$countryRow) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid country selected or not associated with this client.']);
        exit;
    }

    $countryId = $countryRow['id'];

    // Check for duplicate job entry
    $query = "SELECT COUNT(*) as count FROM vacancy WHERE job_position = ? AND country_id = ? AND request_id = ? AND created_at > NOW() - INTERVAL 1 DAY";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sii', $jobPosition, $countryId, $requestId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        echo json_encode(['status' => 'error', 'message' => 'This job position already exists for the selected country and client.']);
        exit;
    }

    // Insert new vacancy
    $query = "INSERT INTO vacancy (job_position, country_id, request_id, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sii', $jobPosition, $countryId, $requestId);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Job vacancy added successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add job vacancy to database.']);
    }
} catch (Exception $e) {
    error_log("Error in process_vacancy.php: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
}
?>