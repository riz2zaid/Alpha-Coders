<?php
session_start();
if (!isset($_SESSION['client'])) {
    header("Location: index.php");
    exit();
}

include "client_connection.php";

$response = ['success' => false, 'message' => ''];

try {
    $id = $_POST['id'] ?? 0;
    
    // First get candidate details to delete files
    $query = "SELECT profile_photo, nic_front, nic_back, passport_scan, certificate_image, 
              technical_image, expiriance_letter, medical_report, covid_card, vision_test,
              police_certificate, other_document, cv, agreement, contract, undertaking_letter
              FROM candidate WHERE id = ?";
    $stmt = Database::$connection->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $candidate = $result->fetch_assoc();
    
    if (!$candidate) {
        throw new Exception("Candidate not found");
    }
    
    // Delete files
    $uploadDir = "../Uploads/candidates/";
    $files = [
        $candidate['profile_photo'],
        $candidate['nic_front'],
        $candidate['nic_back'],
        $candidate['passport_scan'],
        $candidate['certificate_image'],
        $candidate['technical_image'],
        $candidate['expiriance_letter'],
        $candidate['medical_report'],
        $candidate['covid_card'],
        $candidate['vision_test'],
        $candidate['police_certificate'],
        $candidate['other_document'],
        $candidate['cv'],
        $candidate['agreement'],
        $candidate['contract'],
        $candidate['undertaking_letter']
    ];
    
    foreach ($files as $file) {
        if (!empty($file) && file_exists($uploadDir.$file)) {
            unlink($uploadDir.$file);
        }
    }
    
    // Delete from database
    $deleteQuery = "DELETE FROM candidate WHERE id = ?";
    $deleteStmt = Database::$connection->prepare($deleteQuery);
    $deleteStmt->bind_param("i", $id);
    
    if ($deleteStmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Candidate deleted successfully';
    } else {
        throw new Exception("Failed to delete candidate");
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>