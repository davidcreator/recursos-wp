/**
 * NosfirNews Modern JavaScript
 * @package NosfirNews
 * @since 2.0.0
 * Modern ES6+ JavaScript with performance optimizations
 */

// Theme configuration
const THEME_CONFIG = {
    breakpoints: {
        mobile: 768,
        tablet: 1024,
        desktop: 1200
    },
    animations: {
        duration: 300,
        easing: 'ease-in-out'
    },
    performance: {
        debounceDelay: 100,
        throttleDelay: 16
    }
};

// Utility functions
const utils = {
    // Debounce function for performance
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
    },

    // Throttle function for scroll events
    throttle(func, limit) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    },

    // Check if element is in viewport
    isInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    },

    // Get current breakpoint
    getCurrentBreakpoint() {
        const width = window.innerWidth;
        if (width <= THEME_CONFIG.breakpoints.mobile) return 'mobile';
        if (width <= THEME_CONFIG.breakpoints.tablet) return 'tablet';
        return 'desktop';
    },

    // Create element with attributes
    createElement(tag, attributes = {}, content = '') {
        const element = document.createElement(tag);
        Object.entries(attributes).forEach(([key, value]) => {
            if (key === 'className') {
                element.className = value;
            } else if (key === 'innerHTML') {
                element.innerHTML = value;
            } else {
                element.setAttribute(key, value);
            }
        });
        if (content) element.textContent = content;
        return element;
    }
};

// Main theme class
class NosfirNewsTheme {
    constructor() {
        this.components = new Map();
        this.init();
    }

    async init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.initializeComponents());
        } else {
            this.initializeComponents();
        }
    }

    initializeComponents() {
        try {
            // Initialize all components
            this.components.set('mobileMenu', new MobileMenu());
            this.components.set('smoothScrolling', new SmoothScrolling());
            this.components.set('backToTop', new BackToTop());
            this.components.set('searchToggle', new SearchToggle());
            this.components.set('lazyLoading', new LazyLoading());
            this.components.set('readingProgress', new ReadingProgress());
            this.components.set('socialSharing', new SocialSharing());
            this.components.set('accessibility', new AccessibilityEnhancements());
            
            console.log('NosfirNews theme initialized successfully');
        } catch (error) {
            console.error('Error initializing theme components:', error);
        }
    }

    getComponent(name) {
        return this.components.get(name);
    }
}

// Mobile menu component
class MobileMenu {
    constructor() {
        this.menuToggle = document.querySelector('.menu-toggle');
        this.navigation = document.querySelector('.main-navigation ul');
        this.isOpen = false;
        
        if (this.menuToggle && this.navigation) {
            this.init();
        }
    }

    init() {
        this.createMenuToggleHTML();
        this.bindEvents();
        this.setupAccessibility();
    }

    createMenuToggleHTML() {
        if (!this.menuToggle.querySelector('span')) {
            this.menuToggle.innerHTML = `
                <span></span>
                <span></span>
                <span></span>
                <span class="sr-only">Toggle menu</span>
            `;
        }
    }

    bindEvents() {
        this.menuToggle.addEventListener('click', (e) => this.toggleMenu(e));
        
        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (this.isOpen && !this.menuToggle.contains(e.target) && !this.navigation.contains(e.target)) {
                this.closeMenu();
            }
        });

        // Close menu on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.closeMenu();
                this.menuToggle.focus();
            }
        });

        // Handle resize
        window.addEventListener('resize', utils.debounce(() => this.handleResize(), THEME_CONFIG.performance.debounceDelay));
    }

    setupAccessibility() {
        this.menuToggle.setAttribute('aria-expanded', 'false');
        this.menuToggle.setAttribute('aria-controls', 'primary-menu');
        this.navigation.setAttribute('id', 'primary-menu');
    }

    toggleMenu(e) {
        e.preventDefault();
        this.isOpen ? this.closeMenu() : this.openMenu();
    }

    openMenu() {
        this.navigation.classList.add('toggled');
        this.menuToggle.classList.add('active');
        this.menuToggle.setAttribute('aria-expanded', 'true');
        this.isOpen = true;
        
        // Focus first menu item
        const firstMenuItem = this.navigation.querySelector('a');
        if (firstMenuItem) {
            setTimeout(() => firstMenuItem.focus(), 100);
        }
    }

    closeMenu() {
        this.navigation.classList.remove('toggled');
        this.menuToggle.classList.remove('active');
        this.menuToggle.setAttribute('aria-expanded', 'false');
        this.isOpen = false;
    }

    handleResize() {
        if (utils.getCurrentBreakpoint() !== 'mobile' && this.isOpen) {
            this.closeMenu();
        }
    }
}

// Smooth scrolling component
class SmoothScrolling {
    constructor() {
        this.init();
    }

    init() {
        const links = document.querySelectorAll('a[href^="#"]');
        
        links.forEach(link => {
            link.addEventListener('click', (e) => this.handleClick(e, link));
        });
    }

