// pages/designers.js

// ✅ مصممين ديكوريا
const designers = [
  { id: 1, name: "Noura Al-Faisal", avatar: "img/noura.jpg",
    bio: "Modern + Minimal. Focus on small spaces & natural light.",
    styles: ["Modern", "Minimal"], city: "Riyadh", rating: 4.8 },

  { id: 2, name: "Khalid Al-Harbi", avatar: "img/khalid.jpg",
    bio: "Classic luxury. Marble & brass accents, hotel-like feel.",
    styles: ["Classic"], city: "Jeddah", rating: 4.6 },

  { id: 3, name: "Reem Al-Shammari", avatar: "img/reem.jpg",
    bio: "Scandinavian cozy. Soft palettes & wood textures.",
    styles: ["Scandinavian", "Minimal"], city: "Dammam", rating: 4.9 },

  { id: 4, name: "Layla Al-Qahtani", avatar: "img/layla.jpg",
    bio: "Bohemian & rustic vibes with warm earthy palettes.",
    styles: ["Bohemian"], city: "Riyadh", rating: 4.7 },

  { id: 5, name: "Fahad Al-Mutairi", avatar: "img/fahad.jpg",
    bio: "Modern luxury. Statement lighting & premium finishes.",
    styles: ["Modern"], city: "Khobar", rating: 4.5 },

  { id: 6, name: "Maha Al-Otaibi", avatar: "img/maha.jpg",
    bio: "Classic meets Art Deco. Geometric patterns & brass.",
    styles: ["Classic"], city: "Jeddah", rating: 4.8 }
];

// ✅ تصميم كرت المصمم
function card(d) {
  const tags = d.styles.map(s => `<span class="tag">${s}</span>`).join("");
  const rating = `★ ${d.rating.toFixed(1)}`;
  return `
    <article class="designer-card">
      <img class="designer-avatar"
           src="${d.avatar}"
           alt="${d.name}"
           onerror="this.onerror=null;this.src='https://picsum.photos/seed/des${d.id}/600/400'">
      <h3 class="designer-name">${d.name} <span style="color:gold">${rating}</span></h3>
      <p class="designer-meta">${d.city}</p>
      <p>${d.bio}</p>
      <div>${tags}</div>
      <a class="view-btn" href="designer-info.html?id=${d.id}">View Profile</a>
    </article>
  `;
}

// ✅ عرض الكروت مع البحث فقط
function render() {
  const searchBox = document.getElementById("q");
  const term = searchBox ? (searchBox.value || "").toLowerCase() : "";

  const filtered = designers.filter(d =>
    d.name.toLowerCase().includes(term) ||
    d.bio.toLowerCase().includes(term) ||
    d.city.toLowerCase().includes(term) ||
    d.styles.join(" ").toLowerCase().includes(term)
  );

  document.getElementById("grid").innerHTML = filtered.map(card).join("");
}

// ✅ تشغيل البحث فقط إذا فيه مربع بحث
const q = document.getElementById("q");
if (q) q.addEventListener("input", render);

// ✅ تشغيل أولي للكروت
render();

/* ====== PARALLAX EFFECT ====== */
document.addEventListener("DOMContentLoaded", () => {
  const cards = document.querySelectorAll(".designer-card");

  cards.forEach(card => {
    card.addEventListener("mousemove", e => {
      const rect = card.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      const rotateY = (x / rect.width - 0.5) * 12;
      const rotateX = (0.5 - y / rect.height) * 12;
      card.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.05)`;
    });

    card.addEventListener("mouseleave", () => {
      card.style.transform = "rotateX(0deg) rotateY(0deg) scale(1)";
    });
  });
});
