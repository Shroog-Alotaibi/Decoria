<?php
require_once "config.php";
session_start();

// Only designers can access this page
check_login('Designer');

$designerID = $_SESSION['user_id'];

/* ----------------------------------------
   FETCH DESIGNER PROFILE INFO
------------------------------------------- */
$sql = "SELECT 
            u.name,
            d.specialty,
            d.profilePicture,
            d.linkedinURL,
            d.bio
        FROM user u
        JOIN designer d ON d.designerID = u.userID
        WHERE u.userID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $designerID);
$stmt->execute();
$designer = $stmt->get_result()->fetch_assoc();

if (!$designer) {
    // Fallback (in case designer row missing)
    $designer = [
        "name"           => "Designer Name",
        "specialty"      => "",
        "profilePicture" => "photo/defaultAvatar.png",
        "linkedinURL"    => "",
        "bio"            => ""
    ];
}

/* ----------------------------------------
   FETCH DESIGNS FOR THIS DESIGNER
------------------------------------------- */
$sql = "SELECT designID, title, description, image, uploadDate
        FROM design
        WHERE designerID = ?
        ORDER BY uploadDate DESC, designID DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $designerID);
$stmt->execute();
$designs = $stmt->get_result();

/* ----------------------------------------
   FETCH REVIEWS FOR THIS DESIGNER
------------------------------------------- */
$sql = "SELECT 
            r.rating,
            r.comment,
            r.reviewDate,
            u.name AS clientName
        FROM review r
        JOIN user u ON u.userID = r.clientID
        WHERE r.designerID = ?
        ORDER BY r.reviewDate DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $designerID);
