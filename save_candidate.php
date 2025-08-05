<?php
session_start();
if (!isset($_SESSION['client'])) {
    header("Location: index.php");
    exit();
}

include "../DB/connection.php";

$response = ['status' => 'error', 'message' => 'An unknown error occurred'];

try {
    // Initialize database connection
    Database::setupConnection();
    if (!isset(Database::$connection) || Database::$connection->connect_error) {
        throw new Exception("Database connection failed");
    }

    // Get client ID from session
    $clientId = is_array($_SESSION['client']) ? ($_SESSION['client']['id'] ?? null) : ($_SESSION['client'] ?? null);
    if (!$clientId || !is_numeric($clientId)) {
        throw new Exception("Invalid client session");
    }

    // Check candidate limit from request table
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

    // Check if candidate limit is reached
    if ($candidateCount >= $candidateLimit) {
        throw new Exception("You have reached the candidate limit of $candidateLimit. Cannot add more candidates.");
    }

    Database::$connection->begin_transaction();

    // Handle file uploads
    $uploadDir = "../Uploads/candidates/";
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            throw new Exception("Failed to create upload directory");
        }
    }

    function handleFileUpload($file, $uploadDir, $allowedTypes) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExt, $allowedTypes)) {
            throw new Exception("Invalid file type for " . $file['name'] . ". Allowed: " . implode(', ', $allowedTypes));
        }

        $newFilename = uniqid() . '.' . $fileExt;
        $destination = $uploadDir . $newFilename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception("Failed to move uploaded file: " . $file['name']);
        }

        return $newFilename;
    }

    // Define file fields and their allowed types
    $fileFields = [
        'profile_photo' => ['jpg', 'jpeg', 'png'],
        'nic_front_image' => ['jpg', 'jpeg', 'pdf'],
        'nic_back_image' => ['jpg', 'jpeg', 'pdf'],
        'passport_scan_upload' => ['jpg', 'jpeg', 'pdf'],
        'upload_certificates' => ['jpg', 'jpeg', 'pdf'],
        'technical_training' => ['jpg', 'jpeg', 'pdf'],
        'experience_letter' => ['jpg', 'jpeg', 'pdf'],
        'medical_clearance_report' => ['jpg', 'jpeg', 'pdf'],
        'covid_vaccination_card' => ['jpg', 'jpeg', 'pdf'],
        'vision_test_report' => ['jpg', 'jpeg', 'pdf'],
        'police_clearance_certificate' => ['jpg', 'jpeg', 'pdf'],
        'other_documents' => ['jpg', 'jpeg', 'png'],
        'cv_resume' => ['pdf', 'doc', 'docx'],
        'agreement' => ['jpg', 'jpeg', 'png'],
        'contract' => ['pdf', 'doc', 'docx'],
        'undertaking_letter' => ['pdf', 'doc', 'docx'],
        'gs_letter' => ['pdf', 'doc', 'docx']
    ];

    $uploadedFiles = [];
    foreach ($fileFields as $field => $allowedTypes) {
        if (!empty($_FILES[$field]['name'])) {
            $uploadedFiles[$field] = handleFileUpload($_FILES[$field], $uploadDir, $allowedTypes);
        } else {
            $uploadedFiles[$field] = null;
        }
    }

    // Validate required fields
    $requiredFields = ['full_name', 'date_of_birth', 'address', 'contact_number', 'gender', 'civil_status', 'candidate_option'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Required field '$field' is missing or empty");
        }
    }

    // Prepare SQL query with register_date and request_id
    $query = "INSERT INTO candidates (
        fullname, dob, profile_photo, address, district_province,
        contact_number, email, nic_no, issue_date, nic_front,
        nic_back, passport_no, country_issue, passport_issue,
        passport_expair, passport_scan, name, relationship,
        mobile_no, alternative_no, highest_qualification,
        fieldofstudy, institute_name, complete_year, certificate_image,
        language, work_skill, technical_image, company_name,
        position, duration, country_work, reasonof_leave,
        experiance_letter, blood, allergies, chronic,
        medical_report, covid_card, vision_test, fitness_status,
        police_certificate, police_issuedate, police_expiredate,
        other_document, cv, gender_id, clivil_status_id,
        passport_status_id, computer_skill_id, candidate_option_id,
        register_date, request_id, agreement, contract, undertaking_letter, gs_letter
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?)";

    $stmt = Database::$connection->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . Database::$connection->error);
    }

    // Bind parameters
    $params = [
        $_POST['full_name'] ?? '',
        $_POST['date_of_birth'] ?? null,
        $uploadedFiles['profile_photo'],
        $_POST['address'] ?? '',
        $_POST['district_province'] ?? null,
        $_POST['contact_number'] ?? '',
        $_POST['email_address'] ?? null,
        $_POST['nic_number'] ?? null,
        $_POST['nic_issue_date'] ?? null,
        $uploadedFiles['nic_front_image'],
        $uploadedFiles['nic_back_image'],
        $_POST['passport_number'] ?? null,
        $_POST['country_of_issue'] ?? 'Sri Lanka',
        $_POST['passport_issue_date'] ?? null,
        $_POST['passport_expiry_date'] ?? null,
        $uploadedFiles['passport_scan_upload'],
        $_POST['emergency_contact_name'] ?? null,
        $_POST['emergency_contact_relationship'] ?? null,
        $_POST['emergency_contact_mobile'] ?? null,
        $_POST['emergency_contact_alternate'] ?? null,
        $_POST['highest_qualification'] ?? null,
        $_POST['field_of_study'] ?? null,
        $_POST['institution_name'] ?? null,
        $_POST['year_completed'] ?? null,
        $uploadedFiles['upload_certificates'],
        $_POST['languages_spoken'] ?? null,
        $_POST['work_skills'] ?? null,
        $uploadedFiles['technical_training'],
        $_POST['company_name'] ?? null,
        $_POST['position_held'] ?? null,
        $_POST['duration'] ?? null,
        $_POST['country_abroad'] ?? null,
        $_POST['reason_for_leaving'] ?? null,
        $uploadedFiles['experience_letter'],
        $_POST['blood_group'] ?? null,
        $_POST['allergies'] ?? null,
        $_POST['chronic_illness'] ?? null,
        $uploadedFiles['medical_clearance_report'],
        $uploadedFiles['covid_vaccination_card'],
        $uploadedFiles['vision_test_report'],
        $_POST['physical_fitness_status'] ?? null,
        $uploadedFiles['police_clearance_certificate'],
        $_POST['certificate_issue_date'] ?? null,
        $_POST['expiry_date'] ?? null,
        $uploadedFiles['other_documents'],
        $uploadedFiles['cv_resume'],
        (int)($_POST['gender'] ?? 1),
        (int)($_POST['civil_status'] ?? 1),
        (int)($_POST['passport_status'] ?? 1),
        (int)($_POST['computer_skills'] ?? 1),
        (int)($_POST['candidate_option'] ?? 1),
        (int)$clientId,
        $uploadedFiles['agreement'],
        $uploadedFiles['contract'],
        $uploadedFiles['undertaking_letter'],
        $uploadedFiles['gs_letter']
    ];

    // Create type string (46 strings + 5 integers for IDs + 1 integer for request_id + 4 strings for files)
    $types = str_repeat('s', 46) . 'iiiiii' . 'ssss';
    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    Database::$connection->commit();
    $response = ['status' => 'success', 'message' => 'Candidate added successfully!'];
    $stmt->close();
} catch (Exception $e) {
    if (isset(Database::$connection)) {
        Database::$connection->rollback();
    }
    $response = ['status' => 'error', 'message' => $e->getMessage()];
    error_log("Candidate Error: " . $e->getMessage());
}

header('Content-Type: application/json');
echo json_encode($response);
?>