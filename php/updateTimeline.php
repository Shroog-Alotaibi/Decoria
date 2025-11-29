<?php
require_once "../php/config.php"; 
check_login("Designer");

if (!isset($_POST['bookingID'], $_POST['status'])) {
    http_response_code(400);
    exit("Missing parameters");
}

$bookingID = intval($_POST['bookingID']);
$status = $_POST['status'];

// Prevent designers from setting "not_received"
if ($status === "not_received") {
    http_response_code(403);
    exit("Not allowed");
}

// Ensure designer owns this booking
$sql = "SELECT bookingID FROM booking WHERE bookingID = ? AND designerID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $bookingID, $_SESSION['user_id']);
$stmt->execute();
$check = $stmt->get_result();

if ($check->num_rows === 0) {
    http_response_code(403);
    exit("Unauthorized");
}

// Update timeline
$sql2 = "UPDATE bookingtimeline SET steps = ? WHERE bookingID = ?";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("si", $status, $bookingID);
$stmt2->execute();

// Success
echo "OK";
