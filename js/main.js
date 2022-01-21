function markOnLeave(id, submit) {
  let on_leave = document.getElementById(id);
  on_leave.addEventListener('change', ()=>{
    document.getElementById(submit).click();
  });
}