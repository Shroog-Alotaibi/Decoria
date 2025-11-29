<?php
require_once "config.php";
check_login('Designer');

$designerID = $_SESSION['user_id'];

$sql = "SELECT r.rating, r.comment, r.reviewDate, u.name AS clientName
        FROM review r
        LEFT JOIN user u ON u.userID = r.clientID
        WHERE r.designerID = ?
        ORDER BY r.reviewDate DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $designerID);
$stmt->execute();
$res = $stmt->get_result();

$list = [];
while ($row = $res->fetch_assoc()) {
    $list[] = [
        "name"   => $row["clientName"] ?: "Client",
        "rating" => $row["rating"],
        "text"   => $row["comment"],
        "date"   => $row["reviewDate"]
    ];
}

echo json_encode($list);
