// === helpers كما هي ===
const $ = (s, r=document)=>r.querySelector(s);
const byId = id => document.getElementById(id)||null;

// نحدّد المعرف الحالي للـ designer من رابط الصفحة
let CURRENT_ID = 'esra';

// === PATHS كما هي ===
const PATHS = {
  esraLogo:'../photo/‏EsraAljaser‏.jpeg',
  felwaLogo:'../photo/‏FelwaAlthagfan.jpeg',
  ghadaLogo:'../photo/‏GhadaAlotaibi‏.jpeg',
  hessaLogo:'../photo/‏HessaAlnafisah‏.jpeg',
  muntahaLogo:'../photo/muntaha.jpeg',
  ahmedLogo:'../photo/Ahmed.jpeg',
  placeholder:'../photo/Logo.png.png',
};

// === أيقونة المجهول كما هي ===
const ANON_AVATAR="data:image/svg+xml;utf8,\
<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'>\
<circle cx='32' cy='24' r='14' fill='#d9d5c9'/>\
<rect x='10' y='40' width='44' height='18' rx='9' fill='#d9d5c9'/></svg>";

// ✅ أيقونة السهم داخل دائرة صغيرة (بدل شعار لينكدإن)
function arrowCircleIcon(){
  return `
    <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
      <circle cx="16" cy="16" r="15" fill="#0a66c2"/>
      <path d="M11 21 L21 11 M15 11h6v6" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  `;
}

// === mkReviews كما هي ===
function mkReviews(names=[]){
  const stars=[4.8,4.6,5.0,4.9,4.7,4.5];
  const texts=[
    "Great attention to detail.",
    "Loved the material selections.",
    "Elegant and functional.",
    "Creative layout ideas!",
    "On time & professional.",
    "Would hire again."
  ];
  return names.slice(0,6).map((n,i)=>({name:n,avatar:ANON_AVATAR,stars:stars[i],text:texts[i],date:"2025-05"}));
}

// === LABEL كما هو (إنجليزي فقط) ===
const LABEL={
  living:"Living Room", kitchen:"Kitchen",
  bedroom:"Bedroom", kids:"Kids Room",
  majlis:"Majlis", entrance:"Entrance"
};

// ✅ أوصاف عامّة (إنجليزية فقط)
const DESIGN_DESC = {
  esra: {
    living:  "Cozy modern living with delicate accents.",
    kitchen: "Black marble with warm golden lighting.",
    bedroom: "Soft palette for calm rest.",
    kids:    "Bright, playful layout.",
    majlis:  "Contemporary lines with welcoming seating.",
    entrance:"Clean entry with subtle highlights."
  },
  felwa: {
    living:  "Modern Arabic touch, balanced and calm.",
    kitchen: "Wooden kitchen with timber floor.",
    bedroom: "Fresh neutrals, neat layers.",
    kids:    "Wood details with green accents.",
    majlis:  "Refined setup with geometric order.",
    entrance:"Luxurious marble entrance."
  },
  ghada: {
    living:  "Black marble accents, warm lighting, light marble floor.",
    kitchen: "Natural wood textures, clean layout.",
    bedroom: "Wood warmth with calm tones.",
    kids:    "Simple forms with gentle colors.",
    majlis:  "Minimal yet inviting seating.",
    entrance:"Modern, elegant, and luxurious."
  },
  hessa: {
    living:  "Elegant and warm with marble touch.",
    kitchen: "Compact, practical flow.",
    bedroom: "Subtle lighting, restful vibe.",
    kids:    "Interactive and creative.",
    majlis:  "Contemporary geometry.",
    entrance:"Functional welcoming entry."
  },
  muntaha: {
    living:  "Luxury contrasts and statement art.",
    kitchen: "Sleek finishes with metallic hints.",
    bedroom: "Dark elegant mood.",
    kids:    "Playful modern storage.",
    majlis:  "Gold lighting with modern patterns.",
    entrance:"Mirrors and accent lighting."
  },
  ahmed: {
    living:  "Airy contemporary seating.",
    kitchen: "Light-toned wooden kitchen.",
    bedroom: "Balanced lines and calm symmetry.",
    kids:    "Neutral scheme with daylight.",
    majlis:  "Linear modern composition.",
    entrance:"Minimal refined entry."
  }
};

