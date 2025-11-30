<?php
require_once "config.php";
session_start();
check_login('Designer');

header('Content-Type: application/json');

$designerID = $_SESSION['user_id'];

$designID    = intval($_POST['designID'] ?? 0);
$title       = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($designID <= 0 || $title === '' || $description === '') {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
    exit;
}

// Ensure the design belongs to this designer
$sql  = "UPDATE design 
         SET title = ?, description = ?
         WHERE designID = ? AND designerID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssdd", $title, $description, $designID, $designerID);
$stmt->execute();

if ($stmt->affected_rows >= 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Update failed']);
}
