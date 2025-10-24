<?php
/**
 * Template Name: Blog Home
 * Description: Template para a página principal do blog com grid de posts
 * 
 * @package NosfirNews
 * @since 1.0.0
 */

get_header(); ?>

<style>
/* Estilos para o template home.php */
.blog-home-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    background: var(--bg-color, #ffffff);
    color: var(--text-color, #333333);
}

.blog-header {
    text-align: center;
    margin-bottom: 40px;
    padding: 40px 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.blog-header h1 {
    font-size: 3rem;
    margin: 0 0 10px 0;
    font-weight: 700;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.blog-header .description {
    font-size: 1.2rem;
    opacity: 0.9;
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
}

.blog-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
    gap: 15px;
}

.blog-filters {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 8px 16px;
    border: 2px solid #667eea;
    background: transparent;
    color: #667eea;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
    text-decoration: none;
}

.filter-btn:hover,
.filter-btn.active {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.view-controls {
    display: flex;
    gap: 10px;
}

.view-btn {
    padding: 10px;
    border: 2px solid #ddd;
    background: white;
    cursor: pointer;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.view-btn:hover,
.view-btn.active {
    border-color: #667eea;
    background: #667eea;
    color: white;
}

.posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
}

.posts-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-bottom: 40px;
}

.post-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
}

.post-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.post-thumbnail {
    position: relative;
    overflow: hidden;
    height: 200px;
}

.post-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.post-card:hover .post-thumbnail img {
    transform: scale(1.05);
}

.post-category {
    position: absolute;
    top: 15px;
    left: 15px;
    background: #667eea;
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    z-index: 2;
}

.post-content {
    padding: 25px;
}

.post-meta {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
    font-size: 0.9rem;
    color: #666;
}

.post-meta .author {
    display: flex;
    align-items: center;
    gap: 8px;
}

.post-meta .author img {
    width: 24px;
    height: 24px;
    border-radius: 50%;
}

.post-title {
    margin: 0 0 15px 0;
    font-size: 1.4rem;
    line-height: 1.4;
    font-weight: 700;
}

.post-title a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.post-title a:hover {
    color: #667eea;
}

.post-excerpt {
    color: #666;
    line-height: 1.6;
    margin-bottom: 20px;
}

.post-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 20px;
    border-top: 1px solid #f0f0f0;
}

