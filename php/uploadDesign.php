<?php
require_once "config.php";
session_start();
check_login('Designer');

header('Content-Type: application/json');

$designerID = $_SESSION['user_id'];

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($title === '' || $description === '' || !isset($_FILES['image'])) {
    echo json_encode(['success' => false, 'error' => 'Missing fields']);
    exit;
}


$uploadDir = '../photo/designs/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$img      = $_FILES['image'];
$ext      = pathinfo($img['name'], PATHINFO_EXTENSION);
$ext      = strtolower($ext);
$allowed  = ['jpg','jpeg','png','gif','webp'];

if (!in_array($ext, $allowed)) {
    echo json_encode(['success' => false, 'error' => 'Invalid image type']);
    exit;
}

$filename     = 'design_' . $designerID . '_' . time() . '.' . $ext;
$targetPath   = $uploadDir . $filename;
$relativePath = 'photo/designs/' . $filename; 

if (!move_uploaded_file($img['tmp_name'], $targetPath)) {
    echo json_encode(['success' => false, 'error' => 'Failed to save image']);
    exit;
}


$today = date('Y-m-d');
$sql   = "INSERT INTO design (title, description, image, designerID, uploadDate)
          VALUES (?, ?, ?, ?, ?)";
$stmt  = $conn->prepare($sql);
$stmt->bind_param("sssds", $title, $description, $relativePath, $designerID, $today);
$stmt->execute();
$newID = $stmt->insert_id;

echo json_encode([
    'success' => true,
    'design'  => [
        'designID'  => $newID,
        'title'     => $title,
        'description'=> $description,
        'imageUrl'  => '../' . $relativePath,
        'uploadDate'=> $today
    ]
]);
