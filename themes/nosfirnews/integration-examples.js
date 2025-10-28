/**
 * NosfirNews - Exemplos de Integração
 * Demonstra como integrar e estender as funcionalidades do tema
 * @package NosfirNews
 * @since 2.0.0
 */

// ============================================================================
// EXEMPLO 1: Sistema de Favoritos
// ============================================================================

class FavoritesSystem {
    constructor() {
        this.storageKey = 'nosfirnews_favorites';
        this.favorites = this.load();
        this.init();
    }

    init() {
        this.renderFavoriteButtons();
        this.bindEvents();
    }

    load() {
        return NosfirNewsUtils.storage.get(this.storageKey) || [];
    }

    save() {
        NosfirNewsUtils.storage.set(this.storageKey, this.favorites);
    }

    add(postId) {
        if (!this.isFavorite(postId)) {
            this.favorites.push(postId);
            this.save();
            this.updateButton(postId, true);
            NosfirNewsUtils.emit('favorite:added', { postId });
        }
    }

    remove(postId) {
        const index = this.favorites.indexOf(postId);
        if (index > -1) {
            this.favorites.splice(index, 1);
            this.save();
            this.updateButton(postId, false);
            NosfirNewsUtils.emit('favorite:removed', { postId });
        }
    }

    toggle(postId) {
        this.isFavorite(postId) ? this.remove(postId) : this.add(postId);
    }

    isFavorite(postId) {
        return this.favorites.includes(postId);
    }

    renderFavoriteButtons() {
        const posts = document.querySelectorAll('.post, .entry');
        
        posts.forEach(post => {
            const postId = post.dataset.postId;
            if (!postId) return;

            const button = NosfirNewsUtils.createElement('button', {
                className: 'favorite-button',
                dataset: { postId },
                'aria-label': 'Adicionar aos favoritos',
                innerHTML: this.getButtonIcon(postId)
            });

            const header = post.querySelector('.entry-header, .post-header');
            if (header) {
                header.appendChild(button);
            }
        });
    }

    getButtonIcon(postId) {
        const isFav = this.isFavorite(postId);
        return isFav 
            ? '<svg class="icon-heart filled" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>'
            : '<svg class="icon-heart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>';
    }

    updateButton(postId, isFavorite) {
        const button = document.querySelector(`.favorite-button[data-post-id="${postId}"]`);
        if (button) {
            button.innerHTML = this.getButtonIcon(postId);
            button.classList.toggle('active', isFavorite);
        }
    }

    bindEvents() {
        document.addEventListener('click', (e) => {
            const button = e.target.closest('.favorite-button');
            if (button) {
                e.preventDefault();
                const postId = button.dataset.postId;
                this.toggle(postId);
            }
        });
    }

    getFavorites() {
        return [...this.favorites];
    }

    getCount() {
        return this.favorites.length;
    }
}

// Uso
const favorites = new FavoritesSystem();


// ============================================================================
// EXEMPLO 2: Sistema de Compartilhamento
// ============================================================================

class SocialShare {
    constructor() {
        this.networks = {
            facebook: {
                url: 'https://www.facebook.com/sharer/sharer.php?u={url}',
                icon: '<svg viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>'
            },
            twitter: {
                url: 'https://twitter.com/intent/tweet?url={url}&text={title}',
                icon: '<svg viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>'
            },
            linkedin: {
                url: 'https://www.linkedin.com/sharing/share-offsite/?url={url}',
                icon: '<svg viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>'
            },
            whatsapp: {
                url: 'https://wa.me/?text={title}%20{url}',
                icon: '<svg viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>'
            },
            email: {
                url: 'mailto:?subject={title}&body={url}',
                icon: '<svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>'
            }
        };
        
        this.init();
    }

    init() {
        this.injectShareButtons();
        this.bindEvents();
    }

    injectShareButtons() {
        const posts = document.querySelectorAll('.post, .entry');
        
        posts.forEach(post => {
            const footer = post.querySelector('.entry-footer, .post-footer');
            if (!footer || footer.querySelector('.share-buttons')) return;

            const shareContainer = NosfirNewsUtils.createElement('div', {
                className: 'share-buttons'
            });

            const title = post.querySelector('.entry-title, .post-title')?.textContent || '';
            const url = window.location.href;

            Object.entries(this.networks).forEach(([network, data]) => {
                const button = this.createShareButton(network, data, title, url);
                shareContainer.appendChild(button);
            });

            footer.appendChild(shareContainer);
        });
    }

