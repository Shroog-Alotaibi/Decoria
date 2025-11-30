<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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

<h2 class="section-title">Personal Settings</h2>


<div id="messageBox"></div>


<div class="settings-page">

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
    <a href="password.php">
        <i class="fas fa-edit edit-icon" style="color:#3b4d3b;"></i>
    </a>
</div>

<button class="edit-info-btn" onclick="saveChanges(<?= $user['userID'] ?>)">Save Changes</button>

<div id="errorMessage" 
     style="
        margin-top:20px;
        padding:12px 18px;
        border-radius:10px;
        font-weight:600;
        font-size:16px;
        text-align:center;
        background:#fdecea;
        color:#b71c1c;
        border:1px solid #f5c6cb;
        box-shadow:0 2px 6px rgba(0,0,0,0.05);
        width: fit-content;
        margin-left:auto;
        margin-right:auto;
        display:none;
     ">
    <span id="errorText"></span>
</div>

</section>

</div> 

</main>

<script src="../js/settings-personal.js"></script>
<script src="../js/sidebar.js"></script>

</body>
</html>
