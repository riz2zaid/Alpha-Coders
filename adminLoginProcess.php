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

// Check admin credentials
$query = "SELECT * FROM `admin` WHERE `username` = '$username' AND `password` = '$password'";
$result = Database::search($query);

if ($result && $result->num_rows == 1) {
    $_SESSION["admin"] = $result->fetch_assoc();
    echo "Success";
} else {
    echo "Invalid Username OR Password";
}
?>