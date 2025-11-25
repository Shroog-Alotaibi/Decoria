<?php
// ===================================
// إعدادات الاتصال بقاعدة البيانات
// ===================================
$DB_HOST = 'localhost';
$DB_USER = 'root'; 
$DB_PASS = '';     
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
        redirect_to('login.html');
    }
    
    if ($role_required && (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== $role_required)) {
        redirect_to('home.html');
    }
}

// التحقق من تسجيل دخول المصمم
check_login('Designer'); 

$designerID = $_SESSION['userID']; // ID المصمم المسجل دخوله

// 1. جلب طلبات الاجتماعات (Meeting Requests)
$meetings_query = "
    SELECT m.meetingID AS requestID, u.name AS clientName, u.email AS clientEmail,
           m.date, m.time, m.status, m.note, m.price, 'Meeting' AS type
    FROM meeting m
    JOIN user u ON m.clientID = u.userID
    WHERE m.designerID = '$designerID'
";
$meetings_result = $conn->query($meetings_query);

// 2. جلب طلبات الحجز (Booking Requests)
// **ملاحظة:** حقل receipt الآن يحتوي على اسم ملف الصورة
$bookings_query = "
    SELECT b.bookingID AS requestID, u.name AS clientName, u.email AS clientEmail,
           b.date, b.time, b.status, b.price, b.receipt, 'Booking' AS type
    FROM booking b
    JOIN user u ON b.clientID = u.userID
    WHERE b.designerID = '$designerID'
";
$bookings_result = $conn->query($bookings_query);

