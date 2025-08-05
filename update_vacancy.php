<?php
session_start();
include "../DB/connection.php";

header('Content-Type: application/json');

try {
    // Validate input fields
    if (!isset($_POST['id']) || !isset($_POST['job_position']) || !isset($_POST['country_id']) || !isset($_POST['request_id'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    $vacancyId = (int)$_POST['id'];
    $jobPosition = trim($_POST['job_position']);
    $countryId = (int)$_POST['country_id'];
    $requestId = (int)$_POST['request_id'];
    $clientId = is_array($_SESSION['client']) ? ($_SESSION['client']['id'] ?? null) : ($_SESSION['client'] ?? null);

    // Validate session and request_id
    if (!$clientId || !is_numeric($clientId) || $clientId != $requestId) {
        echo json_encode(['success' => false, 'message' => 'Invalid client session or request ID']);
        exit;
    }

    // Validate input data
    if (empty($jobPosition) || !is_numeric($countryId)) {
        echo json_encode(['success' => false, 'message' => 'Invalid job position or country']);
        exit;
    }

    Database::setupConnection();
    $conn = Database::$connection;

    // Verify the vacancy and country belong to the client
    $query = "SELECT COUNT(*) as count FROM vacancy v 
              INNER JOIN country c ON v.country_id = c.id 
              WHERE v.id = ? AND v.request_id = ? AND c.request_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iii', $vacancyId, $requestId, $requestId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] == 0) {
        echo json_encode(['success' => false, 'message' => 'Vacancy or country not found or not authorized']);
        exit;
    }

    // Check for duplicate job position for the same country and client
    $query = "SELECT COUNT(*) as count FROM vacancy 
              WHERE job_position = ? AND country_id = ? AND request_id = ? AND id != ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('siii', $jobPosition, $countryId, $requestId, $vacancyId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        echo json_encode(['success' => false, 'message' => 'This job position already exists for the selected country']);
        exit;
    }

    // Update the vacancy
    $query = "UPDATE vacancy SET job_position = ?, country_id = ? WHERE id = ? AND request_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('siii', $jobPosition, $countryId, $vacancyId, $requestId);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Vacancy updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update vacancy']);
    }

    $stmt->close();
} catch (Exception $e) {
    error_log("Error in update_vacancy.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>