    handleClick(e, link) {
        const targetId = link.getAttribute('href');
        
        // Skip if it's just a hash
        if (targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        
        if (targetElement) {
            e.preventDefault();
            this.scrollToElement(targetElement);
        }
    }

    scrollToElement(element) {
        const headerHeight = document.querySelector('.site-header')?.offsetHeight || 0;
        const targetPosition = element.offsetTop - headerHeight - 20;
        
        window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
        });
    }
}

// Back to top component
class BackToTop {
    constructor() {
        this.button = this.createButton();
        this.isVisible = false;
        this.init();
    }

    createButton() {
        let button = document.querySelector('.back-to-top');
        
        if (!button) {
            button = utils.createElement('button', {
                className: 'back-to-top',
                'aria-label': 'Back to top',
                innerHTML: `
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 19V5M5 12L12 5L19 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="sr-only">Back to top</span>
                `
            });
            document.body.appendChild(button);
        }
        
        return button;
    }

    init() {
        this.bindEvents();
        this.addStyles();
    }

    bindEvents() {
        this.button.addEventListener('click', (e) => this.scrollToTop(e));
        
        window.addEventListener('scroll', 
            utils.throttle(() => this.handleScroll(), THEME_CONFIG.performance.throttleDelay)
        );
    }

    handleScroll() {
        const shouldShow = window.pageYOffset > 300;
        
        if (shouldShow && !this.isVisible) {
            this.showButton();
        } else if (!shouldShow && this.isVisible) {
            this.hideButton();
        }
    }

    showButton() {
        this.button.classList.add('visible');
        this.isVisible = true;
    }

    hideButton() {
        this.button.classList.remove('visible');
        this.isVisible = false;
    }

    scrollToTop(e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    addStyles() {
        if (!document.querySelector('#back-to-top-styles')) {
            const styles = utils.createElement('style', {
                id: 'back-to-top-styles',
                innerHTML: `
                    .back-to-top {
                        position: fixed;
                        bottom: 2rem;
                        right: 2rem;
                        width: 3rem;
                        height: 3rem;
                        background-color: var(--color-primary);
                        color: white;
                        border: none;
                        border-radius: 50%;
                        cursor: pointer;
                        opacity: 0;
                        visibility: hidden;
                        transform: translateY(1rem);
                        transition: all var(--transition-normal);
                        z-index: var(--z-fixed);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        box-shadow: var(--box-shadow-lg);
                    }
                    
                    .back-to-top:hover,
                    .back-to-top:focus {
                        background-color: var(--color-primary-dark);
                        transform: translateY(0);
                    }
                    
                    .back-to-top.visible {
                        opacity: 1;
                        visibility: visible;
                        transform: translateY(0);
                    }
                `
            });
            document.head.appendChild(styles);
        }
    }
}

// Search toggle component
class SearchToggle {
    constructor() {
        this.searchToggle = document.querySelector('.search-toggle');
        this.searchForm = document.querySelector('.search-form');
        this.isOpen = false;
        
        if (this.searchToggle && this.searchForm) {
            this.init();
        }
    }

    init() {
        this.bindEvents();
        this.setupAccessibility();
    }

    bindEvents() {
        this.searchToggle.addEventListener('click', (e) => this.toggleSearch(e));
        
        // Close search on outside click
        document.addEventListener('click', (e) => {
            if (this.isOpen && !this.searchToggle.contains(e.target) && !this.searchForm.contains(e.target)) {
                this.closeSearch();
            }
        });

        // Close search on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.closeSearch();
                this.searchToggle.focus();
            }
        });
    }

    setupAccessibility() {
        this.searchToggle.setAttribute('aria-expanded', 'false');
        this.searchToggle.setAttribute('aria-controls', 'search-form');
        this.searchForm.setAttribute('id', 'search-form');
    }

    toggleSearch(e) {
        e.preventDefault();
        this.isOpen ? this.closeSearch() : this.openSearch();
    }

    openSearch() {
        this.searchForm.classList.add('active');
        this.searchToggle.classList.add('active');
        this.searchToggle.setAttribute('aria-expanded', 'true');
        this.isOpen = true;
        
        // Focus search input
        const searchInput = this.searchForm.querySelector('input[type="search"]');
        if (searchInput) {
            setTimeout(() => searchInput.focus(), 100);
        }
    }

    closeSearch() {
        this.searchForm.classList.remove('active');
        this.searchToggle.classList.remove('active');
        this.searchToggle.setAttribute('aria-expanded', 'false');
        this.isOpen = false;
    }
}

// Lazy loading component
class LazyLoading {
    constructor() {
        this.init();
    }

    init() {
        if ('IntersectionObserver' in window) {
            this.setupIntersectionObserver();
        } else {
            this.fallbackLazyLoading();
        }
    }

