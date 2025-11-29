<?php
require_once "../php/config.php";

// Only logged-in designers can access
check_login('Designer');

// Designer ID comes from session
$designerID = $_SESSION['userID'];
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>DECORIA — Designer Profile</title>

  <!-- Site theme -->
  <link rel="stylesheet" href="../css/decoria.css">
  <!-- Page-specific styles -->
  <link rel="stylesheet" href="../css/designerInfo.css">

  <style>
    .edit-controls {
      text-align: center;
      margin: 20px 0;
    }
    .edit-btn {
      background: var(--brand);
      color: white;
      border: none;
      border-radius: 10px;
      padding: 10px 20px;
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }
    .edit-btn:hover {
      background: var(--primary-btn-hover);
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.12);
    }
    .edit-btn.active {
      background: var(--primary-btn-hover);
    }
    .card-actions {
      position: absolute;
      top: 10px;
      right: 10px;
      display: none;
      gap: 8px;
      z-index: 10;
    }
    .edit-mode .card-actions {
      display: flex;
    }
    .action-btn {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      border: none;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 2px 8px rgba(0,0,0,0.15);
      background: #ffffff;
    }
    .delete-btn {
      background: #fff1f0;
      color: #cf1322;
    }
    .delete-btn:hover {
      background: #ffccc7;
      transform: scale(1.1);
    }
    .reorder-btn {
      background: #f6ffed;
      color: #389e0d;
    }
    .reorder-btn:hover {
      background: #d9f7be;
      transform: scale(1.1);
    }
    .card {
      position: relative;
    }

    .profile-edit-mode .banner-logo {
      cursor: pointer;
      border: 2px dashed #ccc;
      border-radius: 50%;
    }
    .profile-edit-mode .banner-logo:hover {
      border-color: var(--brand);
      opacity: 0.8;
    }
    .profile-edit-mode .banner-head {
      position: relative;
    }
    .edit-profile-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      background: rgba(255,255,255,0.9);
      border: none;
      border-radius: 20px;
      padding: 6px 12px;
      font-size: 12px;
      cursor: pointer;
      display: none;
      align-items: center;
      gap: 4px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .profile-edit-mode .edit-profile-btn {
      display: flex;
    }

    .tabs-container {
      position: relative;
      margin-bottom: 20px;
    }
    .add-design-btn {
      position: absolute;
      right: 0;
      top: 50%;
      transform: translateY(-50%);
      background: var(--brand);
      color: white;
      border: none;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 20px;
      font-weight: bold;
      transition: all 0.3s ease;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
      display: none;
    }
    .add-design-btn:hover {
      background: var(--primary-btn-hover);
      transform: translateY(-50%) scale(1.1);
    }

    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0,0,0,0.5);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 10000;
      padding: 20px;
    }
    .modal-overlay.active {
      display: flex;
    }
    .modal {
      background: white;
      border-radius: 12px;
      padding: 24px;
      max-width: 500px;
      width: 100%;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    }
    .modal h3 {
      margin: 0 0 20px 0;
      color: #002766;
      text-align: center;
    }
    .form-group { margin-bottom: 20px; }
    .form-group label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
      color: #333;
    }
    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 10px 12px;
      border: 2px solid #e8e8e8;
      border-radius: 8px;
      font-size: 14px;
      transition: border-color 0.3s ease;
    }
    .form-group input:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: var(--brand);
    }
    .form-group textarea {
      resize: vertical;
      min-height: 80px;
    }
    .file-upload {
      border: 2px dashed #e8e8e8;
      border-radius: 8px;
      padding: 30px;
      text-align: center;
      cursor: pointer;
      transition: border-color 0.3s ease;
    }
    .file-upload:hover { border-color: var(--brand); }
    .file-upload input { display: none; }

    .upload-icon {
      font-size: 40px;
      color: #ccc;
      margin-bottom: 10px;
    }
    .modal-actions {
      display: flex;
      gap: 12px;
      justify-content: flex-end;
      margin-top: 24px;
    }
    .modal-btn {
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .modal-btn.cancel {
      background: #f5f5f5;
      color: #666;
    }
    .modal-btn.cancel:hover { background: #e8e8e8; }
    .modal-btn.save {
      background: var(--brand);
      color: white;
    }
    .modal-btn.save:hover { background: var(--primary-btn-hover); }

    .tabs {
      display: flex;
      border-bottom: 2px solid #e8e8e8;
      margin-bottom: 20px;
    }
    .tab {
      background: none;
      border: none;
      padding: 12px 24px;
      font-size: 16px;
      font-weight: 600;
      color: #666;
      cursor: pointer;
      border-bottom: 3px solid transparent;
      transition: all 0.3s ease;
    }
    .tab.is-active {
      color: var(--brand);
      border-bottom-color: var(--brand);
    }
    .tab:hover:not(.is-active) {
      color: #333;
      background: #f5f5f5;
    }

    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
      margin-bottom: 40px;
    }
    .card {
      background: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 3px 15px rgba(0,0,0,0.08);
      transition: all 0.3s ease;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }
    .card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
    }
    .card-label {
      padding: 12px 16px;
      font-weight: 700;
      color: #002766;
      text-align: center;
      border-top: 1px solid #f0f0f0;
    }
    .card-desc {
      margin-top: 6px;
      font-size: 0.9rem;
      color: #444;
      text-align: center;
      padding: 0 12px 12px;
    }

    .reviews.is-hidden { display: none; }
    .reviews-title {
      font-size: 24px;
      font-weight: 700;
      color: #002766;
      margin-bottom: 20px;
      text-align: center;
    }
    .reviews-list {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }
    .review-card {
      display: flex;
      align-items: flex-start;
      gap: 16px;
      padding: 20px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    .review-avatar-icon {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background: #f0f0f0;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .review-head {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 8px;
    }
    .review-name {
      font-weight: 700;
      color: #002766;
    }
    .stars {
      color: #ffc107;
      font-weight: 600;
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

  <!-- Sidebar Menu -->
  <div class="sidebar" id="sidebar">
    <span class="close-btn" id="closeSidebar">&times;</span>
    <a href="home.html">Home</a>
    <a href="designers.html">Designers</a>
    <a href="booking.html">Booking</a>
    <a href="designerProfile.php" class="active">Profile</a>
    <a href="meeting.html">Meeting</a>
    <a href="settings.html">Settings</a>
    <hr>
    <a href="logout.php" class="logout">Logout</a>
  </div>
  <div id="overlay"></div>

  <main class="container">

    <!-- Edit Button -->
    <div class="edit-controls">
      <button class="edit-btn" id="editBtn">Edit Portfolio</button>
    </div>

    <!-- Profile banner -->
    <section class="profile-banner" id="profileBanner">
      <a class="back-arrow" href="designers.html" title="Back to Designers">←</a>

      <!-- Designer logo -->
      <img id="designerLogo" class="banner-logo" src="../photo/placeholder.png" alt="Designer Logo">

      <div class="banner-head">
        <button class="edit-profile-btn" id="editProfileBtn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
          </svg>
          Edit Profile
        </button>
        
        <h2 id="designerName" class="banner-name">Designer Name</h2>
        <p id="designerRole" class="banner-role"></p>

        <div class="banner-stats">
          <div><strong id="reviewsCount">0</strong><br>Reviews</div>
        </div>

        <div id="linkWrap" class="banner-link-wrap" hidden>
          <a id="profileLink" class="banner-link" href="#" target="_blank" rel="noopener" aria-label="LinkedIn">↗</a>
          <div class="banner-link-label">LinkedIn</div>
        </div>
      </div>
    </section>

    <!-- Tabs + Add design button -->
    <div class="tabs-container">
      <nav class="tabs" role="tablist">
        <button class="tab is-active" data-tab="designs">Designs</button>
        <button class="tab" data-tab="review">Review</button>
      </nav>
      <button class="add-design-btn" id="addDesignBtn" title="Add New Design">+</button>
    </div>

    <!-- Designs (from DB) -->
    <section id="designsSection" class="cards"></section>

    <!-- Reviews (from DB) -->
    <section id="reviewsSection" class="reviews is-hidden">
      <h3 class="reviews-title">Reviews</h3>
      <div id="reviewsList" class="reviews-list"></div>
    </section>
  </main>

  <!-- Add Design Modal -->
  <div class="modal-overlay" id="addDesignModal">
    <div class="modal">
      <h3>Add New Design</h3>
      <form id="addDesignForm">
        <div class="form-group">
          <label for="designImage">Design Image</label>
          <div class="file-upload" id="designImageUpload">
            <div class="upload-icon">📁</div>
            <p>Click to upload design image</p>
            <input type="file" id="designImage" accept="image/*">
          </div>
          <div id="imagePreview" style="display: none; margin-top: 10px;">
            <img id="previewImg" src="" alt="Preview" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
          </div>
        </div>
        
        <div class="form-group">
          <label for="designTitle">Title</label>
          <input type="text" id="designTitle" placeholder="Enter design title">
        </div>
        
        <div class="form-group">
          <label for="designDescription">Description</label>
          <textarea id="designDescription" placeholder="Enter design description"></textarea>
        </div>
        
        <div class="modal-actions">
          <button type="button" class="modal-btn cancel" id="cancelDesignBtn">Cancel</button>
          <button type="submit" class="modal-btn save">Add Design</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Design Modal -->
  <div class="modal-overlay" id="editDesignModal">
    <div class="modal">
      <h3>Edit Design</h3>
      <form id="editDesignForm">
        <div class="form-group">
          <label for="editDesignTitle">Title</label>
          <input type="text" id="editDesignTitle" placeholder="Edit design title">
        </div>
        <div class="form-group">
          <label for="editDesignDescription">Description</label>
          <textarea id="editDesignDescription" placeholder="Edit design description"></textarea>
        </div>
        <div class="modal-actions">
          <button type="button" class="modal-btn cancel" id="cancelEditDesignBtn">Cancel</button>
          <button type="submit" class="modal-btn save">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Profile Modal -->
  <div class="modal-overlay" id="editProfileModal">
    <div class="modal">
      <h3>Edit Profile</h3>
      <form id="editProfileForm">
        <div class="form-group">
          <label for="profileImage">Profile Picture</label>
          <div class="file-upload" id="profileImageUpload">
            <div class="upload-icon">👤</div>
            <p>Click to upload profile picture</p>
            <input type="file" id="profileImage" accept="image/*">
          </div>
          <div id="profilePreview" style="display: none; margin-top: 10px;">
            <img id="profilePreviewImg" src="" alt="Preview" style="max-width: 100px; max-height: 100px; border-radius: 50%;">
          </div>
        </div>
        
        <div class="form-group">
          <label for="designerSpecialty">Specialty</label>
          <input type="text" id="designerSpecialty" placeholder="Enter your specialty">
        </div>
        
        <div class="form-group">
          <label for="designerBio">Bio</label>
          <textarea id="designerBio" placeholder="Tell us about yourself and your design philosophy"></textarea>
        </div>
        
        <div class="modal-actions">
          <button type="button" class="modal-btn cancel" id="cancelProfileBtn">Cancel</button>
          <button type="submit" class="modal-btn save">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <footer>
    <div class="footer-content">
      <p class="footer-text">
        © <span id="y">2025</span> DECORIA — All rights reserved
        | <a href="terms.html">Terms & Conditions</a>
      </p>
      <img src="../photo/darlfooter.jpeg" alt="DECORIA Footer Image" class="footer-image">
    </div>
  </footer>

  <script src="../js/sidebar.js"></script>
  <script>
    // Expose the logged-in designer ID to JS
    window.DESIGNER_ID_FROM_PHP = <?= $designerID ?>;
  </script>
  <script src="../js/designerProfile.js"></script>
</body>
</html>
