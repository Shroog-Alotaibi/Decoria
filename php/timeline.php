<?php
require_once "config.php";
session_start();
check_login("Customer");

$clientID = $_SESSION['user_id'];

if (!isset($_GET['bookingID'])) {
    die("No booking ID provided.");
}

$bookingID = intval($_GET['bookingID']);

// Fetch booking, timeline, and designer info
$sql = "SELECT 
            b.bookingID,
            b.designerID,
            b.clientID,
            bt.steps,
            bt.lastUpdate,
            u.name AS designerName,
            d.specialty,
            d.profilePicture
        FROM booking b
        JOIN bookingtimeline bt ON bt.bookingID = b.bookingID
        JOIN user u ON u.userID = b.designerID
        JOIN designer d ON d.designerID = b.designerID
        WHERE b.bookingID = ? AND b.clientID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $bookingID, $clientID);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Booking not found or unauthorized access.");
}

$step = $data['steps'];

$stepOrder = [
    "not_received" => 1,
    "request_received" => 2,
    "in_progress" => 3,
    "completed" => 4
];

$stepIndex = $stepOrder[$step];

$images = [
    "not_received" => "../photo/not-received.png",
    "request_received" => "../photo/request-received.png",
    "in_progress" => "../photo/InProgress.png",
    "completed" => "../photo/completed.png"
];
?>
