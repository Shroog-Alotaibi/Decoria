<?php
// ===================================
// Database connection settings
// ===================================
$DB_HOST = 'localhost';
$DB_USER = 'root'; 
$DB_PASS = 'root';     
$DB_NAME = 'decoria';

// Create connection
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure proper Arabic/UTF-8 support
$conn->set_charset("utf8mb4");

// ===================================
// Session management and login check
// ===================================
session_start();

function redirect_to($location) {
    header("Location: $location");
    exit();
}

/**
 * Verify login and required role
 */
function check_login($role_required = '') {
    if (!isset($_SESSION['userID'])) {
        redirect_to('login.php');
    }
    
    if ($role_required && (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== $role_required)) {
        redirect_to('home.html');
    }
}

// Verify customer login
check_login('Customer'); 

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect_to('booking.php');
}

// 1. Handle file upload
$transactionPhotoPath = null;
if (isset($_FILES['transactionPhoto']) && $_FILES['transactionPhoto']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['transactionPhoto']['tmp_name'];
    $fileName = $_FILES['transactionPhoto']['name'];
    $fileSize = $_FILES['transactionPhoto']['size'];
    $fileType = $_FILES['transactionPhoto']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    // Create a unique file name to save it in the folder
    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

    // **Note:** You must create a folder named 'transaction_uploads' in the correct project path
    $uploadFileDir = '../transaction_uploads/'; 
    $destPath = $uploadFileDir . $newFileName;

    if(move_uploaded_file($fileTmpPath, $destPath)) {
        // Save file name or its path (depending on how you plan to display it later)
        $transactionPhotoPath = $conn->real_escape_string($newFileName); 
    } else {
        die("An error occurred while saving the transaction photo. Please try again.");
    }
} else {
     die("The transaction photo is required to confirm the booking. Please go back and attach it.");
}


// 2. Extract data from POST and session
$clientID = $_SESSION['userID'];
$designerID = $conn->real_escape_string($_POST['designerID']);
$date = $conn->real_escape_string($_POST['date']);
$time = $conn->real_escape_string($_POST['time']);
$status = 'Payment Pending'; // Initial status after uploading the transaction photo
$price = 10000; // Default booking price

// 3. Insert booking into the database
// Note: The 'receipt' field is used to store the transaction photo file name ($transactionPhotoPath)
$sql_insert = "INSERT INTO booking (clientID, designerID, date, time, status, price, receipt) 
               VALUES ('$clientID', '$designerID', '$date', '$time', '$status', '$price', '$transactionPhotoPath')";

if ($conn->query($sql_insert) === TRUE) {
    $bookingID = $conn->insert_id;

    // 4. Display success message
    echo "
        <!DOCTYPE html>
        <html lang='en' dir='rtl'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Booking Submitted</title>
            <link rel='stylesheet' href='../css/decoria.css'>
            <style>
                .message-box { max-width: 600px; margin: 50px auto; padding: 30px; border: 1px solid #d8d3c5; border-radius: 8px; background-color: #f8f5ee; text-align: center; }
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
                <p style='margin-top: 20px;'><a href='timeline.html' class='primary-btn'>View Project Timeline</a></p>
            </div>
        </body>
        </html>
    ";

} else {
    // Display error message
    die("An error occurred while booking: " . $conn->error);
}

$conn->close();
?>
