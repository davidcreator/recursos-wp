/**
 * Advanced Mobile Navigation for NosfirNews Theme
 * @package NosfirNews
 * @since 1.0.0
 */

(function() {
    'use strict';

    // Mobile Navigation Object
    const MobileNavigation = {
        
        // Configuration
        config: {
            selectors: {
                menuToggle: '.menu-toggle, .mobile-menu-toggle',
                mobileMenu: '.mobile-menu',
                mobileNav: '.mobile-nav-menu',
                menuClose: '.mobile-menu-close',
                menuOverlay: '.mobile-menu-overlay',
                submenuToggle: '.menu-item-has-children > a',
                submenu: '.sub-menu'
            },
            classes: {
                active: 'active',
                open: 'open',
                submenuOpen: 'submenu-open',
                menuOpen: 'mobile-menu-open',
                animating: 'animating'
            },
            animation: {
                duration: 300,
                easing: 'cubic-bezier(0.4, 0.0, 0.2, 1)'
            }
        },

        // State
        isOpen: false,
        isAnimating: false,
        focusableElements: [],
        firstFocusableElement: null,
        lastFocusableElement: null,

        // Initialize
        init: function() {
            this.bindEvents();
            this.setupAccessibility();
            this.createMobileMenuStructure();
        },

        // Create mobile menu structure if it doesn't exist
        createMobileMenuStructure: function() {
            const existingMobileMenu = document.querySelector(this.config.selectors.mobileMenu);
            
            if (!existingMobileMenu) {
                const mainNav = document.querySelector('.main-navigation');
                const navMenu = document.querySelector('.nav-menu');
                
                if (mainNav && navMenu) {
                    // Clone the navigation menu
                    const mobileNavClone = navMenu.cloneNode(true);
                    mobileNavClone.className = 'mobile-nav-menu';
                    
                    // Create mobile menu container
                    const mobileMenuHTML = `
                        <div class="mobile-menu">
                            <div class="mobile-menu-overlay"></div>
                            <div class="mobile-menu-container">
                                <div class="mobile-menu-header">
                                    <h3 class="mobile-menu-title">Menu</h3>
                                    <button class="mobile-menu-close" aria-label="Fechar menu">
                                        <span class="sr-only">Fechar menu</span>
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                </div>
                                <nav class="mobile-menu-content" role="navigation" aria-label="Menu mobile">
                                </nav>
                            </div>
                        </div>
                    `;
                    
                    // Insert mobile menu into DOM
                    document.body.insertAdjacentHTML('beforeend', mobileMenuHTML);
                    
                    // Insert cloned navigation
                    const mobileMenuContent = document.querySelector('.mobile-menu-content');
                    if (mobileMenuContent) {
                        mobileMenuContent.appendChild(mobileNavClone);
                    }
                }
            }
            
            // Add submenu indicators
            this.addSubmenuIndicators();
        },

        // Add submenu indicators
        addSubmenuIndicators: function() {
            const submenuParents = document.querySelectorAll('.mobile-nav-menu .menu-item-has-children');
            
            submenuParents.forEach(function(parent) {
                const link = parent.querySelector('a');
                if (link && !link.querySelector('.submenu-indicator')) {
                    const indicator = document.createElement('span');
                    indicator.className = 'submenu-indicator';
                    indicator.innerHTML = `
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6,9 12,15 18,9"></polyline>
                        </svg>
                    `;
                    link.appendChild(indicator);
                }
            });
        },

        // Bind events
        bindEvents: function() {
            const self = this;
            
            // Menu toggle
            document.addEventListener('click', function(e) {
                const menuToggle = e.target.closest(self.config.selectors.menuToggle);
                if (menuToggle) {
                    e.preventDefault();
                    e.stopPropagation();
                    self.toggleMenu();
                }
            });

            // Menu close
            document.addEventListener('click', function(e) {
                const menuClose = e.target.closest(self.config.selectors.menuClose);
                if (menuClose) {
                    e.preventDefault();
                    e.stopPropagation();
                    self.closeMenu();
                }
            });

            // Overlay click
            document.addEventListener('click', function(e) {
                const overlay = e.target.closest(self.config.selectors.menuOverlay);
                if (overlay) {
                    e.preventDefault();
                    e.stopPropagation();
                    self.closeMenu();
                }
            });

            // Submenu toggles
            document.addEventListener('click', function(e) {
                const submenuToggle = e.target.closest('.mobile-nav-menu .menu-item-has-children > a');
                if (submenuToggle) {
                    e.preventDefault();
                    e.stopPropagation();
                    self.toggleSubmenu(submenuToggle);
                }
            });

            // Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && self.isOpen) {
                    self.closeMenu();
                }
            });

            // Focus trap
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Tab' && self.isOpen) {
                    self.handleTabKey(e);
                }
            });

            // Close menu on outside click
            document.addEventListener('click', function(e) {
                const mobileMenu = document.querySelector(self.config.selectors.mobileMenu);
                const menuToggle = document.querySelector(self.config.selectors.menuToggle);
                
                if (self.isOpen && mobileMenu && menuToggle) {
                    if (!mobileMenu.contains(e.target) && !menuToggle.contains(e.target)) {
                        self.closeMenu();
                    }
                }
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 991 && self.isOpen) {
                    self.closeMenu();
                }
            });

            // Handle orientation change
            window.addEventListener('orientationchange', function() {
                setTimeout(function() {
                    if (window.innerWidth > 991 && self.isOpen) {
                        self.closeMenu();
                    }
                }, 100);
            });
        },

        // Setup accessibility
        setupAccessibility: function() {
            const menuToggle = document.querySelector(this.config.selectors.menuToggle);
            const mobileMenu = document.querySelector(this.config.selectors.mobileMenu);
            
            if (menuToggle) {
                menuToggle.setAttribute('aria-expanded', 'false');
                menuToggle.setAttribute('aria-controls', 'mobile-menu');
                menuToggle.setAttribute('aria-label', 'Abrir menu de navegação');
            }
            
            if (mobileMenu) {
                mobileMenu.setAttribute('id', 'mobile-menu');
                mobileMenu.setAttribute('aria-hidden', 'true');
                mobileMenu.setAttribute('role', 'dialog');
                mobileMenu.setAttribute('aria-modal', 'true');
                mobileMenu.setAttribute('aria-label', 'Menu de navegação');
            }
        },

        // Update focusable elements
        updateFocusableElements: function() {
            const mobileMenu = document.querySelector(this.config.selectors.mobileMenu);
            if (mobileMenu) {
                this.focusableElements = mobileMenu.querySelectorAll(
                    'a[href], button, textarea, input[type="text"], input[type="radio"], input[type="checkbox"], select, [tabindex]:not([tabindex="-1"])'
                );
                this.firstFocusableElement = this.focusableElements[0];
                this.lastFocusableElement = this.focusableElements[this.focusableElements.length - 1];
            }
        },

        // Handle tab key for focus trap
        handleTabKey: function(e) {
            if (this.focusableElements.length === 0) return;

            if (e.shiftKey) {
                if (document.activeElement === this.firstFocusableElement) {
                    this.lastFocusableElement.focus();
                    e.preventDefault();
                }
            } else {
                if (document.activeElement === this.lastFocusableElement) {
                    this.firstFocusableElement.focus();
                    e.preventDefault();
                }
            }
        },

        // Toggle menu
        toggleMenu: function() {
            if (this.isOpen) {
                this.closeMenu();
            } else {
                this.openMenu();
            }
        },

        // Open menu
        openMenu: function() {
            if (this.isAnimating || this.isOpen) return;

            this.isAnimating = true;
            this.isOpen = true;

            const menuToggle = document.querySelector(this.config.selectors.menuToggle);
            const mobileMenu = document.querySelector(this.config.selectors.mobileMenu);
            const body = document.body;

            // Update classes
            if (menuToggle) {
                menuToggle.classList.add(this.config.classes.active);
                menuToggle.setAttribute('aria-expanded', 'true');
                menuToggle.setAttribute('aria-label', 'Fechar menu de navegação');
            }

            if (mobileMenu) {
                mobileMenu.classList.add(this.config.classes.active);
                mobileMenu.setAttribute('aria-hidden', 'false');
            }

            body.classList.add(this.config.classes.menuOpen);

            // Prevent body scroll
            body.style.overflow = 'hidden';

            // Update focusable elements and focus first element
            this.updateFocusableElements();
            if (this.firstFocusableElement) {
                setTimeout(() => {
                    this.firstFocusableElement.focus();
                }, 100);
            }

            // Animation complete
            setTimeout(() => {
                this.isAnimating = false;
            }, this.config.animation.duration);
        },

        // Close menu
        closeMenu: function() {
            if (this.isAnimating || !this.isOpen) return;

            this.isAnimating = true;
            this.isOpen = false;

            const menuToggle = document.querySelector(this.config.selectors.menuToggle);
            const mobileMenu = document.querySelector(this.config.selectors.mobileMenu);
            const body = document.body;

            // Update classes
            if (menuToggle) {
                menuToggle.classList.remove(this.config.classes.active);
                menuToggle.setAttribute('aria-expanded', 'false');
                menuToggle.setAttribute('aria-label', 'Abrir menu de navegação');
            }

            if (mobileMenu) {
                mobileMenu.classList.remove(this.config.classes.active);
                mobileMenu.setAttribute('aria-hidden', 'true');
            }

            body.classList.remove(this.config.classes.menuOpen);

            // Restore body scroll
            body.style.overflow = '';

            // Close all submenus
            this.closeAllSubmenus();

            // Return focus to menu toggle
            if (menuToggle) {
                menuToggle.focus();
            }

            // Animation complete
            setTimeout(() => {
                this.isAnimating = false;
            }, this.config.animation.duration);
        },

        // Toggle submenu
        toggleSubmenu: function(toggle) {
            const parentItem = toggle.parentElement;
            const submenu = parentItem.querySelector(this.config.selectors.submenu);
            
            if (!submenu) return;

            const isOpen = parentItem.classList.contains(this.config.classes.submenuOpen);
            
            if (isOpen) {
                this.closeSubmenu(parentItem);
            } else {
                this.openSubmenu(parentItem);
            }
        },

        // Open submenu
        openSubmenu: function(parentItem) {
            const submenu = parentItem.querySelector(this.config.selectors.submenu);
            if (!submenu) return;

            parentItem.classList.add(this.config.classes.submenuOpen);
            submenu.style.maxHeight = submenu.scrollHeight + 'px';
            submenu.style.opacity = '1';

            // Update aria attributes
            const toggle = parentItem.querySelector('a');
            if (toggle) {
                toggle.setAttribute('aria-expanded', 'true');
            }
        },

        // Close submenu
        closeSubmenu: function(parentItem) {
            const submenu = parentItem.querySelector(this.config.selectors.submenu);
            if (!submenu) return;

            parentItem.classList.remove(this.config.classes.submenuOpen);
            submenu.style.maxHeight = '0';
            submenu.style.opacity = '0';

            // Update aria attributes
            const toggle = parentItem.querySelector('a');
            if (toggle) {
                toggle.setAttribute('aria-expanded', 'false');
            }
        },

        // Close all submenus
        closeAllSubmenus: function() {
            const openSubmenus = document.querySelectorAll('.mobile-nav-menu .' + this.config.classes.submenuOpen);
            openSubmenus.forEach(item => {
                this.closeSubmenu(item);
            });
        },

        // Get menu state
        isMenuOpen: function() {
            return this.isOpen;
        }
    };

    // Initialize when DOM is loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            MobileNavigation.init();
        });
    } else {
        MobileNavigation.init();
    }

    // Make globally available
    window.MobileNavigation = MobileNavigation;

})();