// ✅ bio محدث (شخصي لكل مصمم)
const DESIGNERS={
  esra:{
    name:"Esra Aljaser",
    role:"Freelance Interior Designer – Riyadh",
    bio:"Passionate about creating cozy yet sophisticated interiors that reflect the client’s personality.",
    logo:PATHS.esraLogo,
    link:"https://sa.linkedin.com/in/esra-aljaser",
    reviews:mkReviews(["Abeer","Reem","Sara","Noura","Faisal","Maha"]),
    designs:{
      living:"../photo/LivingRoom.jpg",kitchen:"../photo/Kitchen.jpg",bedroom:"../photo/Bedroom.jpeg",
      kids:"../photo/Kids Room.jpeg",majlis:"../photo/Majlis.jpeg",entrance:"../photo/Entrance.jpeg"}
  },
  felwa:{
    name:"Felwa Althagfan",
    role:"Interior Designer – Riyadh",
    bio:"Interested in cultural identity in design and how to merge tradition with modern living.",
    logo:PATHS.felwaLogo,
    link:"https://sa.linkedin.com/in/felwa-althagfan-1946171a1",
    reviews:mkReviews(["Lama","Waleed","Huda","Razan","Khalid","Alaa"]),
    designs:{
      living:"../photo/LivingRoom2.jpg",kitchen:"../photo/Kitchen2.jpg",bedroom:"../photo/Bedroom2.jpeg",
      kids:"../photo/Kids Room2.jpeg",majlis:"../photo/Majlis2.jpeg",entrance:"../photo/Entrance2.jpeg"}
  },
  ghada:{
    name:"Ghada Alotaibi",
    role:"Interior Designer – Riyadh",
    bio:"Focused on user comfort and emotional connection with spaces through colors and light.",
    logo:PATHS.ghadaLogo,
    link:"https://sa.linkedin.com/in/ghada-alotaibi-84496b185",
    reviews:mkReviews(["Yara","Saleh","Dana","Rami","Noor","Hanin"]),
    designs:{
      living:"../photo/LivingRoom3.jpg",kitchen:"../photo/Kitchen3.jpeg",bedroom:"../photo/Bedroom3.jpeg",
      kids:"../photo/Kids Room3.jpeg",majlis:"../photo/Majlis3.jpeg",entrance:"../photo/Entrance3.jpeg"}
  },
  hessa:{
    name:"Hessa Alnafisah",
    role:"Interior Designer – Riyadh",
    bio:"Enthusiastic about functional design that balances aesthetics and practicality in daily life.",
    logo:PATHS.hessaLogo,
    link:"https://sa.linkedin.com/in/hessa-alnafisah/en",
    reviews:mkReviews(["Alaa","Ruba","Meshael","Nawaf","Jud","Saad"]),
    designs:{
      living:"../photo/LivingRoom4.jpg",kitchen:"../photo/Kitchen4.jpeg",bedroom:"../photo/Bedroom4.jpeg",
      kids:"../photo/Kids Room4.jpeg",majlis:"../photo/Majlis4.jpeg",entrance:"../photo/Entrance4.jpeg"}
  },
  muntaha:{
    name:"Muntaha",
    role:"Interior Designer – Jeddah",
    bio:"Driven by innovation and detail, always seeking new ways to enhance spatial experiences.",
    logo:PATHS.muntahaLogo,
    link:"https://sa.linkedin.com/in/muntaha-alnafisah-8aa63b1a9",
    reviews:mkReviews(["Huda","Eman","Raghad","Farah","Shaikha","Nada"]),
    designs:{
      living:"../photo/LivingRoom5.jpg",kitchen:"../photo/Kitchen5.jpeg",bedroom:"../photo/Bedroom5.jpeg",
      kids:"../photo/Kids Room5.jpeg",majlis:"../photo/Majlis5.jpeg",entrance:"../photo/Entrance5.jpeg"}
  },
  ahmed:{
    name:"Ahmed Zaher",
    role:"Freelance Interior Designer – Riyadh",
    bio:"Passionate about minimalist design and precision, blending architecture with interior harmony.",
    logo:PATHS.ahmedLogo,
    link:"https://sa.linkedin.com/in/ahmed-zaher--",
    reviews:mkReviews(["Yusra","Mona","Rawan","Hamad","Omar","Bashayer"]),
    designs:{
      living:"../photo/LivingRoom6.jpg",kitchen:"../photo/Kitchen6.jpg",bedroom:"../photo/Bedroom6.jpeg",
      kids:"../photo/Kids Room6.jpeg",majlis:"../photo/Majlis6.jpeg",entrance:"../photo/Entrance6.jpeg"}
  }
};

