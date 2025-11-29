<?php
require_once "config.php";
check_login('Designer');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "msg" => "invalid request"]);
    exit();
}

$designerID  = $_SESSION['user_id'];
$title       = trim($_POST["title"] ?? "");
$description = trim($_POST["description"] ?? "");

if ($title === "" || $description === "" || empty($_FILES["image"]["name"])) {
  echo json_encode(["status" => "error", "msg" => "missing fields"]);
  exit();
}

$targetDir = "../photo/uploads/";
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$fileName   = time() . "_" . basename($_FILES["image"]["name"]);
$targetFile = $targetDir . $fileName;

if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
    echo json_encode(["status" => "error", "msg" => "upload failed"]);
    exit();
}

$imagePath  = "../photo/uploads/" . $fileName;
$uploadDate = date("Y-m-d");

// designerID, title, description, image, uploadDate
$stmt = $conn->prepare("INSERT INTO design (designerID, title, description, image, uploadDate)
                        VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $designerID, $title, $description, $imagePath, $uploadDate);
$stmt->execute();

$designID = $stmt->insert_id;

echo json_encode([
    "status"      => "success",
    "designID"    => $designID,
    "title"       => $title,
    "description" => $description,
    "image"       => $imagePath
]);
