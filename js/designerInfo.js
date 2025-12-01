
const $ = (s, r=document)=>r.querySelector(s);
const byId = id => document.getElementById(id);


function initTabs(){
  const tabs=document.querySelectorAll(".tabs .tab"),
        secDesign=byId("designsSection"),
        secReview=byId("reviewsSection");

  if(!tabs.length||!secDesign||!secReview) return;

  tabs.forEach(btn=>{
    btn.addEventListener("click",()=>{
      tabs.forEach(b=>b.classList.remove("is-active"));
      btn.classList.add("is-active");

      const which=btn.dataset.tab;
      if(which==="designs"){
        secDesign.classList.remove("is-hidden");
        secReview.classList.add("is-hidden");
      } else {
        secReview.classList.remove("is-hidden");
        secDesign.classList.add("is-hidden");
      }
    });
  });
}


function initLikeButtons(){
  document.querySelectorAll(".like-btn").forEach(btn=>{
    btn.addEventListener("click",()=>{
      btn.classList.toggle("liked");
      const id=btn.dataset.like;
      const counter=byId(`lc-${id}`);
      if(counter){
        let n=parseInt(counter.textContent||"0");
        counter.textContent = btn.classList.contains("liked") ? n+1 : Math.max(0, n-1);
      }
    });
  });
}


function setupLightbox(){
  let lb = byId('lb');
  if (!lb){
    lb = document.createElement('div');
    lb.className = 'lb-overlay';
    lb.id = 'lb';
    lb.innerHTML = `
      <div class="lb-box">
        <button class="lb-close">×</button>
        <img class="lb-img" alt="">
        <div class="lb-caption"></div>
      </div>
    `;
    document.body.appendChild(lb);
  }

  const imgEl = lb.querySelector('.lb-img');
  const capEl = lb.querySelector('.lb-caption');
  const closeBtn = lb.querySelector('.lb-close');

  function open(src, caption){
    imgEl.src = src;
    capEl.textContent = caption || "";
    lb.style.display = 'flex';
    document.documentElement.style.overflow='hidden';
  }
  function close(){
    lb.style.display='none';
    document.documentElement.style.overflow='';
  }

  closeBtn.addEventListener('click', close);
  lb.addEventListener('click',(e)=>{ if(e.target===lb) close(); });
  document.addEventListener('keydown',(e)=>{ if(e.key==="Escape") close(); });

 
  const host = byId("designsSection");
  if(!host) return;

  host.addEventListener("click", (e)=>{
    const img=e.target.closest(".card img");
    if(img) open(img.src, img.alt);
  });
}


document.addEventListener("DOMContentLoaded",()=>{
  initTabs();
  initLikeButtons();
  setupLightbox();
});
