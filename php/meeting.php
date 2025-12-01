<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "config.php";


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$clientID = $_SESSION['user_id'];


$designers_query = "
SELECT d.designerID, u.name, d.specialty
FROM designer d 
JOIN user u ON u.userID = d.designerID
";
$designers_result = $conn->query($designers_query);
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8" />
  <title>DECORIA | Meeting</title>
  <link rel="stylesheet" href="../css/decoria.css" />
  <link rel="stylesheet" href="../css/meeting.css" />
</head>

<body>

<header class="site-header">
    <div class="container header-container">
      <div class="brand">
        <img src="../photo/Logo.png.png" class="logo">
      </div>
      <p class="welcome-text">Schedule Your Meeting</p>
      <div class="header-buttons">
        <button class="menu-toggle" id="openSidebar">☰</button>
      </div>
    </div>
</header>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <span class="close-btn" id="closeSidebar">&times;</span>
     <?php include("menu.php"); ?>
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
          Schedule a meeting with one of our designers via Zoom to discuss your project details.
        </p>

        <form class="meeting-form" method="POST" action="process_meeting.php">
          
          <label for="designer">Select Designer</label>
          <select id="designer" name="designerID" required>
            <option value="">Choose...</option>
            <?php 
            if ($designers_result && $designers_result->num_rows > 0) {
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
          <textarea id="notes" name="note" rows="4"></textarea>
          
          <!-- Payment Section -->
<div class="form-group">
    <label style="font-weight:bold;">Payment Method</label>

    <div style="margin-top:8px; display:flex; align-items:center; gap:15px;">

        <!-- Apple Pay option -->
        <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
            <input type="radio" name="paymentMethod" value="Apple Pay" required>
            <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_Pay_logo.svg" 
                 alt="Apple Pay" style="height:22px;">
        </label>

        <!-- Price -->
        <span style="font-size:16px; font-weight:bold; color:#3b4d3b;">
            Price: 350 SAR
        </span>

    </div>
</div>

          <button  class="form-buttonsbutton"type="submit">Book Meeting</button>
        </form>

      </div>
    </div>
</main>

<footer>
    <div class="footer-content">
      <p class="footer-text">© 2025 DECORIA — All rights reserved</p>
      <img src="../photo/darlfooter.jpeg" class="footer-image">
    </div>
</footer>

<script src="../js/sidebar.js"></script>
</body>
</html>
