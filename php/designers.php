<?php

$host     = 'localhost';
$user     = 'root';
$password = 'root';  
$dbname   = 'decoria';


$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "
    SELECT 
        d.designerID,
        d.bio,
        d.profilePicture,
        d.specialty,
        u.name,
        u.address
    FROM designer d
    JOIN user u ON d.designerID = u.userID
    WHERE u.role = 'Designer'
      AND u.name <> 'Samira'
    ORDER BY u.userID
";
$result = $conn->query($sql);

// ماب للتقييمات عشان ما عندكم عمود rating في الجدول
$ratingMap = [
    'Esra Aljaser'    => 4.8,
    'Felwa Althagfan' => 4.6,
    'Ghada Alotaibi'  => 4.9,
    'Hessa Alnafisah' => 4.7,
    'Muntaha'         => 4.5,
    'Ahmed Zaher'     => 4.8,
];
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DECORIA | Interior Designers</title>

  <!-- CSS links -->
  <link rel="stylesheet" href="../css/decoria.css" />
  <link rel="stylesheet" href="../css/designers.css" />
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
    <a href="home.php">Home</a>
    <a href="designers.php" class="active">Designers</a>
    <a href="booking.php">Booking</a>
    <a href="timeline.php">Timeline</a>
    <a href="meeting.php">Meeting</a>
    <a href="settings.php">Settings</a>
    <hr>
    <a href="login.php" class="logout">Logout</a>
  </div>

  <div id="overlay"></div>

  <!-- Main -->
  <main class="container">
    <h2 class="section-title">Interior Designers</h2>

    <!-- مربع البحث -->
    <div class="filters">
      <input id="q" class="search" placeholder="Search for a designer...">
    </div>

    <!-- الكروت -->
    <div id="grid" class="designers-grid">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <?php
            $name = htmlspecialchars($row['name']);

            // نطلع المدينة من العنوان (قبل أول -)
            $addr = $row['address'] ?? '';
            $city = $addr;
            if (strpos($addr, '-') !== false) {
                $parts = explode('-', $addr);
                $city = trim($parts[0]);
            }
            $city = htmlspecialchars($city);

            $bio = htmlspecialchars($row['bio']);

            // مسار الصورة: نخليه ../ + اللي في الجدول
            $avatar = '../' . ltrim($row['profilePicture'], '/');
            $avatar = htmlspecialchars($avatar);

            // التخصصات tags من specialty
            $specialty = $row['specialty'] ?? '';
            $styles = array_filter(array_map('trim', explode(',', $specialty)));

            // التقييم
            $ratingValue = $ratingMap[$row['name']] ?? 4.5;
            $ratingText  = '★ ' . number_format($ratingValue, 1);

            $designerID = (int)$row['designerID'];
          ?>
          <article class="designer-card" data-id="<?php echo $designerID; ?>">
            <img class="designer-avatar"
                 src="<?php echo $avatar; ?>"
                 alt="<?php echo $name; ?>"
                 onerror="this.onerror=null;this.src='../photo/placeholder.png'">

            <h3 class="designer-name">
              <?php echo $name; ?>
              <span style="color:gold"><?php echo $ratingText; ?></span>
            </h3>

            <p class="designer-meta"><?php echo $city; ?></p>
            <p><?php echo $bio; ?></p>

            <div>
              <?php foreach ($styles as $style): ?>
                <span class="tag"><?php echo htmlspecialchars($style); ?></span>
              <?php endforeach; ?>
            </div>

            <a class="view-btn" href="designerinfo.php?id=<?php echo $designerID; ?>">
              View Profile
            </a>
          </article>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No designers found.</p>
      <?php endif; ?>
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
<?php
$conn->close();
?>
