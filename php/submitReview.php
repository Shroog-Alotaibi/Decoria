<?php
require_once "config.php";
session_start();


check_login('Customer');

$clientID   = $_SESSION['user_id'];
$designerID = intval($_POST['designerID']);
$rating     = intval($_POST['rating']);
$comment    = trim($_POST['comment']);


if (!$designerID || !$rating || !$comment) {
    echo "missing";
    exit;
}


$sql = "INSERT INTO review (rating, comment, reviewDate, clientID, designerID)
        VALUES (?, ?, NOW(), ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isii", $rating, $comment, $clientID, $designerID);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "error";
}
?>
