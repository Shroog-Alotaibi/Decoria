// js/designers.js — SIMPLE IMAGE PATHS (no fallbacks)

// غيّري الامتداد هنا إذا صورك .jpg أو .png
const IMG_EXT = "jpeg";
// كل الصور داخل مجلد photo على نفس مستوى مجلد html
// designers.html موجود في /html لذلك نرجع خطوة لـ /photo
const IMG = (name) => `../photo/${name}.${IMG_EXT}`;

// 🧑‍🎨 بيانات المصممين (أسماء الملفات كما ذكرتِها بالضبط)
const designers = [
  {
    id: 1,
    name: "Esra Aljaser",
    avatar: IMG("‏EsraAljaser‏"),
    bio: "Creative residential concepts with functional elegance.",
    styles: ["Modern", "Minimal"],
    city: "Riyadh",
    rating: 4.8,
  },
  {
    id: 2,
    name: "Felwa Althagfan",
    avatar: IMG("‏FelwaAlthagfan"),
    bio: "Interior Designer at Shai Designs SA. Modern Arabic aesthetics.",
    styles: ["Classic"],
    city: "Riyadh",
    rating: 4.6,
  },
  {
    id: 3,
    name: "Ghada Alotaibi",
    avatar: IMG("‏GhadaAlotaibi‏"),
    bio: "Scandinavian cozy. Soft palettes & wood textures.",
    styles: ["Scandinavian", "Minimal"],
    city: "Riyadh",
    rating: 4.9,
  },
  {
    id: 4,
    name: "Hessa Alnafisah",
    avatar: IMG("‏HessaAlnafisah‏"),
    bio: "SCE Member. Creative space planner for homes & offices.",
    styles: ["Bohemian"],
    city: "Riyadh",
    rating: 4.7,
  },
  {
    id: 5,
    name: "Muntaha",
    avatar: IMG("muntaha"), // لاحظي: اسم الملف عندك بحروف صغيرة
    bio: "Modern luxury. Statement lighting & premium finishes.",
    styles: ["Modern"],
    city: "Jeddah",
    rating: 4.5,
  },
  {
    id: 6,
    name: "Ahmed Zaher",
    avatar: IMG("Ahmed"),
    bio: "Freelance Interior Designer — innovative residential & commercial.",
    styles: ["Classic"],
    city: "Riyadh",
    rating: 4.8,
  },
];

// كرت المصمم (بدون أي تعقيد للصور)
function card(d) {
  const tags = d.styles.map((s) => `<span class="tag">${s}</span>`).join("");
  const rating = `★ ${d.rating.toFixed(1)}`;
  return `
    <article class="designer-card">
      <img class="designer-avatar" src="${d.avatar}" alt="${d.name}">
      <h3 class="designer-name">${d.name} <span style="color:gold">${rating}</span></h3>
      <p class="designer-meta">${d.city}</p>
      <p>${d.bio}</p>
      <div>${tags}</div>
      <a class="view-btn" href="designer-info.html?id=${d.id}">View Profile</a>
    </article>
  `;
}

// رندر + بحث
function render() {
  const term = (document.getElementById("q")?.value || "").toLowerCase();
  const filtered = designers.filter(
    (d) =>
      d.name.toLowerCase().includes(term) ||
      d.bio.toLowerCase().includes(term) ||
      d.city.toLowerCase().includes(term) ||
      d.styles.join(" ").toLowerCase().includes(term)
  );
  document.getElementById("grid").innerHTML = filtered.map(card).join("");
}

document.getElementById("q")?.addEventListener("input", render);
render();
