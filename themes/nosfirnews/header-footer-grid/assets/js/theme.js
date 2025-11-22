(function(){
  function qs(sel,ctx){return (ctx||document).querySelector(sel)}
  function qsa(sel,ctx){return Array.prototype.slice.call((ctx||document).querySelectorAll(sel))}
  function toggleMenu(){var nav=qs('.main-navigation .nav-menu');if(!nav)return;nav.style.display=nav.style.display==='block'?'':'block'}
  function toggleSearch(){var form=qs('.search-form');if(!form)return;var s=qs('.search-field',form);form.style.display=form.style.display==='block'?'':'block';if(s)s.focus()}
  document.addEventListener('click',function(e){
    if(e.target.closest('.menu-toggle')){e.preventDefault();toggleMenu()}
    if(e.target.closest('.search-toggle')){e.preventDefault();toggleSearch()}
  })
})();