.read-more {
    background: #667eea;
    color: white;
    padding: 10px 20px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.read-more:hover {
    background: #5a6fd8;
    transform: translateX(5px);
}

.post-tags {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.post-tag {
    background: #f8f9fa;
    color: #666;
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 0.8rem;
    text-decoration: none;
    transition: all 0.3s ease;
}

.post-tag:hover {
    background: #667eea;
    color: white;
}

/* Layout de Lista */
.posts-list .post-card {
    display: flex;
    align-items: center;
    height: auto;
}

.posts-list .post-thumbnail {
    width: 250px;
    height: 150px;
    flex-shrink: 0;
}

.posts-list .post-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin: 40px 0;
}

.pagination {
    display: flex;
    gap: 10px;
    align-items: center;
}

.pagination a,
.pagination span {
    padding: 12px 16px;
    border: 2px solid #ddd;
    color: #666;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.pagination a:hover {
    border-color: #667eea;
    background: #667eea;
    color: white;
}

.pagination .current {
    background: #667eea;
    border-color: #667eea;
    color: white;
}

.no-posts {
    text-align: center;
    padding: 60px 20px;
    background: #f8f9fa;
    border-radius: 15px;
    margin: 40px 0;
}

.no-posts h3 {
    font-size: 1.8rem;
    margin-bottom: 15px;
    color: #333;
}

.no-posts p {
    color: #666;
    font-size: 1.1rem;
    margin-bottom: 25px;
}

.search-form {
    max-width: 400px;
    margin: 0 auto;
    display: flex;
    gap: 10px;
}

.search-form input {
    flex: 1;
    padding: 12px 16px;
    border: 2px solid #ddd;
    border-radius: 25px;
    font-size: 1rem;
}

.search-form button {
    padding: 12px 20px;
    background: #667eea;
    color: white;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.3s ease;
}

.search-form button:hover {
    background: #5a6fd8;
}

/* Responsividade */
@media (max-width: 768px) {
    .blog-home-container {
        padding: 15px;
    }
    
    .blog-header h1 {
        font-size: 2rem;
    }
    
    .blog-controls {
        flex-direction: column;
        align-items: stretch;
    }
    
    .posts-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .posts-list .post-card {
        flex-direction: column;
    }
    
    .posts-list .post-thumbnail {
        width: 100%;
        height: 200px;
    }
    
    .post-content {
        padding: 20px;
    }
}

@media (max-width: 480px) {
    .blog-header {
        padding: 30px 15px;
    }
    
    .blog-header h1 {
        font-size: 1.8rem;
    }
    
    .blog-filters {
        justify-content: center;
    }
    
    .post-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .post-footer {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
}

/* Modo Escuro */
@media (prefers-color-scheme: dark) {
    .blog-home-container {
        background: #1a1a1a;
        color: #e0e0e0;
    }
    
    .post-card {
        background: #2d2d2d;
        border-color: #404040;
    }
    
    .post-title a {
        color: #e0e0e0;
    }
    
    .post-excerpt {
        color: #b0b0b0;
    }
    
    .post-meta {
        color: #888;
    }
    
    .post-footer {
        border-color: #404040;
    }
    
    .no-posts {
        background: #2d2d2d;
    }
    
    .search-form input {
        background: #2d2d2d;
        border-color: #404040;
        color: #e0e0e0;
    }
}

/* Animações */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.post-card {
    animation: fadeInUp 0.6s ease forwards;
}

.post-card:nth-child(2) { animation-delay: 0.1s; }
.post-card:nth-child(3) { animation-delay: 0.2s; }
.post-card:nth-child(4) { animation-delay: 0.3s; }
.post-card:nth-child(5) { animation-delay: 0.4s; }
.post-card:nth-child(6) { animation-delay: 0.5s; }

/* Acessibilidade */
@media (prefers-reduced-motion: reduce) {
    .post-card,
    .post-thumbnail img,
    .read-more {
        animation: none;
        transition: none;
    }
    
    .post-card:hover {
        transform: none;
    }
}

/* Impressão */
@media print {
    .blog-controls,
    .pagination-wrapper,
    .read-more {
        display: none;
    }
    
    .post-card {
        box-shadow: none;
        border: 1px solid #ddd;
        break-inside: avoid;
        margin-bottom: 20px;
    }
    
    .post-thumbnail {
        height: auto;
    }
}
</style>

<div class="blog-home-container">
    <!-- Cabeçalho do Blog -->
    <header class="blog-header">
        <h1><?php bloginfo('name'); ?> - Blog</h1>
        <div class="description">
            <?php 
            $blog_description = get_bloginfo('description');
            echo $blog_description ? $blog_description : 'Fique por dentro das últimas notícias e artigos do nosso blog.';
            ?>
        </div>
    </header>

    <!-- Controles do Blog -->
    <div class="blog-controls">
        <div class="blog-filters">
            <a href="<?php echo home_url('/'); ?>" class="filter-btn <?php echo !is_category() && !is_tag() ? 'active' : ''; ?>">
                Todas
            </a>
            <?php
            $categories = get_categories(array(
                'orderby' => 'count',
                'order' => 'DESC',
                'number' => 6,
                'hide_empty' => true
            ));
            
            foreach ($categories as $category) :
                $active_class = is_category($category->term_id) ? 'active' : '';
            ?>
                <a href="<?php echo get_category_link($category->term_id); ?>" 
                   class="filter-btn <?php echo $active_class; ?>">
                    <?php echo $category->name; ?>
                </a>
            <?php endforeach; ?>
        </div>
        
        <div class="view-controls">
            <button class="view-btn active" data-view="grid" title="Visualização em Grade">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 3h7v7H3V3zm0 11h7v7H3v-7zm11-11h7v7h-7V3zm0 11h7v7h-7v-7z"/>
                </svg>
            </button>
            <button class="view-btn" data-view="list" title="Visualização em Lista">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Grid/Lista de Posts -->
    <div id="posts-container" class="posts-grid">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <article class="post-card" data-category="<?php echo get_the_category_list(' '); ?>">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium_large', array('alt' => get_the_title())); ?>
                            </a>
                            
                            <?php 
                            $categories = get_the_category();
                            if (!empty($categories)) :
                                $primary_category = $categories[0];
                            ?>
                                <a href="<?php echo get_category_link($primary_category->term_id); ?>" 
                                   class="post-category">
                                    <?php echo $primary_category->name; ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="post-content">
                        <div class="post-meta">
                            <div class="author">
                                <?php echo get_avatar(get_the_author_meta('ID'), 24); ?>
                                <span><?php the_author(); ?></span>
                            </div>
                            <time datetime="<?php echo get_the_date('c'); ?>">
                                <?php echo get_the_date(); ?>
                            </time>
                            <span class="reading-time">
                                <?php echo ceil(str_word_count(get_the_content()) / 200); ?> min de leitura
                            </span>
                        </div>
                        
                        <h2 class="post-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        
                        <div class="post-excerpt">
                            <?php 
                            if (has_excerpt()) {
                                the_excerpt();
                            } else {
                                echo wp_trim_words(get_the_content(), 25, '...');
                            }
                            ?>
                        </div>
                        
                        <div class="post-footer">
                            <a href="<?php the_permalink(); ?>" class="read-more">
                                Ler mais
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/>
                                </svg>
                            </a>
                            
                            <?php if (has_tag()) : ?>
                                <div class="post-tags">
                                    <?php 
                                    $tags = get_the_tags();
                                    $tag_count = 0;
                                    foreach ($tags as $tag) :
                                        if ($tag_count >= 3) break;
                                    ?>
                                        <a href="<?php echo get_tag_link($tag->term_id); ?>" 
                                           class="post-tag">#<?php echo $tag->name; ?></a>
                                    <?php 
                                        $tag_count++;
                                    endforeach; 
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <div class="no-posts">
                <h3>Nenhum post encontrado</h3>
                <p>Não há posts para exibir no momento. Que tal fazer uma busca?</p>
                
                <form class="search-form" method="get" action="<?php echo home_url('/'); ?>">
                    <input type="search" 
                           name="s" 
                           placeholder="Buscar posts..." 
                           value="<?php echo get_search_query(); ?>"
                           required>
                    <button type="submit">Buscar</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <!-- Paginação -->
    <?php if (have_posts()) : ?>
        <div class="pagination-wrapper">
            <?php
            echo paginate_links(array(
                'prev_text' => '&laquo; Anterior',
                'next_text' => 'Próximo &raquo;',
                'mid_size' => 2,
                'end_size' => 1,
                'type' => 'list',
                'class' => 'pagination'
            ));
            ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Controles de visualização
    const viewButtons = document.querySelectorAll('.view-btn');
    const postsContainer = document.getElementById('posts-container');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            viewButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Change container class
            const view = this.dataset.view;
            postsContainer.className = view === 'list' ? 'posts-list' : 'posts-grid';
            
            // Save preference
            localStorage.setItem('blog-view-preference', view);
        });
    });
    
    // Restore view preference
    const savedView = localStorage.getItem('blog-view-preference');
    if (savedView) {
        const targetButton = document.querySelector(`[data-view="${savedView}"]`);
        if (targetButton) {
            targetButton.click();
        }
    }
    
    // Lazy loading para imagens
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    // Smooth scroll para links internos
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Adicionar classe de animação aos cards quando visíveis
    if ('IntersectionObserver' in window) {
        const cardObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                }
            });
        }, {
            threshold: 0.1
        });
        
        document.querySelectorAll('.post-card').forEach(card => {
            card.style.animationPlayState = 'paused';
            cardObserver.observe(card);
        });
    }
});
</script>

<?php get_footer(); ?>