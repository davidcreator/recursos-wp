/**
 * NosfirNews Utilities & Helpers
 * @package NosfirNews
 * @since 2.0.0
 */

(function() {
    'use strict';

    /**
     * Utilitários gerais do tema
     */
    const NosfirNewsUtils = {
        
        /**
         * Debounce - Limita execução de função
         */
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

        /**
         * Throttle - Garante execução máxima a cada intervalo
         */
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

        /**
         * Verificar se elemento está visível no viewport
         */
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

        /**
         * Scroll suave para elemento
         */
        smoothScrollTo(target, duration = 500, offset = 0) {
            const targetElement = typeof target === 'string' 
                ? document.querySelector(target) 
                : target;
            
            if (!targetElement) return;

            const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - offset;
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
        },

        /**
         * Criar elemento DOM com atributos
         */
        createElement(tag, attributes = {}, content = '') {
            const element = document.createElement(tag);
            
            Object.entries(attributes).forEach(([key, value]) => {
                if (key === 'className') {
                    element.className = value;
                } else if (key === 'innerHTML') {
                    element.innerHTML = value;
                } else if (key === 'dataset' && typeof value === 'object') {
                    Object.entries(value).forEach(([dataKey, dataValue]) => {
                        element.dataset[dataKey] = dataValue;
                    });
                } else if (key === 'style' && typeof value === 'object') {
                    Object.entries(value).forEach(([styleKey, styleValue]) => {
                        element.style[styleKey] = styleValue;
                    });
                } else {
                    element.setAttribute(key, value);
                }
            });
            
            if (content) {
                if (typeof content === 'string') {
                    element.textContent = content;
                } else if (content instanceof HTMLElement) {
                    element.appendChild(content);
                }
            }
            
            return element;
        },

        /**
         * Adicionar múltiplos event listeners
         */
        addEventListeners(element, events, handler, options = {}) {
            if (typeof events === 'string') {
                events = events.split(' ');
            }
            events.forEach(event => {
                element.addEventListener(event, handler, options);
            });
        },

        /**
         * Remover múltiplos event listeners
         */
        removeEventListeners(element, events, handler) {
            if (typeof events === 'string') {
                events = events.split(' ');
            }
            events.forEach(event => {
                element.removeEventListener(event, handler);
            });
        },

        /**
         * Formatar data
         */
        formatDate(date, format = 'dd/mm/yyyy') {
            const d = new Date(date);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();
            
            const replacements = {
                'dd': day,
                'mm': month,
                'yyyy': year,
                'yy': String(year).slice(-2)
            };
            
            return format.replace(/dd|mm|yyyy|yy/g, matched => replacements[matched]);
        },

        /**
         * Formatar número
         */
        formatNumber(number, decimals = 0, decimalSeparator = ',', thousandsSeparator = '.') {
            const fixed = Number(number).toFixed(decimals);
            const parts = fixed.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSeparator);
            return parts.join(decimalSeparator);
        },

        /**
         * Truncar texto
         */
        truncate(text, length = 100, suffix = '...') {
            if (text.length <= length) return text;
            return text.substring(0, length).trim() + suffix;
        },

        /**
         * Sanitizar string para uso em HTML
         */
        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },

        /**
         * Parse query string
         */
        parseQueryString(url = window.location.search) {
            const params = new URLSearchParams(url);
            const result = {};
            for (const [key, value] of params) {
                result[key] = value;
            }
            return result;
        },

        /**
         * Atualizar query string
         */
        updateQueryString(params, url = window.location.href) {
            const urlObject = new URL(url);
            Object.entries(params).forEach(([key, value]) => {
                if (value === null || value === undefined) {
                    urlObject.searchParams.delete(key);
                } else {
                    urlObject.searchParams.set(key, value);
                }
            });
            return urlObject.toString();
        },

        /**
         * Copiar texto para clipboard
         */
        async copyToClipboard(text) {
            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(text);
                    return true;
                } else {
                    // Fallback para navegadores antigos
                    const textArea = document.createElement('textarea');
                    textArea.value = text;
                    textArea.style.position = 'fixed';
                    textArea.style.left = '-999999px';
                    document.body.appendChild(textArea);
                    textArea.select();
                    try {
                        document.execCommand('copy');
                        textArea.remove();
                        return true;
                    } catch (error) {
                        textArea.remove();
                        return false;
                    }
                }
            } catch (error) {
                console.error('Erro ao copiar:', error);
                return false;
            }
        },

        /**
         * Detectar tipo de dispositivo
         */
        getDeviceType() {
            const ua = navigator.userAgent;
            if (/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i.test(ua)) {
                return 'tablet';
            }
            if (/Mobile|Android|iP(hone|od)|IEMobile|BlackBerry|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/.test(ua)) {
                return 'mobile';
            }
            return 'desktop';
        },

        /**
         * Verificar se é dispositivo touch
         */
        isTouchDevice() {
            return 'ontouchstart' in window || 
                   navigator.maxTouchPoints > 0 || 
                   navigator.msMaxTouchPoints > 0;
        },

        /**
         * Obter informações do navegador
         */
        getBrowserInfo() {
            const ua = navigator.userAgent;
            let browserName = 'Unknown';
            let browserVersion = 'Unknown';

            if (ua.indexOf('Firefox') > -1) {
                browserName = 'Firefox';
                browserVersion = ua.match(/Firefox\/(\d+)/)?.[1];
            } else if (ua.indexOf('Chrome') > -1) {
                browserName = 'Chrome';
                browserVersion = ua.match(/Chrome\/(\d+)/)?.[1];
            } else if (ua.indexOf('Safari') > -1) {
                browserName = 'Safari';
                browserVersion = ua.match(/Version\/(\d+)/)?.[1];
            } else if (ua.indexOf('Edge') > -1) {
                browserName = 'Edge';
                browserVersion = ua.match(/Edge\/(\d+)/)?.[1];
            }

            return { name: browserName, version: browserVersion };
        },

        /**
         * Verificar suporte a recursos
         */
        supports: {
            serviceWorker: 'serviceWorker' in navigator,
            localStorage: (() => {
                try {
                    localStorage.setItem('test', 'test');
                    localStorage.removeItem('test');
                    return true;
                } catch (e) {
                    return false;
                }
            })(),
            intersectionObserver: 'IntersectionObserver' in window,
            webp: (() => {
                const elem = document.createElement('canvas');
                if (elem.getContext && elem.getContext('2d')) {
                    return elem.toDataURL('image/webp').indexOf('data:image/webp') === 0;
                }
                return false;
            })(),
            webGL: (() => {
                try {
                    const canvas = document.createElement('canvas');
                    return !!(canvas.getContext('webgl') || canvas.getContext('experimental-webgl'));
                } catch (e) {
                    return false;
                }
            })()
        },

        /**
         * Armazenamento local com fallback
         */
        storage: {
            set(key, value, expiresIn = null) {
                if (!NosfirNewsUtils.supports.localStorage) return false;
                
                try {
                    const item = {
                        value: value,
                        timestamp: Date.now(),
                        expiresIn: expiresIn
                    };
                    localStorage.setItem(key, JSON.stringify(item));
                    return true;
                } catch (e) {
                    console.error('Erro ao salvar no localStorage:', e);
                    return false;
                }
            },

            get(key) {
                if (!NosfirNewsUtils.supports.localStorage) return null;
                
                try {
                    const itemStr = localStorage.getItem(key);
                    if (!itemStr) return null;
                    
                    const item = JSON.parse(itemStr);
                    
                    // Verificar expiração
                    if (item.expiresIn) {
                        const age = Date.now() - item.timestamp;
                        if (age > item.expiresIn) {
                            localStorage.removeItem(key);
                            return null;
                        }
                    }
                    
                    return item.value;
                } catch (e) {
                    console.error('Erro ao ler do localStorage:', e);
                    return null;
                }
            },

            remove(key) {
                if (!NosfirNewsUtils.supports.localStorage) return false;
                try {
                    localStorage.removeItem(key);
                    return true;
                } catch (e) {
                    return false;
                }
            },

            clear() {
                if (!NosfirNewsUtils.supports.localStorage) return false;
                try {
                    localStorage.clear();
                    return true;
                } catch (e) {
                    return false;
                }
            }
        },

        /**
         * Ajax helper
         */
        async ajax(url, options = {}) {
            const defaults = {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin'
            };

            const config = { ...defaults, ...options };

            try {
                const response = await fetch(url, config);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return await response.json();
                }
                
                return await response.text();
            } catch (error) {
                console.error('Ajax error:', error);
                throw error;
            }
        },

        /**
         * Carregar script dinamicamente
         */
        loadScript(src, async = true) {
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = src;
                script.async = async;
                script.onload = () => resolve(script);
                script.onerror = () => reject(new Error(`Failed to load script: ${src}`));
                document.head.appendChild(script);
            });
        },

        /**
         * Carregar CSS dinamicamente
         */
        loadCSS(href) {
            return new Promise((resolve, reject) => {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = href;
                link.onload = () => resolve(link);
                link.onerror = () => reject(new Error(`Failed to load CSS: ${href}`));
                document.head.appendChild(link);
            });
        },

        /**
         * Observar mudanças em elemento (MutationObserver)
         */
        observeElement(element, callback, options = {}) {
            const defaultOptions = {
                childList: true,
                subtree: true,
                attributes: true
            };

            const config = { ...defaultOptions, ...options };

            const observer = new MutationObserver(callback);
            observer.observe(element, config);

            return observer;
        },

        /**
         * Wait/Sleep função
         */
        wait(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        },

        /**
         * Retry função com exponential backoff
         */
        async retry(fn, maxAttempts = 3, delay = 1000) {
            for (let attempt = 1; attempt <= maxAttempts; attempt++) {
                try {
                    return await fn();
                } catch (error) {
                    if (attempt === maxAttempts) throw error;
                    await this.wait(delay * Math.pow(2, attempt - 1));
                }
            }
        },

        /**
         * Animar valor numérico
         */
        animateValue(element, start, end, duration = 1000) {
            const range = end - start;
            const startTime = performance.now();

            function updateValue(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
                const eased = progress < 0.5
                    ? 4 * progress * progress * progress
                    : 1 - Math.pow(-2 * progress + 2, 3) / 2;
                
                const current = start + (range * eased);
                element.textContent = Math.round(current);

                if (progress < 1) {
                    requestAnimationFrame(updateValue);
                }
            }

            requestAnimationFrame(updateValue);
        },

        /**
         * Validação de email
         */
        isValidEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        },

        /**
         * Validação de URL
         */
        isValidUrl(url) {
            try {
                new URL(url);
                return true;
            } catch (e) {
                return false;
            }
        },

        /**
         * Gerar ID único
         */
        generateId(prefix = 'id') {
            return `${prefix}-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
        },

        /**
         * Deep clone de objeto
         */
        deepClone(obj) {
            if (obj === null || typeof obj !== 'object') return obj;
            if (obj instanceof Date) return new Date(obj);
            if (obj instanceof Array) return obj.map(item => this.deepClone(item));
            
            const clonedObj = {};
            for (const key in obj) {
                if (obj.hasOwnProperty(key)) {
                    clonedObj[key] = this.deepClone(obj[key]);
                }
            }
            return clonedObj;
        },

        /**
         * Merge profundo de objetos
         */
        deepMerge(...objects) {
            const isObject = obj => obj && typeof obj === 'object';
            
            return objects.reduce((prev, obj) => {
                Object.keys(obj).forEach(key => {
                    const pVal = prev[key];
                    const oVal = obj[key];
                    
                    if (Array.isArray(pVal) && Array.isArray(oVal)) {
                        prev[key] = pVal.concat(...oVal);
                    } else if (isObject(pVal) && isObject(oVal)) {
                        prev[key] = this.deepMerge(pVal, oVal);
                    } else {
                        prev[key] = oVal;
                    }
                });
                return prev;
            }, {});
        },

        /**
         * Emit custom event
         */
        emit(eventName, detail = {}, element = document) {
            const event = new CustomEvent(eventName, {
                detail,
                bubbles: true,
                cancelable: true
            });
            element.dispatchEvent(event);
        }
    };

    // Expor globalmente
    window.NosfirNewsUtils = NosfirNewsUtils;

    // Alias curto
    window.$nn = NosfirNewsUtils;

    // Suporte a módulos
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = NosfirNewsUtils;
    }

})();