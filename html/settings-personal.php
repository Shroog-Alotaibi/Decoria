<?php
session_start();
include("config.php");


if (!isset($_SESSION['user_id']) || 
   ($_SESSION['user_type'] !== 'client' && $_SESSION['user_type'] !== 'designer')) {

    header("Location: login.php?error=unauthorized");
    exit();
}


$user_id = intval($_SESSION['user_id']);

$user_query = "SELECT * FROM user WHERE userID = $user_id";
$user_result = mysqli_query($connection, $user_query);
$user = mysqli_fetch_assoc($user_result);

if (!$user) {
    die("User not found. Check userID.");
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <title>DECORIA | Personal Settings</title>

  <link rel="stylesheet" href="../css/decoria.css">
  <link rel="stylesheet" href="../css/designers.css">
  <link rel="stylesheet" href="../css/settings.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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

<main class="container">
<h2 class="section-title">Personal Settings</h2>

<!-- RESULT MESSAGE -->
<div id="messageBox"></div>

<main class="settings-page">
<section class="card">
<h2>Personal Account Settings</h2>

<div class="account-info-item">
    <label>Username:</label>
    <input id="name" value="<?= htmlspecialchars($user['name']) ?>">
</div>

<div class="account-info-item">
    <label>Email:</label>
    <input id="email" value="<?= htmlspecialchars($user['email']) ?>">
</div>

<div class="account-info-item">
    <label>Phone Number:</label>
    <input id="phone" value="<?= htmlspecialchars($user['phoneNumber']) ?>">
</div>

<div class="account-info-item">
    <label>Password:</label>
    <span>*********</span>
    <i class="fas fa-edit edit-icon"></i>
</div>

<button class="edit-info-btn" onclick="saveChanges(<?= $user['userID'] ?>)">Save Changes</button>

</section>
</main>
</main>

<script src="../js/settings-personal.js"></script>
</body>
</html>