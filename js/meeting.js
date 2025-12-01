
(function(){
  'use strict';

 
  const byId = id => document.getElementById(id);

  document.addEventListener('DOMContentLoaded', ()=>{
    const designerSelect = byId('designer');
    const dateInput = byId('date');
    const timeInput = byId('time');
    const formEl = document.querySelector('.meeting-form') || byId('meetingForm');

    const meetingInfoDiv = byId('meetingInfo');
    const editMeetingBtn = byId('editMeeting');
    const cancelMeetingBtn2 = byId('cancelMeeting2');

    const designers = (window.DESIGNERS && typeof window.DESIGNERS === 'object') ? window.DESIGNERS : {};

    if(designerSelect){
      const firstPlaceholder = designerSelect.querySelector('option[value=""]') ? designerSelect.querySelector('option[value=""]').outerHTML : '<option value="">Choose...</option>';
      designerSelect.innerHTML = firstPlaceholder;
      Object.entries(designers).forEach(([id, info])=>{
        const opt = document.createElement('option');
        opt.value = id;
        opt.textContent = info.name || id;
        designerSelect.appendChild(opt);
      });
      const p = new URLSearchParams(location.search);
      const pre = p.get('designer');
      if(pre && designers[pre]) designerSelect.value = pre;
    }


    if(editMeetingBtn){
      editMeetingBtn.addEventListener('click', ()=>{
        formEl.style.display = 'block';
        meetingInfoDiv.style.display = 'none';
      });
    }

    if(cancelMeetingBtn2){
      cancelMeetingBtn2.addEventListener('click', ()=>{
        formEl.style.display = 'block';
        meetingInfoDiv.style.display = 'none';
        formEl.reset();
        alert('Meeting cancelled, you can schedule a new one.');
      });
    }


  }); 
})();
