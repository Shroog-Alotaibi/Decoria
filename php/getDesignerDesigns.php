<?php
require_once "config.php";
check_login('Designer');

$designerID = $_SESSION['userID'];

$stmt = $conn->prepare("SELECT designID, title, description, image 
                        FROM design
                        WHERE designerID = ?
                        ORDER BY uploadDate DESC, designID DESC");
$stmt->bind_param("i", $designerID);
$stmt->execute();
$res = $stmt->get_result();

$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
