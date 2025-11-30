<?php
require_once "config.php";
session_start();
check_login('Designer');

header('Content-Type: application/json');

$designerID = $_SESSION['user_id'];
$designID   = intval($_POST['designID'] ?? 0);

if ($designID <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid design ID']);
    exit;
}

// Optional: get image to delete from disk
$sql  = "SELECT image FROM design WHERE designID = ? AND designerID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("dd", $designID, $designerID);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

if (!$res) {
    echo json_encode(['success' => false, 'error' => 'Not found or not authorized']);
    exit;
}

$imagePath = '../' . $res['image'];

// Delete DB row
$sql  = "DELETE FROM design WHERE designID = ? AND designerID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("dd", $designID, $designerID);
$stmt->execute();

// Optionally delete file
if (is_file($imagePath)) {
    @unlink($imagePath);
}

echo json_encode(['success' => true]);

