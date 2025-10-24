/**
 * Navigation Layouts JavaScript
 * Controla a funcionalidade da navegação mobile e layouts
 *
 * @package NosfirNews
 * @since 2.0.0
 */

(function() {
    'use strict';

    /**
     * Navigation Layout Controller
     */
    const NavigationLayout = {
        
        /**
         * Initialize navigation functionality
         */
        init: function() {
            this.setupMobileToggle();
            this.setupKeyboardNavigation();
            this.setupResponsiveHandling();
            this.setupCustomColors();
        },

        /**
         * Setup mobile menu toggle functionality
         */
        setupMobileToggle: function() {
            const toggleButton = document.querySelector('.mobile-menu-toggle');
            const navigation = document.querySelector('#site-navigation');
            
            if (!toggleButton || !navigation) {
                return;
            }

            toggleButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                const isExpanded = toggleButton.getAttribute('aria-expanded') === 'true';
                const newState = !isExpanded;
                
                // Update ARIA attributes
                toggleButton.setAttribute('aria-expanded', newState);
                
                // Toggle classes
                toggleButton.classList.toggle('active', newState);
                navigation.classList.toggle('active', newState);
                
                // Focus management
                if (newState) {
                    const firstLink = navigation.querySelector('a');
                    if (firstLink) {
                        firstLink.focus();
                    }
                }
            });

            // Close menu when clicking outside
            document.addEventListener('click', function(e) {
                if (!navigation.contains(e.target) && !toggleButton.contains(e.target)) {
                    if (navigation.classList.contains('active')) {
                        toggleButton.click();
                    }
                }
            });

            // Close menu on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && navigation.classList.contains('active')) {
                    toggleButton.click();
                    toggleButton.focus();
                }
            });
        },

        /**
         * Setup keyboard navigation
         */
        setupKeyboardNavigation: function() {
            const menuItems = document.querySelectorAll('.main-navigation a');
            
            menuItems.forEach(function(item, index) {
                item.addEventListener('keydown', function(e) {
                    let targetIndex;
                    
                    switch(e.key) {
                        case 'ArrowRight':
                        case 'ArrowDown':
                            e.preventDefault();
                            targetIndex = (index + 1) % menuItems.length;
                            menuItems[targetIndex].focus();
                            break;
                            
                        case 'ArrowLeft':
                        case 'ArrowUp':
                            e.preventDefault();
                            targetIndex = (index - 1 + menuItems.length) % menuItems.length;
                            menuItems[targetIndex].focus();
                            break;
                            
                        case 'Home':
                            e.preventDefault();
                            menuItems[0].focus();
                            break;
                            
                        case 'End':
                            e.preventDefault();
                            menuItems[menuItems.length - 1].focus();
                            break;
                    }
                });
            });
        },

        /**
         * Setup responsive handling
         */
        setupResponsiveHandling: function() {
            let resizeTimer;
            
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    NavigationLayout.handleResize();
                }, 250);
            });
            
            // Initial check
            this.handleResize();
        },

        /**
         * Handle window resize
         */
        handleResize: function() {
            const toggleButton = document.querySelector('.mobile-menu-toggle');
            const navigation = document.querySelector('#site-navigation');
            
            if (!toggleButton || !navigation) {
                return;
            }

            // Reset mobile menu state on desktop
            if (window.innerWidth > 768) {
                toggleButton.setAttribute('aria-expanded', 'false');
                toggleButton.classList.remove('active');
                navigation.classList.remove('active');
            }
        },

        /**
         * Setup custom colors from customizer
         */
        setupCustomColors: function() {
            const navigation = document.querySelector('.main-navigation');
            
            if (!navigation) {
                return;
            }

            // Apply custom colors if available
            if (window.nosfirNewsCustomizer && window.nosfirNewsCustomizer.navigation) {
                const colors = window.nosfirNewsCustomizer.navigation;
                
                if (colors.backgroundColor) {
                    navigation.style.setProperty('--custom-nav-bg', colors.backgroundColor);
                }
                
                if (colors.textColor) {
                    navigation.style.setProperty('--custom-nav-text', colors.textColor);
                }
            }
        },

        /**
         * Update navigation layout
         */
        updateLayout: function(position, alignment, style) {
            const header = document.querySelector('.site-header');
            const navigation = document.querySelector('.main-navigation');
            
            if (!header || !navigation) {
                return;
            }

            // Remove existing classes
            header.className = header.className.replace(/nav-position-\S+/g, '');
            header.className = header.className.replace(/header-inline-nav/g, '');
            navigation.className = navigation.className.replace(/nav-position-\S+/g, '');
            navigation.className = navigation.className.replace(/nav-align-\S+/g, '');
            navigation.className = navigation.className.replace(/nav-style-\S+/g, '');

            // Add new classes
            if (['right-of-logo', 'next-to-logo', 'center-header', 'right-header'].includes(position)) {
                header.classList.add('header-inline-nav');
            }
            
            header.classList.add('nav-position-' + position);
            navigation.classList.add('nav-position-' + position);
            navigation.classList.add('nav-align-' + alignment);
            navigation.classList.add('nav-style-' + style);
        }
    };

    /**
     * Customizer Live Preview Support
     */
    if (typeof wp !== 'undefined' && wp.customize) {
        
        // Navigation position
        wp.customize('nosfirnews_navigation_position', function(value) {
            value.bind(function(newval) {
                const alignment = wp.customize('nosfirnews_navigation_alignment')();
                const style = wp.customize('nosfirnews_navigation_style')();
                NavigationLayout.updateLayout(newval, alignment, style);
            });
        });

        // Navigation alignment
        wp.customize('nosfirnews_navigation_alignment', function(value) {
            value.bind(function(newval) {
                const position = wp.customize('nosfirnews_navigation_position')();
                const style = wp.customize('nosfirnews_navigation_style')();
                NavigationLayout.updateLayout(position, newval, style);
            });
        });

        // Navigation style
        wp.customize('nosfirnews_navigation_style', function(value) {
            value.bind(function(newval) {
                const position = wp.customize('nosfirnews_navigation_position')();
                const alignment = wp.customize('nosfirnews_navigation_alignment')();
                NavigationLayout.updateLayout(position, alignment, newval);
            });
        });

        // Navigation background color
        wp.customize('nosfirnews_navigation_bg_color', function(value) {
            value.bind(function(newval) {
                const navigation = document.querySelector('.main-navigation');
                if (navigation) {
                    navigation.style.setProperty('--custom-nav-bg', newval || 'transparent');
                }
            });
        });

        // Navigation text color
        wp.customize('nosfirnews_navigation_text_color', function(value) {
            value.bind(function(newval) {
                const navigation = document.querySelector('.main-navigation');
                if (navigation) {
                    navigation.style.setProperty('--custom-nav-text', newval || 'var(--text-color)');
                }
            });
        });
    }

    /**
     * Initialize when DOM is ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            NavigationLayout.init();
        });
    } else {
        NavigationLayout.init();
    }

    // Make NavigationLayout globally available
    window.NosfirNewsNavigationLayout = NavigationLayout;

})();