    createShareButton(network, data, title, url) {
        const shareUrl = data.url
            .replace('{url}', encodeURIComponent(url))
            .replace('{title}', encodeURIComponent(title));

        const button = NosfirNewsUtils.createElement('button', {
            className: `share-button share-${network}`,
            dataset: { 
                network,
                url: shareUrl 
            },
            'aria-label': `Compartilhar no ${network}`,
            innerHTML: data.icon
        });

        return button;
    }

    bindEvents() {
        document.addEventListener('click', (e) => {
            const button = e.target.closest('.share-button');
            if (!button) return;

            e.preventDefault();
            this.share(button.dataset.network, button.dataset.url);
        });
    }

    share(network, url) {
        if (network === 'email') {
            window.location.href = url;
        } else {
            this.openPopup(url, network);
        }

        NosfirNewsUtils.emit('social:share', { network });
    }

    openPopup(url, network) {
        const width = 600;
        const height = 400;
        const left = (screen.width - width) / 2;
        const top = (screen.height - height) / 2;

        window.open(
            url,
            `share-${network}`,
            `width=${width},height=${height},left=${left},top=${top},toolbar=no,menubar=no`
        );
    }

    async copyLink() {
        const success = await NosfirNewsUtils.copyToClipboard(window.location.href);
        if (success) {
            alert('Link copiado!');
        }
    }
}

// Uso
const socialShare = new SocialShare();


// ============================================================================
// EXEMPLO 3: Sistema de Busca com AJAX
// ============================================================================

class AjaxSearch {
    constructor(formSelector) {
        this.form = document.querySelector(formSelector);
        if (!this.form) return;

        this.input = this.form.querySelector('input[type="search"]');
        this.resultsContainer = this.createResultsContainer();
        this.currentQuery = '';
        this.abortController = null;

        this.init();
    }

    init() {
        this.bindEvents();
    }

    createResultsContainer() {
        const container = NosfirNewsUtils.createElement('div', {
            className: 'search-results-dropdown',
            'aria-live': 'polite'
        });

        this.form.appendChild(container);
        return container;
    }

    bindEvents() {
        // Debounce no input
        const debouncedSearch = NosfirNewsUtils.debounce(
            (e) => this.handleInput(e),
            300
        );

        this.input.addEventListener('input', debouncedSearch);

        // Fechar ao clicar fora
        document.addEventListener('click', (e) => {
            if (!this.form.contains(e.target)) {
                this.hideResults();
            }
        });

        // Navegação por teclado
        this.input.addEventListener('keydown', (e) => this.handleKeyboard(e));
    }

    async handleInput(e) {
        const query = e.target.value.trim();

        if (query.length < 3) {
            this.hideResults();
            return;
        }

        if (query === this.currentQuery) return;
        this.currentQuery = query;

        await this.search(query);
    }

    async search(query) {
        // Cancelar requisição anterior
        if (this.abortController) {
            this.abortController.abort();
        }

        this.abortController = new AbortController();

        try {
            this.showLoading();

            const response = await fetch(
                `/wp-json/wp/v2/posts?search=${encodeURIComponent(query)}&per_page=5`,
                { signal: this.abortController.signal }
            );

            if (!response.ok) throw new Error('Search failed');

            const posts = await response.json();
            this.renderResults(posts, query);

        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Search error:', error);
                this.showError();
            }
        }
    }

    showLoading() {
        this.resultsContainer.innerHTML = '<div class="search-loading">Buscando...</div>';
        this.resultsContainer.classList.add('active');
    }

    renderResults(posts, query) {
        if (posts.length === 0) {
            this.resultsContainer.innerHTML = `
                <div class="search-no-results">
                    Nenhum resultado para "${NosfirNewsUtils.escapeHtml(query)}"
                </div>
            `;
            return;
        }

        const resultsHTML = posts.map(post => `
            <a href="${post.link}" class="search-result-item">
                <h4>${this.highlightQuery(post.title.rendered, query)}</h4>
                <p>${this.truncateExcerpt(post.excerpt.rendered)}</p>
            </a>
        `).join('');

        this.resultsContainer.innerHTML = resultsHTML;
        this.resultsContainer.classList.add('active');
    }

    highlightQuery(text, query) {
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<mark>$1</mark>');
    }

    truncateExcerpt(html) {
        const temp = document.createElement('div');
        temp.innerHTML = html;
        const text = temp.textContent || temp.innerText || '';
        return NosfirNewsUtils.truncate(text, 100);
    }

    showError() {
        this.resultsContainer.innerHTML = `
            <div class="search-error">
                Erro ao buscar. Tente novamente.
            </div>
        `;
    }

    hideResults() {
        this.resultsContainer.classList.remove('active');
        this.currentQuery = '';
    }

    handleKeyboard(e) {
        const items = this.resultsContainer.querySelectorAll('.search-result-item');
        if (items.length === 0) return;

        const currentIndex = Array.from(items).findIndex(
            item => item === document.activeElement
        );

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                if (currentIndex < items.length - 1) {
                    items[currentIndex + 1].focus();
                } else {
                    items[0].focus();
                }
                break;

            case 'ArrowUp':
                e.preventDefault();
                if (currentIndex > 0) {
                    items[currentIndex - 1].focus();
                } else {
                    items[items.length - 1].focus();
                }
                break;

            case 'Escape':
                this.hideResults();
                this.input.blur();
                break;
        }
    }
}