$requests = [];
while ($row = $meetings_result->fetch_assoc()) {
    $requests[] = $row;
}
while ($row = $bookings_result->fetch_assoc()) {
    $requests[] = $row;
}
// يمكن فرزها هنا (مثلاً: ترتيب تنازلي حسب التاريخ والوقت)
usort($requests, function($a, $b) {
    $time_a = strtotime($a['date'] . ' ' . $a['time']);
    $time_b = strtotime($b['date'] . ' ' . $b['time']);
    return $time_b - $time_a; // الترتيب من الأحدث للأقدم
});
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DECORIA | Request</title>

  <link rel="stylesheet" href="../css/decoria.css" />
  <link rel="stylesheet" href="../css/designers.css" />
  <link rel="stylesheet" href="../css/settings.css" />
  <link rel="stylesheet" href="../css/Request.css" />
  <style>
    /* أضف بعض التنسيقات لسهولة قراءة رسائل النجاح والخطأ */
    .status-pending { background-color: #ffeb3b; color: #333; }
    .status-in-progress { background-color: #2196f3; color: white; }
    .status-complete { background-color: #4caf50; color: white; }
    .status-payment-pending { background-color: #ff9800; color: white; } /* حالة جديدة */
    .details-popup {
      position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
      background: white; padding: 25px; border-radius: 10px; z-index: 1000;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3); display: none; width: 90%; max-width: 400px;
    }
  </style>
</head>
<body>
<main>
    
  <header class="site-header">
    <div class="container header-container">
      <div class="brand">
        <img src="../photo/Logo.png.png" alt="DECORIA Logo" class="logo">
      </div>

      <p class="welcome-text">Welcome, Designer</p>

      <div class="header-buttons">
        <button class="menu-toggle">☰</button>
      </div>
    </div>
  </header>

 <div class="sidebar" id="sidebar">
    <span class="close-btn" id="closeSidebar">&times;</span>
    <a href="home.html">Home</a>
    <a href="designers.html">Designers</a>
    <a href="booking.html">Booking</a>
    <a href="timeline.html">Timeline</a>
    <a href="meeting.html">Meeting</a>
    <a href="settings.html">Settings</a>
    <hr>
    <a href="login.html" class="logout">Logout</a>
  </div>

  <div id="overlay"></div>

  <main class="container">
      <h2 class="section-title">Client Requests</h2>

      <div class="filters">
        <input id="searchInput" class="search" placeholder="Search client name...">
        <select id="filterType">
            <option value="all">Show All</option>
            <option value="alert-booking">Bookings</option>
            <option value="alert-meeting">Meetings</option>
        </select>
      </div>

      <section id="alerts" class="alerts-grid">
      
        <?php if (empty($requests)): ?>
            <p class="no-requests-message" style="grid-column: 1 / -1; text-align: center; padding: 50px;">لا توجد طلبات جديدة حالياً.</p>
        <?php else: ?>
            <?php foreach ($requests as $req): ?>
                <?php
                $is_meeting = ($req['type'] === 'Meeting');
                $class_type = $is_meeting ? 'alert-meeting' : 'alert-booking';
                // تحويل الحالة لتكون متوافقة مع اسم الكلاس في CSS
                $status_class = 'status-' . strtolower(str_replace(' ', '-', $req['status']));
                $details_json = htmlspecialchars(json_encode($req), ENT_QUOTES, 'UTF-8');
                ?>
                <div class="alert-card <?php echo $class_type; ?>">
                    <div class="alert-info">
                        <h4>
                            <?php echo $req['clientName']; ?> 
                            <span class="badge <?php echo $class_type; ?>">
                                <?php echo $req['type']; ?>
                            </span>
                        </h4>
                        <p><strong>التاريخ:</strong> <?php echo $req['date']; ?></p>
                        <p><strong>الوقت:</strong> <?php echo $req['time']; ?></p>
                    </div>
                    <div class="alert-actions">
                        <span class="status-badge <?php echo $status_class; ?>"><?php echo $req['status']; ?></span>
                        <button class="btn-details" 
                                onclick='showDetailsPopup(<?php echo $details_json; ?>)'>
                            Details
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

      </section>

      <div id="booking-details-popup" class="details-popup" style="display:none;">
          <h3>Booking Details</h3>
          <p><strong>Client:</strong> <span id="clientName_b"></span></p>
          <p><strong>Email:</strong> <span id="clientEmail_b"></span></p>
          <p><strong>Date:</strong> <span id="date_b"></span></p>
          <p><strong>Time:</strong> <span id="time_b"></span></p>
          <p><strong>Price:</strong> <span id="price_b"></span> SAR</p>
          <p><strong>Transaction Photo:</strong> <a href="#" target="_blank" class="receipt-link" id="receipt_b">View Photo</a></p>
          <p><strong>Status:</strong> <span id="status_b"></span></p>
          <div class="btn-row" style="justify-content: center;">
            <button class="primary-btn" onclick="updateStatus('Booking', document.getElementById('requestID_b').value, 'Confirmed')">Approve Payment</button>
          </div>
          <button class="btn-close" onclick="closeDetailsPopup('booking')">Close</button>
          <input type="hidden" id="requestID_b">
      </div>

      <div id="meeting-details-popup" class="details-popup" style="display:none;">
          <h3>Meeting Details</h3>
          <p><strong>Client:</strong> <span id="clientName_m"></span></p>
          <p><strong>Email:</strong> <span id="clientEmail_m"></span></p>
          <p><strong>Meeting Date:</strong> <span id="date_m"></span></p>
          <p><strong>Time:</strong> <span id="time_m"></span></p>
          <p><strong>Price:</strong> <span id="price_m"></span> SAR</p>
          <p><strong>Notes:</strong> <span id="note_m"></span></p>
          <p><strong>Status:</strong> <span id="status_m"></span></p>
          <div class="btn-row" style="justify-content: center;">
             <button class="primary-btn" onclick="updateStatus('Meeting', document.getElementById('requestID_m').value, 'Confirmed')">Confirm Meeting</button>
          </div>
          <button class="btn-close" onclick="closeDetailsPopup('meeting')">Close</button>
          <input type="hidden" id="requestID_m">
      </div>

  </main>

<footer>
    <div class="footer-content">
      <p class="footer-text">
        © 2025 DECORIA — All rights reserved
        | <a href="terms.html">Terms & Conditions</a>
      </p>
      <img src="../photo/darlfooter.jpeg" alt="DECORIA Footer Image" class="footer-image">
    </div>
</footer>

<script>
    // الدوال الأساسية لإظهار وإخفاء النوافذ المنبثقة
    function closeDetailsPopup(type) {
        const popup = type === 'booking' ? document.getElementById('booking-details-popup') : document.getElementById('meeting-details-popup');
        popup.style.display = 'none';
        document.getElementById('overlay').style.display = 'none';
    }

    function showDetailsPopup(data) {
        document.getElementById('overlay').style.display = 'block';

        if (data.type === 'Booking') {
            document.getElementById('clientName_b').textContent = data.clientName;
            document.getElementById('clientEmail_b').textContent = data.clientEmail || 'N/A';
            document.getElementById('date_b').textContent = data.date;
            document.getElementById('time_b').textContent = data.time;
            document.getElementById('price_b').textContent = data.price;
            document.getElementById('status_b').textContent = data.status;
            
            // **الكود المُعدَّل لربط صورة الحوالة:**
            document.getElementById('receipt_b').href = `../transaction_uploads/${data.receipt}`; 
            
            document.getElementById('requestID_b').value = data.requestID;
            document.getElementById('booking-details-popup').style.display = 'block';

        } else { // Meeting
            document.getElementById('clientName_m').textContent = data.clientName;
            document.getElementById('clientEmail_m').textContent = data.clientEmail || 'N/A';
            document.getElementById('date_m').textContent = data.date;
            document.getElementById('time_m').textContent = data.time;
            document.getElementById('price_m').textContent = data.price;
            document.getElementById('note_m').textContent = data.note || 'لا توجد ملاحظات إضافية.';
            document.getElementById('status_m').textContent = data.status;
            document.getElementById('requestID_m').value = data.requestID;
            document.getElementById('meeting-details-popup').style.display = 'block';
        }
    }
    
    // دالة لتحديث حالة الطلب (تحتاج ملف PHP لمعالجة AJAX)
    function updateStatus(type, id, newStatus) {
        if (!confirm(`هل أنت متأكد من تغيير حالة ${type} رقم ${id} إلى ${newStatus}؟`)) {
            return;
        }

        // في بيئة الإنتاج، يجب استخدام XMLHttpRequest أو fetch
        alert(`تم محاكاة تحديث حالة ${type} رقم ${id} إلى ${newStatus}. يجب عليك إنشاء ملف PHP لتنفيذ هذا التحديث في قاعدة البيانات.`);
        window.location.reload(); // إعادة تحميل الصفحة لرؤية التغيير (افتراضياً)
    }

    // JS لتصفية وبحث الطلبات (من ملف Request.js المُحمل)
    document.getElementById("filterType").addEventListener("change", function () {
      const value = this.value;
      document.querySelectorAll(".alert-card").forEach(card => {
        const isVisible = (value === "all" || card.classList.contains(value.replace('alert-', '')));
        card.style.display = isVisible ? "block" : "none";
      });
    });

    document.getElementById("searchInput").addEventListener("input", function () {
      const val = this.value.toLowerCase();
      document.querySelectorAll(".alert-card").forEach(card => {
        const text = card.querySelector('h4').textContent.toLowerCase();
        const currentDisplay = card.style.display;
        if(currentDisplay === 'none') return; // لا تظهر ما تم إخفاؤه بالفلترة

        card.style.display = text.includes(val) ? "block" : "none";
      });
    });

</script>
</body>
</html>
<?php $conn->close(); ?>
