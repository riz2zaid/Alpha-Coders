<?php
include "../DB/connection.php";

// Initialize database connection
Database::setupConnection();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic input validation
   

    // Simple sanitization
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $bname = $_POST['bname'];
    $baddress = $_POST['baddress'];
    $selected_plan_id = $_POST['selected_plan'];

    // Get the selected plan details
    $plan_query = "SELECT * FROM subscription_plans WHERE id = '" . Database::$connection->real_escape_string($selected_plan_id) . "'";
    $plan_result = Database::$connection->query($plan_query);
    
    if ($plan_result->num_rows === 0) {
        echo "Invalid subscription plan selected.";
        exit;
    }
    
    $plan_data = $plan_result->fetch_assoc();
    $candidate_limit = $plan_data['base_candidate_limit'];
    
    // Calculate dates
    $today = date('Y-m-d');
    $subscription_end_date = date('Y-m-d', strtotime($today . ' + 30 days'));
    
    // Calculate year dates (360 days from today)
    $year_start_date = $today;
    $year_end_date = date('Y-m-d', strtotime($today . ' + 360 days'));

    // Generate a simple password (8 characters with letters and numbers)
    $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

    // File upload handling
    if (isset($_FILES['slip'])) {
        if ($_FILES['slip']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['slip'];

            // Validate file type
            $allowed_types = ['image/jpeg', 'image/png'];
            $file_type = mime_content_type($file['tmp_name']);

            if (!in_array($file_type, $allowed_types)) {
                echo "Only JPEG and PNG files are allowed.";
                exit;
            }

            // Validate file size (5MB max)
            $max_size = 5 * 1024 * 1024;
            if ($file['size'] > $max_size) {
                echo "File size must be less than 5MB.";
                exit;
            }

            // Create uploads directory if needed
            $upload_dir = 'slips/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Generate unique filename
            $filename = uniqid() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
            $target_path = $upload_dir . $filename;

            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                // Insert into database with all required fields including password
                $query = "INSERT INTO `request` 
                    (`fullname`, `email`, `bname`, `baddress`, `slip`, 
                     `status_request`, `registration_date`, `is_spacial_client`, 
                     `registration_fee_status`, `subscription_start_date`, 
                     `subscription_end_date`, `subscription_status`, 
                     `candidate_limit`, `canditate_added`, 
                     `allow_additional_candidates`, `subscription_plans_id`, `password`, `year_start_date`, `year_end_date`, `mobile`) 
                    VALUES (
                        '" . Database::$connection->real_escape_string($fullname) . "',
                        '" . Database::$connection->real_escape_string($email) . "',
                        '" . Database::$connection->real_escape_string($bname) . "',
                        '" . Database::$connection->real_escape_string($baddress) . "',
                        '" . Database::$connection->real_escape_string('slips/' . $filename) . "',
                        'Panding',
                        '" . $today . "',
                        0,
                        'Panding',
                        '" . $today . "',
                        '" . $subscription_end_date . "',
                        'Blocked',
                        '" . Database::$connection->real_escape_string($candidate_limit) . "',
                        'No',
                        0,
                        " . Database::$connection->real_escape_string($selected_plan_id) . ",
                        '" . Database::$connection->real_escape_string($password) . "',
                        '" . $year_start_date . "',
                        '" . $year_end_date . "',
                        '". Database::$connection->real_escape_string($mobile) ."'
                    )";

                $result = Database::iud($query);

                if ($result) {
                    echo "Success";
                } else {
                    echo "Database error: " . Database::$connection->error;
                }
            } else {
                echo "Error uploading file.";
            }
        } else {
            echo "File upload error: " . $_FILES['slip']['error'];
        }
    } else {
        echo "Please select a valid payment slip.";
    }
} else {
    echo "Invalid request method.";
}
?>