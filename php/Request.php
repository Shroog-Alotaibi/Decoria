<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Designer') {
    header("Location: home.php");
    exit();
}

$designerID = $_SESSION['user_id'];


$meetings_query = "
SELECT 
    m.meetingID AS requestID,
    u.name AS clientName,
    u.email AS clientEmail,
    m.date,
    m.time,
    m.status,
    m.note,
    m.price,
    'Meeting' AS type
FROM meeting m
JOIN user u ON m.clientID = u.userID
WHERE m.designerID = '$designerID'
";
$meetings_result = $conn->query($meetings_query);

$bookings_query = "
SELECT 
    b.bookingID AS requestID,
    u.name AS clientName,
    u.email AS clientEmail,
    b.date,
    b.time,
    b.status,
    b.price,
    b.receipt,
    'Booking' AS type
FROM booking b
JOIN user u ON b.clientID = u.userID
WHERE b.designerID = '$designerID'
";
$bookings_result = $conn->query($bookings_query);


$requests = [];

while ($row = $meetings_result->fetch_assoc()) $requests[] = $row;
while ($row = $bookings_result->fetch_assoc()) $requests[] = $row;

usort($requests, function($a, $b){
    return strtotime($b['date'].' '.$b['time']) - strtotime($a['date'].' '.$a['time']);
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>DECORIA | Requests</title>

<link rel="stylesheet" href="../css/decoria.css" />
<link rel="stylesheet" href="../css/designers.css" />
<link rel="stylesheet" href="../css/settings.css" />
<link rel="stylesheet" href="../css/Request.css" />

<style>
.details-popup{
    position:fixed; top:50%; left:50%; transform:translate(-50%,-50%);
    background:#fff; padding:25px; border-radius:10px;
    box-shadow:0 4px 20px rgba(0,0,0,.3);
    display:none; width:90%; max-width:430px; z-index:1000;
}
#overlay{
    position:fixed; inset:0; background:rgba(0,0,0,.45);
    display:none; z-index:900;
}
.badge.alert-meeting{background:#2196f3;color:white;}
.badge.alert-booking{background:#673ab7;color:white;}
</style>
</head>

<body>

<header class="site-header">
  <div class="container header-container">
    <img src="../photo/Logo.png.png" class="logo">
    <p class="welcome-text">Welcome, Designer</p>
    <button class="menu-toggle">☰</button>
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
    <p style="grid-column:1/-1;text-align:center;padding:50px;">
        no current requests.
    </p>
<?php else: ?>

<?php foreach($requests as $req): 
      $json = htmlspecialchars(json_encode($req), ENT_QUOTES, 'UTF-8');
      $class = ($req['type']==='Meeting') ? 'alert-meeting' : 'alert-booking';
?>
<div class="alert-card <?= $class ?>">
    <div class="alert-info">
        <h4>
            <?= $req['clientName'] ?>
            <span class="badge <?= $class ?>"><?= $req['type'] ?></span>
        </h4>
        <p><strong>التاريخ:</strong> <?= $req['date'] ?></p>
        <p><strong>الوقت:</strong> <?= $req['time'] ?></p>
    </div>

    <div class="alert-actions">
        <button class="btn-details" onclick="showDetails(<?= $json ?>)">Details</button>
    </div>
</div>
<?php endforeach; ?>

<?php endif; ?>

</section>

</main>

<!-- Popup: Booking -->
<div id="booking-popup" class="details-popup">
    <h3>Booking Details</h3>
    <p><b>Client:</b> <span id="b_name"></span></p>
    <p><b>Email:</b> <span id="b_email"></span></p>
    <p><b>Date:</b> <span id="b_date"></span></p>
    <p><b>Time:</b> <span id="b_time"></span></p>
    <p><b>Price:</b> <span id="b_price"></span> SAR</p>
    <p><b>Receipt:</b> <a id="b_receipt" target="_blank">View Receipt</a></p>

    <a id="timeline_btn" class="primary-btn" style="display:block;text-align:center;margin-top:15px;">
        Open Timeline
    </a>

    <button class="btn-close" onclick="closePopup()">Close</button>
</div>

<!-- Popup: Meeting -->
<div id="meeting-popup" class="details-popup">
    <h3>Meeting Details</h3>
    <p><b>Client:</b> <span id="m_name"></span></p>
    <p><b>Email:</b> <span id="m_email"></span></p>
    <p><b>Date:</b> <span id="m_date"></span></p>
    <p><b>Time:</b> <span id="m_time"></span></p>
    <p><b>Price:</b> <span id="m_price"></span> SAR</p>
    <p><b>Notes:</b> <span id="m_note"></span></p>

    <button class="btn-close" onclick="closePopup()">Close</button>
</div>

<script>
/***********************
 * SIDEBAR
 ***********************/
const sidebar = document.getElementById("sidebar");
const overlay = document.getElementById("overlay");

document.querySelector(".menu-toggle").onclick = ()=>{
    sidebar.classList.add("open");
    overlay.style.display="block";
};
document.getElementById("closeSidebar").onclick = closeSidebar;
overlay.onclick = closeSidebar;

function closeSidebar(){
    sidebar.classList.remove("open");
    overlay.style.display="none";
}

/***********************
 * POPUPS
 ***********************/
function closePopup(){
    document.getElementById("booking-popup").style.display="none";
    document.getElementById("meeting-popup").style.display="none";
    overlay.style.display="none";
}

function showDetails(data){
    overlay.style.display="block";

    if(data.type === "Booking"){
        document.getElementById("b_name").textContent = data.clientName;
        document.getElementById("b_email").textContent = data.clientEmail;
        document.getElementById("b_date").textContent = data.date;
        document.getElementById("b_time").textContent = data.time;
        document.getElementById("b_price").textContent = data.price;
        document.getElementById("b_receipt").href = "../transaction_uploads/" + data.receipt;

        document.getElementById("timeline_btn").href =
            "designer-timeline.php?bookingID=" + data.requestID;

        document.getElementById("booking-popup").style.display="block";
    }
    else{
        document.getElementById("m_name").textContent = data.clientName;
        document.getElementById("m_email").textContent = data.clientEmail;
        document.getElementById("m_date").textContent = data.date;
        document.getElementById("m_time").textContent = data.time;
        document.getElementById("m_price").textContent = data.price;
        document.getElementById("m_note").textContent = data.note ?? "No notes";

        document.getElementById("meeting-popup").style.display="block";
    }
}

/***********************
 * FILTER
 ***********************/
document.getElementById("filterType").onchange = function(){
    let type = this.value;
    document.querySelectorAll(".alert-card").forEach(card=>{
        card.style.display = (type === "all" || card.classList.contains(type))
            ? "block" : "none";
    });
};

/***********************
 * SEARCH
 ***********************/
document.getElementById("searchInput").oninput = function(){
    let text = this.value.toLowerCase();
    document.querySelectorAll(".alert-card").forEach(card=>{
        card.style.display =
            card.textContent.toLowerCase().includes(text)
            ? "block" : "none";
    });
};
</script>

</body>
</html>


