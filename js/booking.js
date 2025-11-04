// js/booking.js
(function(){
  'use strict';

  const LS_KEY = 'decoria_bookings';
  const byId = id => document.getElementById(id);

  function loadLS(){
    try { return JSON.parse(localStorage.getItem(LS_KEY) || "[]"); }
    catch { return []; }
  }
  function saveLS(arr){
    localStorage.setItem(LS_KEY, JSON.stringify(arr));
  }

  function getDesignersMap(){
    return (window.DESIGNERS && typeof window.DESIGNERS === 'object') ? window.DESIGNERS : {};
  }

  function slotConflict(bookings, designerId, date, time){
    return bookings.some(b => b.designerId === designerId && b.date === date && b.time === time && b.status !== 'cancelled');
  }

  function isoNow(){ return (new Date()).toISOString(); }

  document.addEventListener('DOMContentLoaded', ()=>{
    const sel = byId('designer');
    const form = document.querySelector('.booking-form') || byId('bookingForm');
    const dateInput = byId('date');
    const timeInput = byId('time');
    const paymentInput = byId('payment');
    const confirmBtn = byId('confirmBooking');
    const bookingMessage = byId('bookingMessage');
    const viewProgressBtn = byId('viewProgress');

    const designers = getDesignersMap();

    // populate designer select
    if(sel){
      sel.innerHTML = `<option value="">Select a Designer</option>`;
      Object.entries(designers).forEach(([id, info])=>{
        const opt = document.createElement('option');
        opt.value = id;
        opt.textContent = info.name || id;
        sel.appendChild(opt);
      });
      const p = new URLSearchParams(location.search);
      const pre = p.get('designer');
      if(pre && designers[pre]) sel.value = pre;
    }

    function readFormValues(){
      const designerId = sel ? (sel.value || '').trim() : '';
      const date = dateInput ? dateInput.value : '';
      const time = timeInput ? timeInput.value : '';
      const payment = paymentInput ? (paymentInput.value || '') : '';
      const clientName = byId('clientName') ? byId('clientName').value.trim() : (byId('name')? byId('name').value.trim() : 'Guest');
      const clientEmail = byId('clientEmail') ? byId('clientEmail').value.trim() : (byId('email')? byId('email').value.trim() : '');
      return { designerId, date, time, payment, clientName, clientEmail };
    }

    // عرض تفاصيل الحجز
    function updateTimeline() {
      const steps = ['step1','step2','step3'];
      steps.forEach(s => document.getElementById(s)?.classList.add('completed'));
    }

    function showBookingDetails(booking){
      byId('detailName').textContent = booking.clientName;
      byId('detailDate').textContent = booking.date;
      byId('detailTime').textContent = booking.time;
      byId('bookingDetails').style.display = 'block';
      updateTimeline();
    }

    function handleSuccess(newBooking){
      showBookingDetails(newBooking);
      if(bookingMessage) bookingMessage.hidden = false;
    }

    function submitBooking(e){
      if(e && e.preventDefault) e.preventDefault();
      const vals = readFormValues();
      if(!vals.designerId){ alert('Please choose a designer.'); return; }
      if(!vals.date){ alert('Please choose a date.'); return; }
      if(!vals.time){ alert('Please choose a time.'); return; }

      const bookings = loadLS();

      if(slotConflict(bookings, vals.designerId, vals.date, vals.time)){
        const cont = confirm('This slot is already booked for the selected designer. Do you want to continue anyway?');
        if(!cont) return;
      }

      const newBooking = {
        id: 'bk_' + Math.random().toString(36).slice(2,9),
        designerId: vals.designerId,
        designerName: (designers[vals.designerId] && designers[vals.designerId].name) ? designers[vals.designerId].name : vals.designerId,
        date: vals.date,
        time: vals.time,
        payment: vals.payment || '',
        clientName: vals.clientName || 'Guest',
        clientEmail: vals.clientEmail || '',
        status: 'booked',
        createdAt: isoNow()
      };

      bookings.push(newBooking);
      saveLS(bookings);

      alert('Booking confirmed ✅');
      handleSuccess(newBooking);
    }

    form.addEventListener('submit', submitBooking);
    if(confirmBtn) confirmBtn.addEventListener('click', submitBooking);

    // Edit / Cancel buttons داخل details
    const editBtn = byId('editBooking');
    const cancelBtn2 = byId('cancelBooking2');

    if(editBtn){
      editBtn.addEventListener('click', ()=>{
        form.style.display = 'block';
        byId('bookingDetails').style.display = 'none';
      });
    }

    if(cancelBtn2){
      cancelBtn2.addEventListener('click', ()=>{
        byId('bookingDetails').style.display = 'none';
        form.reset();
        form.style.display = 'block';
        alert('Booking cancelled, you can create a new one.');
      });
    }

  }); // DOMContentLoaded
})();
