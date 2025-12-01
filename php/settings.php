<?php
require_once "config.php";   

check_login();

if (
    !isset($_SESSION['role']) ||
    ($_SESSION['role'] !== 'Customer' && $_SESSION['role'] !== 'Designer')
) {
    redirect_to('home.php');
}

$user_id = intval($_SESSION['user_id']);

$user_query = "SELECT * FROM user WHERE userID = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

if (!$user) {
    die("User not found. Check userID.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Decoria | Settings</title>

  <link rel="stylesheet" href="../css/decoria.css">
  <link rel="stylesheet" href="../css/designers.css">
  <link rel="stylesheet" href="../css/settings.css">
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
    
    <h2 class="section-title">Settings</h2>

    <div class="settings-page">

        <section class="card">
            <h2>Personal Information</h2>

            <div class="account-info-item">
                <span>Name:</span>
                <span><?= htmlspecialchars($user['name']) ?></span>
            </div>

            <div class="account-info-item">
                <span>Email:</span>
                <span><?= htmlspecialchars($user['email']) ?></span>
            </div>

            <div class="account-info-item">
                <span>Phone Number:</span>
                <span>+966 <?= htmlspecialchars($user['phoneNumber']) ?></span>
            </div>

            <div class="account-info-item">
                <span>Date of Birth:</span>
                <span><?= htmlspecialchars($user['DOB']) ?></span>
            </div>

            <div class="account-info-item">
                <span>Gender:</span>
                <span><?= htmlspecialchars($user['gender']) ?></span>
            </div>

            <div class="account-info-item">
                <span>Address:</span>
                <span><?= htmlspecialchars($user['address']) ?></span>
            </div>
        </section>

        <section class="card">
            <h2>Personal Account Settings</h2>

            <div class="account-info-item">
                <span>Username:</span>
                <span><?= htmlspecialchars($user['name']) ?></span>
            </div>

            <div class="account-info-item">
                <span>Email:</span>
                <span><?= htmlspecialchars($user['email']) ?></span>
            </div>

            <div class="account-info-item">
                <span>Phone Number:</span>
                <span>+966 <?= htmlspecialchars($user['phoneNumber']) ?></span>
            </div>

            <div class="account-info-item">
                <span>Password:</span>
                <span>********</span>
            </div>

           <button class="edit-info-btn" onclick="window.location.href='settings-personal.php'">Edit</button>
        </section>

    </div> 

</main>

<footer>
  <div class="footer-content">
    <p class="footer-text">
      © 2025 DECORIA — All rights reserved |
      <a href="terms.php">Terms & Conditions</a>
    </p>
    <img src="../photo/darlfooter.jpeg" class="footer-image">
  </div>
</footer>

<script src="../js/sidebar.js"></script>

</body>
</html>
