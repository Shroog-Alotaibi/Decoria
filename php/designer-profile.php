<?php
require_once "config.php";
session_start();


check_login('Designer');

$designerID = $_SESSION['user_id'];


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
    $designer = [
        "name"           => "Designer Name",
        "specialty"      => "",
        "profilePicture" => "photo/defaultAvatar.png",
        "linkedinURL"    => "",
        "bio"            => ""
    ];
}


$sql = "SELECT designID, title, description, image, uploadDate
        FROM design
        WHERE designerID = ?
        ORDER BY uploadDate DESC, designID DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $designerID);
$stmt->execute();
$designs = $stmt->get_result();


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

  <link rel="stylesheet" href="../css/decoria.css" />

  <style>
    
    .profile-header { text-align:center;margin:40px auto 30px;max-width:700px; }
    .profile-header img {
      width:140px;height:140px;border-radius:50%;
      border:3px solid var(--brand);object-fit:cover;
      box-shadow:0 4px 15px rgba(0,0,0,0.12);
    }
    .designer-name { font-size:26px;font-weight:800;color:var(--brand);margin-top:15px;}
    .designer-specialty { color:var(--muted);font-size:15px;margin-top:4px;}
    .designer-bio { margin-top:12px;color:var(--fg-muted);font-size:14px;line-height:1.5;}
    .stats-row { margin-top:15px;font-size:14px;color:var(--muted);}
    .profile-actions {margin-top:20px;display:flex;justify-content:center;gap:10px;}

    
    .primary-btn,.secondary-btn {
      padding:8px 18px;border-radius:999px;font-weight:600;
      cursor:pointer;transition:0.2s;border:none;
    }
    .primary-btn { background:var(--brand);color:white; }
    .primary-btn:hover { background:var(--primary-btn-hover);transform:translateY(-1px);}
    .secondary-btn { background:#f3f3f3;color:#444; }
    .secondary-btn:hover { background:#e7e7e7;transform:translateY(-1px);}

    
    .tabs-row {margin-top:40px;border-bottom:1px solid var(--border);display:flex;}
    .tabs { display:flex;gap:10px; }
    .tab-btn {
      background:none;border:none;padding:10px 18px;font-size:15px;
      font-weight:600;cursor:pointer;color:var(--muted);
      border-bottom:3px solid transparent;
    }
    .tab-btn.active { color:var(--brand);border-bottom-color:var(--brand); }

    
    .posts-container { margin-top:25px;display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px; }
    .post-card {
      background:var(--card);border-radius:14px;padding:12px;border:1px solid var(--border);
      position:relative;transition:.2s;box-shadow:0 3px 12px rgba(0,0,0,0.06);
    }
    .post-card:hover { transform:translateY(-3px);box-shadow:0 8px 20px rgba(0,0,0,0.1);}
    .post-card img { width:100%;height:180px;object-fit:cover;border-radius:10px; }
    .post-title { margin-top:10px;font-weight:700;color:var(--brand); }
    .post-desc { font-size:14px;margin-top:5px;color:var(--fg-muted); }
    .post-meta { margin-top:6px;font-size:12px;color:var(--muted); }

    
    .post-controls { position:absolute;top:10px;right:10px;display:flex;gap:6px; }
    .icon-btn {
      width:32px;height:32px;border-radius:50%;border:none;display:flex;
      justify-content:center;align-items:center;background:white;
      box-shadow:0 2px 8px rgba(0,0,0,0.15);cursor:pointer;transition:.2s;
    }
    .icon-btn.edit { color:#389e0d;background:#f6ffed; }
    .icon-btn.edit:hover { background:#d9f7be;transform:scale(1.05);}
    .icon-btn.delete { color:#cf1322;background:#fff1f0; }
    .icon-btn.delete:hover { background:#ffccc7;transform:scale(1.05);}

    
    .reviews-section { margin-top:25px;display:none; }
    .reviews-section.active { display:block; }
    .review-card {
      display:flex;gap:12px;background:var(--card);padding:14px;border-radius:12px;
      border:1px solid var(--border);box-shadow:0 2px 8px rgba(0,0,0,0.05);
    }
    .review-avatar {
      width:42px;height:42px;border-radius:50%;background:#f0f0f0;
      display:flex;align-items:center;justify-content:center;font-size:18px;
    }

    
    .popup-overlay {
      position:fixed;inset:0;background:rgba(0,0,0,0.5);
      display:none;align-items:center;justify-content:center;z-index:9999;
    }
    .popup-overlay.active { display:flex; }
    .popup {
      background:white;border-radius:14px;padding:20px;width:100%;max-width:480px;
      max-height:90vh;overflow-y:auto;box-shadow:0 14px 40px rgba(0,0,0,0.22);
    }

    .form-group { margin-bottom:12px; }
    .form-group label { font-size:13px;font-weight:600;margin-bottom:4px;display:block; }
    .form-group input,.form-group textarea {
      width:100%;padding:9px;border-radius:8px;border:1px solid var(--border);
      font-size:14px;
    }
    .popup-actions { display:flex;justify-content:flex-end;gap:8px;margin-top:14px;}

    .file-input { padding:8px;border:1px dashed var(--border);background:#fafafa; }
  </style>
</head>

<body>

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
    <?php include "menu.php"; ?>
  </div>
  <div id="overlay"></div>


  <main class="container">

   
    <section class="profile-header">
      <img id="designerLogo"
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
        <span id="reviewsCount"><?php echo $reviewsCount; ?></span> Reviews
      </div>

      <div class="profile-actions">
        <button class="primary-btn" id="editProfileBtn">Edit Profile</button>
        <button class="secondary-btn" id="uploadDesignBtn">+ Add Design</button>
      </div>
    </section>

   
    <section class="tabs-row">
      <div class="tabs">
        <button class="tab-btn active" data-tab="designs">Designs</button>
        <button class="tab-btn" data-tab="reviews">Reviews</button>
      </div>
    </section>

  
    <section id="designsSection">
      <div class="posts-container" id="postsContainer">
        <?php while ($d = $designs->fetch_assoc()): ?>
          <article class="post-card" id="post-<?php echo $d['designID']; ?>">

            <div class="post-controls">
              <button class="icon-btn edit"
                onclick="openEditDesign(
                  <?php echo $d['designID']; ?>,
                  '<?php echo addslashes($d['title']); ?>',
                  '<?php echo addslashes($d['description']); ?>'
                )">✎</button>

              <button class="icon-btn delete"
                onclick="deleteDesign(<?php echo $d['designID']; ?>)">🗑</button>
            </div>

            <img src="../<?php echo $d['image']; ?>">

            <div class="post-title"><?php echo htmlspecialchars($d['title']); ?></div>
            <div class="post-desc"><?php echo htmlspecialchars($d['description']); ?></div>
            <div class="post-meta">Uploaded on <?php echo $d['uploadDate']; ?></div>

          </article>
        <?php endwhile; ?>
      </div>
    </section>


    <section id="reviewsSection" class="reviews-section">
      <h3 style="color:var(--brand)">Reviews</h3>
      <div class="reviews-list">

        <?php if ($reviewsCount == 0): ?>
          <p style="color:var(--muted)">No reviews yet.</p>

        <?php else: ?>
          <?php while ($r = $reviews->fetch_assoc()): ?>
            <article class="review-card">
              <div class="review-avatar">
                <?php echo strtoupper($r['clientName'][0]); ?>
              </div>

              <div class="review-main">
                <div class="review-head">
                  <span class="review-name"><?php echo htmlspecialchars($r['clientName']); ?></span>
                  <span class="review-stars">★ <?php echo $r['rating']; ?></span>
                </div>

                <div class="review-text"><?php echo htmlspecialchars($r['comment']); ?></div>
                <div class="review-date"><?php echo $r['reviewDate']; ?></div>
              </div>
            </article>
          <?php endwhile; ?>

        <?php endif; ?>

      </div>
    </section>

  </main>


  <footer>
    <div class="footer-content">
      <p class="footer-text">© 2025 DECORIA — All rights reserved</p>
      <img src="../photo/darlfooter.jpeg" class="footer-image">
    </div>
  </footer>


  <div class="popup-overlay" id="uploadPopup">
    <div class="popup">
      <h3>Add New Design</h3>

      <div class="form-group">
        <label>Title</label>
        <input type="text" id="uploadTitle">
      </div>

      <div class="form-group">
        <label>Description</label>
        <textarea id="uploadDesc"></textarea>
      </div>

      <div class="form-group">
        <label>Design Image</label>
        <input type="file" id="uploadImage" class="file-input" accept="image/*">
      </div>

      <div class="popup-actions">
        <button class="secondary-btn" onclick="closePopup(uploadPopup)">Cancel</button>
        <button class="primary-btn" id="confirmUploadBtn">Add Design</button>
      </div>

    </div>
  </div>

  <div class="popup-overlay" id="editDesignPopup">
    <div class="popup">
      <h3>Edit Design</h3>
      <input type="hidden" id="editDesignID">

      <div class="form-group">
        <label>Title</label>
        <input type="text" id="editTitle">
      </div>

      <div class="form-group">
        <label>Description</label>
        <textarea id="editDesc"></textarea>
      </div>

      <div class="popup-actions">
        <button class="secondary-btn" onclick="closePopup(editDesignPopup)">Cancel</button>
        <button class="primary-btn" id="saveEditDesignBtn">Save</button>
      </div>
    </div>
  </div>

  <div class="popup-overlay" id="editProfilePopup">
    <div class="popup">
      <h3>Edit Profile</h3>

      <div class="form-group">
        <label>Specialty</label>
        <input type="text" id="newSpecialty" value="<?php echo addslashes($designer['specialty']); ?>">
      </div>

      <div class="form-group">
        <label>Bio</label>
        <textarea id="newBio"><?php echo addslashes($designer['bio']); ?></textarea>
      </div>

      <div class="form-group">
        <label>Profile Picture</label>
        <input type="file" id="newProfilePic" class="file-input">
      </div>

      <div class="popup-actions">
        <button class="secondary-btn" onclick="closePopup(editProfilePopup)">Cancel</button>
        <button class="primary-btn" id="saveProfileBtn">Save</button>
      </div>

    </div>
  </div>

  
  <script src="../js/sidebar.js"></script>

  <script>
    
    function escapeJS(str) {
      return str.replace(/'/g, "\\'");
    }

    /* Popup Controls */
    function openPopup(p) { p.classList.add("active"); }
    function closePopup(p) { p.classList.remove("active"); }

    const uploadPopup = document.getElementById("uploadPopup");
    const editDesignPopup = document.getElementById("editDesignPopup");
    const editProfilePopup = document.getElementById("editProfilePopup");

    document.getElementById("uploadDesignBtn").onclick = () => openPopup(uploadPopup);
    document.getElementById("editProfileBtn").onclick = () => openPopup(editProfilePopup);

    
    document.querySelectorAll(".tab-btn").forEach(btn => {
      btn.addEventListener("click", () => {
        document.querySelectorAll(".tab-btn").forEach(b => b.classList.remove("active"));
        btn.classList.add("active");

        if (btn.dataset.tab === "designs") {
          document.getElementById("designsSection").style.display = "";
          document.getElementById("reviewsSection").classList.remove("active");
        } else {
          document.getElementById("designsSection").style.display = "none";
          document.getElementById("reviewsSection").classList.add("active");
        }
      });
    });

    
    function deleteDesign(id) {
      fetch("deleteDesign.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "designID=" + id
      })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          document.getElementById("post-" + id).remove();
        }
      });
    }

    
    function openEditDesign(id, title, desc) {
      document.getElementById("editDesignID").value = id;
      document.getElementById("editTitle").value = title;
      document.getElementById("editDesc").value = desc;
      openPopup(editDesignPopup);
    }

    document.getElementById("saveEditDesignBtn").onclick = () => {
      const id = document.getElementById("editDesignID").value;
      const title = document.getElementById("editTitle").value;
      const desc = document.getElementById("editDesc").value;

      fetch("editDesign.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "designID=" + id + "&title=" + encodeURIComponent(title) + "&description=" + encodeURIComponent(desc)
      })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          const card = document.getElementById("post-" + id);
          card.querySelector(".post-title").textContent = title;
          card.querySelector(".post-desc").textContent = desc;
          closePopup(editDesignPopup);
        }
      });
    };

    
    document.getElementById("confirmUploadBtn").onclick = () => {
      const title = document.getElementById("uploadTitle").value.trim();
      const desc  = document.getElementById("uploadDesc").value.trim();
      const file  = document.getElementById("uploadImage").files[0];

      if (!title || !desc || !file) {
        alert("Fill all fields.");
        return;
      }

      const form = new FormData();
      form.append("title", title);
      form.append("description", desc);
      form.append("image", file);

      fetch("uploadDesign.php", { method: "POST", body: form })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          const d = res.design;

          const safeTitle = escapeJS(d.title);
          const safeDesc = escapeJS(d.description);

          const article = document.createElement("article");
          article.className = "post-card";
          article.id = "post-" + d.designID;

          article.innerHTML = `
            <div class="post-controls">
              <button class="icon-btn edit"
                onclick="openEditDesign(${d.designID}, '${safeTitle}', '${safeDesc}')">
                ✎
              </button>
              <button class="icon-btn delete"
                onclick="deleteDesign(${d.designID})">🗑</button>
            </div>

            <img src="${d.imageUrl}">
            <div class="post-title">${d.title}</div>
            <div class="post-desc">${d.description}</div>
            <div class="post-meta">Uploaded on ${d.uploadDate}</div>
          `;

          document.getElementById("postsContainer").prepend(article);

          closePopup(uploadPopup);
        }
      });
    };

    
    document.getElementById("saveProfileBtn").onclick = () => {
      const specialty = document.getElementById("newSpecialty").value;
      const bio = document.getElementById("newBio").value;
      const pic = document.getElementById("newProfilePic").files[0];

      const form = new FormData();
      form.append("specialty", specialty);
      form.append("bio", bio);
      if (pic) form.append("image", pic);

      fetch("saveProfile.php", { method: "POST", body: form })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          document.getElementById("designerSpecialty").textContent = res.profile.specialty;
          document.getElementById("designerBio").textContent = res.profile.bio;
          if (res.profile.profilePictureUrl) {
            document.getElementById("designerLogo").src = res.profile.profilePictureUrl;
          }
          closePopup(editProfilePopup);
        }
      });
    };
  </script>

</body>
</html>
