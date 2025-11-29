const byId = id => document.getElementById(id) || null;
const DESIGNER_DB_ID = window.DESIGNER_ID_FROM_PHP;

// ================= PROFILE =================
async function loadDesignerProfile() {
  try {
    const res = await fetch(`../php/getDesignerProfile.php?designerID=${DESIGNER_DB_ID}`);
    const data = await res.json();
    if (data.status !== "success") return;

    const logo = byId("designerLogo");
    const nameEl = byId("designerName");
    const roleEl = byId("designerRole");
    const linkWrap = byId("linkWrap");
    const link = byId("profileLink");

    logo.src = data.profilePicture || "../photo/placeholder.png";
    nameEl.textContent = data.name;

    const specialty = data.specialty || "";
    const city = data.city || "Riyadh";
    const bio = data.bio || "";

    const specText = specialty ? specialty : "";
    const roleLine = specText ? `${specText} – ${city}` : `Interior Designer – ${city}`;

    roleEl.innerHTML = `${roleLine}<br><span class="banner-bio">${bio}</span>`;

    if (data.linkedin) {
      linkWrap.hidden = false;
      link.href = data.linkedin;
    } else {
      linkWrap.hidden = true;
    }

    byId("designerSpecialty").value = specialty;
    byId("designerBio").value = bio;

  } catch (err) {
    console.error("Failed to load profile", err);
  }
}

// ================= DESIGNS =================
function renderDesigns(list) {
  const host = byId("designsSection");
  if (!list.length) {
    host.innerHTML = "<p>No designs yet.</p>";
    return;
  }

  host.innerHTML = list.map(d => `
    <article class="card" data-design-id="${d.designID}">
      <div class="card-actions">
        <button class="action-btn delete-btn" data-design-id="${d.designID}" aria-label="Delete">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
          </svg>
        </button>
        <button class="action-btn reorder-btn" data-design-id="${d.designID}" aria-label="Edit">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
          </svg>
        </button>
      </div>
      <img src="${d.image}" alt="${d.title}">
      <div class="card-label">${d.title}</div>
      <p class="card-desc">${d.description}</p>
    </article>
  `).join("");
}

async function loadDesignsFromDB() {
  try {
    const res = await fetch(`../php/getDesignerDesigns.php?designerID=${DESIGNER_DB_ID}`);
    const data = await res.json();
    renderDesigns(data);
  } catch (err) {
    console.error("Failed to load designs", err);
  }
}

// ================= REVIEWS =================
function renderReviews(list) {
  const host = byId("reviewsList");
  const countEl = byId("reviewsCount");
  countEl.textContent = list.length;

  if (!list.length) {
    host.innerHTML = "<p>No reviews yet.</p>";
    return;
  }

  host.innerHTML = list.map(rv => `
    <div class="review-card">
      <div class="review-avatar-icon">
        <svg width="40" height="40">
          <circle cx="20" cy="20" r="18" fill="#e0e0e0"/>
        </svg>
      </div>
      <div>
        <div class="review-head">
          <span class="review-name">${rv.name}</span>
          <span class="stars">★ ${parseFloat(rv.rating).toFixed(1)}</span>
        </div>
        <p>${rv.text}</p>
      </div>
    </div>
  `).join("");
}

async function loadReviewsFromDB() {
  try {
    const res = await fetch(`../php/get_reviews.php?designerID=${DESIGNER_DB_ID}`);
    const data = await res.json();
    renderReviews(data);
  } catch (err) {
    console.error("Failed to load reviews", err);
  }
}

// ================= TABS =================
function initTabs() {
  const tabs = document.querySelectorAll(".tabs .tab");
  const designs = byId("designsSection");
  const reviews = byId("reviewsSection");

  tabs.forEach(tab => {
    tab.addEventListener("click", () => {
      tabs.forEach(t => t.classList.remove("is-active"));
      tab.classList.add("is-active");

      if (tab.dataset.tab === "designs") {
        designs.classList.remove("is-hidden");
        reviews.classList.add("is-hidden");
      } else {
        reviews.classList.remove("is-hidden");
        designs.classList.add("is-hidden");
      }
    });
  });
}

// ================= EDIT MODE & MODALS =================
let currentEditDesignId = null;

function initEditMode() {
  const editBtn = byId("editBtn");
  const designs = byId("designsSection");
  const profileBanner = byId("profileBanner");
  const addBtn = byId("addDesignBtn");

  editBtn.addEventListener("click", () => {
    const active = !designs.classList.contains("edit-mode");
    designs.classList.toggle("edit-mode", active);
    profileBanner.classList.toggle("profile-edit-mode", active);
    document.body.classList.toggle("edit-mode", active);
    editBtn.classList.toggle("active", active);
    editBtn.textContent = active ? "Exit Edit Mode" : "Edit Portfolio";
    addBtn.style.display = active ? "flex" : "none";
  });

  addBtn.addEventListener("click", () => {
    byId("addDesignModal").classList.add("active");
  });

  byId("editProfileBtn").addEventListener("click", () => {
    byId("editProfileModal").classList.add("active");
  });

  byId("designerLogo").addEventListener("click", () => {
    if (profileBanner.classList.contains("profile-edit-mode")) {
      byId("editProfileModal").classList.add("active");
    }
  });
}