// الباقي كما هو
const heartIcon=()=>`<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 21s-6.5-4.3-9.3-7.1A5.5 5.5 0 1 1 12 6a5.5 5.5 0 1 1 9.3 7.9C18.5 16.7 12 21 12 21z" stroke-width="1.6"/></svg>`;

function renderDesigns(d){
  const host=byId("designsSection"); if(!host)return;
  host.innerHTML=Object.entries(d.designs).map(([key,src])=>{
    const desc = (DESIGN_DESC[CURRENT_ID] && DESIGN_DESC[CURRENT_ID][key]) ? DESIGN_DESC[CURRENT_ID][key] : "";
    return `
    <article class="card" style="--bg:url('${src}')">
      <button class="like-btn" data-like="${key}" aria-label="Like">${heartIcon()}</button>
      <span class="like-count" id="lc-${key}">0</span>
      <img src="${src}" alt="${LABEL[key]||key}">
      <div class="card-label">${LABEL[key]||key}</div>
      ${desc ? `<p class="card-desc">${desc}</p>` : ``}
    </article>`;
  }).join("");

  host.querySelectorAll(".like-btn").forEach(btn=>{
    btn.addEventListener("click",()=>{
      btn.classList.toggle("liked");
      const id=btn.getAttribute("data-like");
      const el=byId(`lc-${id}`); if(!el)return;
      const n=parseInt(el.textContent||"0",10);
      el.textContent=btn.classList.contains("liked")?n+1:Math.max(0,n-1);
    });
  });
}

// ✅ إضافة تنسيق بسيط بالـ CSS مباشرة عبر JS حتى لا يلتصق النص باللايك
const style = document.createElement("style");
style.textContent = `
.card-desc {
  margin-top: 6px;
  font-size: 0.9rem;
  color: #444;
  text-align: center;
}
`;
document.head.appendChild(style);

function anonIcon(){return`<svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg"><circle cx="32" cy="24" r="14"/><rect x="10" y="40" width="44" height="18" rx="9"/></svg>`;}

function renderReviews(d){
  const list=byId("reviewsList"); if(!list)return;
  list.innerHTML=d.reviews.map(rv=>`
    <div class="review-card">
      <div class="review-avatar-icon">${anonIcon()}</div>
      <div>
        <div class="review-head">
          <span class="review-name">${rv.name}</span>
          <span class="stars">★ ${rv.stars.toFixed(1)}</span>
        </div>
        <p>${rv.text}</p>
      </div>
    </div>`).join("");
}

function applyData(d){
  const logo=byId("designerLogo"); if(logo)logo.src=d.logo||PATHS.placeholder;
  const name=byId("designerName"); if(name)name.textContent=d.name;

  const roleEl=byId("designerRole");
  if(roleEl){
    roleEl.innerHTML = `${d.role}<br><span class="banner-bio">${d.bio||""}</span>`;
  }

  const rc=byId("reviewsCount"); if(rc)rc.textContent=d.reviews.length;

  const wrap=byId("linkWrap");
  const lnk=byId("profileLink");
  const has=d.link && d.link!=="#";
  if(wrap) wrap.hidden=!has;
  if(lnk){
    lnk.href = has ? d.link : "#";
    lnk.innerHTML = arrowCircleIcon();
    lnk.setAttribute("title","LinkedIn");
  }

  renderDesigns(d);
  renderReviews(d);
}

function initTabs(){
  const tabs=document.querySelectorAll(".tabs .tab"),
        secDesign=byId("designsSection"),
        secReview=byId("reviewsSection");
  if(!tabs.length||!secDesign||!secReview)return;
  tabs.forEach(btn=>{
    btn.addEventListener("click",()=>{
      tabs.forEach(b=>b.classList.remove("is-active"));
      btn.classList.add("is-active");
      const which=btn.dataset.tab;
      if(which==="designs"){secDesign.classList.remove("is-hidden");secReview.classList.add("is-hidden");}
      else{secReview.classList.remove("is-hidden");secDesign.classList.add("is-hidden");}
    });
  });
}

function getId(){
  const p=new URLSearchParams(location.search);
  return(p.get("id")||"").toLowerCase();
}

