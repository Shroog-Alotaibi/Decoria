<?php
require_once "config.php";
check_login('Designer');

// We always use the logged-in designer
$designerID = $_SESSION['user_id'];

$sql = "SELECT name, specialty, bio, city, profilePicture, linkedin
        FROM designer
        WHERE designerID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $designerID);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows < 1) {
    echo json_encode(["status" => "error", "msg" => "not found"]);
    exit();
}

$row = $res->fetch_assoc();

echo json_encode([
    "status"         => "success",
    "name"           => $row["name"],
    "specialty"      => $row["specialty"],
    "bio"            => $row["bio"],
    "city"           => $row["city"],
    "profilePicture" => $row["profilePicture"],
    "linkedin"       => $row["linkedin"]
]);
