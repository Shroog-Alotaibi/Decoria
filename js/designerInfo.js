const $ = (s, r=document)=>r.querySelector(s);
const byId = id => document.getElementById(id)||null;

const PATHS = {
  esraLogo:'../photo/‏EsraAljaser‏.jpeg',
  felwaLogo:'../photo/‏FelwaAlthagfan.jpeg',
  ghadaLogo:'../photo/‏GhadaAlotaibi‏.jpeg',
  hessaLogo:'../photo/‏HessaAlnafisah‏.jpeg',
  muntahaLogo:'../photo/muntaha.jpeg',
  ahmedLogo:'../photo/Ahmed.jpeg',
  placeholder:'../photo/Logo.png.png',
};

const ANON_AVATAR="data:image/svg+xml;utf8,\
<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'>\
<circle cx='32' cy='24' r='14' fill='#d9d5c9'/>\
<rect x='10' y='40' width='44' height='18' rx='9' fill='#d9d5c9'/></svg>";

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

const LABEL={
  living:"Living Room", kitchen:"Kitchen",
  bedroom:"Bedroom", kids:"Kids Room",
  majlis:"Majlis", entrance:"Entrance"
};

const DESIGNERS={
  esra:{name:"Esra Aljaser",role:"Freelance Interior Designer – Riyadh",logo:PATHS.esraLogo,link:"#",
    reviews:mkReviews(["Abeer","Reem","Sara","Noura","Faisal","Maha"]),
    designs:{
      living:"../photo/LivingRoom.jpg",kitchen:"../photo/Kitchen.jpg",bedroom:"../photo/Bedroom.jpeg",
      kids:"../photo/Kids Room.jpeg",majlis:"../photo/Majlis.jpeg",entrance:"../photo/Entrance.jpeg"}
  },
  felwa:{name:"Felwa Althagfan",role:"Interior Designer – Riyadh",logo:PATHS.felwaLogo,link:"#",
    reviews:mkReviews(["Lama","Waleed","Huda","Razan","Khalid","Alaa"]),
    designs:{
      living:"../photo/LivingRoom2.jpg",kitchen:"../photo/Kitchen2.jpg",bedroom:"../photo/Bedroom2.jpeg",
      kids:"../photo/Kids Room2.jpeg",majlis:"../photo/Majlis2.jpeg",entrance:"../photo/Entrance2.jpeg"}
  },
  ghada:{name:"Ghada Alotaibi",role:"Interior Designer – Riyadh",logo:PATHS.ghadaLogo,link:"#",
    reviews:mkReviews(["Yara","Saleh","Dana","Rami","Noor","Hanin"]),
    designs:{
      living:"../photo/LivingRoom3.jpg",kitchen:"../photo/Kitchen3.jpeg",bedroom:"../photo/Bedroom3.jpeg",
      kids:"../photo/Kids Room3.jpeg",majlis:"../photo/Majlis3.jpeg",entrance:"../photo/Entrance3.jpeg"}
  },
  hessa:{name:"Hessa Alnafisah",role:"Interior Designer – Riyadh",logo:PATHS.hessaLogo,link:"#",
    reviews:mkReviews(["Alaa","Ruba","Meshael","Nawaf","Jud","Saad"]),
    designs:{
      living:"../photo/LivingRoom4.jpg",kitchen:"../photo/Kitchen4.jpeg",bedroom:"../photo/Bedroom4.jpeg",
      kids:"../photo/Kids Room4.jpeg",majlis:"../photo/Majlis4.jpeg",entrance:"../photo/Entrance4.jpeg"}
  },
  muntaha:{name:"Muntaha",role:"Interior Designer – Jeddah",logo:PATHS.muntahaLogo,link:"#",
    reviews:mkReviews(["Huda","Eman","Raghad","Farah","Shaikha","Nada"]),
    designs:{
      living:"../photo/LivingRoom5.jpg",kitchen:"../photo/Kitchen5.jpeg",bedroom:"../photo/Bedroom5.jpeg",
      kids:"../photo/Kids Room5.jpeg",majlis:"../photo/Majlis5.jpeg",entrance:"../photo/Entrance5.jpeg"}
  },
  ahmed:{name:"Ahmed Zaher",role:"Freelance Interior Designer – Riyadh",logo:PATHS.ahmedLogo,link:"#",
    reviews:mkReviews(["Yusra","Mona","Rawan","Hamad","Omar","Bashayer"]),
    designs:{
      living:"../photo/LivingRoom6.jpg",kitchen:"../photo/Kitchen6.jpg",bedroom:"../photo/Bedroom6.jpeg",
      kids:"../photo/Kids Room6.jpeg",majlis:"../photo/Majlis6.jpeg",entrance:"../photo/Entrance6.jpeg"}
  }
};

const heartIcon=()=>`<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 21s-6.5-4.3-9.3-7.1A5.5 5.5 0 1 1 12 6a5.5 5.5 0 1 1 9.3 7.9C18.5 16.7 12 21 12 21z" stroke-width="1.6"/></svg>`;

function renderDesigns(d){
  const host=byId("designsSection"); if(!host)return;
  host.innerHTML=Object.entries(d.designs).map(([key,src])=>`
    <article class="card" style="--bg:url('${src}')">
      <button class="like-btn" data-like="${key}" aria-label="Like">${heartIcon()}</button>
      <span class="like-count" id="lc-${key}">0</span>
      <img src="${src}" alt="${LABEL[key]||key}">
      <div class="card-label">${LABEL[key]||key}</div>
    </article>`).join("");

  host.querySelectorAll(".like-btn").forEach(btn=>{
    btn.addEventListener("click",()=>{
      btn.classList.toggle("liked");
      const id=btn.getAttribute("data-like");
      const el=byId(`lc-${id}`); if(!el)return;
      const n=parseInt(el.textContent||"0",10);
      el.textContent=btn.classList.contains("liked")?n+1:Math.max(0,n-1);
    });
  });

  host.querySelectorAll(".card img").forEach(img=>{
    const onReady=()=>{
      const card=img.closest(".card"); if(!card)return;
      const small=img.naturalWidth<800||img.naturalHeight<800;
      if(small)card.classList.add("has-blur");
    };
    if(img.complete)onReady(); else img.addEventListener("load",onReady,{once:true});
  });
}

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
  const role=byId("designerRole"); if(role)role.textContent=d.role;
  const rc=byId("reviewsCount"); if(rc)rc.textContent=d.reviews.length;
  const lnk=byId("profileLink"),wrap=byId("linkWrap");
  if(lnk)lnk.href=d.link||"#"; if(wrap)wrap.hidden=!(d.link&&d.link!="#");
  renderDesigns(d); renderReviews(d);
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
  initTabs();
  const d=DESIGNERS[getId()]||DESIGNERS.esra;
  applyData(d);
  const y=byId("y"); if(y)y.textContent=new Date().getFullYear();
});
