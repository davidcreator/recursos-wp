(function(){
  // Initialize Swiper sliders
  function initSliders(){
    if (typeof Swiper === 'undefined') return;
    document.querySelectorAll('.brdposts-slider.swiper').forEach(function(el){
      var swiper = new Swiper(el, {
        loop: true,
        slidesPerView: 1,
        pagination: {
          el: el.querySelector('.swiper-pagination'),
          clickable: true
        },
        navigation: {
          nextEl: el.querySelector('.swiper-button-next'),
          prevEl: el.querySelector('.swiper-button-prev')
        }
      });
    });
  }

  // Initialize ticker (simple marquee effect)
  function initTickers(){
    document.querySelectorAll('.brdposts-ticker .brdposts-ticker-content').forEach(function(el){
      var totalWidth = 0;
      el.querySelectorAll('.brdposts-ticker-item').forEach(function(item){
        totalWidth += item.offsetWidth + 30;
      });
      el.style.width = (totalWidth + 50) + 'px';

      var offset = 0;
      function step(){
        offset -= 1; // speed
        if (-offset > totalWidth) offset = 0;
        el.style.transform = 'translateX(' + offset + 'px)';
        requestAnimationFrame(step);
      }
      requestAnimationFrame(step);
    });
  }

  function init(){
    initSliders();
    initTickers();
  }

  if (document.readyState === 'complete' || document.readyState === 'interactive') {
    init();
  } else {
    document.addEventListener('DOMContentLoaded', init);
  }
})();