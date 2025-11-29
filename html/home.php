<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DECORIA | Home</title>

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Tajawal:wght@400;500;700&display=swap');

    :root {
      --bg: #f8f5ee;
      --brand: #3b4d3b;
      --border: #d8d3c5;
      --text: #2e2a23;
      --muted: #6f6a60;
      --card: #ffffff;
      --primary-btn: #3b4d3b;
      --primary-btn-hover: #2c3e2f;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      background: var(--bg);
      color: var(--text);
      font-family: 'Tajawal', sans-serif;
      overflow-x: hidden;
    }

    .container { max-width: 1200px; margin: auto; padding: 20px; }

    /* ===== Header ===== */
    .site-header {
      background: #fffaf3;
      border-bottom: 1px solid var(--border);
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .header-container {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 25px;
    }

    .brand { order: 1; }
    .logo {
      height: 100px;
      width: auto;
      object-fit: contain;
      border-radius: 10px;
      transition: transform 0.3s ease;
    }
    .logo:hover { transform: scale(1.05); }

    .header-buttons { order: 2; display: flex; align-items: center; }
    
    .welcome-text {
      font-family: 'Playfair Display', serif;
      font-size: 22px;
      font-weight: 700;
      color: var(--brand);
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
      white-space: nowrap;
    }

    /* Login/Register buttons */
    .auth-buttons {
      display: inline-flex;
      gap: 10px;
      margin-right: 15px;
    }

    .auth-btn {
      font-weight: 600;
      padding: 8px 16px;
      border-radius: 6px;
      text-decoration: none;
      transition: all 0.3s ease;
      font-size: 15px;
    }

    .auth-btn.login {
      background: var(--brand);
      color: #fff;
    }

    .auth-btn.register {
      background: #fff;
      color: var(--brand);
      border: 1px solid var(--brand);
    }

    .auth-btn.login:hover {
      background: var(--primary-btn-hover);
    }

    .auth-btn.register:hover {
      background: var(--brand);
      color: #fff;
    }

    .menu-toggle {
      font-size: 30px;
      color: var(--text);
      background: none;
      border: none;
      cursor: pointer;
      transition: .3s;
    }
    .menu-toggle:hover { color: var(--brand); transform: rotate(90deg); }

    /* ===== Sidebar ===== */
    .sidebar {
      position: fixed;
      top: 0;
      right: -300px;
      width: 270px;
      height: 100%;
      background: #fffaf3;
      box-shadow: -3px 0 12px rgba(0,0,0,0.15);
      transition: right 0.4s ease;
      z-index: 2000;
      padding: 25px 20px;
      display: flex;
      flex-direction: column;
    }
    .sidebar.open { right: 0; }

    .sidebar a {
      text-decoration: none;
      color: var(--text);
      font-weight: 600;
      margin: 12px 0;
      font-size: 17px;
      transition: color 0.3s ease, transform 0.2s ease;
    }
    .sidebar a:hover { color: var(--brand); transform: translateX(-4px); }

    .close-btn {
      align-self: flex-end;
      font-size: 26px;
      cursor: pointer;
      transition: .3s;
    }
    .close-btn:hover { color: var(--brand); transform: rotate(90deg); }

    #overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.4);
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.4s ease;
      z-index: 1500;
    }
    #overlay.active { opacity: 1; visibility: visible; }

    /* ===== Hero ===== */
    .hero {
      height: 80vh;
      width: 100%;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      color: white;
      background-size: cover;
      background-position: center;
      position: relative;
      transition: background-image 1.5s ease-in-out;
    }

    .hero::before {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(0,0,0,0.35);
    }

    .hero h2 { font-size: 40px; margin-bottom: 10px; position: relative; }
    .hero p { font-size: 18px; max-width: 600px; position: relative; }

    /* ===== User Section ===== */
    .user-section {
      display: flex;
      justify-content: center;
      gap: 30px;
      padding: 40px 20px;
      flex-wrap: wrap;
    }
    .user-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 30px;
      width: 280px;
      text-align: center;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      transition: .3s;
    }
    .user-card:hover { transform: translateY(-6px); }

    .user-card h3 { color: var(--brand); margin-bottom: 10px; }
    .user-card p { color: var(--muted); margin-bottom: 15px; }
    .user-card a {
      display: inline-block;
      background: var(--brand);
      color: white;
      text-decoration: none;
      padding: 10px 20px;
      border-radius: 5px;
    }
    .user-card a:hover { background: var(--primary-btn-hover); }

    /* ===== Footer ===== */
    footer { margin-top: 40px; background: #fffaf3; border-top: 1px solid var(--border); }
    .footer-image {
      width: 100%; height: 250px; object-fit: cover;
    }
    .footer-content { text-align: center; position: relative; }
    .footer-text {
      position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%);
      color: rgb(218,218,218);
    }
  </style>
