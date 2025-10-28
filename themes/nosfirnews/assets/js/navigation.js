/**
 * NosfirNews Navigation System - Otimizado
 * @package NosfirNews
 * @since 2.0.0
 */

(function() {
    'use strict';

    class NavigationSystem {
        constructor() {
            this.config = {
                stickyOffset: 100,
                smoothScrollDuration: 500,
                dropdownDelay: 200,
                mobileBreakpoint: 768
            };

            this.state = {
                isSticky: false,
                isMobile: false,
                activeDropdown: null,
                scrollPosition: 0
            };

            this.elements = {
                header: null,
                navigation: null,
                menuItems: [],
                dropdownToggles: [],
                searchToggle: null,
                searchForm: null
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
            this.setupAccessibility();
            this.bindEvents();
            this.checkMobileView();
        }

        cacheElements() {
            this.elements.header = document.querySelector('.site-header');
            this.elements.navigation = document.querySelector('.main-navigation');
            
            if (this.elements.navigation) {
                this.elements.menuItems = Array.from(
                    this.elements.navigation.querySelectorAll('li')
                );
                this.elements.dropdownToggles = Array.from(
                    this.elements.navigation.querySelectorAll('.menu-item-has-children > a')
                );
            }

            this.elements.searchToggle = document.querySelector('.search-toggle');
            this.elements.searchForm = document.querySelector('.search-form');
        }

        setupStickyHeader() {
            if (!this.elements.header) return;

            // Adicionar placeholder para evitar jump de layout
            this.headerPlaceholder = document.createElement('div');
            this.headerPlaceholder.className = 'header-placeholder';
            this.headerPlaceholder.style.display = 'none';
            this.elements.header.parentNode.insertBefore(
                this.headerPlaceholder,
                this.elements.header
            );

            // Verificar estado inicial
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

            this.emit('headerSticky');
        }

        removeSticky() {
            if (!this.elements.header) return;

            this.headerPlaceholder.style.display = 'none';
            this.elements.header.classList.remove('is-sticky');
            this.state.isSticky = false;

            this.emit('headerNotSticky');
        }

        setupDropdowns() {
            if (this.elements.dropdownToggles.length === 0) return;

            this.elements.dropdownToggles.forEach(toggle => {
                const menuItem = toggle.parentElement;
                const submenu = menuItem.querySelector('ul');
                
                if (!submenu) return;

                // Desktop: hover
                if (!this.isTouchDevice()) {
                    menuItem.addEventListener('mouseenter', () => {
                        this.openDropdown(menuItem, submenu);
                    });

                    menuItem.addEventListener('mouseleave', () => {
                        this.closeDropdown(menuItem, submenu);
                    });
                }

                // Mobile/Touch: click
                toggle.addEventListener('click', (e) => {
                    if (this.state.isMobile || this.isTouchDevice()) {
                        e.preventDefault();
                        this.toggleDropdown(menuItem, submenu);
                    }
                });

                // Acessibilidade: Enter/Space
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
                // Fechar outros dropdowns
                this.closeAllDropdowns();

                menuItem.classList.add('dropdown-open');
                submenu.setAttribute('aria-hidden', 'false');
                
                const toggle = menuItem.querySelector('a');
                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'true');
                }

                this.state.activeDropdown = menuItem;
                this.emit('dropdownOpened', { menuItem });
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

                this.emit('dropdownClosed', { menuItem });
            }, this.config.dropdownDelay);
        }

        toggleDropdown(menuItem, submenu) {
            const isOpen = menuItem.classList.contains('dropdown-open');
            
            if (isOpen) {
                this.closeDropdown(menuItem, submenu);
            } else {
                this.openDropdown(menuItem, submenu);
            }
        }

        closeAllDropdowns() {
            const openDropdowns = this.elements.navigation.querySelectorAll('.dropdown-open');
            
            openDropdowns.forEach(item => {
                const submenu = item.querySelector('ul');
                if (submenu) {
                    this.closeDropdown(item, submenu);
                }
            });
        }

        setupSearch() {
            if (!this.elements.searchToggle || !this.elements.searchForm) return;

            this.elements.searchToggle.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleSearch();
            });

            // Fechar ao clicar fora
            document.addEventListener('click', (e) => {
                if (this.isSearchOpen() && 
                    !this.elements.searchForm.contains(e.target) &&
                    !this.elements.searchToggle.contains(e.target)) {
                    this.closeSearch();
                }
            });

            // Fechar com Escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.isSearchOpen()) {
                    this.closeSearch();
                    this.elements.searchToggle.focus();
                }
            });
        }

        toggleSearch() {
            if (this.isSearchOpen()) {
                this.closeSearch();
            } else {
                this.openSearch();
            }
        }

        openSearch() {
            this.elements.searchForm.classList.add('active');
            this.elements.searchToggle.classList.add('active');
            this.elements.searchToggle.setAttribute('aria-expanded', 'true');

            // Focus no input
            requestAnimationFrame(() => {
                const searchInput = this.elements.searchForm.querySelector('input[type="search"]');
                if (searchInput) searchInput.focus();
            });

            this.emit('searchOpened');
        }

        closeSearch() {
            this.elements.searchForm.classList.remove('active');
            this.elements.searchToggle.classList.remove('active');
            this.elements.searchToggle.setAttribute('aria-expanded', 'false');

            this.emit('searchClosed');
        }

        isSearchOpen() {
            return this.elements.searchForm.classList.contains('active');
        }

        setupAccessibility() {
            // Adicionar atributos ARIA
            this.elements.menuItems.forEach(item => {
                const submenu = item.querySelector('ul');
                if (submenu) {
                    const toggle = item.querySelector('a');
                    if (toggle) {
                        toggle.setAttribute('aria-haspopup', 'true');
                        toggle.setAttribute('aria-expanded', 'false');
                    }
                    submenu.setAttribute('aria-hidden', 'true');
                }
            });

            // Navegação por teclado melhorada
            this.setupKeyboardNavigation();
        }

        setupKeyboardNavigation() {
            const links = this.elements.navigation.querySelectorAll('a');

            links.forEach((link, index) => {
                link.addEventListener('keydown', (e) => {
                    const currentItem = link.parentElement;
                    let targetLink = null;

                    switch (e.key) {
                        case 'ArrowRight':
                            e.preventDefault();
                            targetLink = this.getNextLink(link, links);
                            break;
                        
                        case 'ArrowLeft':
                            e.preventDefault();
                            targetLink = this.getPreviousLink(link, links);
                            break;
                        
                        case 'ArrowDown':
                            e.preventDefault();
                            const submenu = currentItem.querySelector('ul');
                            if (submenu) {
                                const firstSubmenuLink = submenu.querySelector('a');
                                if (firstSubmenuLink) {
                                    this.openDropdown(currentItem, submenu);
                                    targetLink = firstSubmenuLink;
                                }
                            }
                            break;
                        
                        case 'ArrowUp':
                            e.preventDefault();
                            const parentItem = currentItem.closest('li.menu-item-has-children');
                            if (parentItem) {
                                targetLink = parentItem.querySelector('a');
                                const parentSubmenu = parentItem.querySelector('ul');
                                if (parentSubmenu) {
                                    this.closeDropdown(parentItem, parentSubmenu);
                                }
                            }
                            break;
                        
                        case 'Escape':
                            this.closeAllDropdowns();
                            break;
                    }

                    if (targetLink) {
                        targetLink.focus();
                    }
                });
            });
        }

        getNextLink(currentLink, allLinks) {
            const currentIndex = Array.from(allLinks).indexOf(currentLink);
            const nextIndex = (currentIndex + 1) % allLinks.length;
            return allLinks[nextIndex];
        }

        getPreviousLink(currentLink, allLinks) {
            const currentIndex = Array.from(allLinks).indexOf(currentLink);
            const previousIndex = currentIndex === 0 ? allLinks.length - 1 : currentIndex - 1;
            return allLinks[previousIndex];
        }

        bindEvents() {
            // Scroll com throttle
            let scrollTicking = false;
            window.addEventListener('scroll', () => {
                if (!scrollTicking) {
                    window.requestAnimationFrame(() => {
                        this.checkStickyState();
                        scrollTicking = false;
                    });
                    scrollTicking = true;
                }
            }, { passive: true });

            // Resize com debounce
            window.addEventListener('resize', () => {
                clearTimeout(this.timers.resize);
                this.timers.resize = setTimeout(() => {
                    this.checkMobileView();
                    this.adjustDropdownPositions();
                }, 150);
            });

            // Click fora fecha dropdowns
            document.addEventListener('click', (e) => {
                if (this.elements.navigation && 
                    !this.elements.navigation.contains(e.target)) {
                    this.closeAllDropdowns();
                }
            });
        }

        checkMobileView() {
            const wasMobile = this.state.isMobile;
            this.state.isMobile = window.innerWidth < this.config.mobileBreakpoint;

            if (wasMobile !== this.state.isMobile) {
                this.closeAllDropdowns();
                this.emit('breakpointChanged', { 
                    isMobile: this.state.isMobile 
                });
            }
        }

        adjustDropdownPositions() {
            // Ajustar posição dos dropdowns para não sair da tela
            const dropdowns = this.elements.navigation.querySelectorAll('.menu-item-has-children');

            dropdowns.forEach(item => {
                const submenu = item.querySelector('ul');
                if (!submenu) return;

                const rect = submenu.getBoundingClientRect();
                const windowWidth = window.innerWidth;

                if (rect.right > windowWidth) {
                    submenu.classList.add('align-right');
                } else {
                    submenu.classList.remove('align-right');
                }
            });
        }

        isTouchDevice() {
            return 'ontouchstart' in window || 
                   navigator.maxTouchPoints > 0 || 
                   navigator.msMaxTouchPoints > 0;
        }

        emit(eventName, detail = {}) {
            const event = new CustomEvent(`nosfirnews:nav:${eventName}`, {
                detail,
                bubbles: true
            });
            document.dispatchEvent(event);
        }

        // Métodos públicos
        scrollToTop(smooth = true) {
            if (smooth) {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            } else {
                window.scrollTo(0, 0);
            }
        }

        getState() {
            return { ...this.state };
        }

        destroy() {
            // Cleanup
            Object.values(this.timers).forEach(timer => clearTimeout(timer));
            this.closeAllDropdowns();
            if (this.headerPlaceholder) {
                this.headerPlaceholder.remove();
            }
        }
    }

    // Inicializar
    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                window.NosfirNewsNavigation = new NavigationSystem();
            });
        } else {
            window.NosfirNewsNavigation = new NavigationSystem();
        }
    }

    init();

    // Expor classe
    window.NavigationSystem = NavigationSystem;

})();