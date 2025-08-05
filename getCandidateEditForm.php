<?php
session_start();
if (!isset($_SESSION['client'])) {
    die(json_encode(['success' => false, 'message' => 'Unauthorized access']));
}

include "client_connection.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die(json_encode(['success' => false, 'message' => 'Candidate ID not provided']));
}

try {
    Database::setupConnection();
    $id = (int)$_GET['id'];

    // Fetch candidate details
    $query = "SELECT c.*, 
              g.id AS gender_id, g.name AS gender_name,
              cs.id AS civil_status_id, cs.status AS civil_status,
              ps.id AS passport_status_id, ps.status AS passport_status,
              co.id AS candidate_option_id, co.option AS candidate_option,
              comp.id AS computer_skill_id, comp.skill AS computer_skill
              FROM candidate c
              LEFT JOIN gender g ON c.gender_id = g.id
              LEFT JOIN clivil_status cs ON c.clivil_status_id = cs.id
              LEFT JOIN passport_status ps ON c.passport_status_id = ps.id
              LEFT JOIN candidate_option co ON c.candidate_option_id = co.id
              LEFT JOIN computer_skill comp ON c.computer_skill_id = comp.id
              WHERE c.id = ?";
    $stmt = Database::$connection->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $candidate = $result->fetch_assoc();

    if (!$candidate) {
        die(json_encode(['success' => false, 'message' => 'Candidate not found']));
    }

    // Fetch options for dropdowns
    $genderOptions = Database::search("SELECT id, name FROM gender")->fetch_all(MYSQLI_ASSOC);
    $civilStatusOptions = Database::search("SELECT id, status FROM clivil_status")->fetch_all(MYSQLI_ASSOC);
    $passportStatusOptions = Database::search("SELECT id, status FROM passport_status")->fetch_all(MYSQLI_ASSOC);
    $candidateOptions = Database::search("SELECT id, option FROM candidate_option")->fetch_all(MYSQLI_ASSOC);
    $computerSkillOptions = Database::search("SELECT id, skill FROM computer_skill")->fetch_all(MYSQLI_ASSOC);

    // Generate HTML form
    $html = '<form id="editCandidateForm" enctype="multipart/form-data">';
    $html .= '<input type="hidden" name="id" value="' . htmlspecialchars($candidate['id']) . '">';

    // Personal Information Section
    $html .= '<div class="form-section">';
    $html .= '<h4>Personal Information</h4>';
    $html .= '<div class="form-row">';
    $html .= '<div class="form-group">';
    $html .= '<label for="fullname">Full Name</label>';
    $html .= '<input type="text" id="fullname" name="fullname" value="' . htmlspecialchars($candidate['fullname']) . '" required>';
    $html .= '</div>';
    $html .= '<div class="form-group">';
    $html .= '<label for="dob">Date of Birth</label>';
    $html .= '<input type="date" id="dob" name="dob" value="' . htmlspecialchars($candidate['dob']) . '" required>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<div class="form-row">';
    $html .= '<div class="form-group">';
    $html .= '<label for="gender_id">Gender</label>';
    $html .= '<select id="gender_id" name="gender_id" required>';
    foreach ($genderOptions as $option) {
        $selected = $option['id'] == $candidate['gender_id'] ? 'selected' : '';
        $html .= '<option value="' . htmlspecialchars($option['id']) . '" ' . $selected . '>' . htmlspecialchars($option['name']) . '</option>';
    }
    $html .= '</select>';
    $html .= '</div>';
    $html .= '<div class="form-group">';
    $html .= '<label for="civil_status_id">Civil Status</label>';
    $html .= '<select id="civil_status_id" name="civil_status_id" required>';
    foreach ($civilStatusOptions as $option) {
        $selected = $option['id'] == $candidate['civil_status_id'] ? 'selected' : '';
        $html .= '<option value="' . htmlspecialchars($option['id']) . '" ' . $selected . '>' . htmlspecialchars($option['status']) . '</option>';
    }
    $html .= '</select>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';

    // Address & Contact Section
    $html .= '<div class="form-section">';
    $html .= '<h4>Address & Contact</h4>';
    $html .= '<div class="form-group">';
    $html .= '<label for="address">Address</label>';
    $html .= '<textarea id="address" name="address" required>' . htmlspecialchars($candidate['address']) . '</textarea>';
    $html .= '</div>';
    $html .= '<div class="form-row">';
    $html .= '<div class="form-group">';
    $html .= '<label for="district_province">District/Province</label>';
    $html .= '<input type="text" id="district_province" name="district_province" value="' . htmlspecialchars($candidate['district_province']) . '">';
    $html .= '</div>';
    $html .= '<div class="form-group">';
    $html .= '<label for="contact_number">Contact Number</label>';
    $html .= '<input type="tel" id="contact_number" name="contact_number" value="' . htmlspecialchars($candidate['contact_number']) . '" required>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<div class="form-group">';
    $html .= '<label for="email">Email</label>';
    $html .= '<input type="email" id="email" name="email" value="' . htmlspecialchars($candidate['email']) . '">';
    $html .= '</div>';
    $html .= '</div>';

    // NIC Section
    $html .= '<div class="form-section">';
    $html .= '<h4>NIC Details</h4>';
    $html .= '<div class="form-row">';
    $html .= '<div class="form-group">';
    $html .= '<label for="nic_no">NIC Number</label>';
    $html .= '<input type="text" id="nic_no" name="nic_no" value="' . htmlspecialchars($candidate['nic_no']) . '">';
    $html .= '</div>';
    $html .= '<div class="form-group">';
    $html .= '<label for="issue_date">Issue Date</label>';
    $html .= '<input type="date" id="issue_date" name="issue_date" value="' . htmlspecialchars($candidate['issue_date']) . '">';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<div class="form-row">';
    $html .= '<div class="form-group">';
    $html .= '<label for="nic_front">NIC Front Image</label>';
    $html .= '<input type="file" id="nic_front" name="nic_front" class="file-upload-input" accept="image/*">';
    if ($candidate['nic_front']) {
        $html .= '<div class="current-file">Current: ' . htmlspecialchars($candidate['nic_front']) . '</div>';
    }
    $html .= '</div>';
    $html .= '<div class="form-group">';
    $html .= '<label for="nic_back">NIC Back Image</label>';
    $html .= '<input type="file" id="nic_back" name="nic_back" class="file-upload-input" accept="image/*">';
    if ($candidate['nic_back']) {
        $html .= '<div class="current-file">Current: ' . htmlspecialchars($candidate['nic_back']) . '</div>';
    }
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';

    // Passport Section
    $html .= '<div class="form-section">';
    $html .= '<h4>Passport Details</h4>';
    $html .= '<div class="form-row">';
    $html .= '<div class="form-group">';
    $html .= '<label for="passport_no">Passport Number</label>';
    $html .= '<input type="text" id="passport_no" name="passport_no" value="' . htmlspecialchars($candidate['passport_no']) . '">';
    $html .= '</div>';
    $html .= '<div class="form-group">';
    $html .= '<label for="passport_status_id">Passport Status</label>';
    $html .= '<select id="passport_status_id" name="passport_status_id">';
    foreach ($passportStatusOptions as $option) {
        $selected = $option['id'] == $candidate['passport_status_id'] ? 'selected' : '';
        $html .= '<option value="' . htmlspecialchars($option['id']) . '" ' . $selected . '>' . htmlspecialchars($option['status']) . '</option>';
    }
    $html .= '</select>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<div class="form-row">';
    $html .= '<div class="form-group">';
    $html .= '<label for="country_issue">Country of Issue</label>';
    $html .= '<input type="text" id="country_issue" name="country_issue" value="' . htmlspecialchars($candidate['country_issue']) . '">';
    $html .= '</div>';
    $html .= '<div class="form-group">';
    $html .= '<label for="passport_issue">Issue Date</label>';
    $html .= '<input type="date" id="passport_issue" name="passport_issue" value="' . htmlspecialchars($candidate['passport_issue']) . '">';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<div class="form-group">';
    $html .= '<label for="passport_expiry">Expiry Date</label>';
    $html .= '<input type="date" id="passport_expiry" name="passport_expiry" value="' . htmlspecialchars($candidate['passport_expiry']) . '">';
    $html .= '</div>';
    $html .= '<div class="form-group">';
    $html .= '<label for="passport_scan">Passport Scan</label>';
    $html .= '<input type="file" id="passport_scan" name="passport_scan" class="file-upload-input" accept="image/*">';
    if ($candidate['passport_scan']) {
        $html .= '<div class="current-file">Current: ' . htmlspecialchars($candidate['passport_scan']) . '</div>';
    }
    $html .= '</div>';
    $html .= '</div>';

    // Candidate Option
    $html .= '<div class="form-section">';
    $html .= '<h4>Candidate Option</h4>';
    $html .= '<div class="form-group">';
    $html .= '<label for="candidate_option_id">Candidate Option</label>';
    $html .= '<select id="candidate_option_id" name="candidate_option_id" required>';
    foreach ($candidateOptions as $option) {
        $selected = $option['id'] == $candidate['candidate_option_id'] ? 'selected' : '';
        $html .= '<option value="' . htmlspecialchars($option['id']) . '" ' . $selected . '>' . htmlspecialchars($option['option']) . '</option>';
    }
    $html .= '</select>';
    $html .= '</div>';
    $html .= '</div>';

    $html .= '<div class="form-actions">';
    $html .= '<button type="submit" class="btn btn-primary">Save Changes</button>';
    $html .= '</div>';
    $html .= '</form>';

    echo json_encode(['success' => true, 'html' => $html]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}