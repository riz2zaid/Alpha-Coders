<?php
include "connection.php";

// Initialize database connection
Database::setupConnection();

// Get POST data
$username = isset($_POST['username']) ? Database::$connection->real_escape_string($_POST['username']) : '';
$newPassword = isset($_POST['newPassword']) ? Database::$connection->real_escape_string($_POST['newPassword']) : '';

// Validate inputs
if (empty($username)) {
    echo "Username is required";
    exit;
} else if (empty($newPassword)) {
    echo "New password is required";
    exit;
}

// Check if user exists
$checkQuery = "SELECT * FROM `request` WHERE `username` = '$username'";
$checkResult = Database::search($checkQuery);

if ($checkResult && $checkResult->num_rows == 1) {
    // Update password
    $updateQuery = "UPDATE `request` SET `password` = '$newPassword' WHERE `username` = '$username'";
    $updateResult = Database::iud($updateQuery);
    
    if ($updateResult) {
        echo "Success";
    } else {
        echo "Failed to update password. Please try again.";
    }
} else {
    echo "Username not found";
}
?>