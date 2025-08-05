<?php
session_start();
include "../DB/connection.php";

// Initialize database connection
Database::setupConnection();

// Get POST data
$username = isset($_POST['u']) ? Database::$connection->real_escape_string($_POST['u']) : '';
$password = isset($_POST['p']) ? Database::$connection->real_escape_string($_POST['p']) : '';

// Validate inputs
if (empty($username)) {
    echo "Please enter your Username.";
    exit;
} else if (empty($password)) {
    echo "Please enter your Password.";
    exit;
}

// Check user credentials
$query = "SELECT * FROM `request` WHERE `email` = '$username' AND `password` = '$password'";
$result = Database::search($query);

if ($result && $result->num_rows == 1) {
    $user = $result->fetch_assoc();
    
    // Check account status
    if ($user['status_request'] !== 'Approved') {
        echo "Your account is not yet approved. Please contact support.";
        exit;
    }
    
    // Check if subscription is active
    if ($user['subscription_status'] === 'Blocked') {
        echo "Your account is currently blocked. Please contact support.";
        exit;
    }
    
    // All checks passed - login successful
    $_SESSION["client"] = $user;
    echo "Success";
    
} else {
    echo "Invalid Username or Password";
}
?>