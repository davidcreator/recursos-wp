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

})();/**
 * NosfirNews Mobile Menu - Otimizado
 * @package NosfirNews
 * @since 2.0.0
 */

(function() {
    'use strict';

    class MobileMenuEnhanced {
        constructor() {
            this.config = {
                breakpoint: 768,
                animationDuration: 300,
                swipeThreshold: 50
            };

            this.state = {
                isOpen: false,
                isAnimating: false,
                touchStartX: 0,
                touchCurrentX: 0
            };

            this.elements = {
                toggle: null,
                menu: null,
                overlay: null,
                closeBtn: null,
                menuItems: []
            };

            this.init();
        }

        init() {
            this.cacheElements();
            if (!this.elements.toggle) return;

            this.createMobileMenu();
            this.bindEvents();
            this.setupAccessibility();
            this.checkBreakpoint();
        }

        cacheElements() {
            this.elements.toggle = document.querySelector('.mobile-menu-toggle');
            const nav = document.querySelector('.main-navigation');
            if (nav) {
                this.elements.menuItems = Array.from(nav.querySelectorAll('a'));
            }
        }

        createMobileMenu() {
            // Criar overlay
            this.elements.overlay = this.createElement('div', {
                className: 'mobile-menu-overlay',
                'aria-hidden': 'true'
            });

            // Criar container do menu
            this.elements.menu = this.createElement('div', {
                className: 'mobile-menu',
                id: 'mobile-menu',
                'aria-label': 'Menu de navegação mobile'
            });

            // Header do menu
            const header = this.createElement('div', {
                className: 'mobile-menu-header'
            });

            const title = this.createElement('h2', {
                className: 'mobile-menu-title'
            }, 'Menu');

            this.elements.closeBtn = this.createElement('button', {
                className: 'mobile-menu-close',
                'aria-label': 'Fechar menu',
                type: 'button'
            });
            this.elements.closeBtn.innerHTML = `
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            `;

            header.appendChild(title);
            header.appendChild(this.elements.closeBtn);

            // Conteúdo do menu
            const content = this.createElement('div', {
                className: 'mobile-menu-content'
            });

            const nav = document.querySelector('.main-navigation ul');
            if (nav) {
                const menuClone = nav.cloneNode(true);
                menuClone.className = 'mobile-nav-menu';
                this.processMenuItems(menuClone);
                content.appendChild(menuClone);
            }

            // Footer do menu (opcional)
            const footer = this.createMenuFooter();

            // Montar menu
            this.elements.menu.appendChild(header);
            this.elements.menu.appendChild(content);
            if (footer) this.elements.menu.appendChild(footer);

            // Adicionar ao DOM
            document.body.appendChild(this.elements.overlay);
            document.body.appendChild(this.elements.menu);
        }

        processMenuItems(menu) {
            // Adicionar ícones de submenu e funcionalidade
            const items = menu.querySelectorAll('li');
            items.forEach(item => {
                const submenu = item.querySelector('ul');
                if (submenu) {
                    const link = item.querySelector('a');
                    const toggleBtn = this.createElement('button', {
                        className: 'submenu-toggle',
                        'aria-expanded': 'false',
                        'aria-label': 'Expandir submenu'
                    });
                    toggleBtn.innerHTML = `
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    `;
                    
                    toggleBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        this.toggleSubmenu(item, toggleBtn);
                    });

                    link.parentNode.insertBefore(toggleBtn, link.nextSibling);
                }
            });
        }

        toggleSubmenu(item, button) {
            const submenu = item.querySelector('ul');
            const isExpanded = button.getAttribute('aria-expanded') === 'true';

            if (isExpanded) {
                submenu.style.maxHeight = '0';
                button.setAttribute('aria-expanded', 'false');
                button.classList.remove('active');
            } else {
                submenu.style.maxHeight = submenu.scrollHeight + 'px';
                button.setAttribute('aria-expanded', 'true');
                button.classList.add('active');
            }
        }

        createMenuFooter() {
            const socialMenu = document.querySelector('.social-links-menu');
            if (!socialMenu) return null;

            const footer = this.createElement('div', {
                className: 'mobile-menu-footer'
            });

            const socialClone = socialMenu.cloneNode(true);
            footer.appendChild(socialClone);

            return footer;
        }

        bindEvents() {
            // Toggle button
            this.elements.toggle.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggle();
            });

            // Close button
            this.elements.closeBtn.addEventListener('click', () => this.close());

            // Overlay click
            this.elements.overlay.addEventListener('click', () => this.close());

            // Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.state.isOpen) {
                    this.close();
                    this.elements.toggle.focus();
                }
            });

            // Window resize
            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => this.checkBreakpoint(), 150);
            });

            // Touch gestures
            this.setupTouchGestures();

            // Link clicks
            const links = this.elements.menu.querySelectorAll('a');
            links.forEach(link => {
                link.addEventListener('click', () => {
                    // Fechar menu ao clicar em link (exceto se tiver submenu)
                    if (!link.parentElement.querySelector('ul')) {
                        this.close();
                    }
                });
            });
        }

        setupTouchGestures() {
            // Swipe para fechar
            this.elements.menu.addEventListener('touchstart', (e) => {
                this.state.touchStartX = e.touches[0].clientX;
            }, { passive: true });

            this.elements.menu.addEventListener('touchmove', (e) => {
                if (!this.state.isOpen) return;
                this.state.touchCurrentX = e.touches[0].clientX;
            }, { passive: true });

            this.elements.menu.addEventListener('touchend', () => {
                const diff = this.state.touchStartX - this.state.touchCurrentX;
                
                // Swipe para a direita fecha o menu
                if (diff < -this.config.swipeThreshold) {
                    this.close();
                }

                this.state.touchStartX = 0;
                this.state.touchCurrentX = 0;
            });
        }

        setupAccessibility() {
            this.elements.toggle.setAttribute('aria-expanded', 'false');
            this.elements.toggle.setAttribute('aria-controls', 'mobile-menu');
            this.elements.toggle.setAttribute('aria-haspopup', 'true');
        }

        checkBreakpoint() {
            const isMobile = window.innerWidth < this.config.breakpoint;
            
            if (!isMobile && this.state.isOpen) {
                this.close();
            }
        }

        toggle() {
            this.state.isOpen ? this.close() : this.open();
        }

        async open() {
            if (this.state.isOpen || this.state.isAnimating) return;

            this.state.isAnimating = true;

            // Adicionar classes
            this.elements.menu.classList.add('opening');
            this.elements.overlay.classList.add('active');
            this.elements.toggle.classList.add('active');
            
            // Atualizar ARIA
            this.elements.toggle.setAttribute('aria-expanded', 'true');
            this.elements.overlay.setAttribute('aria-hidden', 'false');

            // Prevenir scroll do body
            document.body.style.overflow = 'hidden';
            document.body.classList.add('mobile-menu-open');

            // Aguardar animação
            await this.wait(50);
            this.elements.menu.classList.add('active');
            this.elements.menu.classList.remove('opening');

            await this.wait(this.config.animationDuration);

            // Focus trap
            this.trapFocus();

            this.state.isOpen = true;
            this.state.isAnimating = false;

            // Dispatch event
            this.emit('menuOpened');
        }

        async close() {
            if (!this.state.isOpen || this.state.isAnimating) return;

            this.state.isAnimating = true;

            // Remover classes
            this.elements.menu.classList.add('closing');
            this.elements.menu.classList.remove('active');
            this.elements.overlay.classList.remove('active');
            this.elements.toggle.classList.remove('active');

            // Atualizar ARIA
            this.elements.toggle.setAttribute('aria-expanded', 'false');
            this.elements.overlay.setAttribute('aria-hidden', 'true');

            // Aguardar animação
            await this.wait(this.config.animationDuration);
            this.elements.menu.classList.remove('closing');

            // Restaurar scroll do body
            document.body.style.overflow = '';
            document.body.classList.remove('mobile-menu-open');

            // Liberar focus trap
            this.releaseFocus();

            this.state.isOpen = false;
            this.state.isAnimating = false;

            // Dispatch event
            this.emit('menuClosed');
        }

        trapFocus() {
            const focusableElements = this.elements.menu.querySelectorAll(
                'a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])'
            );
            
            if (focusableElements.length === 0) return;

            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];

            // Focus no primeiro elemento
            requestAnimationFrame(() => firstElement.focus());

            this.focusTrapHandler = (e) => {
                if (e.key !== 'Tab') return;

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
            };

            document.addEventListener('keydown', this.focusTrapHandler);
        }

        releaseFocus() {
            if (this.focusTrapHandler) {
                document.removeEventListener('keydown', this.focusTrapHandler);
                this.focusTrapHandler = null;
            }
        }

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

        wait(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }

        emit(eventName, detail = {}) {
            const event = new CustomEvent(`nosfirnews:${eventName}`, { detail });
            document.dispatchEvent(event);
        }

        destroy() {
            // Cleanup
            if (this.state.isOpen) {
                this.close();
            }

            if (this.elements.menu) {
                this.elements.menu.remove();
            }
            if (this.elements.overlay) {
                this.elements.overlay.remove();
            }

            this.releaseFocus();
        }
    }

    // Inicializar quando DOM estiver pronto
    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                new MobileMenuEnhanced();
            });
        } else {
            new MobileMenuEnhanced();
        }
    }

    init();

    // Expor para uso global
    window.NosfirNewsMobileMenu = MobileMenuEnhanced;

})();