<?php
require_once "config.php";
check_login('Designer');

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["designID"])) {
    echo "error";
    exit();
}

$designerID = $_SESSION['userID'];
$designID   = (int) $_POST["designID"];
$title      = trim($_POST["title"] ?? "");
$desc       = trim($_POST["description"] ?? "");

if ($title === "" || $desc === "") {
    echo "error";
    exit();
}

// extra safety: ensure this design belongs to this designer
$stmt = $conn->prepare("UPDATE design 
                        SET title = ?, description = ?
                        WHERE designID = ? AND designerID = ?");
$stmt->bind_param("ssii", $title, $desc, $designID, $designerID);
$stmt->execute();

echo "ok";
