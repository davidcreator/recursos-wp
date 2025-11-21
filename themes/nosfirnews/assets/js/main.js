/**
 * NosfirNews Main JavaScript - Otimizado
 * @package NosfirNews
 * @since 2.0.0
 */

(function() {
    'use strict';

    // Configuração do tema
    const THEME_CONFIG = {
        breakpoints: {
            mobile: 768,
            tablet: 1024,
            desktop: 1200
        },
        animations: {
            duration: 300,
            easing: 'cubic-bezier(0.4, 0, 0.2, 1)'
        },
        performance: {
            debounceDelay: 150,
            throttleDelay: 16,
            intersectionThreshold: 0.1
        }
    };

    // Utilitários otimizados
    const utils = {
        // Debounce otimizado
        debounce(func, wait, immediate = false) {
            let timeout;
            return function executedFunction(...args) {
                const context = this;
                const later = () => {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                const callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        },

        // Throttle otimizado usando requestAnimationFrame
        throttle(func, limit) {
            let inThrottle;
            let lastRan;
            return function(...args) {
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    lastRan = Date.now();
                    inThrottle = true;
                    setTimeout(() => {
                        if (Date.now() - lastRan >= limit) {
                            func.apply(context, args);
                            lastRan = Date.now();
                        }
                        inThrottle = false;
                    }, limit);
                }
            };
        },

        // Verificar se elemento está no viewport (otimizado)
        isInViewport(element, threshold = 0) {
            if (!element) return false;
            const rect = element.getBoundingClientRect();
            const windowHeight = window.innerHeight || document.documentElement.clientHeight;
            const windowWidth = window.innerWidth || document.documentElement.clientWidth;
            
            return (
                rect.top >= -threshold &&
                rect.left >= -threshold &&
                rect.bottom <= windowHeight + threshold &&
                rect.right <= windowWidth + threshold
            );
        },

        // Obter breakpoint atual
        getCurrentBreakpoint() {
            const width = window.innerWidth;
            if (width < THEME_CONFIG.breakpoints.mobile) return 'mobile';
            if (width < THEME_CONFIG.breakpoints.tablet) return 'tablet';
            return 'desktop';
        },

        // Criar elemento com atributos (otimizado)
        createElement(tag, attributes = {}, content = '') {
            const element = document.createElement(tag);
            Object.entries(attributes).forEach(([key, value]) => {
                if (key === 'className') {
                    element.className = value;
                } else if (key === 'innerHTML') {
                    element.innerHTML = value;
                } else if (key === 'dataset') {
                    Object.entries(value).forEach(([dataKey, dataValue]) => {
                        element.dataset[dataKey] = dataValue;
                    });
                } else {
                    element.setAttribute(key, value);
                }
            });
            if (content) element.textContent = content;
            return element;
        },

        // Animação suave usando requestAnimationFrame
        smoothScroll(target, duration = 500, offset = 0) {
            const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;
            const startPosition = window.pageYOffset;
            const distance = targetPosition - startPosition;
            let startTime = null;

            function animation(currentTime) {
                if (startTime === null) startTime = currentTime;
                const timeElapsed = currentTime - startTime;
                const run = easeInOutCubic(timeElapsed, startPosition, distance, duration);
                window.scrollTo(0, run);
                if (timeElapsed < duration) requestAnimationFrame(animation);
            }

            function easeInOutCubic(t, b, c, d) {
                t /= d / 2;
                if (t < 1) return c / 2 * t * t * t + b;
                t -= 2;
                return c / 2 * (t * t * t + 2) + b;
            }

            requestAnimationFrame(animation);
        }
    };

    // Sistema de eventos customizados
    class EventEmitter {
        constructor() {
            this.events = {};
        }

        on(event, listener) {
            if (!this.events[event]) {
                this.events[event] = [];
            }
            this.events[event].push(listener);
        }

        off(event, listenerToRemove) {
            if (!this.events[event]) return;
            this.events[event] = this.events[event].filter(
                listener => listener !== listenerToRemove
            );
        }

        emit(event, ...args) {
            if (!this.events[event]) return;
            this.events[event].forEach(listener => listener(...args));
        }
    }

    // Menu Mobile otimizado
    class MobileMenu extends EventEmitter {
        constructor() {
            super();
            this.menuToggle = document.querySelector('.mobile-menu-toggle');
            this.navigation = document.querySelector('.main-navigation');
            this.overlay = null;
            this.isOpen = false;
            this.focusTrap = null;
            
            if (this.menuToggle && this.navigation) {
                this.init();
            }
        }

        init() {
            this.createOverlay();
            this.setupMenuStructure();
            this.bindEvents();
            this.setupAccessibility();
        }

        createOverlay() {
            this.overlay = utils.createElement('div', {
                className: 'mobile-menu-overlay',
                'aria-hidden': 'true'
            });
            document.body.appendChild(this.overlay);
        }

        setupMenuStructure() {
            const menu = this.navigation.querySelector('ul');
            if (!menu) return;

            // Clonar menu para mobile
            const mobileMenuContainer = utils.createElement('div', {
                className: 'mobile-menu',
                id: 'mobile-menu'
            });

            const mobileHeader = utils.createElement('div', {
                className: 'mobile-menu-header'
            });

            const closeButton = utils.createElement('button', {
                className: 'mobile-menu-close',
                'aria-label': 'Fechar menu'
            }, '×');

            mobileHeader.appendChild(closeButton);
            mobileMenuContainer.appendChild(mobileHeader);

            const menuClone = menu.cloneNode(true);
            menuClone.className = 'mobile-nav-menu';
            mobileMenuContainer.appendChild(menuClone);

            document.body.appendChild(mobileMenuContainer);
            this.mobileMenu = mobileMenuContainer;
            this.closeButton = closeButton;
        }

        bindEvents() {
            // Toggle button
            this.menuToggle.addEventListener('click', () => this.toggle());

            // Close button
            if (this.closeButton) {
                this.closeButton.addEventListener('click', () => this.close());
            }

            // Overlay click
            this.overlay.addEventListener('click', () => this.close());

            // Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.isOpen) {
                    this.close();
                    this.menuToggle.focus();
                }
            });

            // Resize handler
            window.addEventListener('resize', utils.debounce(() => {
                if (utils.getCurrentBreakpoint() !== 'mobile' && this.isOpen) {
                    this.close();
                }
            }, THEME_CONFIG.performance.debounceDelay));

            // Prevenir scroll quando menu aberto
            this.on('open', () => {
                document.body.style.overflow = 'hidden';
                document.body.classList.add('mobile-menu-open');
            });

            this.on('close', () => {
                document.body.style.overflow = '';
                document.body.classList.remove('mobile-menu-open');
            });
        }

        setupAccessibility() {
            this.menuToggle.setAttribute('aria-expanded', 'false');
            this.menuToggle.setAttribute('aria-controls', 'mobile-menu');
            this.menuToggle.setAttribute('aria-haspopup', 'true');
        }

        toggle() {
            this.isOpen ? this.close() : this.open();
        }

        open() {
            if (this.isOpen) return;

            this.mobileMenu.classList.add('active');
            this.overlay.classList.add('active');
            this.menuToggle.classList.add('active');
            this.menuToggle.setAttribute('aria-expanded', 'true');
            this.isOpen = true;

            // Focus no primeiro item do menu
            requestAnimationFrame(() => {
                const firstLink = this.mobileMenu.querySelector('a');
                if (firstLink) firstLink.focus();
            });

            this.emit('open');
        }

        close() {
            if (!this.isOpen) return;

            this.mobileMenu.classList.remove('active');
            this.overlay.classList.remove('active');
            this.menuToggle.classList.remove('active');
            this.menuToggle.setAttribute('aria-expanded', 'false');
            this.isOpen = false;

            this.emit('close');
        }
    }

    // Scroll suave para âncoras
    class SmoothScrolling {
        constructor() {
            this.init();
        }

        init() {
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a[href^="#"]');
                if (!link) return;

                const href = link.getAttribute('href');
                if (href === '#') return;

                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    const headerHeight = document.querySelector('.site-header')?.offsetHeight || 0;
                    utils.smoothScroll(target, 500, headerHeight + 20);
                    
                    // Atualizar URL sem reload
                    if (history.pushState) {
                        history.pushState(null, null, href);
                    }
                }
            });
        }
    }

    // Back to top otimizado
    class BackToTop {
        constructor() {
            this.button = this.createButton();
            this.isVisible = false;
            this.scrollThreshold = 300;
            this.init();
        }

        createButton() {
            const button = utils.createElement('button', {
                className: 'back-to-top',
                'aria-label': 'Voltar ao topo',
                title: 'Voltar ao topo',
                innerHTML: `
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M12 19V5M5 12L12 5L19 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                `
            });
            document.body.appendChild(button);
            return button;
        }

        init() {
            this.bindEvents();
            this.checkVisibility(); // Check inicial
        }

        bindEvents() {
            this.button.addEventListener('click', () => this.scrollToTop());

            // Usar Intersection Observer para melhor performance
            let ticking = false;
            window.addEventListener('scroll', () => {
                if (!ticking) {
                    window.requestAnimationFrame(() => {
                        this.checkVisibility();
                        ticking = false;
                    });
                    ticking = true;
                }
            }, { passive: true });
        }

        checkVisibility() {
            const shouldShow = window.pageYOffset > this.scrollThreshold;
            
            if (shouldShow && !this.isVisible) {
                this.show();
            } else if (!shouldShow && this.isVisible) {
                this.hide();
            }
        }

        show() {
            this.button.classList.add('visible');
            this.button.setAttribute('aria-hidden', 'false');
            this.isVisible = true;
        }

        hide() {
            this.button.classList.remove('visible');
            this.button.setAttribute('aria-hidden', 'true');
            this.isVisible = false;
        }

        scrollToTop() {
            utils.smoothScroll(document.body, 500);
        }
    }

    // Lazy loading de imagens otimizado
    class LazyLoading {
        constructor() {
            this.images = [];
            this.observer = null;
            this.init();
        }

        init() {
            if ('IntersectionObserver' in window) {
                this.setupIntersectionObserver();
            } else {
                this.fallbackLazyLoading();
            }
        }

        setupIntersectionObserver() {
            const options = {
                root: null,
                rootMargin: '50px',
                threshold: 0.01
            };

            this.observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.loadImage(entry.target);
                        this.observer.unobserve(entry.target);
                    }
                });
            }, options);

            this.observeImages();
        }

        observeImages() {
            const images = document.querySelectorAll('img[data-src], img[loading="lazy"]');
            images.forEach(img => {
                if (img.dataset.src) {
                    this.observer.observe(img);
                }
            });
        }

        loadImage(img) {
            if (!img.dataset.src) return;

            const tempImg = new Image();
            tempImg.onload = () => {
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                img.classList.remove('lazy');
                img.classList.add('loaded');
            };
            tempImg.onerror = () => {
                console.error('Erro ao carregar imagem:', img.dataset.src);
            };
            tempImg.src = img.dataset.src;
        }

        fallbackLazyLoading() {
            const images = document.querySelectorAll('img[data-src]');
            
            const loadImagesInViewport = utils.throttle(() => {
                images.forEach(img => {
                    if (utils.isInViewport(img, 50)) {
                        this.loadImage(img);
                    }
                });
            }, THEME_CONFIG.performance.throttleDelay);

            window.addEventListener('scroll', loadImagesInViewport, { passive: true });
            window.addEventListener('resize', loadImagesInViewport, { passive: true });
            loadImagesInViewport();
        }
    }

    // Barra de progresso de leitura
    class ReadingProgress {
        constructor() {
            if (!document.body.classList.contains('single')) return;
            this.progressBar = this.createProgressBar();
            this.init();
        }

        createProgressBar() {
            const container = utils.createElement('div', {
                className: 'reading-progress',
                'aria-hidden': 'true'
            });
            
            const bar = utils.createElement('div', {
                className: 'reading-progress-bar'
            });
            
            container.appendChild(bar);
            document.body.appendChild(container);
            
            return bar;
        }

        init() {
            let ticking = false;
            window.addEventListener('scroll', () => {
                if (!ticking) {
                    window.requestAnimationFrame(() => {
                        this.updateProgress();
                        ticking = false;
                    });
                    ticking = true;
                }
            }, { passive: true });

            this.updateProgress(); // Inicial
        }

        updateProgress() {
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const trackLength = documentHeight - windowHeight;
            const progress = Math.min((scrollTop / trackLength) * 100, 100);
            
            this.progressBar.style.width = `${progress}%`;
        }
    }

    // Melhorias de acessibilidade
    class AccessibilityEnhancements {
        constructor() {
            this.init();
        }

        init() {
            this.addSkipLinks();
            this.enhanceFocusManagement();
            this.setupKeyboardNavigation();
        }

        addSkipLinks() {
            if (document.querySelector('.skip-links')) return;

            const skipLinks = utils.createElement('div', {
                className: 'skip-links'
            });

            const links = [
                { href: '#main', text: 'Pular para o conteúdo' },
                { href: '#site-navigation', text: 'Pular para navegação' }
            ];

            links.forEach(link => {
                const a = utils.createElement('a', {
                    className: 'skip-link',
                    href: link.href
                }, link.text);
                skipLinks.appendChild(a);
            });

            document.body.insertBefore(skipLinks, document.body.firstChild);
        }

        enhanceFocusManagement() {
            // Adicionar indicadores de foco para navegação por teclado
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Tab') {
                    document.body.classList.add('keyboard-navigation');
                }
            });

            document.addEventListener('mousedown', () => {
                document.body.classList.remove('keyboard-navigation');
            });
        }

        setupKeyboardNavigation() {
            // Melhorar navegação por teclado em elementos interativos
            const interactiveElements = document.querySelectorAll(
                'button, a, input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );

            interactiveElements.forEach(element => {
                if (!element.hasAttribute('aria-label') && !element.textContent.trim()) {
                    console.warn('Elemento interativo sem label:', element);
                }
            });
        }
    }

    // Theme Manager - Gerenciador principal
    class NosfirNewsTheme {
        constructor() {
            this.components = new Map();
            this.isInitialized = false;
            this.init();
        }

        async init() {
            // Aguardar DOM estar pronto
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => this.initializeComponents());
            } else {
                this.initializeComponents();
            }
        }

        initializeComponents() {
            try {
                // Inicializar componentes na ordem correta
                this.components.set('accessibility', new AccessibilityEnhancements());
                this.components.set('smoothScrolling', new SmoothScrolling());
                this.components.set('backToTop', new BackToTop());
                this.components.set('lazyLoading', new LazyLoading());
                this.components.set('readingProgress', new ReadingProgress());

                this.isInitialized = true;
                this.emit('initialized');
                
                console.log('NosfirNews: Tema inicializado com sucesso');
            } catch (error) {
                console.error('NosfirNews: Erro ao inicializar:', error);
            }
        }

        getComponent(name) {
            return this.components.get(name);
        }

        emit(event) {
            document.dispatchEvent(new CustomEvent(`nosfirnews:${event}`));
        }
    }

    // Inicializar tema
    const theme = new NosfirNewsTheme();

    // Expor globalmente para debug e extensões
    window.NosfirNews = {
        theme,
        utils,
        config: THEME_CONFIG
    };

    // Suporte a módulos
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = window.NosfirNews;
    }

})();