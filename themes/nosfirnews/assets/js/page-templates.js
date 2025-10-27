/**
 * Page Templates JavaScript
 * 
 * Interactive functionality for custom page templates
 *
 * @package NosfirNews
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Document ready
    $(document).ready(function() {
        initPageTemplates();
    });

    /**
     * Initialize all page template functionality
     */
    function initPageTemplates() {
        initLandingPage();
        initPortfolioPage();
        initBlogGridPage();
        initMagazinePage();
        initCommonFeatures();
    }

    /**
     * Landing Page Functionality
     */
    function initLandingPage() {
        if (!$('.landing-page').length) return;

        // Smooth scrolling for anchor links
        $('.scroll-down').on('click', function(e) {
            e.preventDefault();
            const target = $(this).attr('href');
            if ($(target).length) {
                $('html, body').animate({
                    scrollTop: $(target).offset().top - 80
                }, 800);
            }
        });

        // Parallax effect for hero background
        $(window).on('scroll', function() {
            const scrolled = $(this).scrollTop();
            const parallax = $('.hero-background');
            const speed = 0.5;
            
            if (parallax.length) {
                parallax.css('transform', 'translateY(' + (scrolled * speed) + 'px)');
            }
        });

        // Animate elements on scroll
        animateOnScroll();
    }

    /**
     * Portfolio Page Functionality
     */
    function initPortfolioPage() {
        if (!$('.portfolio-page').length) return;

        // Portfolio filtering
        $('.filter-btn').on('click', function() {
            const filter = $(this).data('filter');
            
            // Update active button
            $('.filter-btn').removeClass('active');
            $(this).addClass('active');
            
            // Filter portfolio items
            if (filter === '*') {
                $('.portfolio-item').fadeIn(300);
            } else {
                $('.portfolio-item').hide();
                $('.portfolio-item' + filter).fadeIn(300);
            }
        });

        // Portfolio lightbox
        if (typeof $.fn.magnificPopup !== 'undefined') {
            $('.portfolio-lightbox').magnificPopup({
                type: 'image',
                gallery: {
                    enabled: true
                },
                zoom: {
                    enabled: true,
                    duration: 300
                }
            });
        }

        // Load more functionality
        $('.load-more-btn').on('click', function() {
            const button = $(this);
            const currentPage = parseInt(button.data('page'));
            const maxPages = parseInt(button.data('max-pages'));
            
            if (currentPage < maxPages) {
                loadMorePortfolioItems(button, currentPage + 1);
            }
        });

        // Masonry layout
        initMasonryLayout('.portfolio-grid.masonry-layout');
    }

    /**
     * Blog Grid Page Functionality
     */
    function initBlogGridPage() {
        if (!$('.blog-grid-page').length) return;

        // Layout toggle
        $('.layout-btn').on('click', function() {
            const layout = $(this).data('layout');
            
            // Update active button
            $('.layout-btn').removeClass('active');
            $(this).addClass('active');
            
            // Update grid layout
            const grid = $('.blog-grid');
            grid.removeClass('grid-layout list-layout masonry-layout');
            grid.addClass(layout + '-layout');
            
            // Reinitialize masonry if needed
            if (layout === 'masonry') {
                setTimeout(() => {
                    initMasonryLayout('.blog-grid.masonry-layout');
                }, 100);
            }
        });

        // Blog filtering
        $('#category-filter, #tag-filter, #date-filter').on('change', function() {
            filterBlogPosts();
        });

        // Blog search
        let searchTimeout;
        $('#blog-search').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterBlogPosts();
            }, 500);
        });

        $('.search-btn').on('click', function() {
            filterBlogPosts();
        });

        // Initialize masonry for blog grid
        initMasonryLayout('.blog-grid.masonry-layout');
    }

    /**
     * Magazine Page Functionality
     */
    function initMagazinePage() {
        if (!$('.magazine-page').length) return;

        // Breaking news ticker
        initBreakingNewsTicker();

        // Featured stories slider
        if ($('.featured-grid').length && typeof $.fn.slick !== 'undefined') {
            $('.featured-grid').slick({
                dots: true,
                arrows: true,
                infinite: true,
                speed: 500,
                slidesToShow: 1,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 5000,
                responsive: [
                    {
                        breakpoint: 768,
                        settings: {
                            arrows: false
                        }
                    }
                ]
            });
        }

        // Trending posts animation
        animateOnScroll();
    }

    /**
     * Common Features
     */
    function initCommonFeatures() {
        // Smooth scrolling for all anchor links
        $('a[href^="#"]').on('click', function(e) {
            const target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 80
                }, 800);
            }
        });

        // Back to top button
        initBackToTop();

        // Lazy loading for images
        initLazyLoading();

        // Reading progress bar
        initReadingProgress();
    }

    /**
     * Filter blog posts
     */
    function filterBlogPosts() {
        const category = $('#category-filter').val();
        const tag = $('#tag-filter').val();
        const date = $('#date-filter').val();
        const search = $('#blog-search').val().toLowerCase();
        
        let visibleCount = 0;
        
        $('.loading-indicator').show();
        
        setTimeout(() => {
            $('.blog-item').each(function() {
                const item = $(this);
                let show = true;
                
                // Category filter
                if (category && !item.data('categories').toString().split(',').includes(category)) {
                    show = false;
                }
                
                // Tag filter
                if (tag && !item.data('tags').toString().split(',').includes(tag)) {
                    show = false;
                }
                
                // Date filter
                if (date) {
                    const itemDate = new Date(item.data('date'));
                    const now = new Date();
                    
                    switch (date) {
                        case 'today':
                            if (itemDate.toDateString() !== now.toDateString()) {
                                show = false;
                            }
                            break;
                        case 'week':
                            const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                            if (itemDate < weekAgo) {
                                show = false;
                            }
                            break;
                        case 'month':
                            if (itemDate.getMonth() !== now.getMonth() || itemDate.getFullYear() !== now.getFullYear()) {
                                show = false;
                            }
                            break;
                        case 'year':
                            if (itemDate.getFullYear() !== now.getFullYear()) {
                                show = false;
                            }
                            break;
                    }
                }
                
                // Search filter
                if (search && !item.find('.blog-title, .blog-excerpt').text().toLowerCase().includes(search)) {
                    show = false;
                }
                
                if (show) {
                    item.fadeIn(300);
                    visibleCount++;
                } else {
                    item.fadeOut(300);
                }
            });
            
            $('.loading-indicator').hide();
            $('.results-count').text(visibleCount + ' posts encontrados');
        }, 300);
    }

    /**
     * Load more portfolio items
     */
    function loadMorePortfolioItems(button, page) {
        button.text('Carregando...');
        
        // Simulate AJAX call (replace with actual AJAX)
        setTimeout(() => {
            button.data('page', page);
            button.text('Carregar Mais');
            
            if (page >= button.data('max-pages')) {
                button.hide();
            }
        }, 1000);
    }

    /**
     * Initialize masonry layout
     */
    function initMasonryLayout(selector) {
        if (typeof Masonry === 'undefined') return;
        
        const grid = document.querySelector(selector);
        if (!grid) return;
        
        const masonry = new Masonry(grid, {
            itemSelector: grid.classList.contains('portfolio-grid') ? '.portfolio-item' : '.blog-item',
            columnWidth: grid.classList.contains('portfolio-grid') ? '.portfolio-item' : '.blog-item',
            percentPosition: true,
            gutter: 20
        });
        
        // Layout after images load
        imagesLoaded(grid, function() {
            masonry.layout();
        });
    }

    /**
     * Breaking news ticker
     */
    function initBreakingNewsTicker() {
        const ticker = $('.breaking-slider');
        if (!ticker.length) return;
        
        // Clone items for continuous scroll
        const items = ticker.children().clone();
        ticker.append(items);
        
        // Pause on hover
        ticker.on('mouseenter', function() {
            $(this).css('animation-play-state', 'paused');
        }).on('mouseleave', function() {
            $(this).css('animation-play-state', 'running');
        });
    }

    /**
     * Animate elements on scroll
     */
    function animateOnScroll() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        // Observe elements
        document.querySelectorAll('.feature-item, .portfolio-item, .blog-item, .trending-item, .latest-item').forEach(el => {
            observer.observe(el);
        });
    }

    /**
     * Back to top button
     */
    function initBackToTop() {
        // Create back to top button if it doesn't exist
        if (!$('.back-to-top').length) {
            $('body').append('<button class="back-to-top" aria-label="Voltar ao topo"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 14L12 9L17 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>');
        }

        const backToTop = $('.back-to-top');

        $(window).on('scroll', function() {
            if ($(this).scrollTop() > 300) {
                backToTop.addClass('show');
            } else {
                backToTop.removeClass('show');
            }
        });

        backToTop.on('click', function() {
            $('html, body').animate({
                scrollTop: 0
            }, 600);
        });
    }

    /**
     * Lazy loading for images
     */
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }

    /**
     * Reading progress bar
     */
    function initReadingProgress() {
        if (!$('.single-post').length) return;

        $('body').append('<div class="reading-progress"><div class="reading-progress-bar"></div></div>');

        $(window).on('scroll', function() {
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (winScroll / height) * 100;
            
            $('.reading-progress-bar').css('width', scrolled + '%');
        });
    }

    // Fallback for browsers without jQuery
    if (typeof jQuery === 'undefined') {
        // Vanilla JS fallbacks for critical functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scrolling
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Portfolio filters
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const filter = this.dataset.filter;
                    
                    // Update active button
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Filter items
                    document.querySelectorAll('.portfolio-item').forEach(item => {
                        if (filter === '*' || item.classList.contains(filter.substring(1))) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });

            // Layout toggle
            document.querySelectorAll('.layout-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const layout = this.dataset.layout;
                    
                    // Update active button
                    document.querySelectorAll('.layout-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Update grid layout
                    const grid = document.querySelector('.blog-grid');
                    if (grid) {
                        grid.className = grid.className.replace(/\b\w+-layout\b/g, '');
                        grid.classList.add(layout + '-layout');
                    }
                });
            });
        });
    }

})(jQuery);

