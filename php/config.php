<?php
// ===================================
// DATABASE CONNECTION
// ===================================
$DB_HOST = 'localhost';
$DB_USER = 'root'; 
$DB_PASS = 'root';
$DB_NAME = 'decoria';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// ===================================
// SESSION SETUP
// ===================================
session_start();

// ===================================
// HELPERS
// ===================================
function redirect_to($location) {
    header("Location: $location");
    exit();
}

/**
 * Check if the user is logged in AND (optionally) verify role
 */
function check_login($role_required = '') {

    // Must be logged in
    if (!isset($_SESSION['userID'])) {
        redirect_to('login.php');
    }

    // If a role is required, enforce it
    if ($role_required !== '' && (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== $role_required)) {
        redirect_to('home.html');
    }
}
?>