$stmt->execute();
$reviews = $stmt->get_result();
$reviewsCount = $reviews->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>DECORIA — Designer Profile</title>

  <!-- Main theme -->
  <link rel="stylesheet" href="../css/decoria.css" />

  <style>
    /* ====== PROFILE HEADER ====== */
    .profile-header {
      text-align: center;
      margin: 40px auto 30px;
      max-width: 700px;
    }
    .profile-header img {
      width: 140px;
      height: 140px;
      border-radius: 50%;
      border: 3px solid var(--brand);
      object-fit: cover;
      box-shadow: 0 4px 15px rgba(0,0,0,0.12);
    }
    .designer-name {
      font-size: 26px;
      font-weight: 800;
      color: var(--brand);
      margin-top: 15px;
    }
    .designer-specialty {
      color: var(--muted);
      font-size: 15px;
      margin-top: 4px;
    }
    .designer-bio {
      margin-top: 12px;
      color: var(--fg-muted);
      font-size: 14px;
      line-height: 1.5;
    }
    .profile-actions {
      margin-top: 18px;
      display: flex;
      justify-content: center;
      gap: 10px;
    }
    .primary-btn, .secondary-btn {
      border: none;
      border-radius: 999px;
      padding: 8px 18px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .primary-btn {
      background: var(--brand);
      color: #fff;
    }
    .primary-btn:hover {
      background: var(--primary-btn-hover);
      transform: translateY(-1px);
    }
    .secondary-btn {
      background: #f3f3f3;
      color: #444;
    }
    .secondary-btn:hover {
      background: #e7e7e7;
      transform: translateY(-1px);
    }
    .stats-row {
      margin-top: 15px;
      font-size: 14px;
      color: var(--muted);
    }

    /* ====== TABS ====== */
    .tabs-row {
      margin-top: 40px;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .tabs {
      display: flex;
      gap: 10px;
    }
    .tab-btn {
      background: none;
      border: none;
      padding: 10px 18px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      border-bottom: 3px solid transparent;
      color: var(--muted);
    }
    .tab-btn.active {
      border-bottom-color: var(--brand);
      color: var(--brand);
    }

    /* ====== DESIGNS GRID ====== */
    .posts-container {
      margin-top: 25px;
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 20px;
    }
    .post-card {
      background: var(--card);
      border-radius: 14px;
      border: 1px solid var(--border);
      padding: 12px;
      position: relative;
      box-shadow: 0 3px 12px rgba(0,0,0,0.06);
      transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .post-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .post-card img {
      width: 100%;
      height: 180px;
      border-radius: 10px;
      object-fit: cover;
    }
    .post-title {
      font-weight: 700;
      margin-top: 10px;
      color: var(--brand);
      font-size: 15px;
    }
    .post-desc {
      font-size: 14px;
      margin-top: 5px;
      color: var(--fg-muted);
    }
    .post-meta {
      margin-top: 6px;
      font-size: 12px;
      color: var(--muted);
    }
    .post-controls {
      position: absolute;
      top: 10px;
      right: 10px;
      display: flex;
      gap: 6px;
    }
    .icon-btn {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      border: none;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      box-shadow: 0 2px 8px rgba(0,0,0,0.15);
      transition: transform 0.15s ease, background 0.15s ease;
      background: #fff;
    }
    .icon-btn svg {
      width: 16px;
      height: 16px;
    }
    .icon-btn.edit {
      color: #389e0d;
      background: #f6ffed;
    }
    .icon-btn.edit:hover {
      background: #d9f7be;
      transform: scale(1.05);
    }
    .icon-btn.delete {
      color: #cf1322;
      background: #fff1f0;
    }
    .icon-btn.delete:hover {
      background: #ffccc7;
      transform: scale(1.05);
    }

    /* ====== REVIEWS SECTION ====== */
    .reviews-section {
      margin-top: 25px;
      display: none;
    }
    .reviews-section.active {
      display: block;
    }
    .reviews-title {
      font-size: 20px;
      font-weight: 700;
      color: var(--brand);
      margin-bottom: 15px;
    }
    .reviews-list {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }
    .review-card {
      display: flex;
      gap: 12px;
      padding: 14px 16px;
      background: var(--card);
      border-radius: 12px;
      border: 1px solid var(--border);
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .review-avatar {
      width: 42px;
      height: 42px;
      border-radius: 50%;
      background: #f0f0f0;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      flex-shrink: 0;
    }
    .review-main {
      flex: 1;
    }
    .review-head {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 4px;
    }
    .review-name {
      font-weight: 700;
      color: var(--brand);
      font-size: 14px;
    }
    .review-stars {
      color: #ffc107;
      font-size: 14px;
      font-weight: 600;
    }
    .review-text {
      font-size: 14px;
      color: var(--fg-muted);
      margin-bottom: 2px;
    }
    .review-date {
      font-size: 12px;
      color: var(--muted);
    }

    /* ====== POPUP OVERLAY ====== */
    .popup-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.5);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 10000;
      padding: 20px;
    }
    .popup-overlay.active {
      display: flex;
    }
    .popup {
      background: #fff;
      border-radius: 14px;
      padding: 20px 22px;
      max-width: 480px;
      width: 100%;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 14px 40px rgba(0,0,0,0.22);
    }
    .popup h3 {
      margin: 0 0 14px;
      color: var(--brand);
      text-align: center;
    }
    .form-group {
      margin-bottom: 12px;
    }
    .form-group label {
      display: block;
      font-size: 13px;
      margin-bottom: 4px;
      font-weight: 600;
      color: #333;
    }
    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 9px 10px;
      border-radius: 8px;
      border: 1px solid var(--border);
      font-size: 14px;
    }
    .form-group textarea {
      resize: vertical;
      min-height: 70px;
    }
    .popup-actions {
      display: flex;
      justify-content: flex-end;
      gap: 8px;
      margin-top: 14px;
    }
    .btn-cancel {
      background: #f5f5f5;
      color: #555;
    }
    .btn-save {
      background: var(--brand);
      color: #fff;
    }

    .file-input {
      padding: 8px 10px;
      border-radius: 8px;
      border: 1px dashed var(--border);
      background: #fafafa;
      font-size: 13px;
    }

    @media (max-width: 768px) {
      .posts-container {
        grid-template-columns: 1fr;
      }
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
    <a href="home-designer.php">Home</a>
    <a href="designer-profile.php" class="active">Profile</a>
    <a href="Request.php">Requests</a>
    <a href="designer-timeline.php">Timeline</a>
    <a href="meeting.html">Meeting</a>
    <a href="settings.html">Settings</a>
    <hr>
    <a href="logout.php" class="logout">Logout</a>
  </div>
  <div id="overlay"></div>

  <!-- Main Content -->
  <main class="container">

    <!-- PROFILE HEADER -->
    <section class="profile-header">
      <img
        id="designerLogo"
        src="<?php echo '../' . htmlspecialchars($designer['profilePicture']); ?>"
        alt="Designer Avatar">
      <h2 class="designer-name" id="designerName">
        <?php echo htmlspecialchars($designer['name']); ?>
      </h2>
      <p class="designer-specialty" id="designerSpecialty">
        <?php echo htmlspecialchars($designer['specialty']); ?>
      </p>
      <p class="designer-bio" id="designerBio">
        <?php echo nl2br(htmlspecialchars($designer['bio'])); ?>
      </p>

      <div class="stats-row">
        <span id="reviewsCount"><?php echo (int)$reviewsCount; ?></span> Reviews
      </div>

      <div class="profile-actions">
        <button class="primary-btn" id="editProfileBtn">Edit Profile</button>
        <button class="secondary-btn" id="uploadDesignBtn">+ Add Design</button>
      </div>
    </section>

    <!-- TABS -->
    <section class="tabs-row">
      <div class="tabs">
        <button class="tab-btn active" data-tab="designs">Designs</button>
        <button class="tab-btn" data-tab="reviews">Reviews</button>
      </div>
    </section>

    <!-- DESIGNS TAB -->
    <section id="designsSection">
      <div class="posts-container" id="postsContainer">
        <?php while ($d = $designs->fetch_assoc()): ?>
          <article class="post-card" id="post-<?php echo $d['designID']; ?>">
            <div class="post-controls">
              <!-- Edit button -->
              <button
                class="icon-btn edit"
                type="button"
                onclick="openEditDesign(
                  <?php echo (int)$d['designID']; ?>,
                  <?php echo json_encode($d['title']); ?>,
                  <?php echo json_encode($d['description']); ?>
                )"
                aria-label="Edit design">
                <svg viewBox="0 0 24 24" fill="currentColor">
                  <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"/>
                  <path d="M20.71 7.04l-2.34-2.34a1 1 0 0 0-1.41 0L15.13 6.53l3.75 3.75 2.41-2.41a1 1 0 0 0 0-1.41z"/>
                </svg>
              </button>
              <!-- Delete button -->
              <button
                class="icon-btn delete"
                type="button"
                onclick="deleteDesign(<?php echo (int)$d['designID']; ?>)"
                aria-label="Delete design">
                <svg viewBox="0 0 24 24" fill="currentColor">
                  <path d="M6 19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                </svg>
              </button>
            </div>

            <img
              src="<?php echo '../' . htmlspecialchars($d['image']); ?>"
              alt="<?php echo htmlspecialchars($d['title']); ?>">

            <div class="post-title">
              <?php echo htmlspecialchars($d['title']); ?>
            </div>
            <div class="post-desc">
              <?php echo htmlspecialchars($d['description']); ?>
            </div>
            <div class="post-meta">
              Uploaded on <?php echo htmlspecialchars($d['uploadDate']); ?>
            </div>
          </article>
        <?php endwhile; ?>
      </div>
    </section>

    <!-- REVIEWS TAB -->
    <section id="reviewsSection" class="reviews-section">
      <h3 class="reviews-title">Reviews</h3>
      <div class="reviews-list">
        <?php if ($reviewsCount === 0): ?>
          <p style="color: var(--muted); font-size: 14px;">
            No reviews yet.
          </p>
        <?php else: ?>
          <?php while ($r = $reviews->fetch_assoc()): ?>
            <article class="review-card">
              <div class="review-avatar">
                <?php echo strtoupper(substr($r['clientName'], 0, 1)); ?>
              </div>
              <div class="review-main">
                <div class="review-head">
                  <span class="review-name">
                    <?php echo htmlspecialchars($r['clientName']); ?>
                  </span>
                  <span class="review-stars">
                    ★ <?php echo htmlspecialchars($r['rating']); ?>
                  </span>
                </div>
                <div class="review-text">
                  <?php echo htmlspecialchars($r['comment']); ?>
                </div>
                <div class="review-date">
                  <?php echo htmlspecialchars($r['reviewDate']); ?>
                </div>
              </div>
            </article>
          <?php endwhile; ?>
        <?php endif; ?>
      </div>
    </section>

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

  <!-- ========= POPUPS ========= -->

  <!-- Upload Design Popup -->
  <div class="popup-overlay" id="uploadPopup">
    <div class="popup">
      <h3>Add New Design</h3>
      <div class="form-group">
        <label for="uploadTitle">Title</label>
        <input type="text" id="uploadTitle" placeholder="Design title">
      </div>
      <div class="form-group">
        <label for="uploadDesc">Description</label>
        <textarea id="uploadDesc" placeholder="Short description"></textarea>
      </div>
      <div class="form-group">
        <label for="uploadImage">Design Image</label>
        <input type="file" id="uploadImage" class="file-input" accept="image/*">
      </div>
      <div class="popup-actions">
        <button type="button" class="secondary-btn btn-cancel" id="cancelUploadBtn">Cancel</button>
        <button type="button" class="primary-btn btn-save" id="confirmUploadBtn">Add Design</button>
      </div>
    </div>
  </div>

  <!-- Edit Design Popup -->
  <div class="popup-overlay" id="editDesignPopup">
    <div class="popup">
      <h3>Edit Design</h3>
      <input type="hidden" id="editDesignID">
      <div class="form-group">
        <label for="editTitle">Title</label>
        <input type="text" id="editTitle">
      </div>
      <div class="form-group">
        <label for="editDesc">Description</label>
        <textarea id="editDesc"></textarea>
      </div>
      <div class="popup-actions">
        <button type="button" class="secondary-btn btn-cancel" id="cancelEditDesignBtn">Cancel</button>
        <button type="button" class="primary-btn btn-save" id="saveEditDesignBtn">Save Changes</button>
      </div>
    </div>
  </div>

  <!-- Edit Profile Popup -->
  <div class="popup-overlay" id="editProfilePopup">
    <div class="popup">
      <h3>Edit Profile</h3>
      <div class="form-group">
        <label for="newSpecialty">Specialty</label>
        <input type="text" id="newSpecialty" value="<?php echo htmlspecialchars($designer['specialty']); ?>">
      </div>
      <div class="form-group">
        <label for="newBio">Bio</label>
        <textarea id="newBio"><?php echo htmlspecialchars($designer['bio']); ?></textarea>
      </div>
      <div class="form-group">
        <label for="newProfilePic">Profile Image</label>
        <input type="file" id="newProfilePic" class="file-input" accept="image/*">
      </div>
      <div class="popup-actions">
        <button type="button" class="secondary-btn btn-cancel" id="cancelProfileBtn">Cancel</button>
        <button type="button" class="primary-btn btn-save" id="saveProfileBtn">Save Profile</button>
      </div>
    </div>
  </div>

  <!-- JS -->
  <script src="../js/sidebar.js"></script>
  <script>
    const postsContainer     = document.getElementById('postsContainer');
    const designsSection     = document.getElementById('designsSection');
    const reviewsSection     = document.getElementById('reviewsSection');
    const tabs               = document.querySelectorAll('.tab-btn');

    // Popups
    const uploadPopup        = document.getElementById('uploadPopup');
    const editDesignPopup    = document.getElementById('editDesignPopup');
    const editProfilePopup   = document.getElementById('editProfilePopup');

    // Buttons
    const uploadDesignBtn    = document.getElementById('uploadDesignBtn');
    const editProfileBtn     = document.getElementById('editProfileBtn');
    const cancelUploadBtn    = document.getElementById('cancelUploadBtn');
    const confirmUploadBtn   = document.getElementById('confirmUploadBtn');
    const cancelEditDesignBtn= document.getElementById('cancelEditDesignBtn');
    const saveEditDesignBtn  = document.getElementById('saveEditDesignBtn');
    const cancelProfileBtn   = document.getElementById('cancelProfileBtn');
    const saveProfileBtn     = document.getElementById('saveProfileBtn');

    // Tab switching
    tabs.forEach(btn => {
      btn.addEventListener('click', () => {
        tabs.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const tab = btn.dataset.tab;
        if (tab === 'designs') {
          designsSection.style.display = '';
          reviewsSection.classList.remove('active');
        } else {
          designsSection.style.display = 'none';
          reviewsSection.classList.add('active');
        }
      });
    });

    // Popup helpers
    function openPopup(p) { p.classList.add('active'); }
    function closePopup(p) { p.classList.remove('active'); }

    uploadDesignBtn.addEventListener('click', () => openPopup(uploadPopup));
    editProfileBtn.addEventListener('click', () => openPopup(editProfilePopup));

    cancelUploadBtn.addEventListener('click', () => closePopup(uploadPopup));
    cancelEditDesignBtn.addEventListener('click', () => closePopup(editDesignPopup));
    cancelProfileBtn.addEventListener('click', () => closePopup(editProfilePopup));

    // Close popup when clicking outside
    [uploadPopup, editDesignPopup, editProfilePopup].forEach(p => {
      p.addEventListener('click', (e) => {
        if (e.target === p) closePopup(p);
      });
    });

    // ===== DELETE DESIGN (NO CONFIRM MSG, NO RELOAD) =====
    function deleteDesign(id) {
      fetch('deleteDesign.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'designID=' + encodeURIComponent(id)
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          const card = document.getElementById('post-' + id);
          if (card) card.remove();
        }
        // No alert, as requested
      })
      .catch(console.error);
    }

    // ===== OPEN EDIT DESIGN POPUP =====
    function openEditDesign(id, title, desc) {
      document.getElementById('editDesignID').value = id;
      document.getElementById('editTitle').value = title;
      document.getElementById('editDesc').value = desc;
      openPopup(editDesignPopup);
    }

    // ===== SAVE EDITED DESIGN (AJAX, LIVE UPDATE) =====
    saveEditDesignBtn.addEventListener('click', () => {
      const id    = document.getElementById('editDesignID').value;
      const title = document.getElementById('editTitle').value.trim();
      const desc  = document.getElementById('editDesc').value.trim();

      if (!title || !desc) {
        // Tiny validation – no alert requested for success, but
        // for errors it's okay to show
        alert('Please fill both title and description.');
        return;
      }

      const body = `designID=${encodeURIComponent(id)}&title=${encodeURIComponent(title)}&description=${encodeURIComponent(desc)}`;

      fetch('editDesign.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          const card = document.getElementById('post-' + id);
          if (card) {
            const t = card.querySelector('.post-title');
            const d = card.querySelector('.post-desc');
            if (t) t.textContent = title;
            if (d) d.textContent = desc;
          }
          closePopup(editDesignPopup);
        }
      })
      .catch(console.error);
    });

    // ===== UPLOAD NEW DESIGN (AJAX, APPEND CARD WITHOUT RELOAD) =====
    confirmUploadBtn.addEventListener('click', () => {
      const title = document.getElementById('uploadTitle').value.trim();
      const desc  = document.getElementById('uploadDesc').value.trim();
      const file  = document.getElementById('uploadImage').files[0];

      if (!title || !desc || !file) {
        alert('Please fill title, description and select an image.');
        return;
      }

      const form = new FormData();
      form.append('title', title);
      form.append('description', desc);
      form.append('image', file);

      fetch('uploadDesign.php', {
        method: 'POST',
        body: form
      })
      .then(r => r.json())
      .then(data => {
        if (data.success && data.design) {
          const d = data.design;
          // Create new card DOM
          const article = document.createElement('article');
          article.className = 'post-card';
          article.id = 'post-' + d.designID;
          article.innerHTML = `
            <div class="post-controls">
              <button class="icon-btn edit" type="button"
                onclick="openEditDesign(${d.designID}, ${JSON.stringify(d.title)}, ${JSON.stringify(d.description)})"
                aria-label="Edit design">
                <svg viewBox="0 0 24 24" fill="currentColor">
                  <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"/>
                  <path d="M20.71 7.04l-2.34-2.34a1 1 0 0 0-1.41 0L15.13 6.53l3.75 3.75 2.41-2.41a1 1 0 0 0 0-1.41z"/>
                </svg>
              </button>
              <button class="icon-btn delete" type="button"
                onclick="deleteDesign(${d.designID})"
                aria-label="Delete design">
                <svg viewBox="0 0 24 24" fill="currentColor">
                  <path d="M6 19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                </svg>
              </button>
            </div>
            <img src="${d.imageUrl}" alt="${d.title}">
            <div class="post-title">${d.title}</div>
            <div class="post-desc">${d.description}</div>
            <div class="post-meta">Uploaded on ${d.uploadDate}</div>
          `;
          postsContainer.prepend(article);

          // Reset form and close popup
          document.getElementById('uploadTitle').value = '';
          document.getElementById('uploadDesc').value = '';
          document.getElementById('uploadImage').value = '';
          closePopup(uploadPopup);
        }
      })
      .catch(console.error);
    });

    // ===== SAVE PROFILE (AJAX, LIVE UPDATE) =====
    saveProfileBtn.addEventListener('click', () => {
      const specialty = document.getElementById('newSpecialty').value.trim();
      const bio       = document.getElementById('newBio').value.trim();
      const pic       = document.getElementById('newProfilePic').files[0];

      const form = new FormData();
      form.append('specialty', specialty);
      form.append('bio', bio);
      if (pic) form.append('image', pic);

      fetch('saveProfile.php', {
        method: 'POST',
        body: form
      })
      .then(r => r.json())
      .then(data => {
        if (data.success && data.profile) {
          const p = data.profile;
          document.getElementById('designerName').textContent      = p.name;
          document.getElementById('designerSpecialty').textContent = p.specialty;
          document.getElementById('designerBio').textContent       = p.bio;
          if (p.profilePictureUrl) {
            document.getElementById('designerLogo').src = p.profilePictureUrl;
          }
          if (typeof p.reviewsCount !== 'undefined') {
            document.getElementById('reviewsCount').textContent = p.reviewsCount;
          }
          closePopup(editProfilePopup);
        }
      })
      .catch(console.error);
    });
  </script>
</body>
</html>
