// js/meeting.js
(function(){
  'use strict';

  const LS_KEY = 'decoria_meeting';
  const byId = id => document.getElementById(id);

  function saveMeeting(data){ localStorage.setItem(LS_KEY, JSON.stringify(data)); }
  function getMeeting(){ return JSON.parse(localStorage.getItem(LS_KEY) || "null"); }

  document.addEventListener('DOMContentLoaded', ()=>{
    const designerSelect = byId('designer');
    const dateInput = byId('date');
    const timeInput = byId('time');
    const formEl = document.querySelector('.meeting-form') || byId('meetingForm');
    const meetingMessage = byId('meetingMessage');
    const joinBtn = byId('joinMeeting');

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

    function showMeetingDetails(meeting){
      byId('meetingName').textContent = meeting.clientName || byId('name').value;
      byId('meetingDate').textContent = meeting.date;
      byId('meetingTime').textContent = meeting.time;
      byId('zoomLink').href = meeting.zoom || 'https://zoom.us/j/123456789';
      meetingInfoDiv.style.display = 'block';
      formEl.style.display = 'none';
    }

    function handleConfirm(e){
      if(e && e.preventDefault) e.preventDefault();
      const designerId = designerSelect ? (designerSelect.value || '').trim() : '';
      const date = dateInput ? dateInput.value : '';
      const time = timeInput ? timeInput.value : '';
      const clientName = byId('name') ? byId('name').value : 'Guest';

      if(!designerId || !date || !time){
        alert('Please fill all fields before confirming.');
        return;
      }

      const meeting = {
        id: 'mt_' + Math.random().toString(36).slice(2,9),
        designerId,
        designerName: designers[designerId] ? designers[designerId].name : designerId,
        zoom: 'https://zoom.us/j/123456789',
        date, time, clientName,
        createdAt: new Date().toISOString()
      };

      saveMeeting(meeting);
      showMeetingDetails(meeting);
      alert(`Meeting confirmed ✅\nWith: ${meeting.designerName}\n${meeting.date} ${meeting.time}`);
    }

    formEl.addEventListener('submit', handleConfirm);
    const confirmBtn = byId('confirmMeeting');
    if(confirmBtn) confirmBtn.addEventListener('click', handleConfirm);

    if(editMeetingBtn){
      editMeetingBtn.addEventListener('click', ()=>{
        formEl.style.display = 'block';
        meetingInfoDiv.style.display = 'none';
      });
    }

    if(cancelMeetingBtn2){
      cancelMeetingBtn2.addEventListener('click', ()=>{
        localStorage.removeItem(LS_KEY);
        formEl.style.display = 'block';
        meetingInfoDiv.style.display = 'none';
        formEl.reset();
        alert('Meeting cancelled, you can schedule a new one.');
      });
    }

    if(joinBtn){
      joinBtn.addEventListener('click', ()=>{
        const saved = getMeeting();
        if(!saved){ alert('No meeting found.'); return; }
        if(saved.zoom){
          window.open(saved.zoom, '_blank');
        } else {
          alert('Zoom link not available.');
        }
      });
    }

  }); // DOMContentLoaded
})();
