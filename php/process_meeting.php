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

// التحقق من إرسال البيانات بطريقة POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect_to('meeting.php');
}

// 1. استخراج البيانات من الـ POST و الجلسة
$clientID = $_SESSION['userID']; // سحب ID العميل من الجلسة
$designerID = $conn->real_escape_string($_POST['designerID']);
$date = $conn->real_escape_string($_POST['date']);
$time = $conn->real_escape_string($_POST['time']);
$note = $conn->real_escape_string($_POST['note']);
$status = 'Pending'; 
$price = 350; // رسوم الاستشارة الافتراضية

// 2. إدراج الاجتماع في قاعدة البيانات
$sql_insert = "INSERT INTO meeting (clientID, designerID, date, time, status, note, price) 
               VALUES ('$clientID', '$designerID', '$date', '$time', '$status', '$note', '$price')";

if ($conn->query($sql_insert) === TRUE) {
    $meetingID = $conn->insert_id;

    // 3. جلب رابط Zoom واسم المصمم
    $sql_zoom = "SELECT u.name AS designerName, d.zoomLink FROM designer d 
                 JOIN user u ON d.designerID = u.userID 
                 WHERE d.designerID = '$designerID'";
    $result_zoom = $conn->query($sql_zoom);
    $zoom_data = $result_zoom->fetch_assoc();
    $zoomLink = $zoom_data['zoomLink'] ?? '#';

    // 4. عرض رسالة النجاح وتفاصيل الاجتماع (يمكنك توجيهه لصفحة أخرى إذا أردت)
    echo "
        <!DOCTYPE html>
        <html lang='en' dir='rtl'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Meeting Confirmed</title>
            <link rel='stylesheet' href='../css/decoria.css'>
            <style>
                .message-box { max-width: 600px; margin: 50px auto; padding: 30px; border: 1px solid #d8d3c5; border-radius: 8px; background-color: #f8f5ee; text-align: center; }
                .message-box h1 { color: #3b4d3b; }
            </style>
        </head>
        <body>
            <div class='message-box'>
                <h1>✅ تم حجز الاجتماع بنجاح!</h1>
                <p><strong>المصمم:</strong> {$zoom_data['designerName']}</p>
                <p><strong>التاريخ:</strong> $date</p>
                <p><strong>الوقت:</strong> $time</p>
                <p><strong>رابط Zoom:</strong> <a href='{$zoomLink}' target='_blank' style='color:#3b4d3b; font-weight:bold;'>انضم للاجتماع الآن</a></p>
                <p style='margin-top: 20px;'><a href='meeting.php' class='primary-btn'>حجز اجتماع آخر</a></p>
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
