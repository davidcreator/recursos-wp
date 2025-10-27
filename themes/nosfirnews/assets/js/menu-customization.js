/**
 * Menu Customization JavaScript
 * Interactive functionality for advanced menu features
 *
 * @package NosfirNews
 * @since 1.0.0
 */

(function($) {
    'use strict';

    /**
     * Menu Customization Object
     */
    const NosfirNewsMenu = {
        
        /**
         * Initialize menu functionality
         */
        init: function() {
            this.setupMobileMenu();
            this.setupMegaMenu();
            this.setupMenuAnimations();
            this.setupMenuSearch();
            this.setupAccessibility();
            this.setupStickyMenu();
            this.setupMenuHover();
        },

        /**
         * Setup mobile menu functionality
         */
        setupMobileMenu: function() {
            const $mobileToggle = $('.mobile-menu-toggle');
            const $mobileNav = $('.mobile-navigation');
            const $body = $('body');

            // Create mobile overlay if it doesn't exist
            if (!$('.mobile-overlay').length) {
                $body.append('<div class="mobile-overlay"></div>');
            }
            const $overlay = $('.mobile-overlay');

            // Toggle mobile menu
            $mobileToggle.on('click', function(e) {
                e.preventDefault();
                
                const isActive = $(this).hasClass('active');
                
                if (isActive) {
                    // Close menu
                    $(this).removeClass('active').attr('aria-expanded', 'false');
                    $mobileNav.removeClass('active');
                    $overlay.removeClass('active');
                    $body.removeClass('mobile-menu-open');
                } else {
                    // Open menu
                    $(this).addClass('active').attr('aria-expanded', 'true');
                    $mobileNav.addClass('active');
                    $overlay.addClass('active');
                    $body.addClass('mobile-menu-open');
                }
            });

            // Close menu when clicking overlay
            $overlay.on('click', function() {
                $mobileToggle.trigger('click');
            });

            // Close menu on escape key
            $(document).on('keydown', function(e) {
                if (e.keyCode === 27 && $mobileNav.hasClass('active')) {
                    $mobileToggle.trigger('click');
                }
            });

            // Handle submenu toggles in mobile
            $('.mobile-navigation .menu-item-has-children > a').on('click', function(e) {
                e.preventDefault();
                const $submenu = $(this).siblings('.sub-menu');
                const $parent = $(this).parent();
                
                if ($submenu.is(':visible')) {
                    $submenu.slideUp(300);
                    $parent.removeClass('submenu-open');
                } else {
                    $('.mobile-navigation .sub-menu').slideUp(300);
                    $('.mobile-navigation .menu-item-has-children').removeClass('submenu-open');
                    $submenu.slideDown(300);
                    $parent.addClass('submenu-open');
                }
            });
        },

        /**
         * Setup mega menu functionality
         */
        setupMegaMenu: function() {
            const $megaItems = $('.mega-menu-item');
            let hoverTimeout;

            $megaItems.each(function() {
                const $item = $(this);
                const $submenu = $item.find('.sub-menu');

                $item.on('mouseenter', function() {
                    clearTimeout(hoverTimeout);
                    
                    // Close other mega menus
                    $('.mega-menu-item').not(this).removeClass('mega-menu-active');
                    
                    // Open this mega menu
                    $item.addClass('mega-menu-active');
                    
                    // Position mega menu
                    this.positionMegaMenu($submenu);
                }.bind(this));

                $item.on('mouseleave', function() {
                    hoverTimeout = setTimeout(function() {
                        $item.removeClass('mega-menu-active');
                    }, 300);
                });

                // Keep menu open when hovering over submenu
                $submenu.on('mouseenter', function() {
                    clearTimeout(hoverTimeout);
                });

                $submenu.on('mouseleave', function() {
                    hoverTimeout = setTimeout(function() {
                        $item.removeClass('mega-menu-active');
                    }, 300);
                });
            });
        },

        /**
         * Position mega menu properly
         */
        positionMegaMenu: function($submenu) {
            const windowWidth = $(window).width();
            const submenuWidth = $submenu.outerWidth();
            const itemOffset = $submenu.parent().offset().left;
            
            // Reset positioning
            $submenu.css({
                'left': '0',
                'right': 'auto',
                'transform': 'none'
            });

            // Check if submenu goes off screen
            if (itemOffset + submenuWidth > windowWidth) {
                $submenu.css({
                    'left': 'auto',
                    'right': '0'
                });
            }

            // Center mega menu if it's smaller than container
            if (submenuWidth < windowWidth) {
                const centerOffset = (windowWidth - submenuWidth) / 2 - itemOffset;
                $submenu.css({
                    'left': centerOffset + 'px'
                });
            }
        },

        /**
         * Setup menu animations
         */
        setupMenuAnimations: function() {
            const $menuItems = $('.nav-menu li');
            
            // Add animation classes on load
            $menuItems.each(function(index) {
                $(this).css('animation-delay', (index * 0.1) + 's');
            });

            // Smooth scroll for anchor links
            $('.nav-menu a[href^="#"]').on('click', function(e) {
                const target = $(this.getAttribute('href'));
                
                if (target.length) {
                    e.preventDefault();
                    
                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 800, 'easeInOutQuart');
                    
                    // Close mobile menu if open
                    if ($('.mobile-navigation').hasClass('active')) {
                        $('.mobile-menu-toggle').trigger('click');
                    }
                }
            });
        },

        /**
         * Setup menu search functionality
         */
        setupMenuSearch: function() {
            const $searchForm = $('.menu-search');
            const $searchInput = $searchForm.find('input');
            const $searchButton = $searchForm.find('button');

            // Toggle search form
            $searchButton.on('click', function(e) {
                e.preventDefault();
                
                if ($searchInput.is(':visible')) {
                    if ($searchInput.val().trim() !== '') {
                        $searchForm.submit();
                    } else {
                        $searchInput.fadeOut(300);
                    }
                } else {
                    $searchInput.fadeIn(300).focus();
                }
            });

            // Hide search on escape
            $searchInput.on('keydown', function(e) {
                if (e.keyCode === 27) {
                    $(this).fadeOut(300);
                }
            });

            // Submit on enter
            $searchInput.on('keydown', function(e) {
                if (e.keyCode === 13) {
                    $searchForm.submit();
                }
            });
        },

        /**
         * Setup accessibility features
         */
        setupAccessibility: function() {
            // Keyboard navigation
            $('.nav-menu a').on('keydown', function(e) {
                const $this = $(this);
                const $parent = $this.parent();
                const $submenu = $parent.find('.sub-menu');

                switch(e.keyCode) {
                    case 13: // Enter
                    case 32: // Space
                        if ($submenu.length) {
                            e.preventDefault();
                            $submenu.toggle();
                            $parent.toggleClass('submenu-open');
                        }
                        break;
                    
                    case 27: // Escape
                        if ($submenu.is(':visible')) {
                            e.preventDefault();
                            $submenu.hide();
                            $parent.removeClass('submenu-open');
                            $this.focus();
                        }
                        break;
                    
                    case 37: // Left arrow
                        e.preventDefault();
                        $this.parent().prev().find('a').first().focus();
                        break;
                    
                    case 39: // Right arrow
                        e.preventDefault();
                        $this.parent().next().find('a').first().focus();
                        break;
                    
                    case 38: // Up arrow
                        if ($submenu.length && $submenu.is(':visible')) {
                            e.preventDefault();
                            $submenu.find('a').last().focus();
                        }
                        break;
                    
                    case 40: // Down arrow
                        if ($submenu.length) {
                            e.preventDefault();
                            if (!$submenu.is(':visible')) {
                                $submenu.show();
                                $parent.addClass('submenu-open');
                            }
                            $submenu.find('a').first().focus();
                        }
                        break;
                }
            });

            // Focus management for submenus
            $('.sub-menu a').on('keydown', function(e) {
                const $this = $(this);
                const $submenu = $this.closest('.sub-menu');
                const $parentLink = $submenu.siblings('a');

                switch(e.keyCode) {
                    case 27: // Escape
                        e.preventDefault();
                        $submenu.hide();
                        $submenu.parent().removeClass('submenu-open');
                        $parentLink.focus();
                        break;
                    
                    case 38: // Up arrow
                        e.preventDefault();
                        const $prevLink = $this.parent().prev().find('a');
                        if ($prevLink.length) {
                            $prevLink.focus();
                        } else {
                            $parentLink.focus();
                        }
                        break;
                    
                    case 40: // Down arrow
                        e.preventDefault();
                        const $nextLink = $this.parent().next().find('a');
                        if ($nextLink.length) {
                            $nextLink.focus();
                        }
                        break;
                }
            });
        },

        /**
         * Setup sticky menu
         */
        setupStickyMenu: function() {
            const $menu = $('.main-navigation');
            const $header = $('.site-header');
            let lastScrollTop = 0;
            let menuOffset = $menu.offset() ? $menu.offset().top : 0;

            $(window).on('scroll', function() {
                const scrollTop = $(this).scrollTop();
                
                if (scrollTop > menuOffset + 100) {
                    $menu.addClass('sticky-menu');
                    $header.addClass('menu-sticky');
                    
                    // Hide/show on scroll direction
                    if (scrollTop > lastScrollTop && scrollTop > 500) {
                        $menu.addClass('menu-hidden');
                    } else {
                        $menu.removeClass('menu-hidden');
                    }
                } else {
                    $menu.removeClass('sticky-menu menu-hidden');
                    $header.removeClass('menu-sticky');
                }
                
                lastScrollTop = scrollTop;
            });
        },

        /**
         * Setup menu hover effects
         */
        setupMenuHover: function() {
            $('.nav-menu > li').each(function() {
                const $item = $(this);
                const $link = $item.find('> a');
                
                // Add hover indicator
                if (!$link.find('.hover-indicator').length) {
                    $link.append('<span class="hover-indicator"></span>');
                }
            });

            // Magnetic effect for menu items
            $('.nav-menu > li > a').on('mousemove', function(e) {
                const $this = $(this);
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;
                
                $this.css('transform', `translate(${x * 0.1}px, ${y * 0.1}px)`);
            });

            $('.nav-menu > li > a').on('mouseleave', function() {
                $(this).css('transform', '');
            });
        }
    };

    /**
     * Initialize when document is ready
     */
    $(document).ready(function() {
        NosfirNewsMenu.init();
    });

    /**
     * Reinitialize on window resize
     */
    $(window).on('resize', function() {
        // Recalculate menu positions
        $('.mega-menu-item .sub-menu').each(function() {
            NosfirNewsMenu.positionMegaMenu($(this));
        });
    });

    /**
     * AJAX menu loading for dynamic content
     */
    if (typeof nosfirnews_menu !== 'undefined') {
        window.loadMenuContent = function(menuId, callback) {
            $.ajax({
                url: nosfirnews_menu.ajax_url,
                type: 'POST',
                data: {
                    action: 'load_menu_content',
                    menu_id: menuId,
                    nonce: nosfirnews_menu.nonce
                },
                success: function(response) {
                    if (response.success && callback) {
                        callback(response.data);
                    }
                },
                error: function() {
                    console.log('Error loading menu content');
                }
            });
        };
    }

})(jQuery);

/**
 * Vanilla JS fallback for critical functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    // Basic mobile menu toggle without jQuery
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    const mobileNav = document.querySelector('.mobile-navigation');
    
    if (mobileToggle && mobileNav) {
        mobileToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            mobileNav.classList.toggle('active');
            document.body.classList.toggle('mobile-menu-open');
        });
    }
    
    // Basic accessibility for keyboard navigation
    const menuLinks = document.querySelectorAll('.nav-menu a');
    menuLinks.forEach(function(link) {
        link.addEventListener('keydown', function(e) {
            if (e.keyCode === 13 || e.keyCode === 32) {
                const submenu = this.parentNode.querySelector('.sub-menu');
                if (submenu) {
                    e.preventDefault();
                    submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
                }
            }
        });
    });
});