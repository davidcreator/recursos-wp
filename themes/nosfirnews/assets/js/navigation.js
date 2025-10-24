/**
 * Navigation JavaScript for NosfirNews Theme
 * Custom Walker Nav Menu Functionality
 */

class NosfirNewsNavigation {
    constructor() {
        this.init();
    }

    init() {
        this.setupMobileMenu();
        this.setupDropdowns();
        this.setupMegaMenus();
        this.setupKeyboardNavigation();
        this.setupAccessibility();
        this.setupScrollBehavior();
        this.bindEvents();
    }

    /**
     * Setup mobile menu functionality
     */
    setupMobileMenu() {
        const mobileToggle = document.querySelector('.mobile-menu-toggle');
        const mobileMenu = document.querySelector('.mobile-menu');
        const mobileClose = document.querySelector('.mobile-menu-close');
        const mobileOverlay = document.querySelector('.mobile-menu-overlay');

        if (!mobileToggle || !mobileMenu) return;

        // Create overlay if it doesn't exist
        if (!mobileOverlay) {
            const overlay = document.createElement('div');
            overlay.className = 'mobile-menu-overlay';
            document.body.appendChild(overlay);
        }

        // Toggle mobile menu
        mobileToggle.addEventListener('click', () => {
            this.toggleMobileMenu();
        });

        // Close mobile menu
        if (mobileClose) {
            mobileClose.addEventListener('click', () => {
                this.closeMobileMenu();
            });
        }

        // Close on overlay click
        document.querySelector('.mobile-menu-overlay')?.addEventListener('click', () => {
            this.closeMobileMenu();
        });

        // Setup submenu toggles
        this.setupMobileSubmenus();

        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && mobileMenu.classList.contains('active')) {
                this.closeMobileMenu();
            }
        });
    }

    /**
     * Setup mobile submenu toggles
     */
    setupMobileSubmenus() {
        const submenuToggles = document.querySelectorAll('.mobile-submenu-toggle');

        submenuToggles.forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();

                const submenu = toggle.parentElement.querySelector('.mobile-sub-menu');
                const isExpanded = toggle.getAttribute('aria-expanded') === 'true';

                // Close other submenus
                submenuToggles.forEach(otherToggle => {
                    if (otherToggle !== toggle) {
                        otherToggle.setAttribute('aria-expanded', 'false');
                        const otherSubmenu = otherToggle.parentElement.querySelector('.mobile-sub-menu');
                        if (otherSubmenu) {
                            otherSubmenu.classList.remove('active');
                        }
                    }
                });

                // Toggle current submenu
                toggle.setAttribute('aria-expanded', !isExpanded);
                if (submenu) {
                    submenu.classList.toggle('active');
                }
            });
        });
    }

    /**
     * Toggle mobile menu
     */
    toggleMobileMenu() {
        const mobileMenu = document.querySelector('.mobile-menu');
        const mobileOverlay = document.querySelector('.mobile-menu-overlay');
        const body = document.body;

        if (mobileMenu.classList.contains('active')) {
            this.closeMobileMenu();
        } else {
            mobileMenu.classList.add('active');
            mobileOverlay?.classList.add('active');
            body.classList.add('mobile-menu-open');
            
            // Focus first menu item
            const firstMenuItem = mobileMenu.querySelector('a');
            if (firstMenuItem) {
                firstMenuItem.focus();
            }
        }
    }

    /**
     * Close mobile menu
     */
    closeMobileMenu() {
        const mobileMenu = document.querySelector('.mobile-menu');
        const mobileOverlay = document.querySelector('.mobile-menu-overlay');
        const body = document.body;

        mobileMenu?.classList.remove('active');
        mobileOverlay?.classList.remove('active');
        body.classList.remove('mobile-menu-open');

        // Close all submenus
        const submenuToggles = document.querySelectorAll('.mobile-submenu-toggle');
        submenuToggles.forEach(toggle => {
            toggle.setAttribute('aria-expanded', 'false');
            const submenu = toggle.parentElement.querySelector('.mobile-sub-menu');
            if (submenu) {
                submenu.classList.remove('active');
            }
        });
    }

    /**
     * Setup dropdown menus
     */
    setupDropdowns() {
        const dropdownItems = document.querySelectorAll('.menu-item-has-children');

        dropdownItems.forEach(item => {
            const link = item.querySelector('a');
            const submenu = item.querySelector('.sub-menu');

            if (!submenu) return;

            let hoverTimeout;

            // Mouse enter
            item.addEventListener('mouseenter', () => {
                clearTimeout(hoverTimeout);
                this.showDropdown(submenu);
            });

            // Mouse leave
            item.addEventListener('mouseleave', () => {
                hoverTimeout = setTimeout(() => {
                    this.hideDropdown(submenu);
                }, 150);
            });

            // Focus handling
            link.addEventListener('focus', () => {
                this.showDropdown(submenu);
            });

            // Click handling for touch devices
            link.addEventListener('click', (e) => {
                if (window.innerWidth <= 768) return; // Let mobile menu handle this

                if (submenu.style.opacity === '1') {
                    // If submenu is visible, allow navigation
                    return;
                } else {
                    // If submenu is hidden, show it and prevent navigation
                    e.preventDefault();
                    this.showDropdown(submenu);
                }
            });
        });
    }

    /**
     * Show dropdown menu
     */
    showDropdown(submenu) {
        submenu.style.opacity = '1';
        submenu.style.visibility = 'visible';
        submenu.style.transform = 'translateY(0)';
        submenu.classList.add('fade-in');
    }

    /**
     * Hide dropdown menu
     */
    hideDropdown(submenu) {
        submenu.style.opacity = '0';
        submenu.style.visibility = 'hidden';
        submenu.style.transform = 'translateY(-10px)';
        submenu.classList.remove('fade-in');
    }

    /**
     * Setup mega menus
     */
    setupMegaMenus() {
        const megaMenuItems = document.querySelectorAll('.mega-menu-enabled');

        megaMenuItems.forEach(item => {
            const megaMenu = item.querySelector('.mega-menu');
            if (!megaMenu) return;

            let hoverTimeout;

            // Mouse enter
            item.addEventListener('mouseenter', () => {
                clearTimeout(hoverTimeout);
                this.showMegaMenu(megaMenu);
            });

            // Mouse leave
            item.addEventListener('mouseleave', () => {
                hoverTimeout = setTimeout(() => {
                    this.hideMegaMenu(megaMenu);
                }, 200);
            });

            // Focus handling
            const link = item.querySelector('a');
            link.addEventListener('focus', () => {
                this.showMegaMenu(megaMenu);
            });
        });
    }

    /**
     * Show mega menu
     */
    showMegaMenu(megaMenu) {
        megaMenu.style.opacity = '1';
        megaMenu.style.visibility = 'visible';
        megaMenu.style.transform = 'translateX(-50%) translateY(0)';
        megaMenu.classList.add('slide-down');
    }

    /**
     * Hide mega menu
     */
    hideMegaMenu(megaMenu) {
        megaMenu.style.opacity = '0';
        megaMenu.style.visibility = 'hidden';
        megaMenu.style.transform = 'translateX(-50%) translateY(-20px)';
        megaMenu.classList.remove('slide-down');
    }

    /**
     * Setup keyboard navigation
     */
    setupKeyboardNavigation() {
        const menuItems = document.querySelectorAll('.main-navigation a, .mobile-menu a');

        menuItems.forEach(item => {
            item.addEventListener('keydown', (e) => {
                switch (e.key) {
                    case 'ArrowDown':
                        e.preventDefault();
                        this.focusNextMenuItem(item);
                        break;
                    case 'ArrowUp':
                        e.preventDefault();
                        this.focusPreviousMenuItem(item);
                        break;
                    case 'ArrowRight':
                        e.preventDefault();
                        this.focusSubmenu(item);
                        break;
                    case 'ArrowLeft':
                        e.preventDefault();
                        this.focusParentMenu(item);
                        break;
                    case 'Escape':
                        this.closeAllMenus();
                        break;
                }
            });
        });
    }

    /**
     * Focus next menu item
     */
    focusNextMenuItem(currentItem) {
        const menuItems = Array.from(currentItem.closest('ul').querySelectorAll('a'));
        const currentIndex = menuItems.indexOf(currentItem);
        const nextIndex = (currentIndex + 1) % menuItems.length;
        menuItems[nextIndex].focus();
    }

    /**
     * Focus previous menu item
     */
    focusPreviousMenuItem(currentItem) {
        const menuItems = Array.from(currentItem.closest('ul').querySelectorAll('a'));
        const currentIndex = menuItems.indexOf(currentItem);
        const previousIndex = currentIndex === 0 ? menuItems.length - 1 : currentIndex - 1;
        menuItems[previousIndex].focus();
    }

    /**
     * Focus submenu
     */
    focusSubmenu(currentItem) {
        const submenu = currentItem.parentElement.querySelector('.sub-menu, .mega-menu');
        if (submenu) {
            const firstSubmenuItem = submenu.querySelector('a');
            if (firstSubmenuItem) {
                firstSubmenuItem.focus();
            }
        }
    }

    /**
     * Focus parent menu
     */
    focusParentMenu(currentItem) {
        const parentMenuItem = currentItem.closest('.sub-menu, .mega-menu')?.parentElement.querySelector('a');
        if (parentMenuItem) {
            parentMenuItem.focus();
        }
    }

    /**
     * Close all menus
     */
    closeAllMenus() {
        // Close dropdowns
        const dropdowns = document.querySelectorAll('.sub-menu');
        dropdowns.forEach(dropdown => {
            this.hideDropdown(dropdown);
        });

        // Close mega menus
        const megaMenus = document.querySelectorAll('.mega-menu');
        megaMenus.forEach(megaMenu => {
            this.hideMegaMenu(megaMenu);
        });

        // Close mobile menu
        this.closeMobileMenu();
    }

    /**
     * Setup accessibility features
     */
    setupAccessibility() {
        // Add ARIA attributes
        const hasChildrenItems = document.querySelectorAll('.menu-item-has-children > a');
        hasChildrenItems.forEach(item => {
            item.setAttribute('aria-haspopup', 'true');
            item.setAttribute('aria-expanded', 'false');
        });

        // Update ARIA expanded state on hover/focus
        const dropdownItems = document.querySelectorAll('.menu-item-has-children');
        dropdownItems.forEach(item => {
            const link = item.querySelector('a');
            const submenu = item.querySelector('.sub-menu, .mega-menu');

            if (!submenu) return;

            item.addEventListener('mouseenter', () => {
                link.setAttribute('aria-expanded', 'true');
            });

            item.addEventListener('mouseleave', () => {
                link.setAttribute('aria-expanded', 'false');
            });
        });
    }

    /**
     * Setup scroll behavior
     */
    setupScrollBehavior() {
        let lastScrollTop = 0;
        const header = document.querySelector('.site-header');
        
        if (!header) return;

        window.addEventListener('scroll', () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                // Scrolling down
                header.classList.add('header-hidden');
                this.closeAllMenus();
            } else {
                // Scrolling up
                header.classList.remove('header-hidden');
            }
            
            lastScrollTop = scrollTop;
        });
    }

    /**
     * Bind additional events
     */
    bindEvents() {
        // Close menus on window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                this.closeMobileMenu();
            }
        });

        // Close menus on outside click
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.main-navigation') && !e.target.closest('.mobile-menu')) {
                this.closeAllMenus();
            }
        });

        // Handle touch events for better mobile experience
        let touchStartY = 0;
        document.addEventListener('touchstart', (e) => {
            touchStartY = e.touches[0].clientY;
        });

        document.addEventListener('touchmove', (e) => {
            const touchY = e.touches[0].clientY;
            const touchDiff = touchStartY - touchY;

            // Close mobile menu on swipe up
            if (touchDiff > 50 && document.querySelector('.mobile-menu.active')) {
                this.closeMobileMenu();
            }
        });
    }

    /**
     * Public method to refresh navigation
     */
    refresh() {
        this.init();
    }
}

// Initialize navigation when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.nosfirNewsNavigation = new NosfirNewsNavigation();
});

// Reinitialize on AJAX content load (for dynamic content)
document.addEventListener('wp-ajax-complete', () => {
    if (window.nosfirNewsNavigation) {
        window.nosfirNewsNavigation.refresh();
    }
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = NosfirNewsNavigation;
}