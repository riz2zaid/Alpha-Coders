<?php
session_start();
if (!isset($_SESSION['client'])) {
    header("Location: index.php");
    exit();
}

include "client_connection.php";

// Initialize variables
$candidate = [];
$id = $_GET['id'] ?? 0;

// Fetch candidate data
try {
    Database::setupConnection();
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
              WHERE c.id = $id";
    
    $result = Database::search($query);
    if ($result->num_rows > 0) {
        $candidate = $result->fetch_assoc();
    } else {
        header("Location: candidateDatabase.php");
        exit();
    }
} catch (Exception $e) {
    error_log("Error fetching candidate: " . $e->getMessage());
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Database::setupConnection();
        
        // Basic personal info
        $full_name = $_POST['full_name'];
        $date_of_birth = $_POST['date_of_birth'];
        $gender = $_POST['gender'];
        $civil_status = $_POST['civil_status'];
        $address = $_POST['address'];
        $district_province = $_POST['district_province'];
        $contact_number = $_POST['contact_number'];
        $email_address = $_POST['email_address'];
        
        // NIC details
        $nic_number = $_POST['nic_number'];
        $nic_issue_date = $_POST['nic_issue_date'];
        
        // Passport details
        $passport_number = $_POST['passport_number'];
        $country_of_issue = $_POST['country_of_issue'];
        $passport_status = $_POST['passport_status'];
        $passport_issue_date = $_POST['passport_issue_date'];
        $passport_expiry_date = $_POST['passport_expiry_date'];
        
        // Update query
        $updateQuery = "UPDATE candidate SET 
                        fullname = '$full_name',
                        dob = '$date_of_birth',
                        gender_id = $gender,
                        clivil_status_id = $civil_status,
                        address = '$address',
                        district_province = '$district_province',
                        contact_number = '$contact_number',
                        email = '$email_address',
                        nic_no = '$nic_number',
                        issue_date = " . ($nic_issue_date ? "'$nic_issue_date'" : "NULL") . ",
                        passport_no = '$passport_number',
                        country_issue = '$country_of_issue',
                        passport_status_id = $passport_status,
                        passport_issue = " . ($passport_issue_date ? "'$passport_issue_date'" : "NULL") . ",
                        passport_expiry = " . ($passport_expiry_date ? "'$passport_expiry_date'" : "NULL") . "
                        WHERE id = $id";
        
        $result = Database::iud($updateQuery);
        
        if ($result) {
            $_SESSION['success'] = "Candidate updated successfully!";
            header("Location: candidateDatabase.php");
            exit();
        } else {
            throw new Exception("Failed to update candidate");
        }
    } catch (Exception $e) {
        error_log("Error updating candidate: " . $e->getMessage());
        $_SESSION['error'] = "Error updating candidate: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Candidate - SkyLink</title>
    <link rel="icon" type="image/x-icon" href="../image/logo/icon.png">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* Your existing CSS styles from addCandidate.php */
        /* ... [Include all your CSS styles here] ... */
    </style>
</head>
<body>
    <section id="sidebar">
        <!-- Your existing sidebar -->
        <?php include 'sidebar.php'; ?>
    </section>

    <section id="content">
        <nav>
            <!-- Your existing navigation -->
            <?php include 'navbar.php'; ?>
        </nav>

        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Edit Candidate</h1>
                    <ul class="breadcrumb">
                        <li><a href="#">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a href="candidateDatabase.php">Candidate Database</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Edit Candidate</a></li>
                    </ul>
                </div>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="form-container">
                <div class="form-section">
                    <h2><i class="fas fa-user me-2"></i>1. Personal Information</h2>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="full_name" value="<?php echo htmlspecialchars($candidate['fullname'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date of Birth</label>
                                <input type="date" name="date_of_birth" value="<?php echo htmlspecialchars($candidate['dob'] ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Gender</label>
                                <select name="gender" required>
                                    <?php
                                    $genders = Database::search("SELECT * FROM gender");
                                    while ($gender = $genders->fetch_assoc()) {
                                        $selected = ($gender['id'] == $candidate['gender_id']) ? 'selected' : '';
                                        echo "<option value='{$gender['id']}' $selected>{$gender['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Civil Status</label>
                                <select name="civil_status" required>
                                    <?php
                                    $statuses = Database::search("SELECT * FROM clivil_status");
                                    while ($status = $statuses->fetch_assoc()) {
                                        $selected = ($status['id'] == $candidate['clivil_status_id']) ? 'selected' : '';
                                        echo "<option value='{$status['id']}' $selected>{$status['status']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Profile Photo</label>
                                <input type="file" name="profile_photo" accept="image/*">
                                <?php if (!empty($candidate['profile_photo'])): ?>
                                    <small>Current: <?php echo htmlspecialchars($candidate['profile_photo']); ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- [Include all other form sections from addCandidate.php with values populated] -->
                    
                    <div class="form-group">
                        <button type="submit"><i class="fas fa-save me-2"></i>Update Candidate</button>
                    </div>
                </div>
            </form>
        </main>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Your existing JavaScript from addCandidate.php
        // ... [Include all your JavaScript here] ...
    </script>
</body>
</html>