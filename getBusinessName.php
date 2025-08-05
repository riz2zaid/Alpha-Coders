<?php
session_start();
include "../DB/connection.php";

// Initialize database connection
Database::setupConnection();

if (isset($_SESSION["client"])) {
    // If using session array
    echo $_SESSION["client"]["bname"];
} else {
    // Alternative method if session structure is different
    $username = $_SESSION["client"] ?? null;
    if ($username) {
        $query = "SELECT bname FROM request WHERE username = '".Database::$connection->real_escape_string($username)."'";
        $result = Database::search($query);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo $row["bname"];
        }
    }
}
?>