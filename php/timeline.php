<?php
require_once "../php/config.php";
check_login("Client");

// Ensure booking ID exists
if (!isset($_GET['bookingID'])) {
    die("No booking selected.");
}

$bookingID = intval($_GET['bookingID']);
$clientID = $_SESSION['userID'];

// Fetch booking info + designer info + timeline status
$sql = "SELECT b.*, 
               bt.steps,
               d.fullName AS designerName,
               d.specialty AS designerSpecialty,
               d.bio AS designerBio,
               d.profilePic AS designerPic
        FROM booking b
        LEFT JOIN bookingtimeline bt ON bt.bookingID = b.bookingID
        LEFT JOIN user d ON d.userID = b.designerID
        WHERE b.bookingID = ? AND b.clientID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $bookingID, $clientID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Booking not found or you are not authorized to view this timeline.");
}

$data = $result->fetch_assoc();

// Designer Info
$designerName = $data["designerName"];
$designerSpecialty = $data["designerSpecialty"];
$designerBio = $data["designerBio"];
$designerPic = !empty($data["designerPic"]) ? "../" . $data["designerPic"] : "../photo/placeholder.png";

// Timeline Step
$step = $data["steps"] ?? "not_received";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Project Timeline - Decoria</title>

  <link rel="stylesheet" href="../css/decoria.css" />
<style>
/* YOUR CSS EXACTLY AS PROVIDED — UNCHANGED */
.timeline-container {
  max-width: 800px;
  margin: 40px auto;
  padding: 0 20px;
}
.project-info {
  background: var(--card);
  border-radius: 12px;
  padding: 25px;
  margin-bottom: 30px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.08);
  border: 1px solid var(--border);
}
.project-title {
  font-family: 'Playfair Display', serif;
  color: var(--brand);
  margin-bottom: 10px;
}
.designer-info {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 20px;
}
.designer-avatar {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid var(--border);
}
.designer-name {
  font-weight: 700;
  color: var(--brand);
}
.designer-specialty {
  color: var(--muted);
  font-size: 14px;
}

.status-section { text-align: center; margin: 40px 0; }
.status-card {
  background: var(--card);
  border-radius: 12px;
  padding: 30px;
  max-width: 500px;
  margin: 0 auto;
  box-shadow: 0 3px 10px rgba(0,0,0,0.08);
  border: 2px solid var(--brand);
}
.status-image {
  width: 100%;
  max-width: 300px;
  height: 200px;
  object-fit: cover;
  border-radius: 8px;
  margin: 0 auto 20px;
  display: block;
}
.status-title {
  font-weight: 700;
  color: var(--brand);
  margin-bottom: 10px;
  font-size: 22px;
}
.status-description { color: var(--muted); margin-bottom: 20px; }
.status-indicator {
  display: inline-block;
  background: rgba(59, 77, 59, 0.1);
  color: var(--brand);
  padding: 8px 16px;
  border-radius: 20px;
  font-weight: 600;
  font-size: 14px;
}

.timeline-progress {
  display: flex;
  justify-content: space-between;
  position: relative;
  margin: 40px 0;
  max-width: 700px;
  margin-left: auto;
  margin-right: auto;
}
.timeline-progress::before {
  content: '';
  position: absolute;
  top: 15px;
  left: 0;
  right: 0;
  height: 4px;
  background: var(--border);
  z-index: 1;
}
.progress-step {
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
  z-index: 2;
}
.step-circle {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: var(--card);
  border: 3px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 8px;
}
.step-circle.active {
  border-color: var(--brand);
  background: var(--brand);
  color: white;
}
.step-label {
  font-size: 14px;
  color: var(--muted);
  text-align: center;
  max-width: 100px;
}
.step-label.active {
  color: var(--brand);
  font-weight: 600;
}
.next-steps {
  background: rgba(59, 77, 59, 0.05);
  border-radius: 12px;
  padding: 20px;
  margin-top: 30px;
}
.next-steps-title {
  font-weight: 700;
  color: var(--brand);
  margin-bottom: 10px;
}
.next-steps-list { padding-left: 20px; }
.next-steps-list li {
  margin-bottom: 8px;
  color: var(--muted);
}
.contact-support {
  text-align: center;
  margin-top: 30px;
  font-size: 14px;
  color: var(--muted);
}
.contact-link {
  color: var(--brand);
  text-decoration: none;
  font-weight: 600;
}
.contact-link:hover { text-decoration: underline; }
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
  <a href="home.php">Home</a>
  <a href="designers.php">Designers</a>
  <a href="booking.php">Booking</a>
  <a href="myBookings.php" class="active">Timeline</a>
  <a href="meeting.php">Meeting</a>
  <a href="settings.php">Settings</a>
  <hr>
  <a href="../php/logout.php" class="logout">Logout</a>
