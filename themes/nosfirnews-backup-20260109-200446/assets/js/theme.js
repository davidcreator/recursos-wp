/**
 * NosfirNews Theme JavaScript
 * Arquivo principal consolidado
 * 
 * @package NosfirNews
 * @version 1.0.1
 */

(function($) {
    'use strict';

    // ============================================
    // 1. UTILITÁRIOS
    // ============================================

    const NosfirUtils = {
        
        /**
         * Query selector helper
         */
        qs: (selector, context = document) => context.querySelector(selector),
        
        /**
         * Query selector all helper
         */
        qsa: (selector, context = document) => context.querySelectorAll(selector),
        
        /**
         * Debounce function
         */
        debounce: (func, wait = 300) => {
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
        
        /**
         * Throttle function
         */
        throttle: (func, limit = 100) => {
            let inThrottle;
            return function(...args) {
                if (!inThrottle) {
                    func.apply(this, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },
        
        /**
         * Verifica se é mobile
         */
        isMobile: () => window.innerWidth < 992,
        
        /**
         * Adiciona classe com animação
         */
        addClass: (el, className, delay = 0) => {
            setTimeout(() => el?.classList.add(className), delay);
        },
        
        /**
         * Remove classe com animação
         */
        removeClass: (el, className, delay = 0) => {
            setTimeout(() => el?.classList.remove(className), delay);
        }
    };

    // ============================================
    // 2. MOBILE MENU
    // ============================================

    class MobileMenu {
        constructor() {
            this.toggle = NosfirUtils.qs('.nav-toggle');
            this.drawer = NosfirUtils.qs('#mobile-menu');
            this.closeBtn = NosfirUtils.qs('.drawer-close', this.drawer);
            this.body = document.body;
            
            if (!this.toggle || !this.drawer) return;
            
            this.init();
        }
        
        init() {
            // Click no toggle
            this.toggle.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleMenu();
            });
            
            // Click no botão fechar
            if (this.closeBtn) {
                this.closeBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.close();
                });
            }
            
            // Click no backdrop
            this.drawer.addEventListener('click', (e) => {
                if (e.target === this.drawer) {
                    this.close();
                }
            });
            
            // Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.isOpen()) {
                    this.close();
                }
            });
            
            // Submenu toggles
            this.initSubmenus();
        }
        
        toggleMenu() {
            if (this.isOpen()) {
                this.close();
            } else {
                this.open();
            }
        }
        
        open() {
            this.drawer.classList.add('open');
            this.toggle.setAttribute('aria-expanded', 'true');
            this.drawer.setAttribute('aria-hidden', 'false');
            this.body.classList.add('nn-lock');
            
            // Focus no primeiro link
            const firstLink = NosfirUtils.qs('a', this.drawer);
            if (firstLink) {
                setTimeout(() => firstLink.focus(), 300);
            }
        }
        
        close() {
            this.drawer.classList.remove('open');
            this.toggle.setAttribute('aria-expanded', 'false');
            this.drawer.setAttribute('aria-hidden', 'true');
            this.body.classList.remove('nn-lock');
            
            // Retorna focus ao toggle
            this.toggle.focus();
        }
        
        isOpen() {
            return this.drawer.classList.contains('open');
        }
        
        initSubmenus() {
            const submenus = NosfirUtils.qsa('.menu-item-has-children', this.drawer);
            
            submenus.forEach(item => {
                const link = NosfirUtils.qs(':scope > a', item);
                const submenu = NosfirUtils.qs(':scope > .sub-menu', item);
                
                if (!link || !submenu) return;
                
                // Adiciona botão toggle se não existir
                let toggle = NosfirUtils.qs(':scope > .submenu-toggle', item);
                if (!toggle) {
                    toggle = document.createElement('button');
                    toggle.className = 'submenu-toggle';
                    toggle.setAttribute('aria-expanded', 'false');
                    toggle.innerHTML = '▼';
                    link.parentNode.insertBefore(toggle, link.nextSibling);
                }
                
                // Click no toggle
                toggle.addEventListener('click', (e) => {
                    e.preventDefault();
                    const isOpen = submenu.classList.contains('open');
                    
                    // Fecha outros submenus no mesmo nível
                    const siblings = item.parentElement.querySelectorAll(':scope > .menu-item-has-children .sub-menu.open');
                    siblings.forEach(s => {
                        if (s !== submenu) {
                            s.classList.remove('open');
                            const siblingToggle = s.parentElement.querySelector('.submenu-toggle');
                            if (siblingToggle) siblingToggle.setAttribute('aria-expanded', 'false');
                        }
                    });
                    
                    // Toggle atual
                    submenu.classList.toggle('open');
                    toggle.setAttribute('aria-expanded', !isOpen);
                });
            });
        }
    }

    // ============================================
    // 3. STICKY HEADER
    // ============================================

    class StickyHeader {
        constructor() {
            this.header = NosfirUtils.qs('.site-header');
            if (!this.header) return;
            
            this.lastScroll = 0;
            this.scrollThreshold = 100;
            
            this.init();
        }
        
        init() {
            const handleScroll = NosfirUtils.throttle(() => {
                const currentScroll = window.pageYOffset;
                
                if (currentScroll > this.scrollThreshold) {
                    this.header.classList.add('header-scrolled');
                } else {
                    this.header.classList.remove('header-scrolled');
                }
                
                this.lastScroll = currentScroll;
            }, 100);
            
            window.addEventListener('scroll', handleScroll, { passive: true });
            
            // Trigger inicial
            handleScroll();
        }
    }

    // ============================================
    // 4. SEARCH TOGGLE
    // ============================================

    class SearchToggle {
        constructor() {
            this.toggle = NosfirUtils.qs('.search-toggle');
            this.form = NosfirUtils.qs('.search-form');
            
            if (!this.toggle || !this.form) return;
            
            this.init();
        }
        
        init() {
            this.toggle.addEventListener('click', (e) => {
                e.preventDefault();
                const isVisible = this.form.style.display === 'block';
                
                this.form.style.display = isVisible ? 'none' : 'block';
                
                if (!isVisible) {
                    const input = NosfirUtils.qs('input[type="search"]', this.form);
                    if (input) input.focus();
                }
            });
            
            // Fecha ao clicar fora
            document.addEventListener('click', (e) => {
                if (!this.toggle.contains(e.target) && !this.form.contains(e.target)) {
                    this.form.style.display = 'none';
                }
            });
        }
    }

    // ============================================
    // 5. SMOOTH SCROLL
    // ============================================

    class SmoothScroll {
        constructor() {
            this.links = NosfirUtils.qsa('a[href^="#"]');
            if (this.links.length === 0) return;
            
            this.init();
        }
        
        init() {
            this.links.forEach(link => {
                link.addEventListener('click', (e) => {
                    const href = link.getAttribute('href');
                    
                    // Ignora # sozinho
                    if (href === '#') return;
                    
                    const target = NosfirUtils.qs(href);
                    if (!target) return;
                    
                    e.preventDefault();
                    
                    const headerOffset = 80;
                    const elementPosition = target.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                    
                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                    
                    // Update URL
                    if (history.pushState) {
                        history.pushState(null, null, href);
                    }
                });
            });
        }
    }

    // ============================================
    // 6. LAZY LOAD IMAGES
    // ============================================

    class LazyLoad {
        constructor() {
            this.images = NosfirUtils.qsa('img[data-src]');
            if (this.images.length === 0) return;
            
            this.init();
        }
        
        init() {
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                            observer.unobserve(img);
                        }
                    });
                }, {
                    rootMargin: '50px'
                });
                
                this.images.forEach(img => observer.observe(img));
            } else {
                // Fallback para navegadores antigos
                this.images.forEach(img => {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                });
            }
        }
    }

    // ============================================
    // 7. BACK TO TOP
    // ============================================

    class BackToTop {
        constructor() {
            this.button = this.createButton();
            this.init();
        }
        
        createButton() {
            const btn = document.createElement('button');
            btn.className = 'back-to-top';
            btn.innerHTML = '↑';
            btn.setAttribute('aria-label', 'Voltar ao topo');
            btn.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                width: 48px;
                height: 48px;
                border: 0;
                border-radius: 50%;
                background: var(--primary, #1a73e8);
                color: white;
                font-size: 24px;
                cursor: pointer;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
                z-index: 999;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            `;
            document.body.appendChild(btn);
            return btn;
        }
        
        init() {
            // Show/hide no scroll
            const handleScroll = NosfirUtils.throttle(() => {
                if (window.pageYOffset > 300) {
                    this.button.style.opacity = '1';
                    this.button.style.visibility = 'visible';
                } else {
                    this.button.style.opacity = '0';
                    this.button.style.visibility = 'hidden';
                }
            }, 100);
            
            window.addEventListener('scroll', handleScroll, { passive: true });
            
            // Click
            this.button.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
    }

    // ============================================
    // 8. INICIALIZAÇÃO
    // ============================================

    function init() {
        // Inicializa componentes
        new MobileMenu();
        new StickyHeader();
        new SearchToggle();
        new SmoothScroll();
        new LazyLoad();
        new BackToTop();
        
        // Event: DOM Ready
        document.dispatchEvent(new Event('nosfirnews:ready'));
    }

    // Aguarda DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expõe utilitários globalmente
    window.NosfirUtils = NosfirUtils;

})(jQuery);