<?php
session_start();
if (!isset($_SESSION['client'])) {
    die(json_encode(['success' => false, 'message' => 'Unauthorized access']));
}

include "client_connection.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die(json_encode(['success' => false, 'message' => 'Invalid candidate ID']));
}

$candidateId = (int)$_GET['id'];

try {
    Database::setupConnection();
    
    $query = "SELECT c.*, 
              g.name AS gender_name,
              cs.status AS civil_status,
              ps.status AS passport_status,
              co.option AS candidate_option,
              comp.skill AS computer_skill,
              c.profile_photo
              FROM candidate c
              LEFT JOIN gender g ON c.gender_id = g.id
              LEFT JOIN civil_status cs ON c.civil_status_id = cs.id
              LEFT JOIN passport_status ps ON c.passport_status_id = ps.id
              LEFT JOIN candidate_option co ON c.candidate_option_id = co.id
              LEFT JOIN computer_skill comp ON c.computer_skill_id = comp.id
              WHERE c.id = ?";
    
    $stmt = Database::$connection->prepare($query);
    $stmt->bind_param("i", $candidateId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        die(json_encode(['success' => false, 'message' => 'Candidate not found']));
    }
    
    $candidate = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'data' => $candidate
    ]);
    
} catch (Exception $e) {
    die(json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]));
}
?>
