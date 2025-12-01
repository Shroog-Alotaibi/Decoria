<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "config.php";   


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: meeting.php");
    exit();
}


$clientID   = $_SESSION['user_id'];   
$designerID = $conn->real_escape_string($_POST['designerID']);
$date       = $conn->real_escape_string($_POST['date']);
$time       = $conn->real_escape_string($_POST['time']);
$note       = $conn->real_escape_string($_POST['note']);
$status     = 'Pending';
$price      = 350;


$sql_insert = "
INSERT INTO meeting (clientID, designerID, date, time, status, note, price)
VALUES ('$clientID', '$designerID', '$date', '$time', '$status', '$note', '$price')
";

if ($conn->query($sql_insert) === TRUE) {

    $meetingID = $conn->insert_id;

   
    $sql_zoom = "
        SELECT u.name AS designerName, d.zoomLink
        FROM designer d
        JOIN user u ON d.designerID = u.userID
        WHERE d.designerID = '$designerID'
    ";

    $result_zoom = $conn->query($sql_zoom);
    $zoom_data   = $result_zoom->fetch_assoc();

    $designerName = $zoom_data['designerName'] ?? 'Unknown';
    $zoomLink     = $zoom_data['zoomLink'] ?? '#';


    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>Meeting Confirmed</title>
        <link rel='stylesheet' href='../css/decoria.css'>
        <style>
            .message-box {
                max-width: 600px; margin: 50px auto; padding: 30px;
                border: 1px solid #d8d3c5; border-radius: 8px;
                background-color: #f8f5ee; text-align: center;
            }
            .message-box h1 { color: #3b4d3b; }
            a.primary-btn {
                display:inline-block; padding:10px 20px;
                background:#3b4d3b; color:white; border-radius:6px;
                text-decoration:none; margin-top:20px;
            }
        </style>
    </head>

    <body>
        <div class='message-box'>
            <h1>✅ The meeting has been successfully booked!</h1>
            <p><strong>Designer:</strong> $designerName</p>
            <p><strong>Date:</strong> $date</p>
            <p><strong>Time:</strong> $time</p>
            <p><strong>Zoom Link:</strong> 
                <a href='$zoomLink' target='_blank' style='color:#3b4d3b; font-weight:bold;'>Join Meeting</a>
            </p>
      <!-- NEW BUTTON -->
    <p style='margin-top: 25px;'>
        <a href='home.php'
           style='display:inline-block; padding:10px 20px; background:#3b4d3b; 
                  color:white; text-decoration:none; border-radius:8px; font-weight:600;'>
           Go to Homepage
        </a>
    </p>
            <a href='meeting.php' class='primary-btn'>Book another meeting</a>
        </div>
    </body>
    </html>";
    
} else {
    die("Error while booking: " . $conn->error);
}

$conn->close();
?>
