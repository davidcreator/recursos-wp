<?php
/**
 * Template Name: Author Page
 * Description: Template para páginas de autor com biografia e posts do autor
 * 
 * @package NosfirNews
 * @since 1.0.0
 */

get_header(); 

// Obter informações do autor
$author_id = get_queried_object_id();
$author = get_userdata($author_id);
$author_posts_count = count_user_posts($author_id);
$author_description = get_the_author_meta('description', $author_id);
$author_website = get_the_author_meta('user_url', $author_id);
$author_social = array(
    'twitter' => get_the_author_meta('twitter', $author_id),
    'facebook' => get_the_author_meta('facebook', $author_id),
    'linkedin' => get_the_author_meta('linkedin', $author_id),
    'instagram' => get_the_author_meta('instagram', $author_id)
);
?>

<style>
/* Estilos para o template author.php */
.author-page-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    background: var(--bg-color, #ffffff);
    color: var(--text-color, #333333);
}

.author-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 60px 40px;
    border-radius: 20px;
    margin-bottom: 40px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.author-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    pointer-events: none;
}

.author-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    margin: 0 auto 20px;
    border: 5px solid rgba(255,255,255,0.3);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    position: relative;
    z-index: 2;
}

.author-name {
    font-size: 2.5rem;
    margin: 0 0 10px 0;
    font-weight: 700;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    position: relative;
    z-index: 2;
}

.author-title {
    font-size: 1.2rem;
    opacity: 0.9;
    margin-bottom: 20px;
    position: relative;
    z-index: 2;
}

.author-stats {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-bottom: 30px;
    position: relative;
    z-index: 2;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
    margin-top: 5px;
}

.author-social {
    display: flex;
    justify-content: center;
    gap: 15px;
    position: relative;
    z-index: 2;
}

.social-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 45px;
    height: 45px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.social-link:hover {
    background: rgba(255,255,255,0.3);
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.author-content {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 40px;
    margin-bottom: 40px;
}

.author-main {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    border: 1px solid #f0f0f0;
}

.author-bio {
    margin-bottom: 40px;
}

.author-bio h2 {
    font-size: 1.8rem;
    margin-bottom: 20px;
    color: #333;
    border-bottom: 3px solid #667eea;
    padding-bottom: 10px;
}

.author-bio-text {
    font-size: 1.1rem;
    line-height: 1.7;
    color: #666;
    margin-bottom: 20px;
}

.author-website {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #667eea;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.author-website:hover {
    color: #5a6fd8;
}

.posts-section h2 {
    font-size: 1.8rem;
    margin-bottom: 30px;
    color: #333;
    border-bottom: 3px solid #667eea;
    padding-bottom: 10px;
}

.posts-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
    gap: 15px;
}

.posts-filter {
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
    font-size: 0.9rem;
}

.filter-btn:hover,
.filter-btn.active {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.view-toggle {
    display: flex;
    gap: 5px;
    background: #f8f9fa;
    border-radius: 8px;
    padding: 5px;
}

.view-btn {
    padding: 8px 12px;
    border: none;
    background: transparent;
    cursor: pointer;
    border-radius: 5px;
    transition: all 0.3s ease;
    color: #666;
}

.view-btn.active {
    background: white;
    color: #667eea;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.author-posts {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
}

.author-posts.list-view {
    grid-template-columns: 1fr;
}

.post-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 3px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
}

.post-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.list-view .post-card {
    display: flex;
    align-items: center;
}

.post-thumbnail {
    position: relative;
    overflow: hidden;
    height: 180px;
}

.list-view .post-thumbnail {
    width: 200px;
    height: 120px;
    flex-shrink: 0;
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
    top: 12px;
    left: 12px;
    background: #667eea;
    color: white;
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 600;
    text-decoration: none;
    z-index: 2;
}

.post-content {
    padding: 20px;
}

.list-view .post-content {
    flex: 1;
    padding: 15px 20px;
}

.post-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
    font-size: 0.85rem;
    color: #888;
}

.post-title {
    margin: 0 0 12px 0;
    font-size: 1.2rem;
    line-height: 1.4;
    font-weight: 600;
}

.list-view .post-title {
    font-size: 1.1rem;
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
    margin-bottom: 15px;
}

.list-view .post-excerpt {
    font-size: 0.9rem;
    margin-bottom: 10px;
}

.post-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #f0f0f0;
}