// CSS for animations and back to top button
const pageTemplateStyles = `
    .animate-in {
        animation: fadeInUp 0.6s ease forwards;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .back-to-top {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        width: 50px;
        height: 50px;
        background: #007cba;
        color: #fff;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transform: translateY(20px);
        transition: all 0.3s ease;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0, 124, 186, 0.3);
    }

    .back-to-top.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .back-to-top:hover {
        background: #005a87;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 124, 186, 0.4);
    }

    .reading-progress {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: rgba(0, 124, 186, 0.1);
        z-index: 1001;
    }

    .reading-progress-bar {
        height: 100%;
        background: #007cba;
        width: 0%;
        transition: width 0.3s ease;
    }

    .lazy {
        opacity: 0;
        transition: opacity 0.3s;
    }

    .lazy.loaded {
        opacity: 1;
    }

    /* Portfolio and blog item hover effects */
    .portfolio-item,
    .blog-item,
    .trending-item,
    .latest-item {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.6s ease;
    }

    .portfolio-item.animate-in,
    .blog-item.animate-in,
    .trending-item.animate-in,
    .latest-item.animate-in {
        opacity: 1;
        transform: translateY(0);
    }

    /* Stagger animation delays */
    .portfolio-item:nth-child(1) { transition-delay: 0.1s; }
    .portfolio-item:nth-child(2) { transition-delay: 0.2s; }
    .portfolio-item:nth-child(3) { transition-delay: 0.3s; }
    .portfolio-item:nth-child(4) { transition-delay: 0.4s; }

    .blog-item:nth-child(1) { transition-delay: 0.1s; }
    .blog-item:nth-child(2) { transition-delay: 0.2s; }
    .blog-item:nth-child(3) { transition-delay: 0.3s; }
    .blog-item:nth-child(4) { transition-delay: 0.4s; }

    /* Loading states */
    .loading-indicator {
        text-align: center;
        padding: 2rem;
        color: #007cba;
        font-weight: 500;
    }

    .loading-indicator::after {
        content: '';
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 2px solid #007cba;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 1s linear infinite;
        margin-left: 0.5rem;
        vertical-align: middle;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .back-to-top {
            bottom: 1rem;
            right: 1rem;
            width: 45px;
            height: 45px;
        }
    }
`;

// Inject styles
if (typeof document !== 'undefined') {
    const styleSheet = document.createElement('style');
    styleSheet.textContent = pageTemplateStyles;
    document.head.appendChild(styleSheet);
}