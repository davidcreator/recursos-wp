/**
 * Layout Sections JavaScript
 * 
 * @package NosfirNews
 * @since 1.0.0
 */

(function($) {
    'use strict';

    /**
     * Initialize layout sections functionality
     */
    function initLayoutSections() {
        initScrollAnimations();
        initLightbox();
        initParallax();
        initCounters();
        initTestimonialSlider();
        initSmoothScrolling();
        initLazyLoading();
    }

    /**
     * Initialize scroll animations
     */
    function initScrollAnimations() {
        const animatedElements = $('.fade-in, .slide-in-left, .slide-in-right, .scale-in');
        
        if (animatedElements.length === 0) return;

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    $(entry.target).addClass('visible');
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        animatedElements.each(function() {
            observer.observe(this);
        });
    }

    /**
     * Initialize lightbox for gallery
     */
    function initLightbox() {
        const lightboxLinks = $('.gallery-lightbox');
        
        if (lightboxLinks.length === 0) return;

        lightboxLinks.on('click', function(e) {
            e.preventDefault();
            
            const imageUrl = $(this).attr('href');
            const caption = $(this).find('img').attr('alt') || '';
            
            createLightboxModal(imageUrl, caption);
        });
    }

    /**
     * Create lightbox modal
     */
    function createLightboxModal(imageUrl, caption) {
        const modal = $(`
            <div class="nosfirnews-lightbox-modal">
                <div class="lightbox-overlay"></div>
                <div class="lightbox-content">
                    <button class="lightbox-close">&times;</button>
                    <img src="${imageUrl}" alt="${caption}" class="lightbox-image">
                    ${caption ? `<div class="lightbox-caption">${caption}</div>` : ''}
                </div>
            </div>
        `);

        $('body').append(modal);
        
        // Add styles if not already added
        if (!$('#nosfirnews-lightbox-styles').length) {
            $('head').append(`
                <style id="nosfirnews-lightbox-styles">
                    .nosfirnews-lightbox-modal {
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        z-index: 9999;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        opacity: 0;
                        transition: opacity 0.3s ease;
                    }
                    .nosfirnews-lightbox-modal.active {
                        opacity: 1;
                    }
                    .lightbox-overlay {
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(0, 0, 0, 0.9);
                        cursor: pointer;
                    }
                    .lightbox-content {
                        position: relative;
                        max-width: 90%;
                        max-height: 90%;
                        text-align: center;
                    }
                    .lightbox-image {
                        max-width: 100%;
                        max-height: 80vh;
                        border-radius: 8px;
                    }
                    .lightbox-close {
                        position: absolute;
                        top: -40px;
                        right: 0;
                        background: none;
                        border: none;
                        color: white;
                        font-size: 30px;
                        cursor: pointer;
                        padding: 5px 10px;
                        border-radius: 50%;
                        transition: background 0.3s ease;
                    }
                    .lightbox-close:hover {
                        background: rgba(255, 255, 255, 0.2);
                    }
                    .lightbox-caption {
                        color: white;
                        margin-top: 15px;
                        font-size: 16px;
                    }
                </style>
            `);
        }

        // Show modal
        setTimeout(() => modal.addClass('active'), 10);

        // Close modal events
        modal.find('.lightbox-close, .lightbox-overlay').on('click', function() {
            modal.removeClass('active');
            setTimeout(() => modal.remove(), 300);
        });

        // Close on escape key
        $(document).on('keyup.lightbox', function(e) {
            if (e.keyCode === 27) {
                modal.find('.lightbox-close').click();
                $(document).off('keyup.lightbox');
            }
        });
    }

    /**
     * Initialize parallax effect for hero sections
     */
    function initParallax() {
        const heroSections = $('.nosfirnews-hero-section');
        
        if (heroSections.length === 0) return;

        $(window).on('scroll', function() {
            const scrollTop = $(window).scrollTop();
            
            heroSections.each(function() {
                const $this = $(this);
                const offset = $this.offset().top;
                const height = $this.outerHeight();
                
                if (scrollTop + $(window).height() > offset && scrollTop < offset + height) {
                    const yPos = -(scrollTop - offset) * 0.5;
                    $this.css('background-position', `center ${yPos}px`);
                }
            });
        });
    }

    /**
     * Initialize animated counters
     */
    function initCounters() {
        const counters = $('.counter-number');
        
        if (counters.length === 0) return;

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    animateCounter($(entry.target));
                }
            });
        }, {
            threshold: 0.5
        });

        counters.each(function() {
            observer.observe(this);
        });
    }

    /**
     * Animate counter numbers
     */
    function animateCounter($counter) {
        const target = parseInt($counter.data('target')) || parseInt($counter.text());
        const duration = parseInt($counter.data('duration')) || 2000;
        const increment = target / (duration / 16);
        let current = 0;

        const timer = setInterval(function() {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            $counter.text(Math.floor(current));
        }, 16);
    }

    /**
     * Initialize testimonial slider
     */
    function initTestimonialSlider() {
        const testimonialSections = $('.nosfirnews-testimonials-section');
        
        testimonialSections.each(function() {
            const $section = $(this);
            const $grid = $section.find('.testimonials-grid');
            const items = $grid.find('.testimonial-item');
            
            if (items.length <= 1) return;

            // Add slider controls
            $section.append(`
                <div class="testimonial-controls">
                    <button class="testimonial-prev">‹</button>
                    <div class="testimonial-dots"></div>
                    <button class="testimonial-next">›</button>
                </div>
            `);

            const $dots = $section.find('.testimonial-dots');
            items.each(function(index) {
                $dots.append(`<button class="testimonial-dot ${index === 0 ? 'active' : ''}" data-slide="${index}"></button>`);
            });

            let currentSlide = 0;
            const totalSlides = items.length;

            function showSlide(index) {
                items.removeClass('active').eq(index).addClass('active');
                $dots.find('.testimonial-dot').removeClass('active').eq(index).addClass('active');
                currentSlide = index;
            }

            function nextSlide() {
                showSlide((currentSlide + 1) % totalSlides);
            }

            function prevSlide() {
                showSlide((currentSlide - 1 + totalSlides) % totalSlides);
            }

            // Event listeners
            $section.find('.testimonial-next').on('click', nextSlide);
            $section.find('.testimonial-prev').on('click', prevSlide);
            $section.find('.testimonial-dot').on('click', function() {
                showSlide(parseInt($(this).data('slide')));
            });

            // Auto-play
            setInterval(nextSlide, 5000);

            // Initialize first slide
            showSlide(0);
        });
    }

    /**
     * Initialize smooth scrolling for anchor links
     */
    function initSmoothScrolling() {
        $('a[href^="#"]').on('click', function(e) {
            const target = $(this.getAttribute('href'));
            
            if (target.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 80
                }, 800);
            }
        });
    }

    /**
     * Initialize lazy loading for images
     */
    function initLazyLoading() {
        const images = $('img[data-src]');
        
        if (images.length === 0) return;

        const imageObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        images.each(function() {
            imageObserver.observe(this);
        });
    }

    /**
     * Initialize sticky elements
     */
    function initStickyElements() {
        const stickyElements = $('.sticky-element');
        
        if (stickyElements.length === 0) return;

        $(window).on('scroll', function() {
            const scrollTop = $(window).scrollTop();
            
            stickyElements.each(function() {
                const $element = $(this);
                const offset = $element.data('offset') || 0;
                
                if (scrollTop > offset) {
                    $element.addClass('is-sticky');
                } else {
                    $element.removeClass('is-sticky');
                }
            });
        });
    }

    /**
     * Initialize progress bars
     */
    function initProgressBars() {
        const progressBars = $('.progress-bar');
        
        if (progressBars.length === 0) return;

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const $bar = $(entry.target);
                    const percentage = $bar.data('percentage') || 0;
                    
                    $bar.find('.progress-fill').animate({
                        width: percentage + '%'
                    }, 1500);
                }
            });
        }, {
            threshold: 0.5
        });

        progressBars.each(function() {
            observer.observe(this);
        });
    }

    /**
     * Initialize accordion functionality
     */
    function initAccordion() {
        $('.accordion-header').on('click', function() {
            const $header = $(this);
            const $content = $header.next('.accordion-content');
            const $accordion = $header.closest('.accordion-item');
            
            $accordion.toggleClass('active');
            $content.slideToggle(300);
            
            // Close other accordions in the same group
            $accordion.siblings('.accordion-item').removeClass('active')
                .find('.accordion-content').slideUp(300);
        });
    }

    /**
     * Initialize tabs functionality
     */
    function initTabs() {
        $('.tab-nav-item').on('click', function(e) {
            e.preventDefault();
            
            const $tab = $(this);
            const target = $tab.attr('href');
            const $tabGroup = $tab.closest('.tabs-container');
            
            // Update active tab
            $tabGroup.find('.tab-nav-item').removeClass('active');
            $tab.addClass('active');
            
            // Update active content
            $tabGroup.find('.tab-content').removeClass('active');
            $tabGroup.find(target).addClass('active');
        });
    }

    /**
     * Initialize tooltip functionality
     */
    function initTooltips() {
        $('[data-tooltip]').on('mouseenter', function() {
            const $element = $(this);
            const text = $element.data('tooltip');
            const position = $element.data('tooltip-position') || 'top';
            
            const tooltip = $(`<div class="nosfirnews-tooltip nosfirnews-tooltip-${position}">${text}</div>`);
            $('body').append(tooltip);
            
            const elementRect = this.getBoundingClientRect();
            const tooltipRect = tooltip[0].getBoundingClientRect();
            
            let top, left;
            
            switch (position) {
                case 'bottom':
                    top = elementRect.bottom + 10;
                    left = elementRect.left + (elementRect.width - tooltipRect.width) / 2;
                    break;
                case 'left':
                    top = elementRect.top + (elementRect.height - tooltipRect.height) / 2;
                    left = elementRect.left - tooltipRect.width - 10;
                    break;
                case 'right':
                    top = elementRect.top + (elementRect.height - tooltipRect.height) / 2;
                    left = elementRect.right + 10;
                    break;
                default: // top
                    top = elementRect.top - tooltipRect.height - 10;
                    left = elementRect.left + (elementRect.width - tooltipRect.width) / 2;
            }
            
            tooltip.css({ top: top, left: left }).fadeIn(200);
            
            $element.data('tooltip-element', tooltip);
        }).on('mouseleave', function() {
            const tooltip = $(this).data('tooltip-element');
            if (tooltip) {
                tooltip.fadeOut(200, function() {
                    tooltip.remove();
                });
            }
        });
    }

    /**
     * Initialize on document ready
     */
    $(document).ready(function() {
        initLayoutSections();
        initStickyElements();
        initProgressBars();
        initAccordion();
        initTabs();
        initTooltips();
    });

    /**
     * Reinitialize on window resize
     */
    $(window).on('resize', function() {
        // Debounce resize events
        clearTimeout(window.resizeTimeout);
        window.resizeTimeout = setTimeout(function() {
            initParallax();
        }, 250);
    });

    /**
     * Utility functions
     */
    window.NosfirNewsLayout = {
        reinitialize: initLayoutSections,
        showLightbox: createLightboxModal,
        animateCounter: animateCounter
    };

})(jQuery);