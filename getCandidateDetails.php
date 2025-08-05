<?php
session_start();
if (!isset($_SESSION['client'])) {
    die("Unauthorized access");
}

include "client_connection.php";

$id = $_GET['id'] ?? 0;

try {
    $query = "SELECT c.*, 
              g.name AS gender_name,
              cs.status AS civil_status,
              ps.status AS passport_status,
              co.option AS candidate_option,
              comp.skill AS computer_skill
              FROM candidate c
              LEFT JOIN gender g ON c.gender_id = g.id
              LEFT JOIN clivil_status cs ON c.clivil_status_id = cs.id
              LEFT JOIN passport_status ps ON c.passport_status_id = ps.id
              LEFT JOIN candidate_option co ON c.candidate_option_id = co.id
              LEFT JOIN computer_skill comp ON c.computer_skill_id = comp.id
              WHERE c.id = ?";
    
    $stmt = Database::$connection->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $candidate = $result->fetch_assoc();
    
    if ($candidate) {
        echo '<div class="row">';
        echo '<div class="col-md-4">';
        if (!empty($candidate['profile_photo'])) {
            echo '<img src="../Uploads/candidates/'.$candidate['profile_photo'].'" class="img-fluid rounded mb-3" alt="Profile Photo">';
        }
        echo '<p><strong>ID:</strong> '.$candidate['id'].'</p>';
        echo '<p><strong>Status:</strong> <span class="badge '.($candidate['candidate_option_id'] == 1 ? 'badge-active' : 'badge-inactive').'">'.$candidate['candidate_option'].'</span></p>';
        echo '<p><strong>Registered:</strong> '.date('d M Y', strtotime($candidate['register_date'])).'</p>';
        echo '</div>';
        echo '<div class="col-md-8">';
        echo '<h4>'.$candidate['fullname'].'</h4>';
        echo '<p><strong>Gender:</strong> '.$candidate['gender_name'].'</p>';
        echo '<p><strong>Civil Status:</strong> '.$candidate['civil_status'].'</p>';
        echo '<p><strong>DOB:</strong> '.date('d M Y', strtotime($candidate['dob'])).'</p>';
        echo '</div>';
        echo '</div>';
        
        // Personal Information
        echo '<div class="candidate-details-section">';
        echo '<h5>Personal Information</h5>';
        echo '<div class="row">';
        echo '<div class="col-md-6">';
        echo '<div class="detail-row"><span class="detail-label">Address:</span><span class="detail-value">'.$candidate['address'].'</span></div>';
        echo '<div class="detail-row"><span class="detail-label">District/Province:</span><span class="detail-value">'.$candidate['district_province'].'</span></div>';
        echo '<div class="detail-row"><span class="detail-label">Contact Number:</span><span class="detail-value">'.$candidate['contact_number'].'</span></div>';
        echo '<div class="detail-row"><span class="detail-label">Email:</span><span class="detail-value">'.$candidate['email'].'</span></div>';
        echo '</div>';
        echo '<div class="col-md-6">';
        echo '<div class="detail-row"><span class="detail-label">NIC No:</span><span class="detail-value">'.$candidate['nic_no'].'</span></div>';
        echo '<div class="detail-row"><span class="detail-label">NIC Issue Date:</span><span class="detail-value">'.date('d M Y', strtotime($candidate['issue_date'])).'</span></div>';
        if (!empty($candidate['nic_front'])) {
            echo '<div class="detail-row"><span class="detail-label">NIC Front:</span><span class="detail-value"><img src="../Uploads/candidates/'.$candidate['nic_front'].'" class="document-preview"></span></div>';
        }
        if (!empty($candidate['nic_back'])) {
            echo '<div class="detail-row"><span class="detail-label">NIC Back:</span><span class="detail-value"><img src="../Uploads/candidates/'.$candidate['nic_back'].'" class="document-preview"></span></div>';
        }
        echo '</div>';
        echo '</div>';
        echo '</div>';
        
        // Passport Information
        if (!empty($candidate['passport_no'])) {
            echo '<div class="candidate-details-section">';
            echo '<h5>Passport Information</h5>';
            echo '<div class="row">';
            echo '<div class="col-md-6">';
            echo '<div class="detail-row"><span class="detail-label">Passport No:</span><span class="detail-value">'.$candidate['passport_no'].'</span></div>';
            echo '<div class="detail-row"><span class="detail-label">Country of Issue:</span><span class="detail-value">'.$candidate['country_issue'].'</span></div>';
            echo '<div class="detail-row"><span class="detail-label">Issue Date:</span><span class="detail-value">'.date('d M Y', strtotime($candidate['passport_issue'])).'</span></div>';
            echo '</div>';
            echo '<div class="col-md-6">';
            echo '<div class="detail-row"><span class="detail-label">Expiry Date:</span><span class="detail-value">'.date('d M Y', strtotime($candidate['passport_expair'])).'</span></div>';
            echo '<div class="detail-row"><span class="detail-label">Status:</span><span class="detail-value">'.$candidate['passport_status'].'</span></div>';
            if (!empty($candidate['passport_scan'])) {
                echo '<div class="detail-row"><span class="detail-label">Passport Scan:</span><span class="detail-value"><img src="../Uploads/candidates/'.$candidate['passport_scan'].'" class="document-preview"></span></div>';
            }
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        
        // Emergency Contact
        echo '<div class="candidate-details-section">';
        echo '<h5>Emergency Contact</h5>';
        echo '<div class="row">';
        echo '<div class="col-md-6">';
        echo '<div class="detail-row"><span class="detail-label">Name:</span><span class="detail-value">'.$candidate['name'].'</span></div>';
        echo '<div class="detail-row"><span class="detail-label">Relationship:</span><span class="detail-value">'.$candidate['relationship'].'</span></div>';
        echo '</div>';
        echo '<div class="col-md-6">';
        echo '<div class="detail-row"><span class="detail-label">Mobile No:</span><span class="detail-value">'.$candidate['mobile_no'].'</span></div>';
        echo '<div class="detail-row"><span class="detail-label">Alternative No:</span><span class="detail-value">'.$candidate['alternative_no'].'</span></div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        
        // Education
        echo '<div class="candidate-details-section">';
        echo '<h5>Education</h5>';
        echo '<div class="row">';
        echo '<div class="col-md-6">';
        echo '<div class="detail-row"><span class="detail-label">Highest Qualification:</span><span class="detail-value">'.$candidate['highest_qualification'].'</span></div>';
        echo '<div class="detail-row"><span class="detail-label">Field of Study:</span><span class="detail-value">'.$candidate['fieldofstudy'].'</span></div>';
        echo '</div>';
        echo '<div class="col-md-6">';
        echo '<div class="detail-row"><span class="detail-label">Institute Name:</span><span class="detail-value">'.$candidate['institute_name'].'</span></div>';
        echo '<div class="detail-row"><span class="detail-label">Completion Year:</span><span class="detail-value">'.$candidate['complete_year'].'</span></div>';
        echo '</div>';
        echo '</div>';
        if (!empty($candidate['certificate_image'])) {
            echo '<div class="detail-row"><span class="detail-label">Certificate:</span><span class="detail-value"><img src="../Uploads/candidates/'.$candidate['certificate_image'].'" class="document-preview"></span></div>';
        }
        echo '</div>';
        
        // Skills
        echo '<div class="candidate-details-section">';
        echo '<h5>Skills</h5>';
        echo '<div class="row">';
        echo '<div class="col-md-6">';
        echo '<div class="detail-row"><span class="detail-label">Languages:</span><span class="detail-value">'.$candidate['language'].'</span></div>';
        echo '<div class="detail-row"><span class="detail-label">Work Skills:</span><span class="detail-value">'.$candidate['work_skill'].'</span></div>';
        echo '</div>';
        echo '<div class="col-md-6">';
        echo '<div class="detail-row"><span class="detail-label">Computer Skill:</span><span class="detail-value">'.$candidate['computer_skill'].'</span></div>';
        if (!empty($candidate['technical_image'])) {
            echo '<div class="detail-row"><span class="detail-label">Technical Certificate:</span><span class="detail-value"><img src="../Uploads/candidates/'.$candidate['technical_image'].'" class="document-preview"></span></div>';
        }
        echo '</div>';
        echo '</div>';
        echo '</div>';
        
        // Work Experience
        if (!empty($candidate['company_name'])) {
            echo '<div class="candidate-details-section">';
            echo '<h5>Work Experience</h5>';
            echo '<div class="row">';
            echo '<div class="col-md-6">';
            echo '<div class="detail-row"><span class="detail-label">Company Name:</span><span class="detail-value">'.$candidate['company_name'].'</span></div>';
            echo '<div class="detail-row"><span class="detail-label">Position:</span><span class="detail-value">'.$candidate['position'].'</span></div>';
            echo '</div>';
            echo '<div class="col-md-6">';
            echo '<div class="detail-row"><span class="detail-label">Duration:</span><span class="detail-value">'.$candidate['duration'].'</span></div>';
            echo '<div class="detail-row"><span class="detail-label">Country:</span><span class="detail-value">'.$candidate['country_work'].'</span></div>';
            echo '</div>';
            echo '</div>';
            echo '<div class="detail-row"><span class="detail-label">Reason for Leaving:</span><span class="detail-value">'.$candidate['reasonof_leave'].'</span></div>';
            if (!empty($candidate['expiriance_letter'])) {
                echo '<div class="detail-row"><span class="detail-label">Experience Letter:</span><span class="detail-value"><img src="../Uploads/candidates/'.$candidate['expiriance_letter'].'" class="document-preview"></span></div>';
            }
            echo '</div>';
        }
        
        // Medical Information
        echo '<div class="candidate-details-section">';
        echo '<h5>Medical Information</h5>';
        echo '<div class="row">';
        echo '<div class="col-md-6">';
        echo '<div class="detail-row"><span class="detail-label">Blood Group:</span><span class="detail-value">'.$candidate['blood'].'</span></div>';
        echo '<div class="detail-row"><span class="detail-label">Allergies:</span><span class="detail-value">'.$candidate['allergies'].'</span></div>';
        echo '</div>';
        echo '<div class="col-md-6">';
        echo '<div class="detail-row"><span class="detail-label">Chronic Conditions:</span><span class="detail-value">'.$candidate['chronic'].'</span></div>';
        echo '<div class="detail-row"><span class="detail-label">Fitness Status:</span><span class="detail-value">'.$candidate['fitness_status'].'</span></div>';
        echo '</div>';
        echo '</div>';
        if (!empty($candidate['medical_report'])) {
            echo '<div class="detail-row"><span class="detail-label">Medical Report:</span><span class="detail-value"><img src="../Uploads/candidates/'.$candidate['medical_report'].'" class="document-preview"></span></div>';
        }
        if (!empty($candidate['covid_card'])) {
            echo '<div class="detail-row"><span class="detail-label">COVID Card:</span><span class="detail-value"><img src="../Uploads/candidates/'.$candidate['covid_card'].'" class="document-preview"></span></div>';
        }
        if (!empty($candidate['vision_test'])) {
            echo '<div class="detail-row"><span class="detail-label">Vision Test:</span><span class="detail-value"><img src="../Uploads/candidates/'.$candidate['vision_test'].'" class="document-preview"></span></div>';
        }
        echo '</div>';
        
        // Police Clearance
        if (!empty($candidate['police_certificate'])) {
            echo '<div class="candidate-details-section">';
            echo '<h5>Police Clearance</h5>';
            echo '<div class="row">';
            echo '<div class="col-md-6">';
            echo '<div class="detail-row"><span class="detail-label">Certificate No:</span><span class="detail-value">'.$candidate['police_certificate'].'</span></div>';
            echo '<div class="detail-row"><span class="detail-label">Issue Date:</span><span class="detail-value">'.date('d M Y', strtotime($candidate['police_issuedate'])).'</span></div>';
            echo '</div>';
            echo '<div class="col-md-6">';
            echo '<div class="detail-row"><span class="detail-label">Expiry Date:</span><span class="detail-value">'.date('d M Y', strtotime($candidate['police_expiredate'])).'</span></div>';
            echo '<div class="detail-row"><span class="detail-label">Document:</span><span class="detail-value"><img src="../Uploads/candidates/'.$candidate['police_certificate'].'" class="document-preview"></span></div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        
        // Other Documents
        echo '<div class="candidate-details-section">';
        echo '<h5>Other Documents</h5>';
        echo '<div class="row">';
        if (!empty($candidate['other_document'])) {
            echo '<div class="col-md-6">';
            echo '<div class="detail-row"><span class="detail-label">Other Document:</span><span class="detail-value"><img src="../Uploads/candidates/'.$candidate['other_document'].'" class="document-preview"></span></div>';
            echo '</div>';
        }
        if (!empty($candidate['cv'])) {
            echo '<div class="col-md-6">';
            echo '<div class="detail-row"><span class="detail-label">CV:</span><span class="detail-value"><img src="../Uploads/candidates/'.$candidate['cv'].'" class="document-preview"></span></div>';
            echo '</div>';
        }
        echo '</div>';
        if (!empty($candidate['agreement'])) {
            echo '<div class="detail-row"><span class="detail-label">Agreement:</span><span class="detail-value"><img src="../Uploads/candidates/'.$candidate['agreement'].'" class="document-preview"></span></div>';
        }
        if (!empty($candidate['contract'])) {
            echo '<div class="detail-row"><span class="detail-label">Contract:</span><span class="detail-value"><img src="../Uploads/candidates/'.$candidate['contract'].'" class="document-preview"></span></div>';
        }
        if (!empty($candidate['undertaking_letter'])) {
            echo '<div class="detail-row"><span class="detail-label">Undertaking Letter:</span><span class="detail-value"><img src="../Uploads/candidates/'.$candidate['undertaking_letter'].'" class="document-preview"></span></div>';
        }
        echo '</div>';
    } else {
        echo '<div class="alert alert-danger">Candidate not found</div>';
    }
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error loading details: '.$e->getMessage().'</div>';
}
?>