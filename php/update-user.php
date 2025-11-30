<?php
require_once "config.php"; // اتصال + session_start

if (!isset($_POST['userID'])) {
    echo "Missing userID";
    exit();
}

$userID = intval($_POST['userID']);
$name   = mysqli_real_escape_string($conn, $_POST['name']);
$email  = mysqli_real_escape_string($conn, $_POST['email']);
$phone  = mysqli_real_escape_string($conn, $_POST['phone']);

$update = "
    UPDATE user
    SET name='$name',
        email='$email',
        phoneNumber='$phone'
    WHERE userID = $userID
";

if (mysqli_query($conn, $update)) {
    echo "SUCCESS";
} else {
    echo "DB ERROR: " . mysqli_error($conn);
}
