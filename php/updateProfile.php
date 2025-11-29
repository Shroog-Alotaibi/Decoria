<?php
require_once "config.php";
check_login('Designer');

$designerID = $_SESSION['user_id'];
$specialty  = trim($_POST["specialty"] ?? "");
$bio        = trim($_POST["bio"] ?? "");

$newImagePath = null;

if (!empty($_FILES["profileImage"]["name"])) {
    $targetDir = "../photo/profiles/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName   = time() . "_" . basename($_FILES["profileImage"]["name"]);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($_FILES["profileImage"]["tmp_name"], $targetFile)) {
        $newImagePath = "../photo/profiles/" . $fileName;
    }
}

if ($newImagePath) {
    $stmt = $conn->prepare("UPDATE designer 
                            SET specialty = ?, bio = ?, profilePicture = ?
                            WHERE designerID = ?");
    $stmt->bind_param("sssi", $specialty, $bio, $newImagePath, $designerID);
} else {
    $stmt = $conn->prepare("UPDATE designer 
                            SET specialty = ?, bio = ?
                            WHERE designerID = ?");
    $stmt->bind_param("ssi", $specialty, $bio, $designerID);
}

$stmt->execute();

echo json_encode(["status" => "success"]);
