<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


$host = "localhost";
$user = "root";
$pass = "root";     
$dbname = "decoria";

$conn = new mysqli($host, $user, $pass, $dbname );
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}


$designerID = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($designerID <= 0) {
    die("Invalid Designer ID");
}


$designerQuery = $conn->query("
    SELECT u.name, d.specialty, d.profilePicture, d.linkedinURL, d.bio
    FROM designer d
    JOIN user u ON u.userID = d.designerID
    WHERE d.designerID = $designerID
");

$designer = $designerQuery->fetch_assoc();
if (!$designer) {
    die("Designer Not Found");
}


$designer['profilePicture'] = "Software/" . ltrim($designer['profilePicture'], "/");


$designsQuery = $conn->query("
    SELECT title, description, image
    FROM design
    WHERE designerID = $designerID
");

$designs = [];
while ($d = $designsQuery->fetch_assoc()) {
    $d['image'] = "Software/" . ltrim($d['image'], "/");
    $designs[] = $d;
}


$reviewsQuery = $conn->query("
    SELECT r.rating, r.comment, u.name
    FROM review r
    JOIN user u ON u.userID = r.clientID
    WHERE r.designerID = $designerID
");

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DECORIA — Designer Profile</title>

  <link rel="stylesheet" href="../css/decoria.css">
  <link rel="stylesheet" href="../css/designerInfo.css">
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

    <!-- Banner -->
    <section class="profile-banner">
      <a class="back-arrow" href="designers.php">←</a>

      
      <img id="designerLogo" class="banner-logo"
           src="/<?php echo $designer['profilePicture']; ?>" 
           alt="Designer Picture">

      <div class="banner-head">
        <h2 class="banner-name"><?php echo $designer['name']; ?></h2>

        <p class="banner-role"><?php echo $designer['specialty']; ?></p>

        <div class="banner-stats">
          <div>
            <strong><?php echo $reviewsQuery->num_rows; ?></strong><br>Reviews
          </div>
        </div>

        <?php if (!empty($designer['linkedinURL'])): ?>
        <div class="banner-link-wrap">
          <a class="banner-link" href="<?php echo $designer['linkedinURL']; ?>" target="_blank">↗</a>
          <div class="banner-link-label">LinkedIn</div>
        </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- Tabs -->
    <nav class="tabs" role="tablist">
      <button class="tab is-active" data-tab="designs">Designs</button>
      <button class="tab" data-tab="review">Review</button>
    </nav>

    <!-- Designs Section -->
    <section id="designsSection" class="cards">
      <?php foreach ($designs as $d): ?>
      <div class="card">
        <img src="/<?php echo $d['image']; ?>" alt="">
        <h4><?php echo $d['title']; ?></h4>
        <p><?php echo $d['description']; ?></p>
      </div>
      <?php endforeach; ?>
    </section>

    <!-- Reviews Section -->
    <section id="reviewsSection" class="reviews is-hidden">
      <h3 class="reviews-title">Reviews</h3>

      <?php while ($r = $reviewsQuery->fetch_assoc()): ?>
      <div class="review-item">
        <div class="review-header">
          <strong><?php echo $r['name']; ?></strong>
          <span>⭐ <?php echo $r['rating']; ?></span>
        </div>
        <p><?php echo $r['comment']; ?></p>
      </div>
      <?php endwhile; ?>
    </section>

  </main>

  <footer>
    <div class="footer-content">
      <p class="footer-text">
        © 2025 DECORIA — All rights reserved
        | <a href="terms.php">Terms & Conditions</a>
      </p>
      <img src="../photo/darlfooter.jpeg" class="footer-image">
    </div>
  </footer>

  <script src="../js/sidebar.js"></script>
  <script src="../js/designerInfo.js" defer></script>

</body>
</html>

