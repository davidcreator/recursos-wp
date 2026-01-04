(function(){
  function qs(sel,ctx){return (ctx||document).querySelector(sel)}
  function toggleDesktopMenu(){
    var nav = qs('.main-navigation .nav-menu');
    if(!nav) return;
    nav.style.display = nav.style.display==='block' ? '' : 'block';
  }
  function openMobileDrawer(){
    var drawer = qs('#mobile-menu');
    if(!drawer) return;
    drawer.setAttribute('aria-hidden','false');
    drawer.classList.add('open');
    var toggle = qs('.nav-toggle');
    if(toggle) toggle.setAttribute('aria-expanded','true');
    var closeBtn = qs('.drawer-close', drawer);
    if(closeBtn) closeBtn.focus();
    document.body.classList.add('nn-drawer-open');
  }
  function closeMobileDrawer(){
    var drawer = qs('#mobile-menu');
    if(!drawer) return;
    drawer.setAttribute('aria-hidden','true');
    drawer.classList.remove('open');
    var toggle = qs('.nav-toggle');
    if(toggle) toggle.setAttribute('aria-expanded','false');
    document.body.classList.remove('nn-drawer-open');
  }
  function toggleSearch(){
    var form = qs('.search-form');
    if(!form) return;
    var s = qs('.search-field',form);
    form.style.display = form.style.display==='block' ? '' : 'block';
    if(s) s.focus();
  }
  document.addEventListener('click',function(e){
    if(e.target.closest('.nav-toggle')){
      e.preventDefault();
      var drawer = qs('#mobile-menu');
      if(drawer) {
        if(drawer.classList.contains('open')) closeMobileDrawer(); else openMobileDrawer();
      } else {
        toggleDesktopMenu();
      }
    }
    if(e.target.closest('.drawer-close')){ e.preventDefault(); closeMobileDrawer(); }
    if(e.target.closest('.search-toggle')){ e.preventDefault(); toggleSearch(); }
    if(e.target.classList.contains('nn-mobile-drawer') && e.target.id==='mobile-menu'){ closeMobileDrawer(); }
  });
  document.addEventListener('keydown',function(e){
    if(e.key==='Escape'){ closeMobileDrawer(); }
  });
})();