function initModals() {
  const addModal = byId("addDesignModal");
  const editModal = byId("editDesignModal");
  const profileModal = byId("editProfileModal");

  const cancelDesignBtn = byId("cancelDesignBtn");
  const cancelEditDesignBtn = byId("cancelEditDesignBtn");
  const cancelProfileBtn = byId("cancelProfileBtn");

  cancelDesignBtn.addEventListener("click", () => {
    addModal.classList.remove("active");
    byId("addDesignForm").reset();
    byId("imagePreview").style.display = "none";
  });

  cancelEditDesignBtn.addEventListener("click", () => {
    editModal.classList.remove("active");
    byId("editDesignForm").reset();
    currentEditDesignId = null;
  });

  cancelProfileBtn.addEventListener("click", () => {
    profileModal.classList.remove("active");
    byId("editProfileForm").reset();
    byId("profilePreview").style.display = "none";
  });

  [addModal, editModal, profileModal].forEach(modal => {
    modal.addEventListener("click", e => {
      if (e.target === modal) {
        modal.classList.remove("active");
        const form = modal.querySelector("form");
        if (form) form.reset();
        const preview = modal.querySelector('[id$="Preview"]');
        if (preview) preview.style.display = "none";
      }
    });
  });

  // Design image preview
  const designImgUpload = byId("designImageUpload");
  const designImgInput = byId("designImage");
  const previewWrap = byId("imagePreview");
  const previewImg = byId("previewImg");

  designImgUpload.addEventListener("click", () => designImgInput.click());
  designImgInput.addEventListener("change", e => {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = ev => {
      previewImg.src = ev.target.result;
      previewWrap.style.display = "block";
    };
    reader.readAsDataURL(file);
  });

  // Profile image preview
  const profileImgUpload = byId("profileImageUpload");
  const profileImgInput = byId("profileImage");
  const profilePreview = byId("profilePreview");
  const profilePreviewImg = byId("profilePreviewImg");

  profileImgUpload.addEventListener("click", () => profileImgInput.click());
  profileImgInput.addEventListener("change", e => {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = ev => {
      profilePreviewImg.src = ev.target.result;
      profilePreview.style.display = "block";
    };
    reader.readAsDataURL(file);
  });
}

// ================= ADD DESIGN =================
function initAddDesignForm() {
  const form = byId("addDesignForm");

  form.addEventListener("submit", async e => {
    e.preventDefault();

    const title = byId("designTitle").value.trim();
    const description = byId("designDescription").value.trim();
    const file = byId("designImage").files[0];

    if (!file || !title || !description) return;

    const fd = new FormData();
    fd.append("designerID", DESIGNER_DB_ID);
    fd.append("title", title);
    fd.append("description", description);
    fd.append("image", file);

    try {
      const res = await fetch("../php/uploadDesign.php", {
        method: "POST",
        body: fd
      });
      const data = await res.json();
      if (data.status !== "success") return;

      const host = byId("designsSection");
      const card = document.createElement("article");
      card.className = "card";
      card.dataset.designId = data.designID;
      card.innerHTML = `
        <div class="card-actions">
          <button class="action-btn delete-btn" data-design-id="${data.designID}" aria-label="Delete">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
              <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
            </svg>
          </button>
          <button class="action-btn reorder-btn" data-design-id="${data.designID}" aria-label="Edit">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
              <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
            </svg>
          </button>
        </div>
        <img src="${data.image}" alt="${data.title}">
        <div class="card-label">${data.title}</div>
        <p class="card-desc">${data.description}</p>
      `;
      host.prepend(card);

      byId("addDesignModal").classList.remove("active");
      form.reset();
      byId("imagePreview").style.display = "none";

    } catch (err) {
      console.error("Upload failed", err);
    }
  });
}

// ================= EDIT & DELETE DESIGN =================
function initDesignActions() {
  // DELETE
  document.addEventListener("click", async e => {
    const btn = e.target.closest(".delete-btn");
    if (!btn) return;

    const card = btn.closest(".card");
    const id = card.dataset.designId;

    try {
      await fetch("../php/deleteDesign.php", {
        method: "POST",
        headers: { "Content-Type":"application/x-www-form-urlencoded" },
        body: "designID=" + encodeURIComponent(id)
      });
      card.remove();
    } catch (err) {
      console.error("Delete failed", err);
    }
  });

  // OPEN EDIT
  document.addEventListener("click", e => {
    const btn = e.target.closest(".reorder-btn");
    if (!btn) return;

    const card = btn.closest(".card");
    currentEditDesignId = card.dataset.designId;

    byId("editDesignTitle").value = card.querySelector(".card-label").textContent;
    byId("editDesignDescription").value = card.querySelector(".card-desc").textContent;

    byId("editDesignModal").classList.add("active");
  });

  // SAVE EDIT
  byId("editDesignForm").addEventListener("submit", async e => {
    e.preventDefault();
    if (!currentEditDesignId) return;

    const title = byId("editDesignTitle").value.trim();
    const desc = byId("editDesignDescription").value.trim();
    if (!title || !desc) return;

    try {
      await fetch("../php/editDesign.php", {
        method: "POST",
        headers: { "Content-Type":"application/x-www-form-urlencoded" },
        body: `designID=${encodeURIComponent(currentEditDesignId)}&title=${encodeURIComponent(title)}&description=${encodeURIComponent(desc)}`
      });

      const card = document.querySelector(`.card[data-design-id='${currentEditDesignId}']`);
      if (card) {
        card.querySelector(".card-label").textContent = title;
        card.querySelector(".card-desc").textContent = desc;
      }

      byId("editDesignModal").classList.remove("active");
      byId("editDesignForm").reset();
      currentEditDesignId = null;

    } catch (err) {
      console.error("Edit failed", err);
    }
  });
}