</div>

<div id="overlay"></div>

<!-- MAIN CONTENT -->
<main class="container">
  <div class="timeline-container">

    <h2 class="section-title">Project Timeline</h2>

    <!-- DESIGNER INFO -->
    <div class="project-info">
  <h3 class="project-title"> Designer Info :</h3>
  
  <div class="designer-info">
    <img src="<?= $designerPic ?>" alt="Designer Avatar" class="designer-avatar">
    <div>
      <div class="designer-name"><?= htmlspecialchars($designerName) ?></div>
      <div class="designer-specialty"><?= htmlspecialchars($designerSpecialty) ?></div>
    </div>
  </div>
  
  <p><strong>Last Update:</strong>
      <?= date("F j, Y, g:i a", strtotime($data['lastUpdate'])) ?>
  </p>
</div>



    <!-- STATUS DISPLAY BASED ON THE TIMELINE STEP -->
    <div class="status-section">
      <div class="status-card">
        <?php if ($step === "not_received"): ?>
            <img src="../photo/not-received.png" class="status-image">
            <h3 class="status-title">Request Not Received Yet</h3>
            <p class="status-description">Your designer has not received your request yet.</p>

        <?php elseif ($step === "received"): ?>
            <img src="../photo/request-received.png" class="status-image">
            <h3 class="status-title">Request Received</h3>
            <p class="status-description">Your designer received your request and is reviewing it.</p>

        <?php elseif ($step === "in_progress"): ?>
            <img src="../photo/InProgress.png" class="status-image">
            <h3 class="status-title">In Progress</h3>
            <p class="status-description">Your designer is working on your project.</p>

        <?php elseif ($step === "completed"): ?>
            <img src="../photo/completed.png" class="status-image">
            <h3 class="status-title">Completed</h3>
            <p class="status-description">Your project has been fully completed!</p>
        <?php endif; ?>

        <div class="status-indicator">Current Status</div>
      </div>
    </div>

    <!-- VISUAL TIMELINE -->
    <div class="timeline-progress">
      <div class="progress-step">
        <div class="step-circle <?= $step === 'not_received' ? 'active' : '' ?>">1</div>
        <div class="step-label <?= $step === 'not_received' ? 'active' : '' ?>">Request Not Received Yet</div>
      </div>
      
      <div class="progress-step">
        <div class="step-circle <?= $step === 'received' ? 'active' : '' ?>">2</div>
        <div class="step-label <?= $step === 'received' ? 'active' : '' ?>">Request Received</div>
      </div>

      <div class="progress-step">
        <div class="step-circle <?= $step === 'in_progress' ? 'active' : '' ?>">3</div>
        <div class="step-label <?= $step === 'in_progress' ? 'active' : '' ?>">In Progress</div>
      </div>

      <div class="progress-step">
        <div class="step-circle <?= $step === 'completed' ? 'active' : '' ?>">4</div>
        <div class="step-label <?= $step === 'completed' ? 'active' : '' ?>">Completed</div>
      </div>
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
