<?php
require_once "config.php";
check_login('Designer');

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["designID"])) {
    echo "error";
    exit();
}

$designerID = $_SESSION['userID'];
$designID   = (int) $_POST["designID"];

$stmt = $conn->prepare("DELETE FROM design 
                        WHERE designID = ? AND designerID = ?");
$stmt->bind_param("ii", $designID, $designerID);
$stmt->execute();

echo "ok";
