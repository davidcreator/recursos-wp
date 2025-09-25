<?php
/**
 * Template Name: Tag Archive
 * Description: Template para páginas de tags com posts relacionados
 * 
 * @package NosfirNews
 * @since 1.0.0
 */

get_header(); 

// Obter informações da tag atual
$current_tag = get_queried_object();
$tag_name = $current_tag->name;
$tag_description = $current_tag->description;
$tag_slug = $current_tag->slug;
$tag_count = $current_tag->count;

// Obter posts da tag
$posts_count = $wp_query->found_posts;
$current_page = max(1, get_query_var('paged'));
$total_pages = $wp_query->max_num_pages;

// Obter tags relacionadas
$related_tags = get_tags(array(
    'exclude' => $current_tag->term_id,
    'number' => 8,
    'orderby' => 'count',
    'order' => 'DESC'
));
?>

<style>
/* Estilos para o template tag.php */
.tag-page-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    background: var(--bg-color, #ffffff);
    color: var(--text-color, #333333);
}

.tag-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 50px 40px;
    border-radius: 25px;
    margin-bottom: 40px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.tag-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="tag-pattern" width="30" height="30" patternUnits="userSpaceOnUse"><polygon points="15,5 25,15 15,25 5,15" fill="white" opacity="0.05"/></pattern></defs><rect width="100" height="100" fill="url(%23tag-pattern)"/></svg>');
    pointer-events: none;
}

.tag-icon {
    width: 80px;
    height: 80px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 25px;
    backdrop-filter: blur(10px);
    position: relative;
    z-index: 2;
}

.tag-icon svg {
    width: 40px;
    height: 40px;
    fill: white;
}

.tag-title {
    font-size: 2.5rem;
    margin: 0 0 15px 0;
    font-weight: 700;
    position: relative;
    z-index: 2;
}

.tag-title::before {
    content: '#';
    color: rgba(255,255,255,0.7);
    margin-right: 8px;
}

.tag-description {
    font-size: 1.2rem;
    opacity: 0.9;
    margin-bottom: 30px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
    position: relative;
    z-index: 2;
}

.tag-stats {
    display: flex;
    justify-content: center;
    gap: 40px;
    margin-bottom: 30px;
    position: relative;
    z-index: 2;
}

.stat-item {
    text-align: center;
    background: rgba(255,255,255,0.15);
    padding: 20px 25px;
    border-radius: 20px;
    backdrop-filter: blur(15px);
    border: 1px solid rgba(255,255,255,0.2);
}

.stat-number {
    display: block;
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
}

.tag-breadcrumb {
    background: rgba(255,255,255,0.1);
    padding: 12px 20px;
    border-radius: 25px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    position: relative;
    z-index: 2;
    backdrop-filter: blur(10px);
}

.tag-breadcrumb a {
    color: white;
    text-decoration: none;
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.tag-breadcrumb a:hover {
    opacity: 1;
}

.tag-content {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 40px;
    margin-bottom: 40px;
}

.tag-main {
    background: white;
    border-radius: 20px;
    padding: 35px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.1);
    border: 1px solid #f0f0f0;
}

.posts-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 35px;
    flex-wrap: wrap;
    gap: 20px;
}

.posts-info h2 {
    font-size: 1.8rem;
    margin: 0 0 8px 0;
    color: #333;
    font-weight: 600;
}

.posts-meta {
    color: #666;
    font-size: 1rem;
}

.posts-controls {
    display: flex;
    gap: 15px;
    align-items: center;
}

.sort-select {
    padding: 10px 15px;
    border: 2px solid #f0f0f0;
    border-radius: 10px;
    font-size: 0.9rem;
    background: white;
    color: #333;
    cursor: pointer;
    transition: border-color 0.3s ease;
    min-width: 140px;
}

.sort-select:focus {
    outline: none;
    border-color: #667eea;
}

.view-toggle {
    display: flex;
    gap: 5px;
    background: #f8f9fa;
    border-radius: 10px;
    padding: 5px;
}

.view-btn {
    padding: 10px 12px;
    border: none;
    background: transparent;
    cursor: pointer;
    border-radius: 8px;
    transition: all 0.3s ease;
    color: #666;
}

