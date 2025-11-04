// js/booking.js
(function(){
  'use strict';

  const LS_KEY = 'decoria_bookings';
  const byId = id => document.getElementById(id);

  // تحميل الحجز من localStorage
  function loadLS(){
    try { return JSON.parse(localStorage.getItem(LS_KEY) || "[]"); }
    catch { return []; }
  }

  // حفظ الحجز في localStorage
  function saveLS(arr){
    localStorage.setItem(LS_KEY, JSON.stringify(arr));
  }

  // جلب بيانات المصممين
  function getDesignersMap(){
    return (window.DESIGNERS && typeof window.DESIGNERS === 'object') ? window.DESIGNERS : {};
  }

  // التحقق من تعارض المواعيد
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
    const nameInput = byId('clientName');
    const confirmBtn = byId('confirmBooking');

    const bookingDetails = byId('bookingDetails');
    const detailName = byId('detailName');
    const detailDate = byId('detailDate');
    const detailTime = byId('detailTime');

    const editBtn = byId('editBooking');
    const cancelBtn = byId('cancelBooking2');
    const goTimelineBtn = byId('goTimeline');

    const designers = getDesignersMap();

    // populate select
    if(sel){
      sel.innerHTML = `<option value="">Select a Designer</option>`;
      Object.entries(designers).forEach(([id, info])=>{
        const opt = document.createElement('option');
        opt.value = id;
        opt.textContent = info.name || id;
        sel.appendChild(opt);
      });
    }

    // قراءة قيم الفورم
    function readFormValues(){
      return {
        designerId: sel.value.trim(),
        date: dateInput.value,
        time: timeInput.value,
        payment: paymentInput.value,
        clientName: nameInput.value.trim() || 'Guest'
      };
    }

    // عرض تفاصيل الحجز
    function showBookingDetailsUI(booking){
      detailName.textContent = booking.clientName;
      detailDate.textContent = booking.date;
      detailTime.textContent = booking.time;
      form.style.display = 'none';
      bookingDetails.style.display = 'block';
    }

    // حفظ الحجز
    function submitBooking(e){
      if(e) e.preventDefault();
      const vals = readFormValues();

      if(!vals.designerId || !vals.date || !vals.time || !vals.clientName){
        alert('Please fill all required fields.');
        return;
      }

      const bookings = loadLS();

      if(slotConflict(bookings, vals.designerId, vals.date, vals.time)){
        if(!confirm('This slot is already booked. Continue anyway?')) return;
      }

      const newBooking = {
        id: 'bk_' + Math.random().toString(36).slice(2,9),
        designerId: vals.designerId,
        designerName: (designers[vals.designerId] && designers[vals.designerId].name) || vals.designerId,
        date: vals.date,
        time: vals.time,
        payment: vals.payment,
        clientName: vals.clientName,
        status: 'booked',
        createdAt: isoNow()
      };

      bookings.push(newBooking);
      saveLS(bookings);

      alert('Booking confirmed ✅');
      showBookingDetailsUI(newBooking);
    }

    // أحداث الفورم
    form.addEventListener('submit', submitBooking);
    if(confirmBtn) confirmBtn.addEventListener('click', submitBooking);

    // زر Edit: تعديل الاسم فقط
    if(editBtn){
      editBtn.addEventListener('click', ()=>{
        form.style.display = 'block';
        bookingDetails.style.display = 'none';
        nameInput.focus();
      });
    }

    // زر Cancel: حذف الحجز
    if(cancelBtn){
      cancelBtn.addEventListener('click', ()=>{
        let bookings = loadLS();
        // حذف آخر حجز مؤكد فقط
        bookings = bookings.filter(b => b.status !== 'booked');
        saveLS(bookings);

        bookingDetails.style.display = 'none';
        form.reset();
        form.style.display = 'block';
        alert('Booking cancelled. You can create a new one.');
      });
    }

    // زر Go to Timeline
    if(goTimelineBtn){
      goTimelineBtn.addEventListener('click', ()=>{
        location.href = 'timeline.html';
      });
    }

  });
})();