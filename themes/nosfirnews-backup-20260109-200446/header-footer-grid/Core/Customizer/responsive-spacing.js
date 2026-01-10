document.addEventListener('DOMContentLoaded',function(){
  document.querySelectorAll('.nn-spacing-input').forEach(function(el){
    el.addEventListener('keydown',function(e){
      if(e.key==='ArrowUp'){ e.preventDefault(); el.stepUp(); el.dispatchEvent(new Event('input',{bubbles:true})); }
      if(e.key==='ArrowDown'){ e.preventDefault(); el.stepDown(); el.dispatchEvent(new Event('input',{bubbles:true})); }
    });
  });
});