.view-btn.active {
    background: white;
    color: #667eea;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.tag-posts {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 30px;
}

.tag-posts.list-view {
    grid-template-columns: 1fr;
}

.post-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: 1px solid #f5f5f5;
    position: relative;
}

.post-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.list-view .post-card {
    display: flex;
    align-items: center;
}

.post-thumbnail {
    position: relative;
    overflow: hidden;
    height: 200px;
}

.list-view .post-thumbnail {
    width: 250px;
    height: 150px;
    flex-shrink: 0;
}

.post-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.post-card:hover .post-thumbnail img {
    transform: scale(1.08);
}

.post-category {
    position: absolute;
    top: 15px;
    left: 15px;
    background: #667eea;
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-decoration: none;
    z-index: 3;
    transition: all 0.3s ease;
}

.post-category:hover {
    background: #5a6fd8;
    transform: scale(1.05);
}

.post-reading-time {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    backdrop-filter: blur(10px);
}

.post-content {
    padding: 25px;
}

.list-view .post-content {
    flex: 1;
    padding: 20px 25px;
}

.post-meta {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
    font-size: 0.85rem;
    color: #888;
}

.post-meta .author {
    display: flex;
    align-items: center;
    gap: 6px;
}

.post-meta .date {
    display: flex;
    align-items: center;
    gap: 6px;
}

.post-title {
    margin: 0 0 15px 0;
    font-size: 1.3rem;
    line-height: 1.4;
    font-weight: 600;
}

.list-view .post-title {
    font-size: 1.2rem;
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
    font-size: 0.95rem;
    margin-bottom: 20px;
}