// ================= EDIT PROFILE =================
function initEditProfileForm() {
  const form = byId("editProfileForm");

  form.addEventListener("submit", async e => {
    e.preventDefault();

    const fd = new FormData();
    fd.append("specialty", byId("designerSpecialty").value.trim());
    fd.append("bio", byId("designerBio").value.trim());

    const imgFile = byId("profileImage").files[0];
    if (imgFile) {
      fd.append("profileImage", imgFile);
    }

    try {
      const res = await fetch("../php/updateProfile.php", {
        method: "POST",
        body: fd
      });
      const data = await res.json();
      if (data.status !== "success") return;

      await loadDesignerProfile();

      byId("editProfileModal").classList.remove("active");
      form.reset();
      byId("profilePreview").style.display = "none";
    } catch (err) {
      console.error("Profile update failed", err);
    }
  });
}

// ================= LIGHTBOX =================
function initLightbox() {
  const css = `
    .lb-overlay{
      position: fixed; inset:0; background: rgba(0,0,0,.65);
      display:none; align-items:center; justify-content:center;
      z-index:9999; padding:24px;
    }
    .lb-box{
      position:relative; max-width:min(92vw,1100px); max-height:92vh;
      background:#fff; border-radius:14px; box-shadow:0 20px 60px rgba(0,0,0,.35);
      overflow:hidden; display:flex; flex-direction:column;
    }
    .lb-img{
      max-width:92vw; max-height:78vh; object-fit:contain; background:#f5f5f5;
    }
    .lb-caption{
      padding:10px 14px; text-align:center; font-weight:700; color:#002766;
      border-top:1px solid #e8e8e8;
    }
    .lb-close{
      position:absolute; top:8px; right:8px; width:32px; height:32px;
      border-radius:50%; border:none; background:#ffffffcc; cursor:pointer;
      font-size:20px; box-shadow:0 2px 8px rgba(0,0,0,.15);
    }
  `;
  const style = document.createElement("style");
  style.textContent = css;
  document.head.appendChild(style);

  const overlay = document.createElement("div");
  overlay.className = "lb-overlay";
  overlay.innerHTML = `
    <div class="lb-box" role="dialog" aria-modal="true">
      <button class="lb-close" aria-label="Close">×</button>
      <img class="lb-img" alt="">
      <div class="lb-caption"></div>
    </div>
  `;
  document.body.appendChild(overlay);

  const imgEl = overlay.querySelector(".lb-img");
  const capEl = overlay.querySelector(".lb-caption");
  const closeBtn = overlay.querySelector(".lb-close");

  function openLightbox(src, caption) {
    imgEl.src = src;
    imgEl.alt = caption || "";
    capEl.textContent = caption || "";
    overlay.style.display = "flex";
    document.documentElement.style.overflow = "hidden";
  }

  function closeLightbox() {
    overlay.style.display = "none";
    imgEl.src = "";
    capEl.textContent = "";
    document.documentElement.style.overflow = "";
  }

  closeBtn.addEventListener("click", closeLightbox);
  overlay.addEventListener("click", e => {
    if (e.target === overlay) closeLightbox();
  });
  document.addEventListener("keydown", e => {
    if (e.key === "Escape" && overlay.style.display === "flex") closeLightbox();
  });

  const host = byId("designsSection");
  host.addEventListener("click", e => {
    if (host.classList.contains("edit-mode")) return;
    if (e.target.closest(".card-actions")) return;

    const img = e.target.closest(".card img");
    if (!img) return;
    openLightbox(img.currentSrc || img.src, img.alt || "");
  });
}

// ================= INIT =================
document.addEventListener("DOMContentLoaded", () => {
  if (!DESIGNER_DB_ID) {
    console.error("No designerID from session");
    return;
  }

  loadDesignerProfile();
  loadDesignsFromDB();
  loadReviewsFromDB();
  initTabs();
  initEditMode();
  initModals();
  initAddDesignForm();
  initDesignActions();
  initEditProfileForm();
  initLightbox();

  const y = byId("y");
  if (y) y.textContent = new Date().getFullYear();
});