document.addEventListener("DOMContentLoaded",()=>{
  const id = getId();
  CURRENT_ID = (id && DESIGNERS[id]) ? id : 'esra';
  initTabs();
  const d=DESIGNERS[CURRENT_ID]||DESIGNERS.esra;
  applyData(d);
  const y=byId("y"); if(y)y.textContent=new Date().getFullYear();
});




/* ==== Lightbox (enlarge on click) — ADD-ON ONLY ==== */
(function setupDesignLightbox(){
  // Inject CSS once
  if (!document.getElementById('lbStyles')) {
    const css = `
      .lb-overlay{
        position: fixed; inset:0; background: rgba(0,0,0,.65);
        display: none; align-items: center; justify-content: center;
        z-index: 9999; padding: 24px;
      }
      .lb-box{
        position: relative; max-width: min(92vw, 1100px);
        width: fit-content; max-height: 92vh; display:flex; flex-direction:column; gap:10px;
        background: #fff; border-radius: 14px; box-shadow: 0 20px 60px rgba(0,0,0,.35);
        overflow: hidden;
      }
      .lb-img{
        display:block; max-width: 92vw; max-height: 78vh; object-fit: contain;
        background: #f5f5f5;
      }
      .lb-caption{
        padding: 10px 14px; font-weight: 700; color: #002766; border-top: 1px solid #e8e8e8;
        text-align:center;
      }
      .lb-close{
        position:absolute; top:8px; right:8px; border:none; width:36px; height:36px; border-radius:999px;
        background:#ffffffcc; cursor:pointer; font-size:20px; line-height:36px;
        box-shadow: 0 2px 8px rgba(0,0,0,.15);
      }
      .lb-close:hover{ transform: scale(1.06); }
    `;
    const style = document.createElement('style');
    style.id = 'lbStyles';
    style.textContent = css;
    document.head.appendChild(style);
  }

  // Create lightbox DOM once
  let lb = document.getElementById('lb');
  if (!lb){
    lb = document.createElement('div');
    lb.className = 'lb-overlay';
    lb.id = 'lb';
    lb.innerHTML = `
      <div class="lb-box" role="dialog" aria-modal="true" aria-label="Preview">
        <button class="lb-close" aria-label="Close">×</button>
        <img class="lb-img" alt="">
        <div class="lb-caption"></div>
      </div>
    `;
    document.body.appendChild(lb);
  }
  const imgEl = lb.querySelector('.lb-img');
  const capEl = lb.querySelector('.lb-caption');
  const closeBtn = lb.querySelector('.lb-close');

  function openLightbox(src, caption){
    imgEl.src = src;
    imgEl.alt = caption || '';
    capEl.textContent = caption || '';
    lb.style.display = 'flex';
    document.documentElement.style.overflow = 'hidden'; // lock scroll
  }
  function closeLightbox(){
    lb.style.display = 'none';
    imgEl.src = '';
    capEl.textContent = '';
    document.documentElement.style.overflow = '';
  }

  // Close interactions
  closeBtn.addEventListener('click', closeLightbox);
  lb.addEventListener('click', (e)=>{
    if (e.target === lb) closeLightbox(); // click outside box
  });
  document.addEventListener('keydown', (e)=>{
    if (e.key === 'Escape' && lb.style.display === 'flex') closeLightbox();
  });

  // Delegate clicks from designs grid (no changes to existing render)
  const host = document.getElementById('designsSection');
  if (!host) return;

  host.addEventListener('click', (e)=>{
    const img = e.target.closest('.card img');
    if (!img) return;
    openLightbox(img.currentSrc || img.src, img.alt || '');
  }, false);
})();
window.DESIGNERS = {
  esra:  { name: "Esra Aljaser",    zoom: "https://zoom.us/j/9876543210" },
  felwa: { name: "Felwa Althagfan", zoom: "https://zoom.us/j/9876543211" },
  ghada: { name: "Ghada Alotaibi",  zoom: "https://zoom.us/j/9876543212" },
  hessa: { name: "Hessa Alnafisah", zoom: "https://zoom.us/j/9876543213" },
  muntaha:{ name: "Muntaha",        zoom: "https://zoom.us/j/9876543214" },
  ahmed: { name: "Ahmed Zaher",     zoom: "https://zoom.us/j/9876543215" }
};
