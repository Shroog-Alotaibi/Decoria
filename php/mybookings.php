<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "config.php"; 

// ===================================
// Session check
// ===================================
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: booking.php");
    exit();
}

// ===================================
// 1. Handle file upload
// ===================================
$transactionPhotoPath = null;

if (isset($_FILES['transactionPhoto']) && $_FILES['transactionPhoto']['error'] === UPLOAD_ERR_OK) {

    $fileTmpPath = $_FILES['transactionPhoto']['tmp_name'];
    $fileName = $_FILES['transactionPhoto']['name'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    // اسم جديد للصورة
    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

   
    $uploadFileDir = '../photo/uploads';


    // لازم تتأكدين أن مجلد uploads موجود داخل: php/
    $destPath = $uploadFileDir . $newFileName;

    if (move_uploaded_file($fileTmpPath, $destPath)) {
        $transactionPhotoPath = $conn->real_escape_string($newFileName);
    } else {
        die("Error saving transaction photo.");
    }

} else {
    die("Transaction photo is required.");
}

// ===================================
// 2. Extract data
// ===================================
$clientID   = $_SESSION['user_id'];
$designerID = $conn->real_escape_string($_POST['designerID']);
$designID   = $conn->real_escape_string($_POST['designID']); // ← تمت إضافتها
$date       = $conn->real_escape_string($_POST['date']);
$time       = $conn->real_escape_string($_POST['time']);
$status     = 'Request';
$price      = 10000;

// ===================================
// 3. Insert Booking
// ===================================
$sql_insert = "
INSERT INTO booking (clientID, designerID, designID, date, time, status, price, receipt)
VALUES ('$clientID', '$designerID', '$designID', '$date', '$time', '$status', '$price', '$transactionPhotoPath')
";

if ($conn->query($sql_insert) === TRUE) {

    $bookingID = $conn->insert_id;

    echo "
    <!DOCTYPE html>
    <html lang='en' dir='rtl'>
    <head>
        <meta charset='UTF-8'>
        <title>Booking Submitted</title>
        <link rel='stylesheet' href='../css/decoria.css'>
        <style>
            .message-box {
                max-width: 600px;
                margin: 50px auto;
                padding: 30px;
                border: 1px solid #d8d3c5;
                border-radius: 8px;
                background-color: #f8f5ee;
                text-align: center;
            }
            .message-box h1 { color: #3b4d3b; }
        </style>
    </head>
    <body>
        <div class='message-box'>
            <h1>✅ Your booking request has been successfully submitted!</h1>
            <p><strong>Booking Number:</strong> $bookingID</p>
            <p><strong>Date:</strong> $date</p>
            <p><strong>Status:</strong> $status (Transaction photo under review)</p>
            <p style='margin-top: 20px;'>Our team will review your bank transfer photo soon.</p>
             <!-- NEW BUTTON -->
    <p style='margin-top: 25px;'>
        <a href='home.php'
           style='display:inline-block; padding:10px 20px; background:#3b4d3b; 
                  color:white; text-decoration:none; border-radius:8px; font-weight:600;'>
           Go to Homepage
        </a>
    </p>
            
        </div>
    </body>
    </html>
    ";

} else {
    die("Booking error: " . $conn->error);
}

$conn->close();
?>
