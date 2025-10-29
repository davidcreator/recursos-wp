<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package NosfirNews
 * @since 1.0.0
 */

get_header(); ?>

<div class="error-404-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="error-404-content text-center">
                    
                    <!-- Error Icon -->
                    <div class="error-icon">
                        <svg width="200" height="200" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="100" cy="100" r="90" stroke="currentColor" stroke-width="4" fill="none"/>
                            <text x="100" y="120" text-anchor="middle" font-size="60" font-weight="bold" fill="currentColor">404</text>
                        </svg>
                    </div>
                    
                    <!-- Error Message -->
                    <div class="error-message">
                        <h1 class="error-title"><?php esc_html_e( 'Oops! Página não encontrada', 'nosfirnews' ); ?></h1>
                        <p class="error-description">
                            <?php esc_html_e( 'A página que você está procurando pode ter sido removida, teve seu nome alterado ou está temporariamente indisponível.', 'nosfirnews' ); ?>
                        </p>
                    </div>
                    
                    <!-- Search Form -->
                    <div class="error-search">
                        <h3><?php esc_html_e( 'Tente procurar pelo que você precisa:', 'nosfirnews' ); ?></h3>
                        <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                            <div class="search-input-group">
                                <input type="search" 
                                       class="search-field" 
                                       placeholder="<?php echo esc_attr_x( 'Digite sua busca...', 'placeholder', 'nosfirnews' ); ?>" 
                                       value="<?php echo get_search_query(); ?>" 
                                       name="s" 
                                       required />
                                <button type="submit" class="search-submit">
                                    <i class="fas fa-search" aria-hidden="true"></i>
                                    <span class="screen-reader-text"><?php esc_html_e( 'Buscar', 'nosfirnews' ); ?></span>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Navigation Options -->
                    <div class="error-navigation">
                        <h3><?php esc_html_e( 'Ou navegue pelas opções abaixo:', 'nosfirnews' ); ?></h3>
                        <div class="nav-options">
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary">
                                <i class="fas fa-home" aria-hidden="true"></i>
                                <?php esc_html_e( 'Página Inicial', 'nosfirnews' ); ?>
                            </a>
                            <a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>" class="btn btn-outline">
                                <i class="fas fa-newspaper" aria-hidden="true"></i>
                                <?php esc_html_e( 'Blog', 'nosfirnews' ); ?>
                            </a>
                            <a href="javascript:history.back()" class="btn btn-outline">
                                <i class="fas fa-arrow-left" aria-hidden="true"></i>
                                <?php esc_html_e( 'Voltar', 'nosfirnews' ); ?>
                            </a>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        
        <!-- Recent Posts Section -->
        <div class="row">
            <div class="col-12">
                <div class="recent-posts-section">
                    <h3 class="section-title"><?php esc_html_e( 'Últimas Notícias', 'nosfirnews' ); ?></h3>
                    
                    <?php
                    $recent_posts = new WP_Query( array(
                        'post_type'      => 'post',
                        'posts_per_page' => 6,
                        'post_status'    => 'publish',
                        'meta_query'     => array(
                            array(
                                'key'     => '_thumbnail_id',
                                'compare' => 'EXISTS'
                            )
                        )
                    ) );
                    
                    if ( $recent_posts->have_posts() ) : ?>
                        <div class="recent-posts-grid">
                            <?php while ( $recent_posts->have_posts() ) : $recent_posts->the_post(); ?>
                                <article class="recent-post-item">
                                    <div class="post-thumbnail">
                                        <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                            <?php if ( has_post_thumbnail() ) : ?>
                                                <?php the_post_thumbnail( 'medium', array( 'class' => 'img-fluid' ) ); ?>
                                            <?php else : ?>
                                                <div class="no-thumbnail">
                                                    <i class="fas fa-image" aria-hidden="true"></i>
                                                </div>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    <div class="post-content">
                                        <h4 class="post-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h4>
                                        <div class="post-meta">
                                            <span class="post-date">
                                                <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                                                <time datetime="<?php echo get_the_date( 'c' ); ?>">
                                                    <?php echo get_the_date(); ?>
                                                </time>
                                            </span>
                                        </div>
                                        <div class="post-excerpt">
                                            <?php echo wp_trim_words( get_the_excerpt(), 15, '...' ); ?>
                                        </div>
                                    </div>
                                </article>
                            <?php endwhile; ?>
                        </div>
                        <?php wp_reset_postdata(); ?>
                    <?php else : ?>
                        <p class="no-posts"><?php esc_html_e( 'Nenhum post encontrado.', 'nosfirnews' ); ?></p>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
        
        <!-- Categories Section -->
        <div class="row">
            <div class="col-12">
                <div class="categories-section">
                    <h3 class="section-title"><?php esc_html_e( 'Categorias', 'nosfirnews' ); ?></h3>
                    
                    <?php
                    $categories = get_categories( array(
                        'orderby'    => 'count',
                        'order'      => 'DESC',
                        'number'     => 8,
                        'hide_empty' => true
                    ) );
                    
                    if ( $categories ) : ?>
                        <div class="categories-grid">
                            <?php foreach ( $categories as $category ) : ?>
                                <div class="category-item">
                                    <a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" class="category-link">
                                        <div class="category-info">
                                            <h4 class="category-name"><?php echo esc_html( $category->name ); ?></h4>
                                            <span class="category-count">
                                                <?php printf( 
                                                    esc_html( _n( '%s post', '%s posts', $category->count, 'nosfirnews' ) ), 
                                                    number_format_i18n( $category->count ) 
                                                ); ?>
                                            </span>
                                        </div>
                                        <div class="category-icon">
                                            <i class="fas fa-folder" aria-hidden="true"></i>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
        
    </div>
