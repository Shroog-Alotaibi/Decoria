<?php
require_once "../php/config.php"; 
check_login("Designer");

// Ensure bookingID is provided
if (!isset($_GET['bookingID'])) {
    die("No booking selected.");
}

$bookingID = intval($_GET['bookingID']);
$designerID = $_SESSION['userID'];

// Fetch booking + timeline info
$sql = "SELECT b.*, 
               bt.steps, 
               u.fullName AS clientName
        FROM booking b
        LEFT JOIN bookingtimeline bt ON bt.bookingID = b.bookingID
        LEFT JOIN user u ON u.userID = b.clientID
        WHERE b.bookingID = ? AND b.designerID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $bookingID, $designerID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Booking not found or doesn't belong to you.");
}

$data = $result->fetch_assoc();
$currentStep = $data["steps"] ?? "not_received";

// Map DB step → checkbox ID
$selected = [
    "received"     => "requestCheckbox",
    "in_progress"  => "progressCheckbox",
    "completed"    => "completedCheckbox",
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Designer Timeline - Decoria</title>

  <link rel="stylesheet" href="../css/decoria.css" />

  <style>
    /* (YOUR CSS EXACTLY AS PROVIDED — UNCHANGED) */
    .timeline-container {
      max-width: 800px;
      margin: 40px auto;
      padding: 0 20px;
    }
    .timeline-options {
      display: flex;
      justify-content: space-between;
      gap: 20px;
      margin: 30px 0;
    }
    .timeline-option { flex: 1; text-align: center; cursor: pointer; }
    .timeline-option input[type="checkbox"] { display: none; }
    .timeline-card {
      background: var(--card);
      border: 2px solid var(--border);
      border-radius: 12px;
      padding: 20px;
      transition: all 0.3s ease;
      box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }
    .timeline-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }
    .timeline-card.selected {
      border-color: var(--brand);
      background-color: rgba(59, 77, 59, 0.05);
    }
    .timeline-image {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 15px;
    }
    .timeline-title { font-weight: 700; color: var(--brand); margin-bottom: 10px; }
    .timeline-description { color: var(--muted); font-size: 14px; }

    .update-section { text-align: center; margin-top: 40px; }
    .update-btn {
      background: var(--brand);
      color: white;
      border: none;
      border-radius: 10px;
      padding: 12px 24px;
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }
    .update-btn:hover {
      background: var(--primary-btn-hover);
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.12);
    }
    .update-message {
      margin-top: 15px;
      padding: 12px 20px;
      background-color: rgba(59, 77, 59, 0.1);
      border-radius: 8px;
      color: var(--brand);
      font-weight: 600;
      display: none;
      animation: fadeIn 0.5s ease;
    }
    @keyframes fadeIn { from {opacity: 0;} to {opacity: 1;} }
  </style>
</head>

<body>

<!-- HEADER -->
<header class="site-header">
  <div class="container header-container">
    <div class="brand">
      <img src="../photo/Logo.png.png" alt="DECORIA Logo" class="logo">
    </div>
    <p class="welcome-text">Welcome to DECORIA</p>
    <div class="header-buttons"><button class="menu-toggle">☰</button></div>
  </div>
</header>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
  <span class="close-btn" id="closeSidebar">&times;</span>
  <a href="home.html">Home</a>
  <a href="designers.html">Designers</a>
  <a href="booking.html" class="active">Booking</a>
  <a href="profile.php">Profile</a>
  <a href="meeting.html">Meeting</a>
  <a href="settings.html">Settings</a>
  <hr>
  <a href="logout.php" class="logout">Logout</a>
</div>

<div id="overlay"></div>

<!-- MAIN CONTENT -->
<main class="container">
<div class="timeline-container">
  <h2 class="section-title">Designer Timeline</h2>
  <p>Update the status of your current project to keep clients informed.</p>

  <!-- TIMELINE OPTIONS -->
  <form id="timelineForm">
    <div class="timeline-options">

      <!-- RECEIVED -->
      <label class="timeline-option">
        <input type="checkbox" 
               name="status" value="received" 
               id="requestCheckbox"
               <?= ($currentStep == "received" ? "checked" : "") ?>>
        <div class="timeline-card <?= ($currentStep == "received" ? "selected" : "") ?>">
          <img src="../photo/request-received.png" class="timeline-image">
          <h3 class="timeline-title">Got the Request</h3>
          <p class="timeline-description">Initial request received and under review</p>
        </div>
      </label>

      <!-- IN PROGRESS -->
      <label class="timeline-option">
        <input type="checkbox" 
               name="status" value="in_progress" 
               id="progressCheckbox"
               <?= ($currentStep == "in_progress" ? "checked" : "") ?>>
        <div class="timeline-card <?= ($currentStep == "in_progress" ? "selected" : "") ?>">
          <img src="../photo/InProgress.png" class="timeline-image">
          <h3 class="timeline-title">In Progress</h3>
          <p class="timeline-description">Currently working on the project</p>
        </div>
      </label>

      <!-- COMPLETED -->
      <label class="timeline-option">
        <input type="checkbox" 
               name="status" value="completed" 
               id="completedCheckbox"
               <?= ($currentStep == "completed" ? "checked" : "") ?>>
        <div class="timeline-card <?= ($currentStep == "completed" ? "selected" : "") ?>">
          <img src="../photo/completed.png" class="timeline-image">
          <h3 class="timeline-title">Completed</h3>
          <p class="timeline-description">Project finished and delivered</p>
        </div>
      </label>

    </div>

    <div class="update-section">
      <button type="button" class="update-btn" id="updateBtn">Update Status</button>
      <div class="update-message" id="updateMessage">Status updated successfully!</div>
    </div>

  </form>
</div>
</main>

<footer>
  <div class="footer-content">
    <p class="footer-text">© 2025 DECORIA — All rights reserved</p>
    <img src="../photo/darlfooter.jpeg" class="footer-image">
  </div>
</footer>

<script src="../js/sidebar.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  
  const checkboxes = document.querySelectorAll('input[name="status"]');
  checkboxes.forEach(cb => {
    cb.addEventListener('change', function () {
      if (this.checked) {
        checkboxes.forEach(other => {
          if (other !== this) {
            other.checked = false;
            other.parentNode.querySelector(".timeline-card").classList.remove("selected");
          }
        });
        this.parentNode.querySelector(".timeline-card").classList.add("selected");
      }
    });
  });

  // AJAX update
  document.getElementById("updateBtn").addEventListener("click", () => {
    const selected = document.querySelector('input[name="status"]:checked');
    if (!selected) return;

    fetch("updateTimeline.php", {
      method: "POST",
      headers: {"Content-Type": "application/x-www-form-urlencoded"},
      body: `bookingID=<?= $bookingID ?>&status=${selected.value}`
    })
    .then(res => res.text())
    .then(() => {
      document.getElementById("updateMessage").style.display = "block";
      setTimeout(() => {
        document.getElementById("updateMessage").style.display = "none";
      }, 2000);
    });
  });

});
</script>

</body>
</html>