</head>

<body>

  <!-- HEADER -->
  <header class="site-header">
    <div class="container header-container">

      <div class="brand">
        <img src="../photo/Logo.png.png" class="logo">
      </div>

      <p class="welcome-text">Welcome to DECORIA</p>

      <div class="header-buttons">

        <div class="auth-buttons">
          <?php if(isset($_SESSION['username'])): ?>
              <span style="font-weight:700; color:var(--brand); margin-right:10px;">
                Hello, <?php echo $_SESSION['username']; ?>
              </span>
          <?php else: ?>
              <a href="login.php" class="auth-btn login">Login</a>
              <a href="register.php" class="auth-btn register">Register</a>
          <?php endif; ?>
        </div>

        <button class="menu-toggle">☰</button>
      </div>
    </div>
  </header>

  <!-- SIDEBAR -->
  <div class="sidebar" id="sidebar">
    <span class="close-btn" id="closeSidebar">&times;</span>

    <a href="designers.php">Designers</a>
    <a href="mybookings.php">Booking</a>
    <a href="Designer-timeline.php">Timeline</a>
    <a href="meeting.php">Meeting</a>
    <a href="settings.php">Settings</a>
    <hr>

    <?php if(isset($_SESSION['username'])): ?>
        <a href="logout.php" style="color:red; font-weight:bold;">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    <?php endif; ?>
  </div>

  <div id="overlay"></div>

  <!-- HERO -->
  <section class="hero" id="hero">
    <h2>Design Beyond Walls</h2>
    <p>Connecting clients with top-tier interior designers to bring visions to life — from concept to creation.</p>
  </section>

  <!-- USER SECTION -->
  <section class="user-section">
    <div class="user-card">
      <h3>For Clients</h3>
      <p>Find your perfect designer and book your dream project.</p>
      <a href="designers.php">Start Exploring</a>
    </div>

    <div class="user-card">
      <h3>For Designers</h3>
      <p>Join DECORIA and share your creative vision with clients.</p>
      <a href="#" id="joinDesigner">Join DECORIA</a>
    </div>
  </section>

  <!-- FOOTER -->
  <footer>
    <div class="footer-content">
      <p class="footer-text">
        ©️ 2025 DECORIA — All rights reserved |
        <a href="terms.html" style="color:white;">Terms & Conditions</a>
      </p>
      <img src="../photo/darlfooter.jpeg" class="footer-image">
    </div>
  </footer>

  <script>
    // Sidebar
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("overlay");
    document.querySelector(".menu-toggle").onclick = () => {
      sidebar.classList.add("open");
      overlay.classList.add("active");
    };
    document.getElementById("closeSidebar").onclick = () => {
      sidebar.classList.remove("open");
      overlay.classList.remove("active");
    };
    overlay.onclick = () => {
      sidebar.classList.remove("open");
      overlay.classList.remove("active");
    };

    // Hero slideshow
    const hero = document.getElementById("hero");
    const images = ["../photo/room44.jpeg", "../photo/room33.jpeg"];
    let current = 0;
    hero.style.backgroundImage = `url(${images[current]})`;
    setInterval(() => {
      current = (current + 1) % images.length;
      hero.style.backgroundImage = `url(${images[current]})`;
    }, 4000);
  </script>

</body>
</html>
