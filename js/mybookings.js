
const $ = (s, r=document)=>r.querySelector(s);
const byId = id => document.getElementById(id)||null;


const ORDERS = [
  
  {
    id: "ord-2025-001",
    status: "current",
    designer: "Felwa Althagfan",
    room: "Entrance",
    notes: "Modern marble entrance",
    date: "2025-11-02",
    image: "../photo/Entrance2.jpeg"
  },
  {
    id: "ord-2025-002",
    status: "current",
    designer: "Esra Aljaser",
    room: "Living Room",
    notes: "Cozy modern composition",
    date: "2025-11-02",
    image: "../photo/LivingRoom.jpg"
  },

  
  {
    id: "ord-2025-000",
    status: "history",
    designer: "Ghada Alotaibi",
    room: "Majlis",
    notes: "Elegant hosting zone",
    date: "2025-10-12",
    image: "../photo/Majlis3.jpeg"
  }
];


function orderCard(o){
  const badgeClass = o.status === "current"
    ? "badge-current"
    : (o.status === "canceled" ? "badge-canceled" : "badge-history");

  
  const statusLabel = o.status === "current"
    ? "Current"
    : (o.status === "canceled" ? "Canceled" : "Past");

  return `
    <article class="order-card" data-id="${o.id}">
      <div class="order-thumb" style="--bg:url('${o.image}')">
        <img src="${o.image}" alt="${o.room}">
      </div>

      <div class="order-body">
        <div class="order-top">
          <h3 class="order-title">${o.room}</h3>
          <span class="badge ${badgeClass}">${statusLabel}</span>
        </div>

        <div class="order-meta">
          <span><strong>Designer:</strong> ${o.designer}</span>
          <span><strong>Date:</strong> ${o.date}</span>
        </div>

        <p class="order-notes">${o.notes}</p>

        <div class="order-actions">
          ${o.status === "current"
            ? `<button class="btn btn-cancel" data-cancel="${o.id}">Cancel</button>`
            : ``}
        </div>
      </div>
    </article>
  `;
}


function renderLists(){
  const curr = ORDERS.filter(o=>o.status==="current");
  const hist = ORDERS.filter(o=>o.status==="history" || o.status==="canceled");

  const currHost = byId("currentSection");
  const histHost = byId("pastSection");   

  if (currHost) currHost.innerHTML = curr.length ? curr.map(orderCard).join("") : `<p>No current orders.</p>`;
  if (histHost) histHost.innerHTML = hist.length ? hist.map(orderCard).join("") : `<p>No previous orders.</p>`;

  bindCancel();
}


function bindCancel(){
  document.querySelectorAll("[data-cancel]").forEach(btn=>{
    btn.addEventListener("click", ()=>{
      const id = btn.getAttribute("data-cancel");
      const order = ORDERS.find(o=>o.id===id);
      if (!order) return;
      if (!confirm("Cancel this order?")) return;
      order.status = "canceled";
      renderLists();
      showTab("current");
    });
  });
}


function showTab(which){
  const currentSection = byId("currentSection");
  const pastSection    = byId("pastSection");
  const pastHeading    = byId("pastHeading");

  const tabs = document.querySelectorAll(".tab-link");

  tabs.forEach(t=>{
    t.classList.toggle("is-active", t.dataset.tab === which);
    t.setAttribute("aria-selected", t.dataset.tab === which ? "true" : "false");
  });

  if (which === "current"){
    currentSection.classList.remove("is-hidden");
    document.querySelector('.orders-heading').classList.remove("is-hidden");
    pastSection.classList.add("is-hidden");
    pastHeading.classList.add("is-hidden");
  } else {
    currentSection.classList.add("is-hidden");
    document.querySelector('.orders-heading').classList.add("is-hidden");
    pastSection.classList.remove("is-hidden");
    pastHeading.classList.remove("is-hidden");
  }
}


document.addEventListener("DOMContentLoaded", ()=>{
  renderLists();
  showTab("current");

  document.querySelectorAll(".tab-link").forEach(el=>{
    el.addEventListener("click", ()=> showTab(el.dataset.tab));
    el.addEventListener("keydown", (e)=>{
      if (e.key === "Enter" || e.key === " ") showTab(el.dataset.tab);
    });
  });

  const y=byId("y"); if (y) y.textContent = new Date().getFullYear();
});