    setupIntersectionObserver() {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.loadImage(entry.target);
                    imageObserver.unobserve(entry.target);
                }
            });
        }, {
            rootMargin: '50px 0px',
            threshold: 0.01
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }

    fallbackLazyLoading() {
        const images = document.querySelectorAll('img[data-src]');
        
        const loadImagesInViewport = utils.throttle(() => {
            images.forEach(img => {
                if (utils.isInViewport(img)) {
                    this.loadImage(img);
                }
            });
        }, THEME_CONFIG.performance.throttleDelay);

        window.addEventListener('scroll', loadImagesInViewport);
        window.addEventListener('resize', loadImagesInViewport);
        loadImagesInViewport(); // Initial check
    }

    loadImage(img) {
        img.src = img.dataset.src;
        img.classList.remove('lazy');
        img.classList.add('loaded');
        img.removeAttribute('data-src');
    }
}

// Reading progress component
class ReadingProgress {
    constructor() {
        this.progressBar = this.createProgressBar();
        this.init();
    }

    createProgressBar() {
        let progressBar = document.querySelector('.reading-progress');
        
        if (!progressBar) {
            progressBar = utils.createElement('div', {
                className: 'reading-progress',
                innerHTML: '<div class="reading-progress-bar"></div>'
            });
            document.body.appendChild(progressBar);
        }
        
        return progressBar;
    }

    init() {
        this.bindEvents();
        this.addStyles();
    }

    bindEvents() {
        window.addEventListener('scroll', 
            utils.throttle(() => this.updateProgress(), THEME_CONFIG.performance.throttleDelay)
        );
    }

    updateProgress() {
        const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (winScroll / height) * 100;
        
        const progressBar = this.progressBar.querySelector('.reading-progress-bar');
        if (progressBar) {
            progressBar.style.width = scrolled + '%';
        }
    }

    addStyles() {
        if (!document.querySelector('#reading-progress-styles')) {
            const styles = utils.createElement('style', {
                id: 'reading-progress-styles',
                innerHTML: `
                    .reading-progress {
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 3px;
                        background-color: var(--color-border-light);
                        z-index: var(--z-fixed);
                    }
                    
                    .reading-progress-bar {
                        height: 100%;
                        background-color: var(--color-primary);
                        width: 0%;
                        transition: width 0.1s ease;
                    }
                `
            });
            document.head.appendChild(styles);
        }
    }
}

// Social sharing component
class SocialSharing {
    constructor() {
        this.init();
    }

    init() {
        const shareButtons = document.querySelectorAll('.social-share-button');
        shareButtons.forEach(button => {
            button.addEventListener('click', (e) => this.handleShare(e, button));
        });
    }

    handleShare(e, button) {
        e.preventDefault();
        
        const platform = button.dataset.platform;
        const url = encodeURIComponent(window.location.href);
        const title = encodeURIComponent(document.title);
        
        let shareUrl = '';
        
        switch (platform) {
            case 'facebook':
                shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
                break;
            case 'twitter':
                shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
                break;
            case 'linkedin':
                shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
                break;
            case 'whatsapp':
                shareUrl = `https://wa.me/?text=${title}%20${url}`;
                break;
        }
        
        if (shareUrl) {
            window.open(shareUrl, '_blank', 'width=600,height=400');
        }
    }
}

// Accessibility enhancements
class AccessibilityEnhancements {
    constructor() {
        this.init();
    }

    init() {
        this.addSkipLinks();
        this.enhanceFocusManagement();
        this.addScreenReaderStyles();
    }

    addSkipLinks() {
        if (!document.querySelector('.skip-link')) {
            const skipLink = utils.createElement('a', {
                className: 'skip-link',
                href: '#main',
                innerHTML: 'Skip to main content'
            });
            document.body.insertBefore(skipLink, document.body.firstChild);
        }
    }

    enhanceFocusManagement() {
        // Add focus indicators for keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                document.body.classList.add('keyboard-navigation');
            }
        });

        document.addEventListener('mousedown', () => {
            document.body.classList.remove('keyboard-navigation');
        });
    }

    addScreenReaderStyles() {
        if (!document.querySelector('#accessibility-styles')) {
            const styles = utils.createElement('style', {
                id: 'accessibility-styles',
                innerHTML: `
                    .sr-only {
                        position: absolute;
                        width: 1px;
                        height: 1px;
                        padding: 0;
                        margin: -1px;
                        overflow: hidden;
                        clip: rect(0, 0, 0, 0);
                        white-space: nowrap;
                        border: 0;
                    }
                    
                    .keyboard-navigation *:focus {
                        outline: 2px solid var(--color-primary) !important;
                        outline-offset: 2px !important;
                    }
                `
            });
            document.head.appendChild(styles);
        }
    }
}

// Initialize theme
const nosfirNewsTheme = new NosfirNewsTheme();

// Export for global access
window.nosfirNewsTheme = nosfirNewsTheme;