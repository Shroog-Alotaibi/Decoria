<?php
require_once "../php/config.php";
check_login('Customer');

// bookingID must come from mybookings.php as a GET parameter
if (!isset($_GET['bookingID'])) {
    die("Booking not found.");
}

$bookingID  = intval($_GET['bookingID']);
$currentUser = $_SESSION['userID'];

// ------------------------------
// Fetch booking + designer info
// ------------------------------
$sql = "
    SELECT 
        b.*, 
        u_designer.name AS designerName,
        d.specialty      AS designerSpecialty,
        d.profilePicture AS designerProfile
    FROM booking b
    JOIN designer d 
        ON b.designerID = d.designerID
    JOIN user u_designer
        ON u_designer.userID = b.designerID
    WHERE 
        b.bookingID = ? 
        AND b.clientID = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $bookingID, $currentUser);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    // Either booking doesn't exist or doesn't belong to this customer
    die("Unauthorized or invalid booking.");
}

// ---------------------------------
// Fetch latest timeline step
// ---------------------------------
$sql2 = "
    SELECT steps 
    FROM bookingtimeline 
    WHERE bookingID = ?
    ORDER BY timelineID DESC
    LIMIT 1
";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("i", $bookingID);
$stmt2->execute();
$rowTimeline = $stmt2->get_result()->fetch_assoc();

// Default step if nothing yet in DB
$currentStep = $rowTimeline ? $rowTimeline['steps'] : "not_received";

// Map step → image
$stepImages = [
    "not_received" => "../photo/not-received.png",
    "received"     => "../photo/request-received.png",
    "in_progress"  => "../photo/InProgress.png",
    "completed"    => "../photo/completed.png"
];

$statusImage = isset($stepImages[$currentStep]) ? $stepImages[$currentStep] : $stepImages["not_received"];

// Map step → title + description
switch ($currentStep) {
    case "not_received":
        $statusTitle = "Request Not Received Yet";
        $statusDesc  = "Your designer has not yet received or confirmed your project request.";
        break;
    case "received":
        $statusTitle = "Request Received";
        $statusDesc  = "Your designer has received your project request and is currently reviewing it.";
        break;
    case "in_progress":
        $statusTitle = "In Progress";
        $statusDesc  = "Your designer is currently working on your project.";
        break;
    case "completed":
        $statusTitle = "Completed";
        $statusDesc  = "Your project has been completed and delivered.";
        break;
    default:
        $statusTitle = "Request Not Received Yet";
        $statusDesc  = "Your designer has not yet received or confirmed your project request.";
        $currentStep = "not_received";
}

// Progress bar active states
$step1Active = ($currentStep !== "not_received") ? "active" : ""; // step 1 only active AFTER request is received
$step2Active = ($currentStep === "received" || $currentStep === "in_progress" || $currentStep === "completed") ? "active" : "";
$step3Active = ($currentStep === "in_progress" || $currentStep === "completed") ? "active" : "";
$step4Active = ($currentStep === "completed") ? "active" : "";

// Designer picture (fallback)
$designerAvatar = !empty($booking['designerProfile']) ? $booking['designerProfile'] : "../photo/Logo.png.png";

// Booking date formatting
$rawDate = $booking['date'] ?? null;
$prettyDate = $rawDate ? date("F j, Y", strtotime($rawDate)) : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Project Timeline - Decoria</title>

  <!-- CSS links -->
  <link rel="stylesheet" href="../css/decoria.css" />
  <style>
    /* Timeline-specific styles */
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
    
    .status-section {
      text-align: center;
      margin: 40px 0;
    }
    
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
    
    .status-description {
      color: var(--muted);
      margin-bottom: 20px;
    }
    
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
    
    .next-steps-list {
      padding-left: 20px;
    }
    
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
    
    .contact-link:hover {
      text-decoration: underline;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
      .timeline-progress {
        flex-direction: column;
        gap: 25px;
        align-items: flex-start;
      }
      
      .timeline-progress::before {
        display: none;
      }
      
      .progress-step {
        flex-direction: row;
        gap: 15px;
      }
      
      .step-label {
        text-align: left;
        max-width: none;
      }
    }
  </style>
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


 <!-- Sidebar Menu -->
  <div class="sidebar" id="sidebar">
    <span class="close-btn" id="closeSidebar">&times;</span>
    <a href="home.html">Home</a>
    <a href="designers.html">Designers</a>
    <a href="booking.html" class="active">Booking</a>
    <a href="timeline.php">Timeline</a>
    <a href="meeting.html">Meeting</a>
    <a href="settings.html">Settings</a>
    <hr>
    <a href="login.html" class="logout">Logout</a>
  </div>

  <div id="overlay"></div>

  <!-- Main Content -->
  <main class="container">
    <div class="timeline-container">
      <h2 class="section-title">Project Timeline</h2>
      
      <div class="project-info">
        <h3 class="project-title"> Designer Info :</h3>
        
        <div class="designer-info">
          <img src="<?= htmlspecialchars($designerAvatar) ?>" alt="Designer Avatar" class="designer-avatar">
          <div>
            <div class="designer-name"><?= htmlspecialchars($booking['designerName']) ?></div>
            <div class="designer-specialty"><?= htmlspecialchars($booking['designerSpecialty']) ?></div>
          </div>
        </div>
        
        <p><strong>Booking ID:</strong> #<?= htmlspecialchars($bookingID) ?></p>
        <p><strong>Date:</strong> <?= htmlspecialchars($prettyDate) ?></p>
      </div>
      
      <div class="status-section">
        <div class="status-card">
          <img src="<?= htmlspecialchars($statusImage) ?>" alt="<?= htmlspecialchars($statusTitle) ?>" class="status-image">
          <h3 class="status-title"><?= htmlspecialchars($statusTitle) ?></h3>
          <p class="status-description"><?= htmlspecialchars($statusDesc) ?></p>
          <div class="status-indicator">Current Status</div>
        </div>
      </div>
      
      <div class="timeline-progress">
        <div class="progress-step">
          <div class="step-circle <?= $currentStep == 'not_received' ? 'active' : '' ?>">1</div>
          <div class="step-label <?= $currentStep == 'not_received' ? 'active' : '' ?>">Request Not Received Yet</div>
        </div>
        
        <div class="progress-step">
          <div class="step-circle <?= $step2Active ?>">2</div>
          <div class="step-label <?= $step2Active ?>">Request Received</div>
        </div>
        
        <div class="progress-step">
          <div class="step-circle <?= $step3Active ?>">3</div>
          <div class="step-label <?= $step3Active ?>">In Progress</div>
        </div>
        
        <div class="progress-step">
          <div class="step-circle <?= $step4Active ?>">4</div>
          <div class="step-label <?= $step4Active ?>">Completed</div>
        </div>
      </div>
      
      <div class="next-steps">
        <h4 class="next-steps-title">What to Expect Next:</h4>
        <ul class="next-steps-list">
          <li>Your designer will contact you within 1-2 business days to discuss your project in detail</li>
          <li>You'll receive an initial concept proposal and mood board</li>
          <li>Once approved, the designer will begin working on your project</li>
        </ul>
      </div>
      
      <div class="contact-support">
        <p>Have questions about your project? 
          <a href=malito:"random@hotmail.com" class="contact-link">Contact our support team</a>
        </p>
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

  <!-- JS -->
  <script src="../js/sidebar.js"></script>
</body>
</html>