.read-more {
    background: #667eea;
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.read-more:hover {
    background: #5a6fd8;
    transform: translateX(3px);
}

.post-tags {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.post-tag {
    background: #f8f9fa;
    color: #666;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    text-decoration: none;
    transition: all 0.3s ease;
}

.post-tag:hover {
    background: #667eea;
    color: white;
}

.author-sidebar {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.sidebar-widget {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    border: 1px solid #f0f0f0;
}

.widget-title {
    font-size: 1.3rem;
    margin-bottom: 20px;
    color: #333;
    border-bottom: 2px solid #667eea;
    padding-bottom: 8px;
}

.author-contact {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.contact-item:hover {
    background: #667eea;
    color: white;
    transform: translateX(5px);
}

.contact-item a {
    color: inherit;
    text-decoration: none;
    font-weight: 500;
}

.recent-posts-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.recent-post-item {
    display: flex;
    gap: 12px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f0f0f0;
}

.recent-post-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.recent-post-thumb {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
}

.recent-post-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.recent-post-content h4 {
    font-size: 0.9rem;
    margin: 0 0 5px 0;
    line-height: 1.3;
}

.recent-post-content h4 a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.recent-post-content h4 a:hover {
    color: #667eea;
}

.recent-post-date {
    font-size: 0.8rem;
    color: #888;
}

.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin: 40px 0;
}

.pagination {
    display: flex;
    gap: 8px;
    align-items: center;
}

.pagination a,
.pagination span {
    padding: 10px 15px;
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
    font-size: 1.5rem;
    margin-bottom: 15px;
    color: #333;
}

.no-posts p {
    color: #666;
    font-size: 1rem;
}

/* Responsividade */
@media (max-width: 768px) {
    .author-page-container {
        padding: 15px;
    }
    
    .author-hero {
        padding: 40px 20px;
    }
    
    .author-name {
        font-size: 2rem;
    }
    
    .author-stats {
        gap: 20px;
    }
    
    .author-content {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .author-main {
        padding: 20px;
    }
    
    .posts-controls {
        flex-direction: column;
        align-items: stretch;
    }
    
    .author-posts {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .list-view .post-card {
        flex-direction: column;
    }
    
    .list-view .post-thumbnail {
        width: 100%;
        height: 180px;
    }
}

@media (max-width: 480px) {
    .author-hero {
        padding: 30px 15px;
    }
    
    .author-name {
        font-size: 1.8rem;
    }
    
    .author-stats {
        flex-direction: column;
        gap: 15px;
    }
    
    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(255,255,255,0.1);
        padding: 10px 15px;
        border-radius: 10px;
    }
    
    .author-social {
        gap: 10px;
    }
    
    .social-link {
        width: 40px;
        height: 40px;
    }
    
    .posts-filter {
        justify-content: center;
    }
    
    .sidebar-widget {
        padding: 20px;
    }
}

/* Modo Escuro */
@media (prefers-color-scheme: dark) {
    .author-page-container {
        background: #1a1a1a;
        color: #e0e0e0;
    }
    
    .author-main,
    .sidebar-widget {
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
    
    .contact-item {
        background: #404040;
    }
    
    .recent-post-item {
        border-color: #404040;
    }
    
    .no-posts {
        background: #2d2d2d;
    }
}

/* Animações */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
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
    .contact-item {
        animation: none;
        transition: none;
    }
    
    .post-card:hover {
        transform: none;
    }
}

/* Impressão */
@media print {
    .posts-controls,
    .pagination-wrapper,
    .read-more,
    .author-social {
        display: none;
    }
    
    .author-content {
        grid-template-columns: 1fr;
    }
    
    .post-card {
        box-shadow: none;
        border: 1px solid #ddd;
        break-inside: avoid;
        margin-bottom: 20px;
    }
}
</style>

<div class="author-page-container">
    <!-- Hero do Autor -->
    <section class="author-hero">
        <?php echo get_avatar($author_id, 120, '', '', array('class' => 'author-avatar')); ?>
        
        <h1 class="author-name"><?php echo $author->display_name; ?></h1>
        
        <?php if ($author->user_description) : ?>
            <div class="author-title"><?php echo wp_trim_words($author->user_description, 10); ?></div>
        <?php endif; ?>
        
        <div class="author-stats">
            <div class="stat-item">
                <span class="stat-number"><?php echo $author_posts_count; ?></span>
                <span class="stat-label">Artigos</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?php echo date('Y') - date('Y', strtotime($author->user_registered)); ?></span>
                <span class="stat-label">Anos</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?php echo date('M Y', strtotime($author->user_registered)); ?></span>
                <span class="stat-label">Desde</span>
            </div>
        </div>
        
        <?php if (array_filter($author_social) || $author_website) : ?>
            <div class="author-social">
                <?php if ($author_website) : ?>
                    <a href="<?php echo esc_url($author_website); ?>" class="social-link" target="_blank" rel="noopener" title="Website">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                        </svg>
                    </a>
                <?php endif; ?>
                
                <?php if ($author_social['twitter']) : ?>
                    <a href="https://twitter.com/<?php echo esc_attr($author_social['twitter']); ?>" class="social-link" target="_blank" rel="noopener" title="Twitter">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
                        </svg>
                    </a>
                <?php endif; ?>
                
                <?php if ($author_social['facebook']) : ?>
                    <a href="https://facebook.com/<?php echo esc_attr($author_social['facebook']); ?>" class="social-link" target="_blank" rel="noopener" title="Facebook">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                <?php endif; ?>
                
                <?php if ($author_social['linkedin']) : ?>
                    <a href="https://linkedin.com/in/<?php echo esc_attr($author_social['linkedin']); ?>" class="social-link" target="_blank" rel="noopener" title="LinkedIn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                <?php endif; ?>
                
                <?php if ($author_social['instagram']) : ?>
                    <a href="https://instagram.com/<?php echo esc_attr($author_social['instagram']); ?>" class="social-link" target="_blank" rel="noopener" title="Instagram">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Conteúdo Principal -->
    <div class="author-content">
        <main class="author-main">
            <!-- Biografia do Autor -->
            <?php if ($author_description) : ?>
                <section class="author-bio">
                    <h2>Sobre o Autor</h2>
                    <div class="author-bio-text">
                        <?php echo wpautop($author_description); ?>
                    </div>
                    
                    <?php if ($author_website) : ?>
                        <a href="<?php echo esc_url($author_website); ?>" class="author-website" target="_blank" rel="noopener">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                            </svg>
                            Visite meu website
                        </a>
                    <?php endif; ?>
                </section>
            <?php endif; ?>

            <!-- Posts do Autor -->
            <section class="posts-section">
                <h2>Artigos de <?php echo $author->display_name; ?> (<?php echo $author_posts_count; ?>)</h2>
                
                <!-- Controles dos Posts -->
                <div class="posts-controls">
                    <div class="posts-filter">
                        <a href="<?php echo get_author_posts_url($author_id); ?>" class="filter-btn <?php echo !is_category() && !is_tag() ? 'active' : ''; ?>">
                            Todos
                        </a>
                        <?php
                        // Obter categorias dos posts do autor
                        $author_categories = get_terms(array(
                            'taxonomy' => 'category',
                            'hide_empty' => true,
                            'meta_query' => array(
                                array(
                                    'key' => 'author',
                                    'value' => $author_id,
                                    'compare' => '='
                                )
                            ),
                            'number' => 5
                        ));
                        
                        foreach ($author_categories as $category) :
                            $active_class = is_category($category->term_id) ? 'active' : '';
                        ?>
                            <a href="<?php echo add_query_arg('cat', $category->term_id, get_author_posts_url($author_id)); ?>" 
                               class="filter-btn <?php echo $active_class; ?>">
                                <?php echo $category->name; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    
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

                <!-- Lista de Posts -->
                <div id="author-posts" class="author-posts">
                    <?php if (have_posts()) : ?>
                        <?php while (have_posts()) : the_post(); ?>
                            <article class="post-card">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="post-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('medium', array('alt' => get_the_title())); ?>
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
                                        <time datetime="<?php echo get_the_date('c'); ?>">
                                            <?php echo get_the_date(); ?>
                                        </time>
                                        <span class="reading-time">
                                            <?php echo ceil(str_word_count(get_the_content()) / 200); ?> min
                                        </span>
                                    </div>
                                    
                                    <h3 class="post-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    
                                    <div class="post-excerpt">
                                        <?php 
                                        if (has_excerpt()) {
                                            the_excerpt();
                                        } else {
                                            echo wp_trim_words(get_the_content(), 20, '...');
                                        }
                                        ?>
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
                                                $tags = get_the_tags();
                                                $tag_count = 0;
                                                foreach ($tags as $tag) :
                                                    if ($tag_count >= 2) break;
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
                            <p>Este autor ainda não publicou nenhum artigo.</p>
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
            </section>
        </main>

        <!-- Sidebar -->
        <aside class="author-sidebar">
            <!-- Informações de Contato -->
            <div class="sidebar-widget">
                <h3 class="widget-title">Contato</h3>
                <div class="author-contact">
                    <div class="contact-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                        </svg>
                        <a href="mailto:<?php echo $author->user_email; ?>">
                            <?php echo $author->user_email; ?>
                        </a>
                    </div>
                    
                    <?php if ($author_website) : ?>
                        <div class="contact-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                            </svg>
                            <a href="<?php echo esc_url($author_website); ?>" target="_blank" rel="noopener">
                                Website
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="contact-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <span>Membro desde <?php echo date('M Y', strtotime($author->user_registered)); ?></span>
                    </div>
                </div>
            </div>

            <!-- Posts Recentes -->
            <div class="sidebar-widget">
                <h3 class="widget-title">Posts Recentes</h3>
                <div class="recent-posts-list">
                    <?php
                    $recent_posts = get_posts(array(
                        'author' => $author_id,
                        'numberposts' => 5,
                        'post_status' => 'publish'
                    ));
                    
                    foreach ($recent_posts as $post) :
                        setup_postdata($post);
                    ?>
                        <div class="recent-post-item">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="recent-post-thumb">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('thumbnail'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="recent-post-content">
                                <h4>
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h4>
                                <div class="recent-post-date"><?php echo get_the_date(); ?></div>
                            </div>
                        </div>
                    <?php 
                    endforeach;
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Controles de visualização
    const viewButtons = document.querySelectorAll('.view-btn');
    const postsContainer = document.getElementById('author-posts');
    
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
            localStorage.setItem('author-view-preference', view);
        });
    });
    
    // Restore view preference
    const savedView = localStorage.getItem('author-view-preference');
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
});
</script>

<?php get_footer(); ?>