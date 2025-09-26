/**
 * Mobile Menu Functionality
 * Handles responsive navigation with hamburger menu
 *
 * @package NosfirNews
 * @since 2.0.0
 */

(function() {
    'use strict';

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        initMobileMenu();
    });

    /**
     * Initialize mobile menu functionality
     */
    function initMobileMenu() {
        const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
        const mobileMenu = document.querySelector('.mobile-menu');
        const mobileMenuOverlay = document.querySelector('.mobile-menu-overlay');
        const mobileMenuClose = document.querySelector('.mobile-menu-close');
        const body = document.body;

        // Check if elements exist
        if (!mobileMenuToggle || !mobileMenu || !mobileMenuOverlay) {
            return;
        }

        // Open mobile menu
        mobileMenuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            openMobileMenu();
        });

        // Close mobile menu
        if (mobileMenuClose) {
            mobileMenuClose.addEventListener('click', function(e) {
                e.preventDefault();
                closeMobileMenu();
            });
        }

        // Close menu when clicking overlay
        mobileMenuOverlay.addEventListener('click', function() {
            closeMobileMenu();
        });

        // Close menu on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mobileMenu.classList.contains('active')) {
                closeMobileMenu();
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 991) {
                closeMobileMenu();
            }
        });

        // Handle submenu toggles (if any)
        const submenuToggles = mobileMenu.querySelectorAll('.submenu-toggle');
        submenuToggles.forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                toggleSubmenu(this);
            });
        });

        /**
         * Open mobile menu
         */
        function openMobileMenu() {
            mobileMenu.classList.add('active');
            mobileMenuOverlay.classList.add('active');
            mobileMenuToggle.classList.add('active');
            body.classList.add('mobile-menu-open');
            
            // Update ARIA attributes
            mobileMenuToggle.setAttribute('aria-expanded', 'true');
            mobileMenu.setAttribute('aria-hidden', 'false');
            mobileMenuOverlay.setAttribute('aria-hidden', 'false');
            
            // Focus management
            const firstFocusableElement = mobileMenu.querySelector('a, button');
            if (firstFocusableElement) {
                firstFocusableElement.focus();
            }
            
            // Trap focus within menu
            trapFocus(mobileMenu);
        }

        /**
         * Close mobile menu
         */
        function closeMobileMenu() {
            mobileMenu.classList.remove('active');
            mobileMenuOverlay.classList.remove('active');
            mobileMenuToggle.classList.remove('active');
            body.classList.remove('mobile-menu-open');
            
            // Update ARIA attributes
            mobileMenuToggle.setAttribute('aria-expanded', 'false');
            mobileMenu.setAttribute('aria-hidden', 'true');
            mobileMenuOverlay.setAttribute('aria-hidden', 'true');
            
            // Return focus to toggle button
            mobileMenuToggle.focus();
            
            // Remove focus trap
            removeFocusTrap();
        }

        /**
         * Toggle submenu
         */
        function toggleSubmenu(toggle) {
            const submenu = toggle.nextElementSibling;
            const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
            
            if (submenu) {
                toggle.setAttribute('aria-expanded', !isExpanded);
                submenu.style.display = isExpanded ? 'none' : 'block';
                toggle.classList.toggle('active');
            }
        }

        /**
         * Trap focus within element
         */
        function trapFocus(element) {
            const focusableElements = element.querySelectorAll(
                'a[href], button:not([disabled]), textarea:not([disabled]), input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'
            );
            
            if (focusableElements.length === 0) return;
            
            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];
            
            element.addEventListener('keydown', function(e) {
                if (e.key === 'Tab') {
                    if (e.shiftKey) {
                        if (document.activeElement === firstElement) {
                            e.preventDefault();
                            lastElement.focus();
                        }
                    } else {
                        if (document.activeElement === lastElement) {
                            e.preventDefault();
                            firstElement.focus();
                        }
                    }
                }
            });
        }

        /**
         * Remove focus trap
         */
        function removeFocusTrap() {
            // Remove event listeners added for focus trapping
            const clonedMenu = mobileMenu.cloneNode(true);
            mobileMenu.parentNode.replaceChild(clonedMenu, mobileMenu);
            // Re-initialize menu after cloning
            setTimeout(initMobileMenu, 100);
        }
    }

    /**
     * Smooth scroll for anchor links
     */
    function initSmoothScroll() {
        const anchorLinks = document.querySelectorAll('a[href^="#"]');
        
        anchorLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement && targetId !== '#') {
                    e.preventDefault();
                    
                    // Close mobile menu if open
                    const mobileMenu = document.querySelector('.mobile-menu');
                    if (mobileMenu && mobileMenu.classList.contains('active')) {
                        document.querySelector('.mobile-menu-close').click();
                    }
                    
                    // Smooth scroll to target
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    // Initialize smooth scroll
    document.addEventListener('DOMContentLoaded', initSmoothScroll);

})();