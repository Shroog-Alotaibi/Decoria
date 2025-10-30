// فتح / إغلاق القائمة الجانبية
const sidebar = document.getElementById("sidebar");
const toggle = document.querySelector(".menu-toggle");
const closeBtn = document.getElementById("closeSidebar");
const overlay = document.getElementById("overlay");

toggle.addEventListener("click", () => {
  sidebar.classList.add("open");
  overlay.classList.add("active");
});
closeBtn.addEventListener("click", () => {
  sidebar.classList.remove("open");
  overlay.classList.remove("active");
});
overlay.addEventListener("click", () => {
  sidebar.classList.remove("open");
  overlay.classList.remove("active");
});
