<?php

$DB_HOST = 'localhost';
$DB_USER = 'root'; 
$DB_PASS = 'root';     
$DB_NAME = 'decoria';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");


session_start();

function redirect_to($location) {
    header("Location: $location");
    exit();
}


function check_login($role_required = '') {
    if (!isset($_SESSION['user_id'])) {
        redirect_to('login.php');
    }
    
    if (
        $role_required !== '' &&
        (!isset($_SESSION['role']) || $_SESSION['role'] !== $role_required)
    ) {
        
        redirect_to('home.php');
    }
}
