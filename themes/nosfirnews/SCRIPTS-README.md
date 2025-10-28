# 📚 Documentação dos Scripts - NosfirNews Theme

## 📑 Índice
- [Visão Geral](#visão-geral)
- [Scripts Principais](#scripts-principais)
- [Utilitários](#utilitários)
- [Exemplos de Uso](#exemplos-de-uso)
- [Performance](#performance)
- [Compatibilidade](#compatibilidade)

---

## 🎯 Visão Geral

O tema NosfirNews utiliza JavaScript moderno (ES6+) com foco em **performance**, **acessibilidade** e **experiência do usuário**. Todos os scripts são otimizados e seguem as melhores práticas.

### Arquitetura
```
assets/js/
├── main.js           → Script principal (gerenciador geral)
├── navigation.js     → Sistema de navegação
├── mobile-menu.js    → Menu mobile avançado
├── pwa.js           → PWA Manager
├── utils.js         → Utilitários e helpers
└── admin.js         → Scripts do admin (WordPress)

sw.js                → Service Worker (raiz do tema)
```

---

## 🚀 Scripts Principais

### 1. **main.js** - Gerenciador Principal

Inicializa todos os componentes do tema.

#### Componentes Incluídos:
- ✅ Menu Mobile
- ✅ Smooth Scrolling
- ✅ Back to Top
- ✅ Lazy Loading
- ✅ Reading Progress
- ✅ Accessibility Enhancements

#### Uso:
```javascript
// Acessar o tema
const theme = window.NosfirNews.theme;

// Obter componente específico
const mobileMenu = theme.getComponent('mobileMenu');

// Escutar eventos
document.addEventListener('nosfirnews:initialized', () => {
    console.log('Tema inicializado!');
});
```

---

### 2. **navigation.js** - Sistema de Navegação

Gerencia navegação desktop/mobile, sticky header e dropdowns.

#### Recursos:
- 📌 Sticky Header inteligente
- 🎯 Dropdowns com delay
- ⌨️ Navegação por teclado
- 📱 Detecção de touch devices
- 🔍 Sistema de busca integrado

#### Uso:
```javascript
const nav = window.NosfirNewsNavigation;

// Obter estado
const state = nav.getState();
console.log('Is sticky?', state.isSticky);
console.log('Is mobile?', state.isMobile);

// Scroll to top
nav.scrollToTop();

// Eventos
document.addEventListener('nosfirnews:nav:headerSticky', () => {
    console.log('Header ficou sticky');
});
```

---

### 3. **mobile-menu.js** - Menu Mobile

Menu mobile avançado com animações e gestures.

#### Recursos:
- 📱 Swipe para fechar
- 🔒 Focus trap
- 🎭 Animações suaves
- 📂 Submenus expansíveis
- ⌨️ Navegação por teclado

#### Uso:
```javascript
// Menu é inicializado automaticamente

// Escutar eventos
document.addEventListener('nosfirnews:menuOpened', () => {
    console.log('Menu aberto');
});

document.addEventListener('nosfirnews:menuClosed', () => {
    console.log('Menu fechado');
});
```

---

### 4. **pwa.js** - PWA Manager

Gerencia funcionalidades PWA (Progressive Web App).

#### Recursos:
- 📦 Instalação do app
- 🔄 Detecção de updates
- 🔔 Push notifications
- 📡 Status de conexão
- 💾 Gerenciamento de cache

#### Uso:
```javascript
const pwa = window.NosfirNewsPWA;

// Verificar status
console.log('Instalado?', pwa.isInstalled());
console.log('Online?', pwa.isOnline());

// Solicitar instalação
await pwa.promptInstall();

// Solicitar permissão de notificações
await pwa.requestNotificationPermission();

// Cachear URL específica
await pwa.cacheUrl('/page-important/');

// Limpar cache
await pwa.clearCache();

// Eventos
document.addEventListener('nosfirnews:pwa:appInstalled', () => {
    console.log('App instalado!');
});

document.addEventListener('nosfirnews:pwa:updateAvailable', () => {
    console.log('Update disponível!');
});
```

---

### 5. **sw.js** - Service Worker

Worker que gerencia cache e offline.

#### Estratégias de Cache:

**Cache First** (Assets estáticos):
```javascript
// CSS, JS, Fonts
// Busca primeiro no cache, depois na rede
```

**Network First** (Documentos HTML):
```javascript
// Páginas, posts
// Busca primeiro na rede, fallback no cache
```

**Stale While Revalidate** (Imagens):
```javascript
// Retorna cache e atualiza em background
```

#### Comunicação com SW:
```javascript
// Pular waiting e ativar novo SW
if ('serviceWorker' in navigator) {
    const registration = await navigator.serviceWorker.ready;
    registration.waiting?.postMessage({ type: 'SKIP_WAITING' });
}

// Cachear URLs
registration.active.postMessage({
    type: 'CACHE_URLS',
    urls: ['/page1/', '/page2/']
});

// Limpar cache
registration.active.postMessage({ type: 'CLEAR_CACHE' });
```

---

## 🛠️ Utilitários (utils.js)

### Funções de Performance

```javascript
const utils = window.NosfirNewsUtils; // ou window.$nn

// Debounce
const handleResize = utils.debounce(() => {
    console.log('Resize!');
}, 300);
window.addEventListener('resize', handleResize);

// Throttle
const handleScroll = utils.throttle(() => {
    console.log('Scroll!');
}, 16);
window.addEventListener('scroll', handleScroll);
```

### DOM Manipulation

```javascript
// Criar elemento
const button = utils.createElement('button', {
    className: 'my-button',
    dataset: { action: 'submit' },
    style: { color: 'blue' }
}, 'Click me');

// Verificar viewport
if (utils.isInViewport(element)) {
    element.classList.add('visible');
}

// Smooth scroll
utils.smoothScrollTo('#section', 500, 80);
```

### Storage

```javascript
// Salvar com expiração (1 hora)
utils.storage.set('user-prefs', { theme: 'dark' }, 3600000);

// Recuperar
const prefs = utils.storage.get('user-prefs');

// Remover
utils.storage.remove('user-prefs');
```

### Ajax

```javascript
// GET request
const data = await utils.ajax('/api/posts');

// POST request
const result = await utils.ajax('/api/save', {
    method: 'POST',
    body: JSON.stringify({ title: 'Test' })
});
```

### Validação

```javascript
// Email
if (utils.isValidEmail('user@example.com')) {
    // válido
}

// URL
if (utils.isValidUrl('https://example.com')) {
    // válido
}
```

### Formatação

```javascript
// Data
utils.formatDate(new Date(), 'dd/mm/yyyy'); // "28/10/2025"

// Número
utils.formatNumber(1234567.89, 2); // "1.234.567,89"

// Truncar texto
utils.truncate('Long text...', 10); // "Long text..."
```

### Clipboard

```javascript
// Copiar texto
const success = await utils.copyToClipboard('Hello World');
if (success) {
    alert('Copiado!');
}
```

### Device Detection

```javascript
// Tipo de dispositivo
const device = utils.getDeviceType(); // 'desktop', 'tablet', 'mobile'

// É touch?
if (utils.isTouchDevice()) {
    // habilitar features touch
}

// Browser info
const browser = utils.getBrowserInfo();
console.log(browser.name, browser.version);
```

### Feature Detection

```javascript
if (utils.supports.serviceWorker) {
    // registrar SW
}

if (utils.supports.webp) {
    // usar imagens WebP
}

if (utils.supports.localStorage) {
    // usar localStorage
}
```

### Outros Helpers

```javascript
// Carregar script
await utils.loadScript('https://cdn.example.com/lib.js');

// Carregar CSS
await utils.loadCSS('https://cdn.example.com/style.css');

// Wait/Sleep
await utils.wait(1000); // aguarda 1 segundo

// Retry com backoff
const data = await utils.retry(() => fetchData(), 3, 1000);

// Animar valor
utils.animateValue(element, 0, 100, 2000);

// Gerar ID único
const id = utils.generateId('user'); // "user-1698501234567-abc123def"

// Deep clone
const clone = utils.deepClone(complexObject);

// Deep merge
const merged = utils.deepMerge(obj1, obj2, obj3);

// Emit evento
utils.emit('custom:event', { data: 'value' });
```

---

## 📊 Exemplos de Uso

### Exemplo 1: Lazy Loading Customizado

```javascript
document.addEventListener('DOMContentLoaded', () => {
    const images = document.querySelectorAll('img[data-src]');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });
    
    images.forEach(img => observer.observe(img));
});
```

### Exemplo 2: Notificação Customizada

```javascript
// Criar notificação toast
function showToast(message, type = 'info', duration = 3000) {
    const toast = NosfirNewsUtils.createElement('div', {
        className: `toast toast-${type}`,
        innerHTML: `
            <div class="toast-content">
                <span class="toast-icon">${getIcon(type)}</span>
                <span class="toast-message">${message}</span>
            </div>
        `
    });
    
    document.body.appendChild(toast);
    
    // Animar entrada
    requestAnimationFrame(() => {
        toast.classList.add('visible');
    });
    
    // Auto-remover
    setTimeout(() => {
        toast.classList.remove('visible');
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

function getIcon(type) {
    const icons = {
        success: '✓',
        error: '✗',
        warning: '⚠',
        info: 'ℹ'
    };
    return icons[type] || icons.info;
}

// Uso
showToast('Post salvo com sucesso!', 'success');
showToast('Erro ao carregar dados', 'error');
```

### Exemplo 3: Infinite Scroll

```javascript
class InfiniteScroll {
    constructor(container, loadMoreCallback) {
        this.container = container;
        this.loadMore = loadMoreCallback;
        this.page = 1;
        this.loading = false;
        this.hasMore = true;
        
        this.init();
    }
    
    init() {
        this.sentinel = document.createElement('div');
        this.sentinel.className = 'infinite-scroll-sentinel';
        this.container.appendChild(this.sentinel);
        
        this.observer = new IntersectionObserver(
            entries => this.handleIntersect(entries),
            { rootMargin: '200px' }
        );
        
        this.observer.observe(this.sentinel);
    }
    
    async handleIntersect(entries) {
        if (entries[0].isIntersecting && !this.loading && this.hasMore) {
            this.loading = true;
            
            try {
                this.page++;
                const items = await this.loadMore(this.page);
                
                if (items.length === 0) {
                    this.hasMore = false;
                    this.destroy();
                }
            } catch (error) {
                console.error('Erro no infinite scroll:', error);
            } finally {
                this.loading = false;
            }
        }
    }
    
    destroy() {
        this.observer.disconnect();
        this.sentinel.remove();
    }
}

// Uso
const infiniteScroll = new InfiniteScroll(
    document.querySelector('.posts-grid'),
    async (page) => {
        const response = await fetch(`/wp-json/wp/v2/posts?page=${page}`);
        const posts = await response.json();
        
        posts.forEach(post => {
            const element = createPostElement(post);
            document.querySelector('.posts-grid').appendChild(element);
        });
        
        return posts;
    }
);
```

### Exemplo 4: Form Validation

```javascript
class FormValidator {
    constructor(form) {
        this.form = form;
        this.errors = new Map();
        this.init();
    }
    
    init() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        
        // Validação em tempo real
        const inputs = this.form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
        });
    }
    
    async handleSubmit(e) {
        e.preventDefault();
        
        this.errors.clear();
        const inputs = this.form.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => this.validateField(input));
        
        if (this.errors.size === 0) {
            await this.submitForm();
        }
    }
    
    validateField(field) {
        const value = field.value.trim();
        const rules = this.getRules(field);
        
        for (const rule of rules) {
            const error = this.applyRule(field, value, rule);
            if (error) {
                this.showError(field, error);
                this.errors.set(field.name, error);
                return;
            }
        }
        
        this.clearError(field);
        this.errors.delete(field.name);
    }
    
    getRules(field) {
        const rules = [];
        
        if (field.required) {
            rules.push({ type: 'required' });
        }
        
        if (field.type === 'email') {
            rules.push({ type: 'email' });
        }
        
        if (field.minLength) {
            rules.push({ type: 'minLength', value: field.minLength });
        }
        
        return rules;
    }
    
    applyRule(field, value, rule) {
        switch (rule.type) {
            case 'required':
                return !value ? 'Este campo é obrigatório' : null;
            
            case 'email':
                return !NosfirNewsUtils.isValidEmail(value) 
                    ? 'Email inválido' 
                    : null;
            
            case 'minLength':
                return value.length < rule.value 
                    ? `Mínimo ${rule.value} caracteres` 
                    : null;
            
            default:
                return null;
        }
    }
    
    showError(field, message) {
        field.classList.add('error');
        
        let errorElement = field.parentElement.querySelector('.error-message');
        if (!errorElement) {
            errorElement = document.createElement('span');
            errorElement.className = 'error-message';
            field.parentElement.appendChild(errorElement);
        }
        
        errorElement.textContent = message;
    }
    
    clearError(field) {
        field.classList.remove('error');
        const errorElement = field.parentElement.querySelector('.error-message');
        if (errorElement) {
            errorElement.remove();
        }
    }
    
    async submitForm() {
        const formData = new FormData(this.form);
        
        try {
            const response = await fetch(this.form.action, {
                method: 'POST',
                body: formData
            });
            
            if (response.ok) {
                showToast('Formulário enviado com sucesso!', 'success');
                this.form.reset();
            }
        } catch (error) {
            showToast('Erro ao enviar formulário', 'error');
        }
    }
}

// Uso
document.querySelectorAll('form.validate').forEach(form => {
    new FormValidator(form);
});
```

### Exemplo 5: Modal System

```javascript
class Modal {
    constructor(options = {}) {
        this.options = {
            title: options.title || '',
            content: options.content || '',
            closeOnOverlay: options.closeOnOverlay !== false,
            closeOnEscape: options.closeOnEscape !== false,
            showCloseButton: options.showCloseButton !== false,
            onOpen: options.onOpen || null,
            onClose: options.onClose || null,
            ...options
        };
        
        this.isOpen = false;
        this.create();
    }
    
    create() {
        // Overlay
        this.overlay = NosfirNewsUtils.createElement('div', {
            className: 'modal-overlay'
        });
        
        // Modal
        this.modal = NosfirNewsUtils.createElement('div', {
            className: 'modal',
            'aria-modal': 'true',
            'role': 'dialog'
        });
        
        // Header
        const header = NosfirNewsUtils.createElement('div', {
            className: 'modal-header'
        });
        
        const title = NosfirNewsUtils.createElement('h2', {
            className: 'modal-title'
        }, this.options.title);
        
        header.appendChild(title);
        
        if (this.options.showCloseButton) {
            const closeBtn = NosfirNewsUtils.createElement('button', {
                className: 'modal-close',
                'aria-label': 'Fechar modal',
                innerHTML: '×'
            });
            closeBtn.addEventListener('click', () => this.close());
            header.appendChild(closeBtn);
        }
        
        // Body
        const body = NosfirNewsUtils.createElement('div', {
            className: 'modal-body'
        });
        
        if (typeof this.options.content === 'string') {
            body.innerHTML = this.options.content;
        } else {
            body.appendChild(this.options.content);
        }
        
        // Footer (opcional)
        if (this.options.footer) {
            const footer = NosfirNewsUtils.createElement('div', {
                className: 'modal-footer'
            });
            footer.appendChild(this.options.footer);
            this.modal.appendChild(footer);
        }
        
        // Montar modal
        this.modal.appendChild(header);
        this.modal.appendChild(body);
        
        // Eventos
        if (this.options.closeOnOverlay) {
            this.overlay.addEventListener('click', () => this.close());
        }
        
        if (this.options.closeOnEscape) {
            this.escapeHandler = (e) => {
                if (e.key === 'Escape' && this.isOpen) {
                    this.close();
                }
            };
            document.addEventListener('keydown', this.escapeHandler);
        }
    }
    
    open() {
        if (this.isOpen) return;
        
        document.body.appendChild(this.overlay);
        document.body.appendChild(this.modal);
        
        requestAnimationFrame(() => {
            this.overlay.classList.add('active');
            this.modal.classList.add('active');
        });
        
        document.body.style.overflow = 'hidden';
        this.isOpen = true;
        
        // Focus trap
        this.trapFocus();
        
        if (this.options.onOpen) {
            this.options.onOpen();
        }
    }
    
    close() {
        if (!this.isOpen) return;
        
        this.overlay.classList.remove('active');
        this.modal.classList.remove('active');
        
        setTimeout(() => {
            this.overlay.remove();
            this.modal.remove();
        }, 300);
        
        document.body.style.overflow = '';
        this.isOpen = false;
        
        if (this.options.onClose) {
            this.options.onClose();
        }
    }
    
    trapFocus() {
        const focusableElements = this.modal.querySelectorAll(
            'a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])'
        );
        
        if (focusableElements.length === 0) return;
        
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];
        
        firstElement.focus();
        
        this.tabHandler = (e) => {
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
        
        document.addEventListener('keydown', this.tabHandler);
    }
    
    destroy() {
        if (this.isOpen) this.close();
        if (this.escapeHandler) {
            document.removeEventListener('keydown', this.escapeHandler);
        }
        if (this.tabHandler) {
            document.removeEventListener('keydown', this.tabHandler);
        }
    }
}

// Uso
const modal = new Modal({
    title: 'Confirmar Ação',
    content: '<p>Tem certeza que deseja continuar?</p>',
    footer: (() => {
        const footer = document.createElement('div');
        footer.innerHTML = `
            <button class="btn btn-secondary" data-action="cancel">Cancelar</button>
            <button class="btn btn-primary" data-action="confirm">Confirmar</button>
        `;
        
        footer.querySelector('[data-action="cancel"]').addEventListener('click', () => {
            modal.close();
        });
        
        footer.querySelector('[data-action="confirm"]').addEventListener('click', () => {
            console.log('Confirmado!');
            modal.close();
        });
        
        return footer;
    })(),
    onOpen: () => console.log('Modal aberto'),
    onClose: () => console.log('Modal fechado')
});

modal.open();
```

---

## ⚡ Performance

### Otimizações Implementadas

1. **Debounce e Throttle**
   - Eventos de scroll/resize são throttled
   - Input events são debounced
   - Reduz execuções desnecessárias

2. **Intersection Observer**
   - Lazy loading de imagens
   - Infinite scroll
   - Animações on-scroll
   - Melhor performance que scroll events

3. **RequestAnimationFrame**
   - Todas as animações usam RAF
   - Sincronizado com refresh rate
   - Evita layout thrashing

4. **Event Delegation**
   - Um listener para múltiplos elementos
   - Melhor performance em listas grandes
   - Funciona com elementos dinâmicos

5. **Passive Event Listeners**
   ```javascript
   window.addEventListener('scroll', handler, { passive: true });
   ```

6. **Code Splitting**
   - Scripts carregados sob demanda
   - Reduz bundle inicial
   - Faster first paint

### Medindo Performance

```javascript
// Performance API
const start = performance.now();
// ... código ...
const end = performance.now();
console.log(`Executado em ${end - start}ms`);

// Performance Observer
const observer = new PerformanceObserver((list) => {
    for (const entry of list.getEntries()) {
        console.log(entry.name, entry.duration);
    }
});

observer.observe({ entryTypes: ['measure', 'navigation'] });
```

---

## 🌐 Compatibilidade

### Navegadores Suportados

| Navegador | Versão Mínima |
|-----------|---------------|
| Chrome    | 90+           |
| Firefox   | 88+           |
| Safari    | 14+           |
| Edge      | 90+           |

### Polyfills Incluídos

Não é necessário adicionar polyfills. Os scripts usam apenas APIs modernas suportadas pelos navegadores listados.

### Fallbacks

```javascript
// Intersection Observer fallback
if (!('IntersectionObserver' in window)) {
    // Usa scroll events
}

// Service Worker fallback
if (!('serviceWorker' in navigator)) {
    console.warn('PWA features não disponíveis');
}

// LocalStorage fallback
if (!NosfirNewsUtils.supports.localStorage) {
    // Usa memória ou cookies
}
```

---

## 🐛 Debug

### Ativar Logs de Debug

```javascript
// No console do navegador
localStorage.setItem('nosfirnews_debug', 'true');

// Recarregar página
location.reload();
```

### Verificar Estado

```javascript
// Theme
console.log(window.NosfirNews.theme.getState());

// Navigation
console.log(window.NosfirNewsNavigation.getState());

// PWA
console.log(window.NosfirNewsPWA.getState());
```

### Performance Monitoring

```javascript
// Tempo de carregamento
window.addEventListener('load', () => {
    const perfData = performance.getEntriesByType('navigation')[0];
    console.log('Load time:', perfData.loadEventEnd - perfData.fetchStart, 'ms');
});

// Cache size
if ('storage' in navigator && 'estimate' in navigator.storage) {
    navigator.storage.estimate().then(estimate => {
        console.log('Cache usage:', estimate.usage);
        console.log('Cache quota:', estimate.quota);
    });
}
```

---

## 📝 Notas Importantes

### ❗ Não usar localStorage/sessionStorage em Artifacts

Se estiver testando em Claude.ai, **não use** `localStorage` ou `sessionStorage` diretamente. Use o sistema de storage do tema:

```javascript
// ✅ Correto
NosfirNewsUtils.storage.set('key', 'value');

// ❌ Errado (não funciona em artifacts)
localStorage.setItem('key', 'value');
```

### ❗ Async/Await Support

Todos os scripts assumem suporte nativo a async/await. Se precisar suportar navegadores muito antigos, adicione Babel ao build.

### ❗ ES Modules

Os scripts usam IIFE (Immediately Invoked Function Expression) para compatibilidade. Para usar ES modules:

```javascript
// Adicionar type="module" ao script
<script type="module" src="main.js"></script>

// Exportar/importar
export { MyClass };
import { MyClass } from './main.js';
```

---

## 🎓 Recursos Adicionais

- [MDN Web Docs](https://developer.mozilla.org/)
- [Web.dev](https://web.dev/)
- [Can I Use](https://caniuse.com/)
- [Intersection Observer API](https://developer.mozilla.org/en-US/docs/Web/API/Intersection_Observer_API)
- [Service Worker API](https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API)

---

## 📄 Licença

GPL v3 or later - Veja LICENSE para detalhes.

---

## 👨‍💻 Desenvolvedor

**David L. Almeida**
- Email: contato@davidalmeida.xyz
- GitHub: [@davidcreator](https://github.com/davidcreator)

---

**Última atualização:** Outubro 2025