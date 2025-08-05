<?php
session_start();
include "../DB/connection.php";

$response = ['canAdd' => false, 'message' => 'An unknown error occurred'];

try {
    // Get client ID from session
    $clientId = is_array($_SESSION['client']) ? ($_SESSION['client']['id'] ?? null) : ($_SESSION['client'] ?? null);
    if (!$clientId || !is_numeric($clientId)) {
        throw new Exception("Invalid client session");
    }

    // Initialize database connection
    Database::setupConnection();
    if (!isset(Database::$connection) || Database::$connection->connect_error) {
        throw new Exception("Database connection failed");
    }

    // Get candidate limit from request table
    $limitQuery = "SELECT candidate_limit FROM request WHERE id = ?";
    $limitStmt = Database::$connection->prepare($limitQuery);
    if (!$limitStmt) {
        throw new Exception("Prepare failed for candidate limit check: " . Database::$connection->error);
    }
    $limitStmt->bind_param('i', $clientId);
    $limitStmt->execute();
    $limitResult = $limitStmt->get_result();
    if ($limitResult->num_rows === 0) {
        throw new Exception("No request found for this client");
    }
    $limitRow = $limitResult->fetch_assoc();
    $candidateLimit = (int)($limitRow['candidate_limit'] ?? 0);
    $limitStmt->close();

    // Count existing candidates for this client
    $countQuery = "SELECT COUNT(*) as candidate_count FROM candidates WHERE request_id = ?";
    $countStmt = Database::$connection->prepare($countQuery);
    if (!$countStmt) {
        throw new Exception("Prepare failed for candidate count: " . Database::$connection->error);
    }
    $countStmt->bind_param('i', $clientId);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $countRow = $countResult->fetch_assoc();
    $candidateCount = (int)($countRow['candidate_count'] ?? 0);
    $countStmt->close();

    if ($candidateCount >= $candidateLimit) {
        $response = [
            'canAdd' => false,
            'message' => "You have reached the candidate limit of $candidateLimit. Cannot add more candidates."
        ];
    } else {
        $response = [
            'canAdd' => true,
            'message' => "You can add more candidates (Current: $candidateCount, Limit: $candidateLimit)"
        ];
    }
} catch (Exception $e) {
    $response = ['canAdd' => false, 'message' => $e->getMessage()];
    error_log("Candidate Limit Check Error: " . $e->getMessage());
}

header('Content-Type: application/json');
echo json_encode($response);
?>