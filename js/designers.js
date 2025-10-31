// js/designers.js — DECORIA (links to designerInfo.html?id=...)
/* لا تغيّر الهيدر/الفوتر/الـ CSS. هذا الملف فقط يبني كروت المصممين ويضبط روابطهم. */

const designers = [
  {
    id: "esra",
    name: "Esra Aljaser",
    avatar: "../photo/‏EsraAljaser‏.jpeg", // ← غيّري الامتداد/الاسم إذا لزم
    bio: "Creative residential concepts with functional elegance.",
    styles: ["Modern", "Minimal"],
    city: "Riyadh",
    rating: 4.8,
  },
  {
    id: "felwa",
    name: "Felwa Althagfan",
    avatar: "../photo/‏FelwaAlthagfan.jpeg",
    bio: "Interior Designer at Shai Designs SA. Modern Arabic aesthetics.",
    styles: ["Classic"],
    city: "Riyadh",
    rating: 4.6,
  },
  {
    id: "ghada",
    name: "Ghada Alotaibi",
    avatar: "../photo/‏GhadaAlotaibi‏.jpeg",
    bio: "Scandinavian cozy. Soft palettes & wood textures.",
    styles: ["Scandinavian", "Minimal"],
    city: "Riyadh",
    rating: 4.9,
  },
  {
    id: "hessa",
    name: "Hessa Alnafisah",
    avatar: "../photo/‏HessaAlnafisah‏.jpeg",
    bio: "SCE Member. Creative space planner for homes & offices.",
    styles: ["Bohemian"],
    city: "Riyadh",
    rating: 4.7,
  },
  {
    id: "muntaha",
    name: "Muntaha",
    avatar: "../photo/muntaha.jpeg",
    bio: "Modern luxury. Statement lighting & premium finishes.",
    styles: ["Modern"],
    city: "Jeddah",
    rating: 4.5,
  },
  {
    id: "ahmed",
    name: "Ahmed Zaher",
    avatar: "../photo/Ahmed.jpeg",
    bio: "Freelance Interior Designer — innovative residential & commercial.",
    styles: ["Classic"],
    city: "Riyadh",
    rating: 4.8,
  },
];

function card(d) {
  const tags = d.styles.map((s) => `<span class="tag">${s}</span>`).join("");
  const rating = `★ ${d.rating.toFixed(1)}`;
  // fallback للصور لو الاسم/المسار خطأ
  const onErr = "this.onerror=null;this.src='../photo/placeholder.png'";
  return `
    <article class="designer-card" data-id="${d.id}">
      <img class="designer-avatar" src="${d.avatar}" alt="${d.name}" onerror="${onErr}">
      <h3 class="designer-name">${d.name} <span style="color:gold">${rating}</span></h3>
      <p class="designer-meta">${d.city}</p>
      <p>${d.bio}</p>
      <div>${tags}</div>
      <!-- فتح صفحة المصمم بالـ id الصحيح -->
      <a class="view-btn" href="designerInfo.html?id=${d.id}">View Profile</a>
    </article>
  `;
}

function render() {
  const term = (document.getElementById("q")?.value || "").toLowerCase();
  const filtered = designers.filter(
    (d) =>
      d.name.toLowerCase().includes(term) ||
      d.bio.toLowerCase().includes(term) ||
      d.city.toLowerCase().includes(term) ||
      d.styles.join(" ").toLowerCase().includes(term)
  );
  const grid = document.getElementById("grid");
  if (grid) grid.innerHTML = filtered.map(card).join("");
}

document.getElementById("q")?.addEventListener("input", render);
document.addEventListener("DOMContentLoaded", render);
