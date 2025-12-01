<?php
require_once "config.php";
session_start();
check_login("Designer");

$designerID = $_SESSION['user_id'];

if (!isset($_POST['bookingID'], $_POST['status'])) {
    die("missing");
}

$bookingID = intval($_POST['bookingID']);
$status    = $_POST['status'];


$validSteps = ["received", "in_progress", "completed"];
if (!in_array($status, $validSteps)) {
    die("invalid");
}


$sql = "SELECT designerID FROM booking WHERE bookingID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $bookingID);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if (!$result || $result['designerID'] != $designerID) {
    die("unauthorized");
}


$sql = "UPDATE bookingtimeline 
        SET steps = ?, lastUpdate = NOW()
        WHERE bookingID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $bookingID);
$stmt->execute();



$bookingStatus = "";

switch ($status) {
    case "received":
        
        $bookingStatus = "Request";
        break;

    case "in_progress":
        $bookingStatus = "In Progress";
        break;

    case "completed":
        $bookingStatus = "Completed";
        break;
}


$sql = "UPDATE booking SET status = ? WHERE bookingID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $bookingStatus, $bookingID);
$stmt->execute();

echo "success";
exit;
?>
