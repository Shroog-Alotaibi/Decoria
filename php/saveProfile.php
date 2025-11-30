<?php
require_once "../config.php";
session_start();
check_login('Designer');

header('Content-Type: application/json');

$designerID = $_SESSION['userID'];

$specialty = trim($_POST['specialty'] ?? '');
$bio       = trim($_POST['bio'] ?? '');

// First, get current profile so we can keep old image if none uploaded
$sql  = "SELECT d.profilePicture, u.name
         FROM designer d
         JOIN user u ON u.userID = d.designerID
         WHERE d.designerID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("d", $designerID);
$stmt->execute();
$current = $stmt->get_result()->fetch_assoc();

if (!$current) {
    echo json_encode(['success' => false, 'error' => 'Designer not found']);
    exit;
}

$profilePicture = $current['profilePicture'];

// If a new image uploaded
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../photo/designer/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $img     = $_FILES['image'];
    $ext     = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif','webp'];

    if (!in_array($ext, $allowed)) {
        echo json_encode(['success' => false, 'error' => 'Invalid image type']);
        exit;
    }

    $filename     = 'designer_' . $designerID . '_' . time() . '.' . $ext;
    $targetPath   = $uploadDir . $filename;
    $relativePath = 'photo/designer/' . $filename;

    if (!move_uploaded_file($img['tmp_name'], $targetPath)) {
        echo json_encode(['success' => false, 'error' => 'Failed to save image']);
        exit;
    }

    // Optionally delete old pic if you want and it’s not default
    $oldPath = '../' . $profilePicture;
    if ($profilePicture && strpos($profilePicture, 'defaultAvatar') === false && is_file($oldPath)) {
        @unlink($oldPath);
    }

    $profilePicture = $relativePath;
}

// Update DB
$sql  = "UPDATE designer
         SET specialty = ?, bio = ?, profilePicture = ?
         WHERE designerID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssd", $specialty, $bio, $profilePicture, $designerID);
$stmt->execute();

// Recount reviews
$sql  = "SELECT COUNT(*) AS cnt FROM review WHERE designerID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("d", $designerID);
$stmt->execute();
$rc   = $stmt->get_result()->fetch_assoc();
$reviewsCount = $rc ? (int)$rc['cnt'] : 0;

echo json_encode([
    'success' => true,
    'profile' => [
        'name'             => $current['name'],
        'specialty'        => $specialty,
        'bio'              => $bio,
        'profilePictureUrl'=> '../' . $profilePicture,
        'reviewsCount'     => $reviewsCount
    ]
]);
