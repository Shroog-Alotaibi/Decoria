<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ================================
// 1. الاتصال بقاعدة البيانات
// ================================
$host = "localhost";
$user = "root"; 
$pass = "root";     
$dbname = "decoria";

$conn = new mysqli($host, $user, $pass, $dbname);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// ================================
// 2. استلام رقم المصمم من الرابط
// ================================
$designerID = isset($_GET['id']) ? intval($_GET['id']) : 0;

// ================================
// 3. جلب بيانات المصمّم
// ================================
$designerQuery = $conn->query("
    SELECT u.name, d.specialty, d.profilePicture, d.linkedinURL, d.bio
    FROM designer d
    JOIN user u ON u.userID = d.designerID
    WHERE d.designerID = $designerID
");

$designer = $designerQuery->fetch_assoc();

// ================================
// 4. جلب التصاميم
// ================================
$designsQuery = $conn->query("
    SELECT * FROM design
    WHERE designerID = $designerID
");

// ================================
// 5. جلب الريفيوهات (التعديل المهم هنا)
// ================================
$reviewsQuery = $conn->query("
    SELECT r.rating, r.comment, r.reviewDate, u.name
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

  <div class="sidebar" id="sidebar">
    <span class="close-btn" id="closeSidebar">&times;</span>
    <a href="home.html">Home</a>
    <a href="designers.php">Designers</a>
    <a href="booking.php" class="active">Booking</a>
    <a href="timeline.php">Timeline</a>
    <a href="meeting.php">Meeting</a>
    <a href="settings.php">Settings</a>
    <hr>
    <a href="login.html" class="logout">Logout</a>
  </div>
  <div id="overlay"></div>

  <main class="container">

    <!-- Banner -->
    <section class="profile-banner" id="profileBanner">
      <a class="back-arrow" href="designers.php">←</a>

      <img id="designerLogo" class="banner-logo" 
           src="<?php echo $designer['profilePicture']; ?>">

      <div class="banner-head">
        <h2 id="designerName" class="banner-name">
          <?php echo $designer['name']; ?>
        </h2>

        <p id="designerRole" class="banner-role">
          <?php echo $designer['specialty']; ?>
        </p>

        <div class="banner-stats">
          <div>
            <strong id="reviewsCount"><?php echo $reviewsQuery->num_rows; ?></strong><br>Reviews
          </div>
        </div>

        <?php if (!empty($designer['linkedinURL'])): ?>
        <div id="linkWrap" class="banner-link-wrap">
          <a id="profileLink" class="banner-link"
             href="<?php echo $designer['linkedinURL']; ?>" target="_blank">↗</a>
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
      <?php while ($d = $designsQuery->fetch_assoc()): ?>
      <div class="card">
        <img src="<?php echo $d['image']; ?>" alt="">
        <h4><?php echo $d['title']; ?></h4>
        <p><?php echo $d['description']; ?></p>
      </div>
      <?php endwhile; ?>
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
        | <a href="terms.html">Terms & Conditions</a>
      </p>
      <img src="../photo/darlfooter.jpeg" class="footer-image">
    </div>
  </footer>

  <script src="../js/sidebar.js"></script>
  <script src="../js/designerInfo.js" defer></script>

</body>
</html>
