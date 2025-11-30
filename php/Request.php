<?php
// ===============================
// Error reporting
// ===============================
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ===============================
// Database connection (نفس طريقة code#1)
// ===============================
require_once "config.php";

// ===============================
// Session check (نفس code#1)
// ===============================

// لو ما فيه user_id → رجّعيه للّوق إن
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// التحقق من الدور (مصمم)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Designer') {
    header("Location: home.php");
    exit();
}

// مصمم مسجل دخوله حالياً
$designerID = $_SESSION['user_id'];

// ===============================
// 1. Meetings Requests
// ===============================
$meetings_query = "
    SELECT m.meetingID AS requestID, u.name AS clientName, u.email AS clientEmail,
           m.date, m.time, m.status, m.note, m.price, 'Meeting' AS type
    FROM meeting m
    JOIN user u ON m.clientID = u.userID
    WHERE m.designerID = '$designerID'
";
$meetings_result = $conn->query($meetings_query);

// ===============================
// 2. Booking Requests
// ===============================
$bookings_query = "
    SELECT b.bookingID AS requestID, u.name AS clientName, u.email AS clientEmail,
           b.date, b.time, b.status, b.price, b.receipt, 'Booking' AS type
    FROM booking b
    JOIN user u ON b.clientID = u.userID
    WHERE b.designerID = '$designerID'
";
$bookings_result = $conn->query($bookings_query);

// ===============================
// دمج النتائج
// ===============================
$requests = [];
while ($row = $meetings_result->fetch_assoc()) {
    $requests[] = $row;
}
while ($row = $bookings_result->fetch_assoc()) {
    $requests[] = $row;
}

// ترتيب حسب التاريخ والوقت
usort($requests, function($a, $b) {
    return strtotime($b['date'] . ' ' . $b['time']) - strtotime($a['date'] . ' ' . $a['time']);
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
    .status-pending { background-color: #ffeb3b; color: #333; }
    .status-in-progress { background-color: #2196f3; color: white; }
    .status-complete { background-color: #4caf50; color: white; }
    .status-payment-pending { background-color: #ff9800; color: white; }
    .details-popup {
      position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
      background: white; padding: 25px; border-radius: 10px; z-index: 1000;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3); display: none; width: 90%; max-width: 400px;
    }
  </style>
</head>

<body>

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

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <span class="close-btn" id="closeSidebar">&times;</span>
    <?php include("menu.php"); ?>
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
                $status_class = 'status-' . strtolower(str_replace(' ', '-', $req['status']));
                $details_json = htmlspecialchars(json_encode($req), ENT_QUOTES, 'UTF-8');
                ?>

                <div class="alert-card <?php echo $class_type; ?>">
                    <div class="alert-info">
                        <h4>
                            <?php echo $req['clientName']; ?> 
                            <span class="badge <?php echo $class_type; ?>"><?php echo $req['type']; ?></span>
                        </h4>
                        <p><strong>التاريخ:</strong> <?= $req['date'] ?></p>
                        <p><strong>الوقت:</strong> <?= $req['time'] ?></p>
                    </div>

                    <div class="alert-actions">
                        <span class="status-badge <?= $status_class ?>"><?= $req['status'] ?></span>

                        <button class="btn-details"
                                onclick='showDetailsPopup(<?= $details_json ?>)'>
                            Details
                        </button>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>

      </section>

      <!-- Booking Popup -->
      <div id="booking-details-popup" class="details-popup">
          <h3>Booking Details</h3>
          <p><strong>Client:</strong> <span id="clientName_b"></span></p>
          <p><strong>Email:</strong> <span id="clientEmail_b"></span></p>
          <p><strong>Date:</strong> <span id="date_b"></span></p>
          <p><strong>Time:</strong> <span id="time_b"></span></p>
          <p><strong>Price:</strong> <span id="price_b"></span> SAR</p>

          <p><strong>Transaction Photo:</strong> 
             <a href="#" target="_blank" id="receipt_b">View Photo</a></p>

          <p><strong>Status:</strong> <span id="status_b"></span></p>

          <div class="btn-row" style="justify-content: center;">
            <button class="primary-btn"
            onclick="updateStatus('Booking', document.getElementById('requestID_b').value, 'Confirmed')">
            Approve Payment
            </button>
          </div>

          <button class="btn-close" onclick="closeDetailsPopup('booking')">Close</button>
          <input type="hidden" id="requestID_b">
      </div>

      <!-- Meeting Popup -->
      <div id="meeting-details-popup" class="details-popup">
          <h3>Meeting Details</h3>
          <p><strong>Client:</strong> <span id="clientName_m"></span></p>
          <p><strong>Email:</strong> <span id="clientEmail_m"></span></p>
          <p><strong>Meeting Date:</strong> <span id="date_m"></span></p>
          <p><strong>Time:</strong> <span id="time_m"></span></p>
          <p><strong>Price:</strong> <span id="price_m"></span> SAR</p>
          <p><strong>Notes:</strong> <span id="note_m"></span></p>
          <p><strong>Status:</strong> <span id="status_m"></span></p>

          <div class="btn-row" style="justify-content: center;">
            <button class="primary-btn"
            onclick="updateStatus('Meeting', document.getElementById('requestID_m').value, 'Confirmed')">
            Confirm Meeting
            </button>
          </div>

          <button class="btn-close" onclick="closeDetailsPopup('meeting')">Close</button>
          <input type="hidden" id="requestID_m">
      </div>

</main>

<footer>
    <div class="footer-content">
      <p class="footer-text">
        © 2025 DECORIA — All rights reserved
      </p>
      <img src="../photo/darlfooter.jpeg" alt="DECORIA Footer Image" class="footer-image">
    </div>
</footer>

<script>
// ======================
// JS popups logic
// ======================
function closeDetailsPopup(type) {
    const popup = (type === 'booking')
        ? document.getElementById('booking-details-popup')
        : document.getElementById('meeting-details-popup');

    popup.style.display = 'none';
    document.getElementById('overlay').style.display = 'none';
}

function showDetailsPopup(data) {
    document.getElementById('overlay').style.display = 'block';

    if (data.type === 'Booking') {

        document.getElementById('clientName_b').textContent = data.clientName;
        document.getElementById('clientEmail_b').textContent = data.clientEmail;
        document.getElementById('date_b').textContent = data.date;
        document.getElementById('time_b').textContent = data.time;
        document.getElementById('price_b').textContent = data.price;
        document.getElementById('status_b').textContent = data.status;

        document.getElementById('receipt_b').href = `../transaction_uploads/${data.receipt}`;

        document.getElementById('requestID_b').value = data.requestID;
        document.getElementById('booking-details-popup').style.display = 'block';

    } else {

        document.getElementById('clientName_m').textContent = data.clientName;
        document.getElementById('clientEmail_m').textContent = data.clientEmail;
        document.getElementById('date_m').textContent = data.date;
        document.getElementById('time_m').textContent = data.time;
        document.getElementById('price_m').textContent = data.price;
        document.getElementById('note_m').textContent = data.note ?? 'لا توجد ملاحظات.';
        document.getElementById('status_m').textContent = data.status;
        document.getElementById('requestID_m').value = data.requestID;

        document.getElementById('meeting-details-popup').style.display = 'block';
    }
}

// ======================
// Placeholder UpdateStatus
// ======================
function updateStatus(type, id, newStatus) {
    alert(`محاكاة تغيير الحالة. يجب إضافة ملف update_status.php لاحقًا.`);
    location.reload();
}
</script>

</body>
</html>

<?php $conn->close(); ?>
