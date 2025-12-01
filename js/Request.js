
function showBookingDetails() {
  const bookingDetails = document.getElementById('booking-details');
  bookingDetails.style.display = 'block';  
}


function showMeetingDetails() {
  const meetingDetails = document.getElementById('meeting-details');
  meetingDetails.style.display = 'block';  
}


function closeDetailsPopup(type) {
  const popup = type === 'booking' ? document.getElementById('booking-details') : document.getElementById('meeting-details');
  popup.style.display = 'none';  
}


document.getElementById("filterType").addEventListener("change", function () {
  const value = this.value;
  document.querySelectorAll(".alert-card").forEach(card => {
    card.style.display = (value === "all" || card.classList.contains(value)) ? "block" : "none";
  });
});


document.getElementById("searchInput").addEventListener("input", function () {
  const val = this.value.toLowerCase();
  document.querySelectorAll(".alert-card").forEach(card => {
    const text = card.textContent.toLowerCase();
    card.style.display = text.includes(val) ? "block" : "none";
  });
});

