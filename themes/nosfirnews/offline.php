<?php
/**
 * Offline Page Template
 * 
 * @package NosfirNews
 * @since 2.0.0
 */

get_header(); ?>

<div class="offline-page">
    <div class="container">
        <div class="offline-content">
            <div class="offline-icon">
                <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M3 12h18m-9-9v18"/>
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M12 1a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                </svg>
            </div>
            
            <h1 class="offline-title"><?php esc_html_e( 'Você está offline', 'nosfirnews' ); ?></h1>
            
            <p class="offline-description">
                <?php esc_html_e( 'Parece que você perdeu a conexão com a internet. Não se preocupe, você ainda pode navegar pelas páginas que visitou recentemente.', 'nosfirnews' ); ?>
            </p>
            
            <div class="offline-actions">
                <button class="btn btn-primary retry-connection" onclick="window.location.reload()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="23 4 23 10 17 10"></polyline>
                        <polyline points="1 20 1 14 7 14"></polyline>
                        <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path>
                    </svg>
                    <?php esc_html_e( 'Tentar Novamente', 'nosfirnews' ); ?>
                </button>
                
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-secondary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9,22 9,12 15,12 15,22"></polyline>
                    </svg>
                    <?php esc_html_e( 'Ir para Início', 'nosfirnews' ); ?>
                </a>
            </div>
            
            <div class="cached-content">
                <h3><?php esc_html_e( 'Conteúdo Disponível Offline', 'nosfirnews' ); ?></h3>
                <div class="cached-posts" id="cached-posts">
                    <!-- Cached posts will be loaded here via JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.offline-page {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.offline-content {
    text-align: center;
    max-width: 600px;
    background: white;
    padding: 60px 40px;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
}

.offline-icon {
    color: #64b5f6;
    margin-bottom: 30px;
    opacity: 0.8;
}

.offline-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
    line-height: 1.2;
}

.offline-description {
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 40px;
    line-height: 1.6;
}

.offline-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-bottom: 50px;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    font-size: 1rem;
}

.btn-primary {
    background: #2196F3;
    color: white;
}

.btn-primary:hover {
    background: #1976D2;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(33, 150, 243, 0.3);
}

.btn-secondary {
    background: #f5f5f5;
    color: #333;
    border: 2px solid #e0e0e0;
}

.btn-secondary:hover {
    background: #e0e0e0;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.cached-content {
    border-top: 1px solid #e0e0e0;
    padding-top: 40px;
    margin-top: 40px;
}

.cached-content h3 {
    font-size: 1.5rem;
    color: #333;
    margin-bottom: 25px;
}

.cached-posts {
    display: grid;
    gap: 15px;
    text-align: left;
}

.cached-post {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #2196F3;
    transition: all 0.3s ease;
}

.cached-post:hover {
    background: #e3f2fd;
    transform: translateX(5px);
}

.cached-post h4 {
    margin: 0 0 8px 0;
    font-size: 1.1rem;
    color: #333;
}

.cached-post p {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
}

.cached-post a {
    color: inherit;
    text-decoration: none;
}

@media (max-width: 768px) {
    .offline-content {
        padding: 40px 20px;
        margin: 20px;
    }
    
    .offline-title {
        font-size: 2rem;
    }
    
    .offline-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .btn {
        width: 100%;
        max-width: 250px;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .offline-title {
        font-size: 1.8rem;
    }
    
    .offline-description {
        font-size: 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load cached posts
    function loadCachedPosts() {
        if ('caches' in window) {
            caches.open('nosfirnews-v2.0.0').then(cache => {
                cache.keys().then(requests => {
                    const postRequests = requests.filter(request => 
                        request.url.includes('/20') &&
                        !request.url.includes('/wp-') &&
                        !request.url.includes('.css') &&
                        !request.url.includes('.js')
                    );
                    
                    const cachedPostsContainer = document.getElementById('cached-posts');
                    
                    if (postRequests.length === 0) {
                        cachedPostsContainer.innerHTML = '<p style="color: #666; font-style: italic;"><?php esc_html_e( 'Nenhum conteúdo offline disponível ainda. Navegue pelo site quando estiver online para ter acesso offline.', 'nosfirnews' ); ?></p>';
                        return;
                    }
                    
                    postRequests.slice(0, 5).forEach(request => {
                        const url = new URL(request.url);
                        const pathParts = url.pathname.split('/').filter(part => part);
                        const title = pathParts[pathParts.length - 1] || '<?php esc_html_e( 'Artigo', 'nosfirnews' ); ?>';
                        
                        const postElement = document.createElement('div');
                        postElement.className = 'cached-post';
                        postElement.innerHTML = `
                            <a href="${request.url}">
                                <h4>${title.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</h4>
                                <p><?php esc_html_e( 'Disponível offline', 'nosfirnews' ); ?></p>
                            </a>
                        `;
                        
                        cachedPostsContainer.appendChild(postElement);
                    });
                });
            }).catch(error => {
                console.log('Error loading cached posts:', error);
                document.getElementById('cached-posts').innerHTML = '<p style="color: #666;"><?php esc_html_e( 'Erro ao carregar conteúdo offline.', 'nosfirnews' ); ?></p>';
            });
        }
    }
    
    loadCachedPosts();
    
    // Auto-retry connection every 30 seconds
    setInterval(() => {
        if (navigator.onLine) {
            window.location.reload();
        }
    }, 30000);
});
</script>

<?php get_footer(); ?>