.list-view .post-excerpt {
    font-size: 0.9rem;
    margin-bottom: 15px;
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
    font-size: 0.85rem;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.read-more:hover {
    background: #5a6fd8;
    transform: translateX(3px);
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
    font-size: 0.75rem;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.post-tag:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.post-tag.current-tag {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.tag-sidebar {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.1);
    border: 1px solid #f0f0f0;
    height: fit-content;
    position: sticky;
    top: 20px;
}

.sidebar-section {
    margin-bottom: 35px;
}

.sidebar-section:last-child {
    margin-bottom: 0;
}

.sidebar-title {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 20px;
    color: #333;
    border-bottom: 3px solid #667eea;
    padding-bottom: 10px;
}

.related-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.related-tag {
    background: #f8f9fa;
    color: #666;
    padding: 8px 15px;
    border-radius: 20px;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border: 2px solid #f0f0f0;
    display: flex;
    align-items: center;
    gap: 6px;
}

.related-tag:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
    transform: translateY(-2px);
}

.tag-count {
    background: rgba(255,255,255,0.8);
    color: #666;
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 600;
}

.related-tag:hover .tag-count {
    background: rgba(255,255,255,0.2);
    color: white;
}

.popular-posts {
    list-style: none;
    padding: 0;
    margin: 0;
}

.popular-post {
    display: flex;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.3s ease;
}

.popular-post:last-child {
    border-bottom: none;
}

.popular-post:hover {
    background: #f8f9fa;
    margin: 0 -15px;
    padding: 15px;
    border-radius: 10px;
}

.popular-post-thumb {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    overflow: hidden;
    flex-shrink: 0;
}

.popular-post-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.popular-post-content {
    flex: 1;
}

.popular-post-title {
    font-size: 0.9rem;
    font-weight: 600;
    margin: 0 0 5px 0;
    line-height: 1.3;
}

.popular-post-title a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.popular-post-title a:hover {
    color: #667eea;
}

.popular-post-meta {
    font-size: 0.75rem;
    color: #888;
}

.no-posts {
    text-align: center;
    padding: 80px 20px;
    background: #f8f9fa;
    border-radius: 20px;
    margin: 40px 0;
}

.no-posts h3 {
    font-size: 2rem;
    margin-bottom: 15px;
    color: #333;
}

.no-posts p {
    color: #666;
    font-size: 1.1rem;
    margin-bottom: 30px;
}

.browse-tags {
    background: #667eea;
    color: white;
    padding: 12px 25px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.browse-tags:hover {
    background: #5a6fd8;
    transform: translateY(-2px);
}

.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin: 50px 0;
}

.pagination {
    display: flex;
    gap: 10px;
    align-items: center;
}

.pagination a,
.pagination span {
    padding: 12px 18px;
    border: 2px solid #ddd;
    color: #666;
    text-decoration: none;
    border-radius: 10px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.pagination a:hover {
    border-color: #667eea;
    background: #667eea;
    color: white;
    transform: translateY(-2px);
}

.pagination .current {
    background: #667eea;
    border-color: #667eea;
    color: white;
}

/* Responsividade */
@media (max-width: 768px) {
    .tag-page-container {
        padding: 15px;
    }
    
    .tag-header {
        padding: 40px 25px;
    }
    
    .tag-title {
        font-size: 2rem;
    }
    
    .tag-stats {
        flex-direction: column;
        gap: 20px;
    }
    
    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-align: left;
    }
    
    .tag-content {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .tag-sidebar {
        order: 2;
        position: static;
    }
    
    .tag-main {
        padding: 25px;
        order: 1;
    }
    
    .posts-header {
        flex-direction: column;
        align-items: stretch;
    }
    
    .posts-controls {
        justify-content: space-between;
    }
    
    .tag-posts {
        grid-template-columns: 1fr;
        gap: 25px;
    }
    
    .list-view .post-card {
        flex-direction: column;
    }
    
    .list-view .post-thumbnail {
        width: 100%;
        height: 200px;
    }
}

@media (max-width: 480px) {
    .tag-header {
        padding: 30px 20px;
    }
    
    .tag-title {
        font-size: 1.8rem;
    }
    
    .tag-icon {
        width: 60px;
        height: 60px;
        margin-bottom: 20px;
    }
    
    .tag-icon svg {
        width: 30px;
        height: 30px;
    }
    
    .sidebar-section {
        margin-bottom: 30px;
    }
    
    .posts-controls {
        flex-direction: column;
        gap: 15px;
    }
    
    .view-toggle {
        width: 100%;
        justify-content: center;
    }
    
    .related-tags {
        gap: 8px;
    }
    
    .related-tag {
        padding: 6px 12px;
        font-size: 0.8rem;
    }
}

/* Modo Escuro */
@media (prefers-color-scheme: dark) {
    .tag-page-container {
        background: #1a1a1a;
        color: #e0e0e0;
    }
    
    .tag-main,
    .tag-sidebar {
        background: #2d2d2d;
        border-color: #404040;
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
    
    .popular-post:hover {
        background: #404040;
    }
    
    .popular-post-title a {
        color: #e0e0e0;
    }
    
    .no-posts {
        background: #2d2d2d;
    }
    
    .related-tag {
        background: #404040;
        border-color: #555;
        color: #e0e0e0;
    }
    
    .post-tag {
        background: #404040;
        border-color: #555;
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
    .read-more,
    .related-tag {
        animation: none;
        transition: none;
    }
    
    .post-card:hover {
        transform: none;
    }
}

/* Impressão */
@media print {
    .tag-sidebar,
    .posts-controls,
    .pagination-wrapper,
    .read-more {
        display: none;
    }
    
    .tag-content {
        grid-template-columns: 1fr;
    }
    
    .post-card {
        box-shadow: none;
        border: 1px solid #ddd;
        break-inside: avoid;
        margin-bottom: 20px;
    }
    
    .tag-header {
        background: #f8f9fa;
        color: #333;
    }
}
</style>

<div class="tag-page-container">
    <!-- Cabeçalho da Tag -->
    <header class="tag-header">
        <div class="tag-breadcrumb">
            <a href="<?php echo esc_url(home_url('/')); ?>">Início</a>
            <span>/</span>
            <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>">Blog</a>
            <span>/</span>
            <span>Tags</span>
        </div>
        
        <div class="tag-icon">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M17.63 5.84C17.27 5.33 16.67 5 16 5L5 5.01C3.9 5.01 3 5.9 3 7v10c0 1.1.9 2 2 2h11c.67 0 1.27-.33 1.63-.84L22 12l-4.37-6.16z"/>
            </svg>
        </div>
        
        <h1 class="tag-title"><?php echo esc_html($tag_name); ?></h1>
        
        <?php if ($tag_description) : ?>
            <div class="tag-description">
                <?php echo esc_html($tag_description); ?>
            </div>
        <?php endif; ?>
        
        <div class="tag-stats">
            <div class="stat-item">
                <span class="stat-number"><?php echo $tag_count; ?></span>
                <span class="stat-label">Posts</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?php echo $current_page; ?></span>
                <span class="stat-label">Página</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?php echo $total_pages; ?></span>
                <span class="stat-label">Total</span>
            </div>
        </div>
    </header>

    <!-- Conteúdo Principal -->
    <div class="tag-content">
        <!-- Área Principal dos Posts -->
        <main class="tag-main">
            <?php if (have_posts()) : ?>
                <!-- Cabeçalho dos Posts -->
                <div class="posts-header">
                    <div class="posts-info">
                        <h2>Posts com a tag "<?php echo esc_html($tag_name); ?>"</h2>
                        <div class="posts-meta">
                            Mostrando <?php echo (($current_page - 1) * get_option('posts_per_page')) + 1; ?>-<?php echo min($current_page * get_option('posts_per_page'), $posts_count); ?> de <?php echo $posts_count; ?> posts
                        </div>
                    </div>
                    
                    <div class="posts-controls">
                        <!-- Ordenação -->
                        <select class="sort-select" onchange="updateSort(this.value)">
                            <option value="date">Mais recentes</option>
                            <option value="title">Título A-Z</option>
                            <option value="popularity">Popularidade</option>
                            <option value="comments">Comentários</option>
                        </select>
                        
                        <!-- Toggle de Visualização -->
                        <div class="view-toggle">
                            <button class="view-btn active" data-view="grid" title="Visualização em Grade">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 3h7v7H3V3zm0 11h7v7H3v-7zm11-11h7v7h-7V3zm0 11h7v7h-7v-7z"/>
                                </svg>
                            </button>
                            <button class="view-btn" data-view="list" title="Visualização em Lista">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Lista de Posts -->
                <div id="tag-posts" class="tag-posts">
                    <?php while (have_posts()) : the_post(); ?>
                        <article class="post-card">
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
                                    
                                    <div class="post-reading-time">
                                        <?php echo ceil(str_word_count(get_the_content()) / 200); ?> min
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="post-content">
                                <div class="post-meta">
                                    <span class="date">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                                        </svg>
                                        <time datetime="<?php echo get_the_date('c'); ?>">
                                            <?php echo get_the_date(); ?>
                                        </time>
                                    </span>
                                    <span class="author">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                        </svg>
                                        por <?php the_author(); ?>
                                    </span>
                                </div>
                                
                                <h3 class="post-title">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>
                                
                                <div class="post-excerpt">
                                    <?php echo has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 25, '...'); ?>
                                </div>
                                
                                <div class="post-footer">
                                    <a href="<?php the_permalink(); ?>" class="read-more">
                                        Ler mais
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/>
                                        </svg>
                                    </a>
                                    
                                    <?php if (has_tag()) : ?>
                                        <div class="post-tags">
                                            <?php 
                                            $post_tags = get_the_tags();
                                            $tag_count = 0;
                                            foreach ($post_tags as $post_tag) :
                                                if ($tag_count >= 3) break;
                                                $is_current = ($post_tag->term_id == $current_tag->term_id);
                                            ?>
                                                <a href="<?php echo get_tag_link($post_tag->term_id); ?>" 
                                                   class="post-tag <?php echo $is_current ? 'current-tag' : ''; ?>">
                                                    #<?php echo $post_tag->name; ?>
                                                </a>
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
                </div>

                <!-- Paginação -->
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

            <?php else : ?>
                <!-- Nenhum Post Encontrado -->
                <div class="no-posts">
                    <h3>Nenhum post encontrado</h3>
                    <p>Não há posts com a tag "<?php echo esc_html($tag_name); ?>" no momento.</p>
                    <a href="<?php echo esc_url(home_url('/tags')); ?>" class="browse-tags">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.63 5.84C17.27 5.33 16.67 5 16 5L5 5.01C3.9 5.01 3 5.9 3 7v10c0 1.1.9 2 2 2h11c.67 0 1.27-.33 1.63-.84L22 12l-4.37-6.16z"/>
                        </svg>
                        Explorar todas as tags
                    </a>
                </div>
            <?php endif; ?>
        </main>

        <!-- Sidebar -->
        <aside class="tag-sidebar">
            <!-- Tags Relacionadas -->
            <?php if (!empty($related_tags)) : ?>
                <div class="sidebar-section">
                    <h3 class="sidebar-title">Tags Relacionadas</h3>
                    <div class="related-tags">
                        <?php foreach ($related_tags as $related_tag) : ?>
                            <a href="<?php echo get_tag_link($related_tag->term_id); ?>" 
                               class="related-tag">
                                #<?php echo $related_tag->name; ?>
                                <span class="tag-count"><?php echo $related_tag->count; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Posts Populares -->
            <div class="sidebar-section">
                <h3 class="sidebar-title">Posts Populares</h3>
                <ul class="popular-posts">
                    <?php
                    $popular_posts = new WP_Query(array(
                        'posts_per_page' => 5,
                        'meta_key' => 'post_views_count',
                        'orderby' => 'meta_value_num',
                        'order' => 'DESC',
                        'post_status' => 'publish'
                    ));
                    
                    if ($popular_posts->have_posts()) :
                        while ($popular_posts->have_posts()) : $popular_posts->the_post();
                    ?>
                        <li class="popular-post">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="popular-post-thumb">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('thumbnail', array('alt' => get_the_title())); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="popular-post-content">
                                <h4 class="popular-post-title">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php echo wp_trim_words(get_the_title(), 8, '...'); ?>
                                    </a>
                                </h4>
                                <div class="popular-post-meta">
                                    <?php echo get_the_date(); ?> • <?php echo get_comments_number(); ?> comentários
                                </div>
                            </div>
                        </li>
                    <?php 
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </ul>
            </div>

            <!-- Newsletter -->
            <div class="sidebar-section">
                <h3 class="sidebar-title">Newsletter</h3>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 15px; text-align: center;">
                    <p style="margin-bottom: 15px; color: #666; font-size: 0.9rem;">
                        Receba as últimas notícias e atualizações diretamente no seu email.
                    </p>
                    <form style="display: flex; flex-direction: column; gap: 10px;">
                        <input type="email" 
                               placeholder="Seu email" 
                               style="padding: 10px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 0.9rem;">
                        <button type="submit" 
                                style="background: #667eea; color: white; padding: 10px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                            Inscrever-se
                        </button>
                    </form>
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Controles de visualização
    const viewButtons = document.querySelectorAll('.view-btn');
    const postsContainer = document.getElementById('tag-posts');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            viewButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Change container class
            const view = this.dataset.view;
            if (view === 'list') {
                postsContainer.classList.add('list-view');
            } else {
                postsContainer.classList.remove('list-view');
            }
            
            // Save preference
            localStorage.setItem('tag-view-preference', view);
        });
    });
    
    // Restore view preference
    const savedView = localStorage.getItem('tag-view-preference');
    if (savedView) {
        const targetButton = document.querySelector(`[data-view="${savedView}"]`);
        if (targetButton) {
            targetButton.click();
        }
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
    
    // Lazy loading para imagens
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    // Animação de entrada para cards
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const cardObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.post-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        cardObserver.observe(card);
    });
});

// Função para atualizar ordenação
function updateSort(sortValue) {
    const url = new URL(window.location);
    url.searchParams.set('orderby', sortValue);
    window.location.href = url.toString();
}

// Função para destacar tag atual
function highlightCurrentTag() {
    const currentTagSlug = '<?php echo esc_js($tag_slug); ?>';
    const tagLinks = document.querySelectorAll('.post-tag');
    
    tagLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && href.includes(currentTagSlug)) {
            link.classList.add('current-tag');
        }
    });
}

// Executar highlight após carregamento
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', highlightCurrentTag);
} else {
    highlightCurrentTag();
}

// Função para scroll suave no topo
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Adicionar botão de voltar ao topo (se necessário)
window.addEventListener('scroll', function() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const backToTopBtn = document.getElementById('back-to-top');
    
    if (backToTopBtn) {
        if (scrollTop > 300) {
            backToTopBtn.style.display = 'block';
        } else {
            backToTopBtn.style.display = 'none';
        }
    }
});
</script>

<?php get_footer(); ?>