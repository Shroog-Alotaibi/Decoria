<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DECORIA | Booking</title>

  <!-- CSS links -->
<link rel="stylesheet" href="decoria.css" />
<link rel="stylesheet" href="booking.css" />
</head>
<body>

  <!-- Header -->
  <header class="site-header">
    <div class="container header-container">
      <div class="brand">
        <img src="/photo/Logo.png" alt="DECORIA Logo" class="logo">
      </div>
      <p class="welcome-text">Book Your Designer</p>
      <div class="header-buttons">
        <button class="menu-toggle">☰</button>
      </div>
    </div>
  </header>

  <!-- Sidebar Menu -->
  <div class="sidebar" id="sidebar">
    <span class="close-btn" id="closeSidebar">&times;</span>
    <a href="home.html">Home</a>
    <a href="designers.html">Designers</a>
    <a href="booking.html" class="active">Booking</a>
    <a href="timeline.html">Timeline</a>
    <a href="meeting.html">Meeting</a>
    <a href="about.html">About</a>
    <a href="#">My Account</a>
    <a href="settings.html">Settings</a>
    <hr>
    <a href="#" class="logout">Logout</a>
  </div>

  <div id="overlay"></div>

  <!-- Main Content -->
  <main class="container">
    <h2 class="section-title">Book Your Designer</h2>

    <form class="booking-form">
	<!-- Client Name -->
<label for="clientName">Your Name</label>
<input type="text" id="clientName" placeholder="Enter your name" required>

      <!-- Choose Designer -->
      <label for="designer">Choose Designer:</label>
      <select id="designer" required>
        <option value="">Select a Designer</option>
      </select>

      <!-- Choose Date -->
      <label for="date">Choose Date:</label>
      <input type="date" id="date" required>

      <!-- Choose Time -->
      <label for="time">Choose Time:</label>
      <input type="time" id="time" required>

      <!-- Payment Method -->
      <label for="payment">Payment Method:</label>
      <select id="payment" required>
        <option value="">Select Payment Method</option>
        <option value="credit">Credit Card</option>
        <option value="bank">Bank Transfer</option>
        <option value="applepay">applepay</option>
      </select>

     <div class="btn-row">
  <button id="confirmBooking" class="primary-btn">Confirm Booking</button>
</div>


<div id="bookingDetails" class="booking-message" style="display:none;">
  <p>✅ Booking confirmed successfully!</p>
  <p><strong>Name:</strong> <span id="detailName"></span></p>
  <p><strong>Date:</strong> <span id="detailDate"></span></p>
  <p><strong>Time:</strong> <span id="detailTime"></span></p>

  <div class="btn-row">
    <button id="editBooking" class="secondary-btn">Edit</button>
    <button id="cancelBooking2" class="secondary-btn">Cancel</button>
    <button id="goTimeline" class="primary-btn">Go to Timeline</button>
  </div>
</div>
  </main>

  <!-- Footer -->
  <footer>
    <div class="footer-content">
      <p class="footer-text">©️ 2025 DECORIA — All rights reserved</p>
      <img src="/photo/darlfooter.jpeg" alt="DECORIA Logo" class="footer-image">
    </div>
  </footer>

  <!-- JS -->
  <script src="sidebar.js"></script>
     <script src="designerInfo.js"></script>
  <script src="booking.js"></script>
 
</body>
</html>
