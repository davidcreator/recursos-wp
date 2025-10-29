(function ($) {
  $(function () {
    // Smooth scroll for internal anchors
    $('a[href*="#"]').on('click', function (e) {
      var href = $(this).attr('href');
      if (!href || href.indexOf('#') === -1) return;
      var id = href.split('#')[1];
      var target = document.getElementById(id);
      if (target) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: $(target).offset().top - 16 }, 400);
      }
    });

    // IntersectionObserver for reveal animations (alinhado com o markup atual)
    var selectors = ['.news-card', '.feature-card', '.testimonial-card', '.layout-columns .layout-column', '.demo-gallery .demo-gallery-item'];
    if ('IntersectionObserver' in window) {
      var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('animate-in');
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.1 });

      selectors.forEach(function (sel) {
        document.querySelectorAll(sel).forEach(function (el) {
          observer.observe(el);
        });
      });
    } else {
      selectors.forEach(function (sel) {
        document.querySelectorAll(sel).forEach(function (el) {
          el.classList.add('animate-in');
        });
      });
    }

    // Respect prefers-reduced-motion
    var prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReduced) {
      document.querySelectorAll('.animate-in').forEach(function (el) {
        el.style.animation = 'none';
      });
    }
  });
})(jQuery);