// Uso
const ajaxSearch = new AjaxSearch('.search-form');


// ============================================================================
// EXEMPLO 4: Sistema de Comentários em Tempo Real
// ============================================================================

class LiveComments {
    constructor(postId) {
        this.postId = postId;
        this.commentsList = document.querySelector('.comment-list');
        this.commentForm = document.querySelector('#commentform');
        
        if (!this.commentsList || !this.commentForm) return;

        this.init();
    }

    init() {
        this.enhanceForm();
        this.startPolling();
    }

    enhanceForm() {
        this.commentForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.submitComment(e.target);
        });

        // Preview do comentário
        const textarea = this.commentForm.querySelector('textarea');
        if (textarea) {
            const preview = NosfirNewsUtils.createElement('div', {
                className: 'comment-preview',
                innerHTML: '<h4>Preview:</h4><div class="preview-content"></div>'
            });

            textarea.parentNode.appendChild(preview);

            const debouncedPreview = NosfirNewsUtils.debounce(() => {
                this.updatePreview(textarea.value, preview);
            }, 300);

            textarea.addEventListener('input', debouncedPreview);
        }
    }

    updatePreview(content, container) {
        const previewContent = container.querySelector('.preview-content');
        if (!previewContent) return;

        if (!content.trim()) {
            container.style.display = 'none';
            return;
        }

        container.style.display = 'block';
        previewContent.innerHTML = this.formatComment(content);
    }

    formatComment(content) {
        // Sanitizar e formatar
        return NosfirNewsUtils.escapeHtml(content)
            .replace(/\n/g, '<br>')
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>');
    }

    async submitComment(form) {
        const formData = new FormData(form);
        const submitButton = form.querySelector('[type="submit"]');

        try {
            submitButton.disabled = true;
            submitButton.textContent = 'Enviando...';

            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });

            if (!response.ok) throw new Error('Submit failed');

            const result = await response.text();
            
            // Extrair novo comentário do HTML retornado
            this.addNewComment(result);
            form.reset();

            alert('Comentário enviado com sucesso!');

        } catch (error) {
            console.error('Comment submission error:', error);
            alert('Erro ao enviar comentário');
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'Enviar Comentário';
        }
    }

    addNewComment(html) {
        const temp = document.createElement('div');
        temp.innerHTML = html;
        
        const newComment = temp.querySelector('.comment');
        if (newComment) {
            newComment.style.opacity = '0';
            this.commentsList.insertBefore(newComment, this.commentsList.firstChild);
            
            // Animar entrada
            requestAnimationFrame(() => {
                newComment.style.transition = 'opacity 0.5s ease';
                newComment.style.opacity = '1';
            });
        }
    }

    startPolling() {
        // Verificar novos comentários a cada 30 segundos
        setInterval(() => this.checkNewComments(), 30000);
    }

    async checkNewComments() {
        try {
            const response = await fetch(
                `/wp-json/wp/v2/comments?post=${this.postId}&per_page=5&order=desc`
            );

            if (!response.ok) return;

            const comments = await response.json();
            
            // Verificar se há comentários novos
            const existingIds = Array.from(this.commentsList.querySelectorAll('.comment'))
                .map(c => c.id);

            comments.forEach(comment => {
                if (!existingIds.includes(`comment-${comment.id}`)) {
                    this.renderComment(comment);
                }
            });

        } catch (error) {
            console.error('Error checking new comments:', error);
        }
    }

    renderComment(comment) {
        const commentElement = NosfirNewsUtils.createElement('div', {
            className: 'comment new-comment',
            id: `comment-${comment.id}`,
            innerHTML: `
                <div class="comment-author">
                    <img src="${comment.author_avatar_urls[48]}" alt="${comment.author_name}">
                    <cite>${comment.author_name}</cite>
                </div>
                <div class="comment-content">
                    ${comment.content.rendered}
                </div>
                <div class="comment-meta">
                    <time>${this.formatDate(comment.date)}</time>
                </div>
            `
        });

        this.commentsList.insertBefore(commentElement, this.commentsList.firstChild);
        
        // Notificar
        this.showNotification('Novo comentário adicionado!');
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000);

        if (diff < 60) return 'agora mesmo';
        if (diff < 3600) return `${Math.floor(diff / 60)} minutos atrás`;
        if (diff < 86400) return `${Math.floor(diff / 3600)} horas atrás`;
        
        return NosfirNewsUtils.formatDate(date);
    }

    showNotification(message) {
        const notification = NosfirNewsUtils.createElement('div', {
            className: 'comment-notification',
            innerHTML: message
        });

        document.body.appendChild(notification);

        requestAnimationFrame(() => {
            notification.classList.add('visible');
        });

        setTimeout(() => {
            notification.classList.remove('visible');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
}

// Uso
if (document.body.classList.contains('single')) {
    const postId = document.querySelector('.post')?.dataset.postId;
    if (postId) {
        const liveComments = new LiveComments(postId);
    }
}


// ============================================================================
// EXEMPLO 5: Sistema de Dark Mode
// ============================================================================

class DarkModeToggle {
    constructor() {
        this.storageKey = 'nosfirnews_dark_mode';
        this.isDark = this.loadPreference();
        
        this.init();
    }

    init() {
        this.createToggleButton();
        this.applyTheme();
        this.detectSystemPreference();
    }

    loadPreference() {
        const saved = NosfirNewsUtils.storage.get(this.storageKey);
        if (saved !== null) return saved === 'true';

        // Detectar preferência do sistema
        return window.matchMedia && 
               window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    savePreference() {
        NosfirNewsUtils.storage.set(this.storageKey, String(this.isDark));
    }

    createToggleButton() {
        const button = NosfirNewsUtils.createElement('button', {
            className: 'dark-mode-toggle',
            'aria-label': 'Alternar tema',
            'aria-pressed': String(this.isDark),
            innerHTML: this.getIcon()
        });

        button.addEventListener('click', () => this.toggle());

        // Adicionar ao header
        const header = document.querySelector('.site-header .container');
        if (header) {
            header.appendChild(button);
        }

        this.button = button;
    }

    getIcon() {
        return this.isDark
            ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>'
            : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>';
    }

    toggle() {
        this.isDark = !this.isDark;
        this.applyTheme();
        this.savePreference();
        
        if (this.button) {
            this.button.innerHTML = this.getIcon();
            this.button.setAttribute('aria-pressed', String(this.isDark));
        }

        NosfirNewsUtils.emit('theme:changed', { isDark: this.isDark });
    }

    applyTheme() {
        document.documentElement.classList.toggle('dark-mode', this.isDark);
        
        // Atualizar meta theme-color
        let metaTheme = document.querySelector('meta[name="theme-color"]');
        if (!metaTheme) {
            metaTheme = document.createElement('meta');
            metaTheme.name = 'theme-color';
            document.head.appendChild(metaTheme);
        }
        metaTheme.content = this.isDark ? '#1a1a1a' : '#ffffff';
    }

    detectSystemPreference() {
        if (window.matchMedia) {
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            mediaQuery.addEventListener('change', (e) => {
                if (NosfirNewsUtils.storage.get(this.storageKey) === null) {
                    this.isDark = e.matches;
                    this.applyTheme();
                }
            });
        }
    }
}

// Uso
const darkMode = new DarkModeToggle();


// ============================================================================
// INTEGRAÇÃO COM WORDPRESS
// ============================================================================

// Adicionar nonce do WordPress aos requests AJAX
if (window.nosfirnews_ajax) {
    const originalAjax = NosfirNewsUtils.ajax;
    NosfirNewsUtils.ajax = function(url, options = {}) {
        if (!options.headers) options.headers = {};
        options.headers['X-WP-Nonce'] = window.nosfirnews_ajax.nonce;
        return originalAjax(url, options);
    };
}

// Expor para uso global
window.NosfirNewsExamples = {
    FavoritesSystem,
    SocialShare,
    AjaxSearch,
    LiveComments,
    DarkModeToggle
};

console.log('NosfirNews Integration Examples loaded');