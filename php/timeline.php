<?php
require_once "config.php";
session_start();


check_login('Customer');

$clientID = $_SESSION['user_id'];

$bookingID = intval($_GET['bookingID']);


$sql = "SELECT 
            b.bookingID,
            b.designerID,
            b.clientID,
            bt.steps,
            bt.lastUpdate,
            u.name AS designerName,
            d.specialty,
            d.profilePicture
        FROM booking b
        JOIN bookingtimeline bt ON bt.bookingID = b.bookingID
        JOIN user u ON u.userID = b.designerID
        JOIN designer d ON d.designerID = b.designerID
        WHERE b.bookingID = ? AND b.clientID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $bookingID, $clientID);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Booking not found or unauthorized.");
}

$step = $data['steps'];

$stepOrder = [
    "not_received" => 1,
    "request_received" => 2,
    "in_progress" => 3,
    "completed" => 4
];

$stepIndex = $stepOrder[$step];

$images = [
    "not_received" => "../photo/not-received.png",
    "request_received" => "../photo/request-received.png",
    "in_progress" => "../photo/InProgress.png",
    "completed" => "../photo/completed.png"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Project Timeline - Decoria</title>

  <link rel="stylesheet" href="../css/decoria.css" />

  <style>
    
    .timeline-container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
    .project-info { background: var(--card); border-radius: 12px; padding: 25px; margin-bottom: 30px; box-shadow: 0 3px 10px rgba(0,0,0,0.08); border: 1px solid var(--border); }
    .designer-info { display: flex; align-items: center; gap: 15px; margin-bottom: 20px; }
    .designer-avatar { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border); }
    .status-section { text-align: center; margin: 40px 0; }
    .status-card { background: var(--card); border-radius: 12px; padding: 30px; max-width: 500px; margin: 0 auto; border: 2px solid var(--brand); }
    .timeline-progress { display: flex; justify-content: space-between; position: relative; margin: 40px auto; max-width: 700px; }
    .timeline-progress::before { content: ''; position: absolute; top: 15px; left: 0; right: 0; height: 4px; background: var(--border); }
    .progress-step { display: flex; flex-direction: column; align-items: center; }
    .step-circle { width: 34px; height: 34px; border-radius: 50%; border: 3px solid var(--border); display: flex; align-items: center; justify-content: center; }
    .step-circle.active { background: var(--brand); color: white; border-color: var(--brand); }

    
    #reviewBtn {
      background: var(--brand);
      color: white;
      border: none;
      border-radius: 10px;
      padding: 12px 20px;
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
      display: block;
      margin: 20px auto 0;
    }
    #reviewBtn:hover { background: var(--primary-btn-hover); }

    
    .popup-overlay {
      position: fixed; inset: 0; background: rgba(0,0,0,0.5);
      display: none; align-items: center; justify-content: center;
      z-index: 9999;
    }
    .popup-overlay.active { display: flex; }
    .popup {
      background: white; width: 90%; max-width: 450px;
      padding: 20px; border-radius: 12px;
    }
    .popup h3 { margin-bottom: 15px; color: var(--brand); text-align: center; }
    .form-group { margin-bottom: 15px; }
    .form-group input, .form-group textarea {
      width: 100%; padding: 10px; border-radius: 8px;
      border: 1px solid var(--border);
    }
    .popup-actions { display: flex; justify-content: flex-end; gap: 10px; }
    .btn { padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; }
    .btn-cancel { background: #ddd; }
    .btn-submit { background: var(--brand); color: white; }
    
    
.status-image {
  width: 100%;
  max-width: 260px; 
  height: 180px;    
  object-fit: contain; 
  display: block;
  margin: 0 auto 20px;
}

  </style>
</head>

<body>

<!-- HEADER -->
<header class="site-header">
  <div class="container header-container">
    <div class="brand"><img src="../photo/Logo.png.png" class="logo"></div>
    <p class="welcome-text">Welcome to DECORIA</p>
    <div class="header-buttons"><button class="menu-toggle">☰</button></div>
  </div>
</header>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
  <span class="close-btn" id="closeSidebar">&times;</span>
  <?php include "menu.php"; ?>
</div>
<div id="overlay"></div>

<!-- MAIN -->
<main class="container">

  <div class="timeline-container">

    <h2 class="section-title">Project Timeline</h2>

    
    <div class="project-info">
      <h3 class="project-title">Designer Info :</h3>

      <div class="designer-info">
        <img src="<?php echo '../' . htmlspecialchars($data['profilePicture']); ?>" class="designer-avatar">
        <div>
          <div class="designer-name"><?= $data['designerName'] ?></div>
          <div class="designer-specialty"><?= $data['specialty'] ?></div>
        </div>
      </div>

      <p><strong>Booking ID:</strong> #<?= $data['bookingID'] ?></p>
      <p><strong>Last Update:</strong> <?= date("F j, Y, g:i a", strtotime($data['lastUpdate'])) ?></p>
    </div>

    
    <div class="status-section">
      <div class="status-card">
        <img src="<?= $images[$step] ?>" class="status-image">
        <h3 class="status-title"><?= ucwords(str_replace("_", " ", $step)) ?></h3>
        <p class="status-description">Your designer is currently working through this stage.</p>
        <div class="status-indicator">Current Status</div>
      </div>
    </div>

    
    <div class="timeline-progress">
      <?php 
      $labels = ["Request Not Received Yet","Request Received","In Progress","Completed"];
      for ($i = 1; $i <= 4; $i++):
        $active = ($i <= $stepIndex) ? "active" : "";
      ?>
      <div class="progress-step">
        <div class="step-circle <?= $active ?>"><?= $i ?></div>
        <div class="step-label <?= $active ?>"><?= $labels[$i-1] ?></div>
      </div>
      <?php endfor; ?>
    </div>

    
    <?php if ($step === "completed"): ?>
      <button id="reviewBtn">Leave a Review</button>
    <?php endif; ?>

  </div>
</main>


<div class="popup-overlay" id="reviewPopup">
  <div class="popup">
    <h3>Leave a Review</h3>

    <input type="hidden" id="reviewBookingID" value="<?= $bookingID ?>">
    <input type="hidden" id="reviewDesignerID" value="<?= $data['designerID'] ?>">

    <div class="form-group">
      <label>Rating (1–5)</label>
      <input type="number" id="reviewRating" min="1" max="5">
    </div>

    <div class="form-group">
      <label>Your Review</label>
      <textarea id="reviewComment" rows="4"></textarea>
    </div>

    <div class="popup-actions">
      <button class="btn btn-cancel" id="cancelReview">Cancel</button>
      <button class="btn btn-submit" id="submitReview">Submit</button>
    </div>
  </div>
</div>

<footer>
  <div class="footer-content">
    <p class="footer-text">© 2025 DECORIA — All rights reserved | <a href="terms.php">Terms & Conditions</a></p>
    <img src="../photo/darlfooter.jpeg" class="footer-image">
  </div>
</footer>

<script src="../js/sidebar.js"></script>

<script>

document.getElementById("reviewBtn")?.addEventListener("click", () => {
    document.getElementById("reviewPopup").classList.add("active");
});


document.getElementById("cancelReview").addEventListener("click", () => {
    document.getElementById("reviewPopup").classList.remove("active");
});


document.getElementById("submitReview").addEventListener("click", () => {
    const rating = document.getElementById("reviewRating").value;
    const comment = document.getElementById("reviewComment").value;
    const designerID = document.getElementById("reviewDesignerID").value;
    const bookingID = document.getElementById("reviewBookingID").value;

    if (!rating || !comment) {
        alert("Please fill all fields.");
        return;
    }

    const formData = new FormData();
    formData.append("rating", rating);
    formData.append("comment", comment);
    formData.append("designerID", designerID);
    formData.append("bookingID", bookingID);

    fetch("submitReview.php", {
        method: "POST",
        body: formData
    })
    .then(r => r.text())
    .then(d => {
        if (d.trim() === "success") {
            alert("Review submitted successfully!");
            document.getElementById("reviewPopup").classList.remove("active");
        } else {
            alert("Error submitting review.");
        }
    });
});
</script>

</body>
</html>
