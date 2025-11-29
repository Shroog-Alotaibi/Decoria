<?php
require_once "../php/config.php";
check_login("Client");

// Validate bookingID
if (!isset($_GET['bookingID'])) {
    die("No booking selected.");
}

$bookingID = intval($_GET['bookingID']);
$clientID = $_SESSION['userID'];

// Fetch booking + timeline + designer info
$sql = "SELECT b.*, 
               bt.steps,
               u.fullName AS designerName,
               u.specialty AS designerSpecialty,
               u.photoFileName AS designerPhoto
        FROM booking b
        LEFT JOIN bookingtimeline bt ON bt.bookingID = b.bookingID
        LEFT JOIN user u ON u.userID = b.designerID
        WHERE b.bookingID = ? AND b.clientID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $bookingID, $clientID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Booking not found or not yours.");
}

$data = $result->fetch_assoc();
$currentStep = $data["steps"] ?? "not_received";

// Map step to display info
$stepImages = [
    "not_received" => "../photo/not-received.png",
    "received"     => "../photo/request-received.png",
    "in_progress"  => "../photo/InProgress.png",
    "completed"    => "../photo/completed.png"
];

$stepTitles = [
    "not_received" => "Request Not Received Yet",
    "received"     => "Request Received",
    "in_progress"  => "In Progress",
    "completed"    => "Completed"
];

$stepDescriptions = [
    "not_received" => "Your request has not been reviewed yet.",
    "received"     => "Your designer has received your project request and is reviewing it.",
    "in_progress"  => "Your designer is currently working on your project.",
    "completed"    => "Your project has been completed and delivered."
];

// Progress bar steps: set active
function activeCircle($stepName, $currentStep) {
    $order = ["not_received", "received", "in_progress", "completed"];
    return array_search($currentStep, $order) >= array_search($stepName, $order);
}
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

<header class="site-header">
  <div class="container header-container">
    <div class="brand">
      <img src="../photo/Logo.png.png" class="logo">
    </div>
    <p class="welcome-text">Welcome to DECORIA</p>
    <div class="header-buttons"><button class="menu-toggle">☰</button></div>
  </div>
</header>

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

<main class="container">
<div class="timeline-container">
  <h2 class="section-title">Project Timeline</h2>

  <!-- DESIGNER INFO -->
  <div class="project-info">
    <h3 class="project-title">Designer Info :</h3>

    <div class="designer-info">
      <img src="../photo/<?= $data['designerPhoto'] ?>" class="designer-avatar">
      <div>
        <div class="designer-name"><?= $data['designerName'] ?></div>
        <div class="designer-specialty"><?= $data['designerSpecialty'] ?></div>
      </div>
    </div>

    <p><strong>Booking ID:</strong> #<?= $bookingID ?></p>
    <p><strong>Date:</strong> <?= $data['bookingDate'] ?></p>
  </div>

  <!-- STATUS CARD -->
  <div class="status-section">
    <div class="status-card">
      <img src="<?= $stepImages[$currentStep] ?>" class="status-image">

      <h3 class="status-title"><?= $stepTitles[$currentStep] ?></h3>
      <p class="status-description"><?= $stepDescriptions[$currentStep] ?></p>

      <div class="status-indicator">Current Status</div>
    </div>
  </div>

  <!-- PROGRESS BAR -->
  <div class="timeline-progress">

    <div class="progress-step">
      <div class="step-circle <?= activeCircle("not_received", $currentStep) ? 'active' : '' ?>">1</div>
      <div class="step-label <?= activeCircle("not_received", $currentStep) ? 'active' : '' ?>">Request Not Received Yet</div>
    </div>

    <div class="progress-step">
      <div class="step-circle <?= activeCircle("received", $currentStep) ? 'active' : '' ?>">2</div>
      <div class="step-label <?= activeCircle("received", $currentStep) ? 'active' : '' ?>">Request Received</div>
    </div>

    <div class="progress-step">
      <div class="step-circle <?= activeCircle("in_progress", $currentStep) ? 'active' : '' ?>">3</div>
      <div class="step-label <?= activeCircle("in_progress", $currentStep) ? 'active' : '' ?>">In Progress</div>
    </div>

    <div class="progress-step">
      <div class="step-circle <?= activeCircle("completed", $currentStep) ? 'active' : '' ?>">4</div>
      <div class="step-label <?= activeCircle("completed", $currentStep) ? 'active' : '' ?>">Completed</div>
    </div>

  </div>

  <!-- NEXT STEPS -->
  <div class="next-steps">
    <h4 class="next-steps-title">What to Expect Next:</h4>
    <ul class="next-steps-list">
      <li>Your designer will contact you soon.</li>
      <li>You will receive updates automatically.</li>
      <li>Feel free to reach out if you have questions.</li>
    </ul>
  </div>

  <div class="contact-support">
    <p>Have questions? <a href="mailto:support@decoria.com" class="contact-link">Contact our support team</a></p>
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
