

<?php
include("config.php");
session_start();


if (!isset($_POST['bookingID'])) {
    echo "error";
    exit();
}

$bookingID = (int)$_POST['bookingID'];


$sql = "DELETE FROM booking WHERE bookingID = $bookingID";

if (mysqli_query($connection, $sql)) {
    echo "success";
} else {
    echo "error";
}
