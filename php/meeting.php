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

// التحقق من تسجيل دخول العميل
check_login('Customer'); 

// جلب قائمة المصممين لملء القائمة المنسدلة
$designers_query = "SELECT designerID, name, specialty FROM designer JOIN user ON designerID = userID";
$designers_result = $conn->query($designers_query);
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DECORIA | Meeting</title>

  <link rel="stylesheet" href="../css/decoria.css" />
  <link rel="stylesheet" href="../css/meeting.css" />
</head>
<body>

  <header class="site-header">
    <div class="container header-container">
      <div class="brand">
        <img src="../photo/Logo.png.png" alt="DECORIA Logo" class="logo">
      </div>

      <p class="welcome-text">Schedule Your Meeting</p>

      <div class="header-buttons">
        <button class="menu-toggle" id="openSidebar">☰</button>
      </div>
    </div>
  </header>

 <div class="sidebar" id="sidebar">
    <span class="close-btn" id="closeSidebar">&times;</span>
    <a href="home.html">Home</a>
    <a href="designers.html">Designers</a>
    <a href="booking.html" class="active">Booking</a>
    <a href="timeline.html">Timeline</a>
    <a href="meeting.html">Meeting</a>
    <a href="settings.html">Settings</a>
    <hr>
    <a href="login.html" class="logout">Logout</a>
  </div>

  <div id="overlay"></div>

  <main class="container">
    <h2 class="section-title">Online Meeting</h2>

    <div class="meeting-container">
      <div class="meeting-image">
        <img src="../photo/zoom.png" alt="Zoom Meeting">
      </div>

      <div class="meeting-content">
        <p class="meeting-description">
          Schedule a meeting with one of our designers via Zoom to discuss your project details, share ideas, and ensure your vision is perfectly understood.
        </p>

        <form class="meeting-form" method="POST" action="process_meeting.php">
          
          <label for="designer">Select Designer</label>
          <select id="designer" name="designerID" required>
            <option value="">Choose...</option>
            <?php 
            if ($designers_result->num_rows > 0) {
                while($row = $designers_result->fetch_assoc()) {
                    echo "<option value='{$row['designerID']}'>{$row['name']} ({$row['specialty']})</option>";
                }
            }
            ?>
          </select>

          <label for="date">Meeting Date</label>
          <input type="date" id="date" name="date" required>

          <label for="time">Meeting Time</label>
          <input type="time" id="time" name="time" required>

          <label for="notes">Additional Notes</label>
          <textarea id="notes" name="note" rows="4" placeholder="Add any specific topics you’d like to discuss..."></textarea>

          <div class="form-buttons">
            <button type="submit">Book Meeting</button>
          </div>
        </form>
        <div id="meetingInfo" class="meeting-message" style="display:none;">
          <p><strong>Name:</strong> <span id="meetingName"></span></p>
          <p><strong>Date:</strong> <span id="meetingDate"></span></p>
          <p><strong>Time:</strong> <span id="meetingTime"></span></p>
          <p><strong>Zoom Link:</strong> <a href="#" target="_blank" id="zoomLink">Join Meeting</a></p>
          <div class="btn-row">
            <button type="submit">Edit</button>
            <button type="submit">Cancel</button>
          </div>
        </div>
      </div>
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

  <script src="../js/sidebar.js"></script>
  <script src="../js/designerInfo.js"></script>
  <script src="../js/meeting.js"></script>
</body>
</html>
<?php $conn->close(); ?>
