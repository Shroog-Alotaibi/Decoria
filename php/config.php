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
// SESSION & AUTH HELPERS
// ===================================
session_start();

function redirect_to($location) {
    header("Location: $location");
    exit();
}

/**
 * Check login and (optional) role
 * $role_required example: 'Designer'
 */
function check_login($role_required = '') {
    if (!isset($_SESSION['userID'])) {
        redirect_to('login.php');
    }
    
    if (
        $role_required !== '' &&
        (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== $role_required)
    ) {
        // User logged in but with wrong role
        redirect_to('home.html');
    }
}
