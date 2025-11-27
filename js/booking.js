// js/booking.js (مع إضافة منطق تعديل واختيار التصميم ديناميكياً)
(function(){
  'use strict';

  // تم إزالة جميع الدوال المتعلقة بـ Local Storage للسماح بالإرسال إلى الخادم
  
  const byId = id => document.getElementById(id);

  function getDesignersMap(){
    // يفترض أن هذا الكائن موجود ومملوء ببيانات المصممين
    return (window.DESIGNERS && typeof window.DESIGNERS === 'object') ? window.DESIGNERS : {};
  }

  document.addEventListener('DOMContentLoaded', ()=>{
    const sel = byId('designer'); // قائمة المصممين
    const designSelect = byId('design'); // **الجديد: قائمة التصاميم**
    const form = document.querySelector('.booking-form') || byId('bookingForm');
    const dateInput = byId('date');
    const timeInput = byId('time');
    const bookingDetailsDiv = byId('bookingDetails'); 

    const designers = getDesignersMap();

    // 1. ملء قائمة المصممين
    if(sel){
      sel.innerHTML = `<option value="">Select a Designer</option>`;
      Object.entries(designers).forEach(([id, info])=>{
        const opt = document.createElement('option');
        opt.value = id;
        opt.textContent = info.name || id;
        sel.appendChild(opt);
      });
      
      // **الجديد: إضافة معالج لحدث التغيير على قائمة المصممين**
      sel.addEventListener('change', fetchDesignsByDesigner);
    }

    // **2. دالة جلب التصاميم بناءً على المصمم (محاكاة)**
    function fetchDesignsByDesigner() {
        const designerId = sel.value;
        designSelect.innerHTML = '<option value="">Loading designs...</option>';
        designSelect.disabled = true;

        if (designerId) {
            // **ملاحظة:** يجب تغيير هذا الجزء لاستدعاء ملف PHP على الخادم (مثل fetch('fetch_designs.php?designerID=' + designerId))
            
            // محاكاة بيانات التصاميم (للتجربة فقط)
            const mockDesigns = [
                {id: 'd001', name: 'Luxury Villa Design'},
                {id: 'd002', name: 'Cozy Apartment Plan'},
                {id: 'd003', name: 'Office Layout and Decor'}
            ];
            
            setTimeout(() => { // محاكاة وقت الاستجابة
                designSelect.innerHTML = '<option value="">Select a Design</option>';
                mockDesigns.forEach(design => {
                    const opt = document.createElement('option');
                    opt.value = design.id;
                    opt.textContent = design.name;
                    designSelect.appendChild(opt);
                });
                designSelect.disabled = false; // تفعيل القائمة
            }, 300);

        } else {
            designSelect.innerHTML = '<option value="">Please select a designer first</option>';
            designSelect.disabled = true;
        }
    }

    // 3. معالج إرسال النموذج (التحقق الأولي)
    function submitBooking(e){
        const designerId = sel.value;
        const designId = designSelect.value; // **التحقق من التصميم**
        const date = dateInput ? dateInput.value : '';
        const time = timeInput ? timeInput.value : '';
        const transactionPhoto = byId('transactionPhoto');

        if(!designerId){ alert('Please choose a designer.'); e.preventDefault(); return; }
        if(!designId){ alert('Please choose a design.'); e.preventDefault(); return; } // **تحقق جديد**
        if(!date){ alert('Please choose a date.'); e.preventDefault(); return; }
        if(!time){ alert('Please choose a time.'); e.preventDefault(); return; }
        if(!transactionPhoto || !transactionPhoto.files.length){ 
          alert('Please upload a transaction photo.'); e.preventDefault(); return; 
        }
        
        // إذا مر التحقق، يسمح للإرسال بالاستمرار إلى process_booking.php
        alert('Submitting booking and file upload to server...');
    }

    form.addEventListener('submit', submitBooking);

    // 4. أزرار التعديل والإلغاء (تعمل محلياً فقط)
    const editBtn = document.querySelector('#bookingDetails .btn-row button:first-child');
    const cancelBtn = document.querySelector('#bookingDetails .btn-row button:last-child');

    if(editBtn){
      editBtn.addEventListener('click', ()=>{
        form.style.display = 'block';
        bookingDetailsDiv.style.display = 'none';
      });
    }

    if(cancelBtn){
      cancelBtn.addEventListener('click', ()=>{
        bookingDetailsDiv.style.display = 'none';
        form.reset();
        form.style.display = 'block';
        alert('Booking cancelled.');
      });
    }

    // **إعداد الحالة الأولية لقائمة التصاميم**
    if (!sel.value) {
        designSelect.disabled = true;
        designSelect.innerHTML = '<option value="">Please select a designer first</option>';
    }

  }); // DOMContentLoaded
})();
