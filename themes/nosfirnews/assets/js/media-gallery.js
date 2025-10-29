/**
 * Advanced Media Gallery JavaScript
 * Handles lightbox, filtering, lazy loading, and gallery interactions
 */

(function($) {
    'use strict';

    // Gallery Manager Class
    class MediaGallery {
        constructor(element) {
            this.gallery = $(element);
            this.container = this.gallery.find('.gallery-container');
            this.items = this.gallery.find('.gallery-item');
            this.layout = this.gallery.data('layout') || 'grid';
            this.columns = this.gallery.data('columns') || 3;
            this.lightbox = this.gallery.data('lightbox') === true;
            this.lazy = this.gallery.data('lazy') === true;
            
            this.currentSlide = 0;
            this.isPlaying = false;
            this.autoplayInterval = null;
            this.lightboxImages = [];
            this.currentLightboxIndex = 0;
            
            this.init();
        }

        init() {
            this.setupLayout();
            this.setupLazyLoading();
            this.setupLightbox();
            this.setupFilters();
            this.setupSlider();
            this.setupCarousel();
            this.setupMasonry();
            this.setupKeyboardNavigation();
            this.setupTouchGestures();
            this.bindEvents();
        }

        setupLayout() {
            // Set CSS custom properties for responsive layouts
            this.gallery[0].style.setProperty('--gallery-columns', this.columns);
            
            // Add layout-specific classes
            this.gallery.addClass(`gallery-${this.layout}`);
            
            // Initialize layout-specific features
            switch (this.layout) {
                case 'slider':
                    this.setupSliderControls();
                    break;
                case 'carousel':
                    this.setupCarouselControls();
                    break;
                case 'masonry':
                    this.initMasonry();
                    break;
            }
        }

        setupLazyLoading() {
            if (!this.lazy) return;
            
            // Use Intersection Observer for lazy loading
            if ('IntersectionObserver' in window) {
                const lazyImages = this.gallery.find('img.lazy');
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            img.classList.add('loaded');
                            imageObserver.unobserve(img);
                        }
                    });
                });
                
                lazyImages.each(function() {
                    imageObserver.observe(this);
                });
            } else {
                // Fallback for older browsers
                this.gallery.find('img.lazy').each(function() {
                    const img = $(this);
                    img.attr('src', img.data('src'));
                    img.removeClass('lazy').addClass('loaded');
                });
            }
        }

        setupLightbox() {
            if (!this.lightbox) return;
            
            // Collect all lightbox images
            this.lightboxImages = [];
            this.gallery.find('.gallery-lightbox-link').each((index, link) => {
                const $link = $(link);
                this.lightboxImages.push({
                    src: $link.attr('href'),
                    caption: $link.data('caption') || '',
                    alt: $link.data('alt') || ''
                });
            });
            
            // Create lightbox HTML
            this.createLightboxHTML();
        }

        createLightboxHTML() {
            const lightboxHTML = `
                <div class="gallery-lightbox-overlay">
                    <div class="gallery-lightbox-content">
                        <img class="gallery-lightbox-image" src="" alt="">
                        <div class="gallery-lightbox-caption"></div>
                        <button class="gallery-lightbox-close" aria-label="Fechar lightbox">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <button class="gallery-lightbox-nav gallery-lightbox-prev" aria-label="Imagem anterior">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <button class="gallery-lightbox-nav gallery-lightbox-next" aria-label="Próxima imagem">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            
            $('body').append(lightboxHTML);
            this.lightboxOverlay = $('.gallery-lightbox-overlay');
            this.lightboxImage = $('.gallery-lightbox-image');
            this.lightboxCaption = $('.gallery-lightbox-caption');
        }

        setupFilters() {
            const filters = this.gallery.find('.gallery-filters');
            if (!filters.length) return;
            
            filters.on('click', '.filter-btn', (e) => {
                e.preventDefault();
                const btn = $(e.target);
                const filter = btn.data('filter');
                
                // Update active state
                filters.find('.filter-btn').removeClass('active');
                btn.addClass('active');
                
                // Filter items
                this.filterItems(filter);
            });
        }

        filterItems(filter) {
            this.items.each(function() {
                const item = $(this);
                const category = item.data('category');
                
                if (filter === '*' || category === filter) {
                    item.show().addClass('gallery-fade-in');
                } else {
                    item.hide().removeClass('gallery-fade-in');
                }
            });
            
            // Reinitialize masonry if needed
            if (this.layout === 'masonry') {
                setTimeout(() => this.initMasonry(), 300);
            }
        }

        setupSlider() {
            if (this.layout !== 'slider') return;
            
            this.setupSliderControls();
            this.setupAutoplay();
        }

        setupSliderControls() {
            const navHTML = `
                <button class="gallery-nav gallery-nav-prev" aria-label="Slide anterior">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <button class="gallery-nav gallery-nav-next" aria-label="Próximo slide">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            `;
            
            this.gallery.append(navHTML);
            
            // Add indicators
            const indicatorsHTML = `
                <div class="gallery-indicators">
                    ${this.items.map((index) => 
                        `<button class="gallery-indicator ${index === 0 ? 'active' : ''}" data-slide="${index}"></button>`
                    ).join('')}
                </div>
            `;
            
            this.gallery.append(indicatorsHTML);
        }

        setupAutoplay() {
            const autoplay = this.gallery.data('autoplay');
            const speed = this.gallery.data('autoplay-speed') || 3000;
            
            if (autoplay) {
                this.startAutoplay(speed);
                
                // Pause on hover
                this.gallery.on('mouseenter', () => this.pauseAutoplay());
                this.gallery.on('mouseleave', () => this.startAutoplay(speed));
            }
        }

        startAutoplay(speed) {
            this.isPlaying = true;
            this.autoplayInterval = setInterval(() => {
                this.nextSlide();
            }, speed);
        }

        pauseAutoplay() {
            this.isPlaying = false;
            if (this.autoplayInterval) {
                clearInterval(this.autoplayInterval);
            }
        }

        nextSlide() {
            this.currentSlide = (this.currentSlide + 1) % this.items.length;
            this.goToSlide(this.currentSlide);
        }

        prevSlide() {
            this.currentSlide = this.currentSlide === 0 ? this.items.length - 1 : this.currentSlide - 1;
            this.goToSlide(this.currentSlide);
        }

        goToSlide(index) {
            this.currentSlide = index;
            const translateX = -index * 100;
            this.container.css('transform', `translateX(${translateX}%)`);
            
            // Update indicators
            this.gallery.find('.gallery-indicator').removeClass('active');
            this.gallery.find(`.gallery-indicator[data-slide="${index}"]`).addClass('active');
            
            // Update thumbnails
            this.gallery.find('.gallery-thumb').removeClass('active');
            this.gallery.find(`.gallery-thumb[data-slide="${index}"]`).addClass('active');
        }

        setupCarousel() {
            if (this.layout !== 'carousel') return;
            
            this.setupCarouselControls();
        }

        setupCarouselControls() {
            const navHTML = `
                <button class="gallery-nav gallery-nav-prev" aria-label="Slides anteriores">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <button class="gallery-nav gallery-nav-next" aria-label="Próximos slides">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            `;
            
            this.gallery.append(navHTML);
        }

        setupMasonry() {
            if (this.layout !== 'masonry') return;
            
            // Wait for images to load before initializing masonry
            this.gallery.find('img').on('load', () => {
                this.initMasonry();
            });
        }

        initMasonry() {
            if (typeof Masonry !== 'undefined') {
                new Masonry(this.container[0], {
                    itemSelector: '.gallery-item',
                    columnWidth: '.gallery-item',
                    gutter: 16,
                    percentPosition: true
                });
            }
        }

        setupKeyboardNavigation() {
            $(document).on('keydown', (e) => {
                if (!this.lightboxOverlay || !this.lightboxOverlay.hasClass('active')) return;
                
                switch (e.key) {
                    case 'Escape':
                        this.closeLightbox();
                        break;
                    case 'ArrowLeft':
                        this.prevLightboxImage();
                        break;
                    case 'ArrowRight':
                        this.nextLightboxImage();
                        break;
                }
            });
        }

        setupTouchGestures() {
            let startX = 0;
            let startY = 0;
            
            this.gallery.on('touchstart', (e) => {
                startX = e.originalEvent.touches[0].clientX;
                startY = e.originalEvent.touches[0].clientY;
            });
            
            this.gallery.on('touchend', (e) => {
                if (!startX || !startY) return;
                
                const endX = e.originalEvent.changedTouches[0].clientX;
                const endY = e.originalEvent.changedTouches[0].clientY;
                
                const diffX = startX - endX;
                const diffY = startY - endY;
                
                // Only handle horizontal swipes
                if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
                    if (diffX > 0) {
                        // Swipe left - next
                        if (this.layout === 'slider') {
                            this.nextSlide();
                        }
                    } else {
                        // Swipe right - prev
                        if (this.layout === 'slider') {
                            this.prevSlide();
                        }
                    }
                }
                
                startX = 0;
                startY = 0;
            });
        }

        bindEvents() {
            // Lightbox events
            if (this.lightbox) {
                this.gallery.on('click', '.gallery-lightbox-link', (e) => {
                    e.preventDefault();
                    const index = this.gallery.find('.gallery-lightbox-link').index(e.currentTarget);
                    this.openLightbox(index);
                });
                
                $(document).on('click', '.gallery-lightbox-close', () => this.closeLightbox());
                $(document).on('click', '.gallery-lightbox-overlay', (e) => {
                    if (e.target === e.currentTarget) {
                        this.closeLightbox();
                    }
                });
                $(document).on('click', '.gallery-lightbox-prev', () => this.prevLightboxImage());
                $(document).on('click', '.gallery-lightbox-next', () => this.nextLightboxImage());
            }
            
            // Slider/Carousel navigation
            this.gallery.on('click', '.gallery-nav-prev', () => {
                if (this.layout === 'slider') {
                    this.prevSlide();
                } else if (this.layout === 'carousel') {
                    this.prevCarouselSlide();
                }
            });
            
            this.gallery.on('click', '.gallery-nav-next', () => {
                if (this.layout === 'slider') {
                    this.nextSlide();
                } else if (this.layout === 'carousel') {
                    this.nextCarouselSlide();
                }
            });
            
            // Slider indicators
            this.gallery.on('click', '.gallery-indicator', (e) => {
                const slideIndex = parseInt($(e.target).data('slide'));
                this.goToSlide(slideIndex);
            });
            
            // Slider thumbnails
            this.gallery.on('click', '.gallery-thumb', (e) => {
                const slideIndex = parseInt($(e.target).data('slide'));
                this.goToSlide(slideIndex);
            });
            
            // Window resize
            $(window).on('resize', this.debounce(() => {
                this.handleResize();
            }, 250));
        }

        openLightbox(index) {
            this.currentLightboxIndex = index;
            this.updateLightboxImage();
            this.lightboxOverlay.addClass('active');
            $('body').addClass('lightbox-open');
        }

        closeLightbox() {
            this.lightboxOverlay.removeClass('active');
            $('body').removeClass('lightbox-open');
        }

        updateLightboxImage() {
            const image = this.lightboxImages[this.currentLightboxIndex];
            if (!image) return;
            
            this.lightboxImage.attr('src', image.src).attr('alt', image.alt);
            this.lightboxCaption.text(image.caption);
            
            // Show/hide navigation buttons
            const prevBtn = $('.gallery-lightbox-prev');
            const nextBtn = $('.gallery-lightbox-next');
            
            if (this.lightboxImages.length <= 1) {
                prevBtn.hide();
                nextBtn.hide();
            } else {
                prevBtn.toggle(this.currentLightboxIndex > 0);
                nextBtn.toggle(this.currentLightboxIndex < this.lightboxImages.length - 1);
            }
        }

        nextLightboxImage() {
            if (this.currentLightboxIndex < this.lightboxImages.length - 1) {
                this.currentLightboxIndex++;
                this.updateLightboxImage();
            }
        }

        prevLightboxImage() {
            if (this.currentLightboxIndex > 0) {
                this.currentLightboxIndex--;
                this.updateLightboxImage();
            }
        }

        nextCarouselSlide() {
            const itemWidth = this.items.first().outerWidth(true);
            const containerWidth = this.container.parent().width();
            const visibleItems = Math.floor(containerWidth / itemWidth);
            const maxSlide = Math.max(0, this.items.length - visibleItems);
            
            if (this.currentSlide < maxSlide) {
                this.currentSlide++;
                const translateX = -this.currentSlide * itemWidth;
                this.container.css('transform', `translateX(${translateX}px)`);
            }
        }

        prevCarouselSlide() {
            if (this.currentSlide > 0) {
                this.currentSlide--;
                const itemWidth = this.items.first().outerWidth(true);
                const translateX = -this.currentSlide * itemWidth;
                this.container.css('transform', `translateX(${translateX}px)`);
            }
        }

        handleResize() {
            // Recalculate layout on resize
            if (this.layout === 'masonry') {
                this.initMasonry();
            } else if (this.layout === 'carousel') {
                this.currentSlide = 0;
                this.container.css('transform', 'translateX(0)');
            }
        }

        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    }

    // Initialize galleries when document is ready
    $(document).ready(() => {
        $('.nosfirnews-gallery').each(function() {
            new MediaGallery(this);
        });
    });

    // Reinitialize galleries after AJAX content loads
    $(document).on('gallery:refresh', () => {
        $('.nosfirnews-gallery').each(function() {
            if (!$(this).data('gallery-initialized')) {
                new MediaGallery(this);
                $(this).data('gallery-initialized', true);
            }
        });
    });

    // Add CSS for lightbox body class
    const style = document.createElement('style');
    style.textContent = `
        body.lightbox-open {
            overflow: hidden;
        }
    `;
    document.head.appendChild(style);

})(jQuery);