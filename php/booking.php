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

// جلب قائمة المصممين لملء القائمة المنسدلة
$designers_query = "SELECT designerID, name, specialty FROM designer JOIN user ON designerID = userID";
$designers_result = $conn->query($designers_query);
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DECORIA | Booking</title>

  <link rel="stylesheet" href="../css/decoria.css" />
<link rel="stylesheet" href="../css/booking.css" />
</head>
<body>
  <header class="site-header">
    <div class="container header-container">
      <div class="brand">
        <img src="../photo/Logo.png.png" alt="DECORIA Logo" class="logo">
      </div>

      <p class="welcome-text">Book Your Designer</p>

      <div class="header-buttons">
        <button class="menu-toggle" id="openSidebar">☰</button>
      </div>
    </div>
  </header>


  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <span class="close-btn" id="closeSidebar">&times;</span>
    <a href="home.html">Home</a>
    <a href="designers.php">Designers</a>
    <a href="booking.php" class="active">Booking</a>
    <a href="timeline.php">Timeline</a>
    <a href="meeting.php">Meeting</a>
    <a href="settings.php">Settings</a>
    <hr>
    <a href="login.php" class="logout">Logout</a>
  </div>

  <div id="overlay"></div>

  <main class="container">
    <h2 class="section-title">Book Your Designer</h2>

    <form class="booking-form" method="POST" action="process_booking.php" enctype="multipart/form-data">
      
      <label for="designer">Choose Designer:</label>
      <select id="designer" name="designerID" required>
        <option value="">Select a Designer</option>
        <?php 
        if ($designers_result->num_rows > 0) {
            while($row = $designers_result->fetch_assoc()) {
                echo "<option value='{$row['designerID']}'>{$row['name']} ({$row['specialty']})</option>";
            }
        }
        ?>
      </select>

       <label for="time">Choose design:</label>
       <select id="design" name="designID" required disabled>
       <option value="">Please select a designer first</option>
        </select>

      <label for="date">Choose Date:</label>
      <input type="date" id="date" name="date" required>

      <label for="time">Choose Time:</label>
      <input type="time" id="time" name="time" required>
        
      <label for="transactionPhoto">Upload Transaction Photo:</label>
      <input type="file" id="transactionPhoto" name="transactionPhoto" accept="image/*" required>
      <div class="btn-row">
  <button type="submit">Confirm Booking</button>
</div>
</form>

<div id="bookingDetails" class="booking-message" style="display:none;">
  <p>✅ Booking confirmed successfully!</p>
  <p><strong>Designer:</strong> <span id="detailName"></span></p>
  <p><strong>Design:</strong> <span id="detailDesign"></span></p>
  <p><strong>Date:</strong> <span id="detailDate"></span></p>
  <p><strong>Time:</strong> <span id="detailTime"></span></p>

  <div class="btn-row">
    <button type="submit">Edit</button>
    <button type="submit">Cancel</button>
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
  <script src="../js/booking.js"></script>
  
</body>

</html>
<?php $conn->close(); ?>
