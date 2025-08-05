<?php
session_start();
include "../DB/connection.php";

header('Content-Type: application/json');

try {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid vacancy ID']);
        exit;
    }

    $vacancyId = (int)$_GET['id'];
    $clientId = is_array($_SESSION['client']) ? ($_SESSION['client']['id'] ?? null) : ($_SESSION['client'] ?? null);

    if (!$clientId || !is_numeric($clientId)) {
        echo json_encode(['success' => false, 'message' => 'Invalid client session']);
        exit;
    }

    Database::setupConnection();
    $conn = Database::$connection;

    // Verify the vacancy belongs to the client
    $query = "SELECT COUNT(*) as count FROM vacancy WHERE id = ? AND request_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $vacancyId, $clientId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] == 0) {
        echo json_encode(['success' => false, 'message' => 'Vacancy not found or not authorized']);
        exit;
    }

    // Delete the vacancy
    $query = "DELETE FROM vacancy WHERE id = ? AND request_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $vacancyId, $clientId);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Vacancy deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete vacancy']);
    }
} catch (Exception $e) {
    error_log("Error in delete_vacancy.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>