<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "config.php";   
check_login('Customer');

$clientID = $_SESSION['user_id'];


$current_sql = "
SELECT 
    b.*,
    u.name AS designerName,
    d.profilePicture,
    ds.image AS designImage,
    ds.title AS designTitle,
    ds.description AS designDescription
FROM booking b
JOIN designer d ON d.designerID = b.designerID
JOIN user u ON u.userID = b.designerID
JOIN design ds ON ds.designID = b.designID
WHERE b.clientID = $clientID
AND (b.status = 'In Progress' OR b.status = 'Request')
ORDER BY b.date DESC;
";

$current_result = mysqli_query($conn, $current_sql);



$past_sql = "
SELECT 
    b.*,
    u.name AS designerName,
    d.profilePicture,
    ds.image AS designImage,
    ds.title AS designTitle,
    ds.description AS designDescription
FROM booking b
JOIN designer d ON d.designerID = b.designerID
JOIN user u ON u.userID = b.designerID
JOIN design ds ON ds.designID = b.designID
WHERE b.clientID = $clientID
AND b.status = 'Completed'
ORDER BY b.date DESC;
";

$past_result = mysqli_query($conn, $past_sql);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DECORIA — My Booking</title>

  <link rel="stylesheet" href="../css/decoria.css">
  <link rel="stylesheet" href="../css/mybookings.css">

  <style>
    .btn-timeline {
      display: inline-block;
      margin-top: 10px;
      padding: 8px 16px;
      background: var(--brand);
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 600;
      transition: 0.2s;
    }

    .btn-timeline:hover {
      background: var(--primary-btn-hover);
      transform: translateY(-2px);
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
        <button class="menu-toggle" aria-label="Open menu">☰</button>
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

    <h2 class="section-title">My Booking</h2>

    <!-- Tabs -->
    <nav class="tabs-center" role="tablist">
      <span class="tab-link is-active" data-tab="current">Current Order</span>
      <span class="tab-link" data-tab="past">Past Order</span>
    </nav>

    
    <h3 class="orders-heading">Current Orders</h3>
    <section id="currentSection" class="orders">

      <?php if (mysqli_num_rows($current_result) == 0): ?>
        <p>No current orders.</p>
      <?php endif; ?>

      <?php while ($row = mysqli_fetch_assoc($current_result)): ?>

        <div class="order-card">

          <div class="order-thumb">
            <img src="../<?= $row['designImage'] ?>" alt="">
          </div>

          <div class="order-body">

            <div class="order-top">
              <h3><?= $row['designTitle'] ?></h3>
              <span class="badge badge-current">Current</span>
            </div>

            <p><?= $row['designDescription'] ?></p>

            <p><strong>Designer:</strong> <?= $row['designerName'] ?></p>
            <p><strong>Date:</strong> <?= $row['date'] ?></p>

            <?php if ($row['status'] == 'Request'): ?>
              <div class="cancel-wrapper">
                <button class="btn-cancel-small" data-id="<?= $row['bookingID'] ?>">Cancel</button>
              </div>
            <?php endif; ?>

            
            <div class="timeline-btn-wrapper">
              <a href="timeline.php?bookingID=<?= $row['bookingID'] ?>" class="btn-timeline">
                View Timeline
              </a>
            </div>

          </div>
        </div>

      <?php endwhile; ?>

    </section>


    
    <h3 class="orders-heading is-hidden" id="pastHeading">Past Orders</h3>
    <section id="pastSection" class="orders is-hidden">

      <?php if (mysqli_num_rows($past_result) == 0): ?>
        <p>No past orders.</p>
      <?php endif; ?>

      <?php while ($row = mysqli_fetch_assoc($past_result)): ?>

        <div class="order-card">

          <div class="order-thumb">
            <img src="../<?= $row['designImage'] ?>" alt="">
          </div>

          <div class="order-body">

            <div class="order-top">
              <h3><?= $row['designTitle'] ?></h3>
              <span class="badge badge-history">Past</span>
            </div>

            <p><?= $row['designDescription'] ?></p>

            <p><strong>Designer:</strong> <?= $row['designerName'] ?></p>
            <p><strong>Date:</strong> <?= $row['date'] ?></p>


          </div>
        </div>

      <?php endwhile; ?>

    </section>

  </main>


  <footer>
    <div class="footer-content">
      <p class="footer-text">© 2025 DECORIA — All rights reserved | <a href="terms.php">Terms & Conditions</a></p>
      <img src="../photo/darlfooter.jpeg" alt="" class="footer-image">
    </div>
  </footer>

  <script src="../js/sidebar.js"></script>


  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const tabs = document.querySelectorAll(".tab-link");
      const currentH = document.querySelector(".orders-heading");
      const pastH = document.getElementById("pastHeading");
      const currentS = document.getElementById("currentSection");
      const pastS = document.getElementById("pastSection");

      tabs.forEach(tab => {
        tab.addEventListener("click", () => {

          tabs.forEach(t => t.classList.remove("is-active"));
          tab.classList.add("is-active");

          if (tab.dataset.tab === "current") {

            currentH.classList.remove("is-hidden");
            currentS.classList.remove("is-hidden");

            pastH.classList.add("is-hidden");
            pastS.classList.add("is-hidden");

          } else {

            currentH.classList.add("is-hidden");
            currentS.classList.add("is-hidden");

            pastH.classList.remove("is-hidden");
            pastS.classList.remove("is-hidden");
          }
        });
      });
    });
  </script>


  <script>
    document.addEventListener("DOMContentLoaded", () => {

      document.querySelectorAll(".btn-cancel-small").forEach(btn => {
        btn.addEventListener("click", function() {

          if (!confirm("Are you sure you want to cancel this booking?")) return;

          let bookingID = this.dataset.id;
          let card = this.closest(".order-card");

          fetch("cancel_booking.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "bookingID=" + bookingID
          })
          .then(res => res.text())
          .then(data => {
            if (data.trim() === "success") {
              card.style.opacity = "0";
              setTimeout(() => card.remove(), 300);
            } else {
              alert("Error deleting booking.");
            }
          });
        });
      });

    });
  </script>

</body>
</html>