</div>

<style>
/* 404 Page Styles */
.error-404-page {
    padding: 4rem 0;
    min-height: 70vh;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.error-404-content {
    padding: 2rem;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 3rem;
}

.error-icon {
    margin-bottom: 2rem;
    color: var(--color-primary, #007cba);
}

.error-icon svg {
    max-width: 200px;
    height: auto;
}

.error-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 1rem;
}

.error-description {
    font-size: 1.2rem;
    color: #666;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.error-search {
    margin-bottom: 2rem;
}

.error-search h3 {
    font-size: 1.3rem;
    margin-bottom: 1rem;
    color: #333;
}

.search-input-group {
    display: flex;
    max-width: 400px;
    margin: 0 auto;
    border-radius: 25px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.search-field {
    flex: 1;
    padding: 12px 20px;
    border: none;
    font-size: 1rem;
    outline: none;
}

.search-submit {
    background: var(--color-primary, #007cba);
    color: white;
    border: none;
    padding: 12px 20px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.search-submit:hover {
    background: var(--color-primary-dark, #005a87);
}

.error-navigation h3 {
    font-size: 1.3rem;
    margin-bottom: 1.5rem;
    color: #333;
}

.nav-options {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 12px 24px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.btn-primary {
    background: var(--color-primary, #007cba);
    color: white;
}

.btn-primary:hover {
    background: var(--color-primary-dark, #005a87);
    color: white;
    transform: translateY(-2px);
}

.btn-outline {
    background: transparent;
    color: var(--color-primary, #007cba);
    border-color: var(--color-primary, #007cba);
}

.btn-outline:hover {
    background: var(--color-primary, #007cba);
    color: white;
    transform: translateY(-2px);
}

/* Recent Posts Section */
.recent-posts-section,
.categories-section {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    margin-bottom: 2rem;
}

.section-title {
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: #333;
    text-align: center;
    position: relative;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 3px;
    background: var(--color-primary, #007cba);
    border-radius: 2px;
}

.recent-posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.recent-post-item {
    background: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.recent-post-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
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

.post-thumbnail:hover img {
    transform: scale(1.05);
}

.no-thumbnail {
    width: 100%;
    height: 100%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 2rem;
}

.post-content {
    padding: 1.5rem;
}

.post-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.post-title a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.post-title a:hover {
    color: var(--color-primary, #007cba);
}

.post-meta {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 0.5rem;
}

.post-excerpt {
    font-size: 0.95rem;
    color: #555;
    line-height: 1.5;
}

/* Categories Section */
.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.category-item {
    background: #f8f9fa;
    border-radius: 8px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.category-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.category-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem;
    text-decoration: none;
    color: inherit;
}

.category-name {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: #333;
}

.category-count {
    font-size: 0.9rem;
    color: #666;
}

.category-icon {
    color: var(--color-primary, #007cba);
    font-size: 1.5rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .error-404-page {
        padding: 2rem 0;
    }
    
    .error-title {
        font-size: 2rem;
    }
    
    .error-description {
        font-size: 1rem;
    }
    
    .nav-options {
        flex-direction: column;
        align-items: center;
    }
    
    .btn {
        width: 200px;
        justify-content: center;
    }
    
    .recent-posts-grid {
        grid-template-columns: 1fr;
    }
    
    .categories-grid {
        grid-template-columns: 1fr;
    }
}

.screen-reader-text {
    clip: rect(1px, 1px, 1px, 1px);
    position: absolute !important;
    height: 1px;
    width: 1px;
    overflow: hidden;
}
</style>

<?php get_footer(); ?>