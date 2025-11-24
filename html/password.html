<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// اتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "root", "decoria");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// نأخذ userID من الرابط
$userID = isset($_GET['id']) ? intval($_GET['id']) : 0;

$message = "";

// عند الضغط على زر تغيير الباسورد
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $current = $_POST['current'];
    $new = $_POST['new'];
    $confirm = $_POST['confirm'];

    // جلب الباسورد الحالي من قاعدة البيانات
    $query = $conn->query("SELECT password FROM user WHERE userID = $userID");

    if ($query->num_rows == 1) {
        $row = $query->fetch_assoc();
        $storedPassword = $row['password']; // md5 محفوظ

        // التحقق من الباسورد الحالي
        if (md5($current) !== $storedPassword) {
            $message = "<p style='color:red;'>❌ Current password is incorrect</p>";
        }
        // التحقق من المطابقة
        else if ($new !== $confirm) {
            $message = "<p style='color:red;'>❌ New passwords do not match</p>";
        }
        // تحديث
        else {
            $newHashed = md5($new);
            $update = $conn->query("UPDATE user SET password='$newHashed' WHERE userID=$userID");

            if ($update) {
                $message = "<p style='color:green;'>✅ Password updated successfully</p>";
            } else {
                $message = "<p style='color:red;'>❌ Error updating password</p>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DECORIA | Change Password</title>

  <link rel="stylesheet" href="../css/decoria.css" />
  <link rel="stylesheet" href="../css/designers.css" />
  <link rel="stylesheet" href="../css/settings.css" />
</head>
<body>

<!-- Header -->
<header class="site-header">
    <div class="container header-container">
      <div class="brand">
        <img src="../photo/Logo.png.png" alt="DECORIA Logo" class="logo">
      </div>
      <p class="welcome-text">Welcome to DECORIA</p>
      <div class="header-buttons">
        <button class="menu-toggle">☰</button>
      </div>
    </div>
</header>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <span class="close-btn" id="closeSidebar">&times;</span>
    <a href="home.html">Home</a>
    <a href="designers.php">Designers</a>
    <a href="booking.php">Booking</a>
    <a href="timeline.php">Timeline</a>
    <a href="meeting.php">Meeting</a>
    <a href="settings.php">Settings</a>
    <hr>
    <a href="login.php" class="logout">Logout</a>
</div>

<div id="overlay"></div>

<main class="container">
<h2 class="section-title">Change Password</h2>

<div class="settings-page">
  <div class="card">

    <!-- رسالة PHP -->
    <div id="message"><?php echo $message; ?></div>

    <!-- النموذج (مربوط بقاعدة البيانات) -->
    <form method="POST">

      <div class="account-info-item">
        <label>Current Password:</label>
        <input type="password" name="current" id="current-password" required>
      </div>

      <div class="account-info-item">
        <label>New Password:</label>
        <input type="password" name="new" id="new-password" required>
      </div>

      <div class="account-info-item">
        <label>Confirm New Password:</label>
        <input type="password" name="confirm" id="confirm-password" required>
      </div>

      <div id="password-criteria"></div>

      <button class="edit-info-btn" id="saveBtn" type="submit">
        Done
      </button>

    </form>

  </div>
</div>

</main>

<footer>
    <div class="footer-content">
      <p class="footer-text">
        © 2025 DECORIA — All rights reserved |
        <a href="terms.html">Terms & Conditions</a>
      </p>
      <img src="../photo/darlfooter.jpeg" class="footer-image">
    </div>
</footer>

<script src="../js/sidebar.js"></script>
<script src="../js/password.js"></script>

</body>
</html>
