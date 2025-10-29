/**
 * Responsive JavaScript for NosfirNews Theme
 * @package NosfirNews
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Responsive System Object
    const NosfirNewsResponsive = {
        
        // Configuration
        config: {
            breakpoints: {
                mobile: 767,
                tablet: 1024,
                desktop: 1200
            },
            debounceDelay: 250,
            mobileMenuSelector: '.mobile-menu-toggle',
            navigationSelector: '.main-navigation',
            sidebarSelector: '.sidebar'
        },

        // Current breakpoint
        currentBreakpoint: '',

        // Initialize
        init: function() {
            this.bindEvents();
            this.checkBreakpoint();
            this.initMobileMenu();
            this.initResponsiveImages();
            this.initResponsiveEmbeds();
            if (this.initTouchNavigation) {
                this.initTouchNavigation();
            }
            this.initResponsiveTables();
            this.initLazyLoad();
        },

        // Bind events
        bindEvents: function() {
            const self = this;
            
            // Window resize with debounce
            $(window).on('resize', this.debounce(function() {
                self.checkBreakpoint();
                self.handleResize();
            }, this.config.debounceDelay));

            // Orientation change
            $(window).on('orientationchange', function() {
                setTimeout(function() {
                    self.checkBreakpoint();
                    self.handleResize();
                }, 100);
            });

            // Mobile menu toggle
            $(document).on('click', this.config.mobileMenuSelector, function(e) {
                e.preventDefault();
                self.toggleMobileMenu();
            });

            // Close mobile menu on outside click
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.site-header').length) {
                    self.closeMobileMenu();
                }
            });

            // Escape key to close mobile menu
            $(document).on('keydown', function(e) {
                if (e.keyCode === 27) {
                    self.closeMobileMenu();
                }
            });
        },

        // Check current breakpoint
        checkBreakpoint: function() {
            const width = $(window).width();
            let newBreakpoint = '';

            if (width <= this.config.breakpoints.mobile) {
                newBreakpoint = 'mobile';
            } else if (width <= this.config.breakpoints.tablet) {
                newBreakpoint = 'tablet';
            } else if (width <= this.config.breakpoints.desktop) {
                newBreakpoint = 'desktop';
            } else {
                newBreakpoint = 'large';
            }

            if (newBreakpoint !== this.currentBreakpoint) {
                this.currentBreakpoint = newBreakpoint;
                this.onBreakpointChange();
            }
        },

        // Handle breakpoint change
        onBreakpointChange: function() {
            $('body').removeClass('mobile tablet desktop large')
                     .addClass(this.currentBreakpoint);

            // Trigger custom event
            $(window).trigger('breakpointChange', [this.currentBreakpoint]);

            // Handle specific breakpoint logic
            this.handleBreakpointSpecific();
        },

        // Handle breakpoint specific logic
        handleBreakpointSpecific: function() {
            switch(this.currentBreakpoint) {
                case 'mobile':
                    this.handleMobileBreakpoint();
                    break;
                case 'tablet':
                    this.handleTabletBreakpoint();
                    break;
                case 'desktop':
                case 'large':
                    this.handleDesktopBreakpoint();
                    break;
            }
        },

        // Handle mobile breakpoint
        handleMobileBreakpoint: function() {
            // Show mobile menu toggle
            $(this.config.mobileMenuSelector).show();
            
            // Hide desktop navigation
            $(this.config.navigationSelector).removeClass('desktop-nav');
            
            // Stack sidebar below content
            this.stackSidebar();
            
            // Enable touch navigation
            this.enableTouchNavigation();
        },

        // Handle tablet breakpoint
        handleTabletBreakpoint: function() {
            // Show mobile menu toggle
            $(this.config.mobileMenuSelector).show();
            
            // Adjust navigation for tablet
            $(this.config.navigationSelector).removeClass('desktop-nav');
            
            // Handle sidebar position
            this.handleTabletSidebar();
        },

        // Handle desktop breakpoint
        handleDesktopBreakpoint: function() {
            // Hide mobile menu toggle
            $(this.config.mobileMenuSelector).hide();
            
            // Show desktop navigation
            $(this.config.navigationSelector).addClass('desktop-nav').show();
            
            // Reset sidebar position
            this.resetSidebar();
            
            // Disable touch navigation
            this.disableTouchNavigation();
            
            // Close mobile menu if open
            this.closeMobileMenu();
        },

        // Initialize mobile menu
        initMobileMenu: function() {
            const $toggle = $(this.config.mobileMenuSelector);
            const $nav = $(this.config.navigationSelector);

            if ($toggle.length && $nav.length) {
                // Add mobile menu classes
                $nav.addClass('mobile-nav');
                
                // Create hamburger icon if not exists
                if (!$toggle.find('span').length) {
                    $toggle.html('<span></span><span></span><span></span>');
                }
            }
        },

        // Toggle mobile menu
        toggleMobileMenu: function() {
            const $toggle = $(this.config.mobileMenuSelector);
            const $nav = $(this.config.navigationSelector);

            $toggle.toggleClass('active');
            $nav.toggleClass('open');
            $('body').toggleClass('mobile-menu-open');

            // Update aria attributes
            const isOpen = $nav.hasClass('open');
            $toggle.attr('aria-expanded', isOpen);
            $nav.attr('aria-hidden', !isOpen);
        },

        // Close mobile menu
        closeMobileMenu: function() {
            const $toggle = $(this.config.mobileMenuSelector);
            const $nav = $(this.config.navigationSelector);

            $toggle.removeClass('active');
            $nav.removeClass('open');
            $('body').removeClass('mobile-menu-open');

            // Update aria attributes
            $toggle.attr('aria-expanded', false);
            $nav.attr('aria-hidden', true);
        },

        // Stack sidebar
        stackSidebar: function() {
            const $sidebar = $(this.config.sidebarSelector);
            if ($sidebar.length) {
                $sidebar.addClass('stacked');
            }
        },

        // Handle tablet sidebar
        handleTabletSidebar: function() {
            const $sidebar = $(this.config.sidebarSelector);
            if ($sidebar.length) {
                $sidebar.removeClass('stacked').addClass('tablet-sidebar');
            }
        },

        // Reset sidebar
        resetSidebar: function() {
            const $sidebar = $(this.config.sidebarSelector);
            if ($sidebar.length) {
                $sidebar.removeClass('stacked tablet-sidebar');
            }
        },

        // Initialize responsive images
        initResponsiveImages: function() {
            $('img').each(function() {
                const $img = $(this);
                if (!$img.attr('srcset') && !$img.hasClass('no-responsive')) {
                    $img.addClass('responsive-image');
                }
            });
        },

        // Initialize responsive embeds
        initResponsiveEmbeds: function() {
            $('iframe, embed, object, video').each(function() {
                const $embed = $(this);
                if (!$embed.parent('.responsive-embed').length) {
                    $embed.wrap('<div class="responsive-embed"></div>');
                }
            });
        },

        // Initialize touch navigation
        initTouchNavigation: function() {
            if ('ontouchstart' in window) {
                this.enableTouchNavigation();
            }
        },

        // Enable touch navigation
        enableTouchNavigation: function() {
            if ('ontouchstart' in window) {
                $('body').addClass('touch-device');
                
                // Add touch-friendly navigation
                $('.main-navigation a').on('touchstart', function() {
                    $(this).addClass('touch-active');
                }).on('touchend', function() {
                    $(this).removeClass('touch-active');
                });
            }
        },

        // Disable touch navigation
        disableTouchNavigation: function() {
            $('body').removeClass('touch-device');
            $('.main-navigation a').off('touchstart touchend');
        },

        // Initialize responsive tables
        initResponsiveTables: function() {
            $('table').each(function() {
                const $table = $(this);
                if (!$table.parent('.table-responsive').length) {
                    $table.wrap('<div class="table-responsive"></div>');
                }
            });
        },

        // Initialize lazy loading
        initLazyLoad: function() {
            if ('IntersectionObserver' in window) {
                const lazyImages = document.querySelectorAll('img[data-src]');
                const imageObserver = new IntersectionObserver(function(entries, observer) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            imageObserver.unobserve(img);
                        }
                    });
                });

                lazyImages.forEach(function(img) {
                    imageObserver.observe(img);
                });
            }
        },

        // Handle window resize
        handleResize: function() {
            // Recalculate responsive elements
            this.recalculateElements();
            
            // Trigger custom resize event
            $(window).trigger('responsiveResize', [this.currentBreakpoint]);
        },

        // Recalculate responsive elements
        recalculateElements: function() {
            // Recalculate masonry layouts
            if (typeof $.fn.masonry !== 'undefined') {
                $('.masonry').masonry('layout');
            }

            // Recalculate equal heights
            this.equalizeHeights();
        },

        // Equalize heights
        equalizeHeights: function() {
            $('.equal-height').each(function() {
                const $container = $(this);
                const $items = $container.find('.equal-height-item');
                
                // Reset heights
                $items.css('height', 'auto');
                
                // Only equalize on desktop
                if (NosfirNewsResponsive.currentBreakpoint === 'desktop' || 
                    NosfirNewsResponsive.currentBreakpoint === 'large') {
                    let maxHeight = 0;
                    
                    $items.each(function() {
                        const height = $(this).outerHeight();
                        if (height > maxHeight) {
                            maxHeight = height;
                        }
                    });
                    
                    $items.css('height', maxHeight + 'px');
                }
            });
        },

        // Debounce function
        debounce: function(func, wait, immediate) {
            let timeout;
            return function() {
                const context = this;
                const args = arguments;
                const later = function() {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                const callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        },

        // Get current breakpoint
        getCurrentBreakpoint: function() {
            return this.currentBreakpoint;
        },

        // Check if mobile
        isMobile: function() {
            return this.currentBreakpoint === 'mobile';
        },

        // Check if tablet
        isTablet: function() {
            return this.currentBreakpoint === 'tablet';
        },

        // Check if desktop
        isDesktop: function() {
            return this.currentBreakpoint === 'desktop' || this.currentBreakpoint === 'large';
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        NosfirNewsResponsive.init();
    });

    // Make responsive object globally available
    window.NosfirNewsResponsive = NosfirNewsResponsive;

    // Custom events for theme integration
    $(window).on('breakpointChange', function(e, breakpoint) {
        console.log('Breakpoint changed to:', breakpoint);
    });

    $(window).on('responsiveResize', function(e, breakpoint) {
        console.log('Responsive resize on:', breakpoint);
    });

})(jQuery);