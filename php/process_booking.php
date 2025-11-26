<?php
// ===================================
// إعدادات الاتصال بقاعدة البيانات
// ===================================
$DB_HOST = 'localhost';
$DB_USER = 'root'; 
$DB_PASS = 'root';     
$DB_NAME = 'decoria';

// إنشاء الاتصال
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// لضمان دعم اللغة العربية بشكل صحيح
$conn->set_charset("utf8mb4");

// ===================================
// إدارة الجلسات والتحقق من تسجيل الدخول
// ===================================
session_start();

function redirect_to($location) {
    header("Location: $location");
    exit();
}

/**
 * التحقق من تسجيل الدخول والدور المطلوب
 */
function check_login($role_required = '') {
    if (!isset($_SESSION['userID'])) {
        redirect_to('login.php');
    }
    
    if ($role_required && (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== $role_required)) {
        redirect_to('home.html');
    }
}

// التحقق من تسجيل دخول العميل
check_login('Customer'); 

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect_to('booking.php');
}

// 1. معالجة رفع الملف
$transactionPhotoPath = null;
if (isset($_FILES['transactionPhoto']) && $_FILES['transactionPhoto']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['transactionPhoto']['tmp_name'];
    $fileName = $_FILES['transactionPhoto']['name'];
    $fileSize = $_FILES['transactionPhoto']['size'];
    $fileType = $_FILES['transactionPhoto']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    // إنشاء اسم ملف فريد لحفظه في المجلد
    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

    // **ملاحظة:** يجب إنشاء مجلد باسم 'transaction_uploads' في المسار المناسب لمشروعك
    $uploadFileDir = '../transaction_uploads/'; 
    $destPath = $uploadFileDir . $newFileName;

    if(move_uploaded_file($fileTmpPath, $destPath)) {
        // يتم تخزين اسم الملف فقط أو مساره (بناءً على كيف ستعرضه لاحقاً)
        $transactionPhotoPath = $conn->real_escape_string($newFileName); 
    } else {
        die("حدث خطأ في حفظ صورة الحوالة. الرجاء المحاولة مرة أخرى.");
    }
} else {
     die("صورة الحوالة مطلوبة لتأكيد الحجز. يرجى العودة وإرفاقها.");
}


// 2. استخراج البيانات من الـ POST و الجلسة
$clientID = $_SESSION['userID'];
$designerID = $conn->real_escape_string($_POST['designerID']);
$date = $conn->real_escape_string($_POST['date']);
$time = $conn->real_escape_string($_POST['time']);
$status = 'Payment Pending'; // الحالة الأولية بعد إرفاق صورة الحوالة
$price = 10000; // سعر الحجز الافتراضي

// 3. إدراج الحجز في قاعدة البيانات
// ملاحظة: تم استخدام حقل 'receipt' لتخزين اسم ملف صورة الحوالة ($transactionPhotoPath)
$sql_insert = "INSERT INTO booking (clientID, designerID, date, time, status, price, receipt) 
               VALUES ('$clientID', '$designerID', '$date', '$time', '$status', '$price', '$transactionPhotoPath')";

if ($conn->query($sql_insert) === TRUE) {
    $bookingID = $conn->insert_id;

    // 4. عرض رسالة النجاح
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
                <h1>✅ تم إرسال طلب الحجز بنجاح!</h1>
                <p><strong>رقم الحجز:</strong> $bookingID</p>
                <p><strong>التاريخ:</strong> $date</p>
                <p><strong>الحالة:</strong> $status (قيد مراجعة صورة الحوالة)</p>
                <p style='margin-top: 20px;'>سوف يتم مراجعة صورة الحوالة البنكية من قبل فريقنا قريباً.</p>
                <p style='margin-top: 20px;'><a href='timeline.html' class='primary-btn'>عرض جدول المشروع (Timeline)</a></p>
            </div>
        </body>
        </html>
    ";

} else {
    // عرض رسالة خطأ
    die("حدث خطأ أثناء الحجز: " . $conn->error);
}

$conn->close();
?>
