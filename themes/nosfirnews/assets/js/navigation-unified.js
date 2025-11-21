/**
 * NosfirNews Unified Navigation System
 * Consolidates desktop navigation, mobile navigation, and mobile menu functionality
 * @package NosfirNews
 * @since 2.1.0
 */

(function() {
    'use strict';

    class UnifiedNavigation {
        constructor() {
            this.config = {
                stickyOffset: 100,
                smoothScrollDuration: 500,
                dropdownDelay: 200,
                mobileBreakpoint: 768,
                animationDuration: 300,
                animationEasing: 'cubic-bezier(0.4, 0.0, 0.2, 1)'
            };

            this.state = {
                isSticky: false,
                isMobile: false,
                activeDropdown: null,
                scrollPosition: 0,
                mobileMenuOpen: false,
                isAnimating: false
            };

            this.elements = {
                header: null,
                navigation: null,
                menuItems: [],
                dropdownToggles: [],
                searchToggle: null,
                searchForm: null,
                mobileMenuToggle: null,
                mobileMenu: null,
                mobileMenuClose: null,
                mobileMenuOverlay: null,
                mobileNavMenu: null
            };

            this.timers = {
                dropdown: null,
                scroll: null,
                resize: null
            };

            this.init();
        }

        init() {
            this.cacheElements();
            if (!this.elements.navigation) return;

            this.setupStickyHeader();
            this.setupDropdowns();
            this.setupSearch();
            this.setupMobileMenu();
            this.setupAccessibility();
            this.bindEvents();
            this.checkMobileView();
        }

        cacheElements() {
            this.elements.header = document.querySelector('.site-header');
            this.elements.navigation = document.querySelector('.main-navigation');
            this.elements.mobileMenuToggle = document.querySelector('.menu-toggle, .mobile-menu-toggle');
            this.elements.mobileMenu = document.querySelector('.mobile-menu');
            this.elements.mobileMenuClose = document.querySelector('.mobile-menu-close');
            this.elements.mobileMenuOverlay = document.querySelector('.mobile-menu-overlay');
            this.elements.mobileNavMenu = document.querySelector('.mobile-nav-menu');
            this.elements.searchToggle = document.querySelector('.search-toggle');
            this.elements.searchForm = document.querySelector('.search-form');
            
            if (this.elements.navigation) {
                this.elements.menuItems = Array.from(this.elements.navigation.querySelectorAll('li'));
                this.elements.dropdownToggles = Array.from(this.elements.navigation.querySelectorAll('.menu-item-has-children > a'));
            }
        }

        setupStickyHeader() {
            if (!this.elements.header) return;

            this.headerPlaceholder = document.createElement('div');
            this.headerPlaceholder.className = 'header-placeholder';
            this.headerPlaceholder.style.display = 'none';
            this.elements.header.parentNode.insertBefore(this.headerPlaceholder, this.elements.header);

            this.checkStickyState();
        }

        checkStickyState() {
            const scrollY = window.pageYOffset || document.documentElement.scrollTop;
            const shouldBeSticky = scrollY > this.config.stickyOffset;

            if (shouldBeSticky && !this.state.isSticky) {
                this.makeSticky();
            } else if (!shouldBeSticky && this.state.isSticky) {
                this.removeSticky();
            }

            this.state.scrollPosition = scrollY;
        }

        makeSticky() {
            if (!this.elements.header) return;

            const headerHeight = this.elements.header.offsetHeight;
            this.headerPlaceholder.style.height = `${headerHeight}px`;
            this.headerPlaceholder.style.display = 'block';
            this.elements.header.classList.add('is-sticky');
            this.state.isSticky = true;

            // Expor altura do header para CSS ajustar espaçamento do conteúdo
            document.documentElement.style.setProperty('--header-sticky-offset', `${headerHeight}px`);
        }

        removeSticky() {
            if (!this.elements.header) return;

            this.headerPlaceholder.style.display = 'none';
            this.elements.header.classList.remove('is-sticky');
            this.state.isSticky = false;

            // Resetar offset do conteúdo quando o header não estiver sticky
            document.documentElement.style.setProperty('--header-sticky-offset', `0px`);
        }

        setupDropdowns() {
            if (this.elements.dropdownToggles.length === 0) return;

            this.elements.dropdownToggles.forEach(toggle => {
                const menuItem = toggle.parentElement;
                const submenu = menuItem.querySelector('ul');
                
                if (!submenu) return;

                if (!this.isTouchDevice()) {
                    menuItem.addEventListener('mouseenter', () => {
                        this.openDropdown(menuItem, submenu);
                    });

                    menuItem.addEventListener('mouseleave', () => {
                        this.closeDropdown(menuItem, submenu);
                    });
                }

                toggle.addEventListener('click', (e) => {
                    if (this.state.isMobile || this.isTouchDevice()) {
                        e.preventDefault();
                        this.toggleDropdown(menuItem, submenu);
                    }
                });

                toggle.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.toggleDropdown(menuItem, submenu);
                    }
                });
            });
        }

        openDropdown(menuItem, submenu) {
            clearTimeout(this.timers.dropdown);

            this.timers.dropdown = setTimeout(() => {
                this.closeAllDropdowns();
                menuItem.classList.add('dropdown-open');
                submenu.setAttribute('aria-hidden', 'false');
                
                const toggle = menuItem.querySelector('a');
                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'true');
                }

                this.state.activeDropdown = menuItem;
            }, this.config.dropdownDelay);
        }

        closeDropdown(menuItem, submenu) {
            clearTimeout(this.timers.dropdown);

            this.timers.dropdown = setTimeout(() => {
                menuItem.classList.remove('dropdown-open');
                submenu.setAttribute('aria-hidden', 'true');
                
                const toggle = menuItem.querySelector('a');
                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'false');
                }

                if (this.state.activeDropdown === menuItem) {
                    this.state.activeDropdown = null;
                }
            }, this.config.dropdownDelay);
        }

        toggleDropdown(menuItem, submenu) {
            if (menuItem.classList.contains('dropdown-open')) {
                this.closeDropdown(menuItem, submenu);
            } else {
                this.openDropdown(menuItem, submenu);
            }
        }

        closeAllDropdowns() {
            this.elements.dropdownToggles.forEach(toggle => {
                const menuItem = toggle.parentElement;
                const submenu = menuItem.querySelector('ul');
                if (submenu) {
                    menuItem.classList.remove('dropdown-open');
                    submenu.setAttribute('aria-hidden', 'true');
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });
        }

        setupMobileMenu() {
            if (!this.elements.mobileMenuToggle) return;

            this.createMobileMenuStructure();
            this.setupMobileSubmenuToggles();
        }

        createMobileMenuStructure() {
            if (this.elements.mobileMenu) return;

            const mainNav = document.querySelector('.main-navigation');
            const navMenu = document.querySelector('.nav-menu');
            
            if (mainNav && navMenu) {
                const mobileNavClone = navMenu.cloneNode(true);
                mobileNavClone.className = 'mobile-nav-menu';
                
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
                
                document.body.insertAdjacentHTML('beforeend', mobileMenuHTML);
                
                const mobileMenuContent = document.querySelector('.mobile-menu-content');
                if (mobileMenuContent) {
                    mobileMenuContent.appendChild(mobileNavClone);
                }
                
                this.elements.mobileMenu = document.querySelector('.mobile-menu');
                this.elements.mobileMenuClose = document.querySelector('.mobile-menu-close');
                this.elements.mobileMenuOverlay = document.querySelector('.mobile-menu-overlay');
                this.elements.mobileNavMenu = document.querySelector('.mobile-nav-menu');
            }
        }

        setupMobileSubmenuToggles() {
            const submenuParents = document.querySelectorAll('.mobile-nav-menu .menu-item-has-children');
            
            submenuParents.forEach(parent => {
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
        }

        setupSearch() {
            if (!this.elements.searchToggle || !this.elements.searchForm) return;

            this.elements.searchToggle.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleSearch();
            });
        }

        toggleSearch() {
            if (!this.elements.searchForm) return;

            const isOpen = this.elements.searchForm.classList.contains('search-form-open');
            
            if (isOpen) {
                this.elements.searchForm.classList.remove('search-form-open');
                this.elements.searchToggle.setAttribute('aria-expanded', 'false');
            } else {
                this.elements.searchForm.classList.add('search-form-open');
                this.elements.searchToggle.setAttribute('aria-expanded', 'true');
                
                const searchInput = this.elements.searchForm.querySelector('input[type="search"]');
                if (searchInput) {
                    searchInput.focus();
                }
            }
        }

        setupAccessibility() {
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    if (this.state.mobileMenuOpen) {
                        this.closeMobileMenu();
                    } else if (this.state.activeDropdown) {
                        this.closeAllDropdowns();
                    }
                }

                if (e.key === 'Tab' && this.state.mobileMenuOpen) {
                    this.handleMobileMenuTabKey(e);
                }
            });
        }

        bindEvents() {
            window.addEventListener('scroll', () => {
                clearTimeout(this.timers.scroll);
                this.timers.scroll = setTimeout(() => {
                    this.checkStickyState();
                }, 10);
            });

            window.addEventListener('resize', () => {
                clearTimeout(this.timers.resize);
                this.timers.resize = setTimeout(() => {
                    this.checkMobileView();
                }, 250);
            });

            if (this.elements.mobileMenuToggle) {
                this.elements.mobileMenuToggle.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.toggleMobileMenu();
                });
            }

            if (this.elements.mobileMenuClose) {
                this.elements.mobileMenuClose.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.closeMobileMenu();
                });
            }

            if (this.elements.mobileMenuOverlay) {
                this.elements.mobileMenuOverlay.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.closeMobileMenu();
                });
            }

            document.addEventListener('click', (e) => {
                const mobileMenu = this.elements.mobileMenu;
                const menuToggle = this.elements.mobileMenuToggle;
                
                if (this.state.mobileMenuOpen && mobileMenu && menuToggle) {
                    if (!mobileMenu.contains(e.target) && !menuToggle.contains(e.target)) {
                        this.closeMobileMenu();
                    }
                }
            });

            document.addEventListener('click', (e) => {
                const submenuToggle = e.target.closest('.mobile-nav-menu .menu-item-has-children > a');
                if (submenuToggle) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.toggleMobileSubmenu(submenuToggle);
                }
            });
        }

        toggleMobileMenu() {
            if (this.state.isAnimating) return;

            if (this.state.mobileMenuOpen) {
                this.closeMobileMenu();
            } else {
                this.openMobileMenu();
            }
        }

        openMobileMenu() {
            if (!this.elements.mobileMenu || this.state.isAnimating) return;

            this.state.isAnimating = true;
            this.state.mobileMenuOpen = true;
            
            document.body.classList.add('mobile-menu-open');
            this.elements.mobileMenu.classList.add('active');
            
            this.elements.mobileMenuToggle.setAttribute('aria-expanded', 'true');
            
            setTimeout(() => {
                this.state.isAnimating = false;
                const firstFocusable = this.getFirstFocusableElement();
                if (firstFocusable) {
                    firstFocusable.focus();
                }
            }, this.config.animationDuration);
        }

        closeMobileMenu() {
            if (!this.elements.mobileMenu || this.state.isAnimating) return;

            this.state.isAnimating = true;
            
            document.body.classList.remove('mobile-menu-open');
            this.elements.mobileMenu.classList.remove('active');
            
            this.elements.mobileMenuToggle.setAttribute('aria-expanded', 'false');
            
            setTimeout(() => {
                this.state.isAnimating = false;
                this.state.mobileMenuOpen = false;
                this.elements.mobileMenuToggle.focus();
            }, this.config.animationDuration);
        }

        toggleMobileSubmenu(submenuToggle) {
            const parent = submenuToggle.parentElement;
            const submenu = parent.querySelector('.sub-menu');
            
            if (!submenu) return;

            if (parent.classList.contains('submenu-open')) {
                parent.classList.remove('submenu-open');
                submenu.style.maxHeight = '0';
            } else {
                parent.classList.add('submenu-open');
                submenu.style.maxHeight = submenu.scrollHeight + 'px';
            }
        }

        checkMobileView() {
            const wasMobile = this.state.isMobile;
            this.state.isMobile = window.innerWidth < this.config.mobileBreakpoint;

            if (wasMobile !== this.state.isMobile) {
                if (!this.state.isMobile && this.state.mobileMenuOpen) {
                    this.closeMobileMenu();
                }
            }
        }

        isTouchDevice() {
            return 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        }

        getFirstFocusableElement() {
            const focusableElements = this.elements.mobileMenu.querySelectorAll(
                'a[href], button, input, textarea, select, details, [tabindex]:not([tabindex="-1"])'
            );
            return focusableElements[0];
        }

        getLastFocusableElement() {
            const focusableElements = this.elements.mobileMenu.querySelectorAll(
                'a[href], button, input, textarea, select, details, [tabindex]:not([tabindex="-1"])'
            );
            return focusableElements[focusableElements.length - 1];
        }

        handleMobileMenuTabKey(e) {
            const firstFocusable = this.getFirstFocusableElement();
            const lastFocusable = this.getLastFocusableElement();

            if (e.shiftKey) {
                if (document.activeElement === firstFocusable) {
                    e.preventDefault();
                    lastFocusable.focus();
                }
            } else {
                if (document.activeElement === lastFocusable) {
                    e.preventDefault();
                    firstFocusable.focus();
                }
            }
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            new UnifiedNavigation();
        });
    } else {
        new UnifiedNavigation();
    }

})();