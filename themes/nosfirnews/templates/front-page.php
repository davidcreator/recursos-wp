<?php
/**
 * The template for displaying the front page
 *
 * @package NosfirNews
 * @since 1.0.0
 */

get_header(); ?>

<div class="front-page">
    
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="hero-content">
                        
                        <!-- Main Featured Post -->
                        <?php
                        $hero_posts = nosfirnews_get_hero_posts(1);
                        
                        if ( $hero_posts->have_posts() ) :
                            while ( $hero_posts->have_posts() ) : $hero_posts->the_post();
                                $categories = get_the_category();
                                $category = $categories ? $categories[0] : null;
                                $views = get_post_meta( get_the_ID(), 'post_views', true );
                        ?>
                            <div class="hero-main">
                                <div class="hero-image">
                                    <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                        <?php if ( has_post_thumbnail() ) : ?>
                                            <?php the_post_thumbnail( 'full', array( 'class' => 'img-fluid' ) ); ?>
                                        <?php else : ?>
                                            <div class="no-thumbnail">
                                                <i class="fas fa-newspaper" aria-hidden="true"></i>
                                            </div>
                                        <?php endif; ?>
                                    </a>
                                    <div class="hero-overlay">
                                        <div class="hero-badge">
                                            <i class="fas fa-star" aria-hidden="true"></i>
                                            <?php esc_html_e( 'Destaque Principal', 'nosfirnews' ); ?>
                                        </div>
                                        <div class="hero-meta">
                                            <?php if ( $category ) : ?>
                                                <span class="hero-category">
                                                    <a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
                                                </span>
                                            <?php endif; ?>
                                            <span class="hero-date">
                                                <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                                                <time datetime="<?php echo get_the_date( 'c' ); ?>">
                                                    <?php echo get_the_date(); ?>
                                                </time>
                                            </span>
                                        </div>
                                        <h1 class="hero-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h1>
                                        <div class="hero-excerpt">
                                            <?php echo wp_trim_words( get_the_excerpt(), 30, '...' ); ?>
                                        </div>
                                        <div class="hero-actions">
                                            <a href="<?php the_permalink(); ?>" class="btn btn-primary">
                                                <?php esc_html_e( 'Ler Matéria', 'nosfirnews' ); ?>
                                                <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                            </a>
                                            <div class="hero-stats">
                                                <?php if ( comments_open() || get_comments_number() ) : ?>
                                                    <span class="stat-item">
                                                        <i class="fas fa-comments" aria-hidden="true"></i>
                                                        <?php echo get_comments_number(); ?>
                                                    </span>
                                                <?php endif; ?>
                                                <span class="stat-item">
                                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                                    <?php echo $views ? number_format_i18n( $views ) : '0'; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                            endwhile;
                            wp_reset_postdata();
                        endif;
                        ?>
                        
                        <!-- Secondary Featured Posts -->
                        <div class="hero-secondary">
                            <?php
                            $featured_posts = nosfirnews_get_featured_posts(4, true); // true para excluir heróis
                            
                            if ( $featured_posts->have_posts() ) :
                            ?>
                                <div class="secondary-posts">
                                    <?php while ( $featured_posts->have_posts() ) : $featured_posts->the_post(); 
                                        $categories = get_the_category();
                                        $category = $categories ? $categories[0] : null;
                                    ?>
                                        <article class="secondary-post">
                                            <?php echo nosfirnews_get_post_card(get_the_ID(), 'compact', array(
                                                'show_category' => true,
                                                'show_date' => true,
                                                'show_excerpt' => true,
                                                'excerpt_length' => 15,
                                                'thumbnail_size' => 'medium'
                                            )); ?>
                                        </article>
                                    <?php endwhile; ?>
                                </div>
                                <?php wp_reset_postdata(); ?>
                            <?php endif; ?>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Breaking News Section -->
    <section class="breaking-news-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="breaking-news">
                        <div class="breaking-header">
                            <span class="breaking-label">
                                <i class="fas fa-bolt" aria-hidden="true"></i>
                                <?php esc_html_e( 'Últimas Notícias', 'nosfirnews' ); ?>
                            </span>
                        </div>
                        <div class="breaking-content">
                            <div class="breaking-ticker">
                                <?php
                                $breaking_posts = nosfirnews_get_breaking_news(5);
                                
                                if ( $breaking_posts->have_posts() ) :
                                    while ( $breaking_posts->have_posts() ) : $breaking_posts->the_post();
                                ?>
                                    <div class="breaking-item">
                                        <span class="breaking-time">
                                            <?php echo get_the_date( 'H:i' ); ?>
                                        </span>
                                        <a href="<?php the_permalink(); ?>" class="breaking-link">
                                            <?php the_title(); ?>
                                        </a>
                                    </div>
                                <?php
                                    endwhile;
                                    wp_reset_postdata();
                                endif;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Main Content Sections -->
    <div class="main-content">
        <div class="container">
            <div class="row">
                
                <!-- Primary Content -->
                <div class="col-lg-8 col-md-12">
                    
                    <?php if ( class_exists('WooCommerce') && get_theme_mod('nosfirnews_woo_banner_enable', false) ) : ?>
                    <section class="woo-banner-carousel" data-autoplay="<?php echo get_theme_mod('nosfirnews_woo_banner_autoplay', true) ? 'true' : 'false'; ?>" data-speed="<?php echo absint( get_theme_mod('nosfirnews_woo_banner_speed', 4000) ); ?>">
                        <div class="section-header">
                            <h2 class="section-title">
                                <i class="fas fa-tags" aria-hidden="true"></i>
                                <?php esc_html_e( 'Ofertas e Destaques', 'nosfirnews' ); ?>
                            </h2>
                        </div>
                        <div class="carousel-track">
                            <?php
                            $args = array(
                                'post_type'      => 'product',
                                'posts_per_page' => 8,
                                'post_status'    => 'publish',
                            );
                            $source = get_theme_mod( 'nosfirnews_woo_banner_source', 'featured' );
                            if ( $source === 'featured' ) {
                                $args['tax_query'] = array(
                                    array(
                                        'taxonomy' => 'product_visibility',
                                        'field'    => 'name',
                                        'terms'    => array( 'featured' ),
                                    ),
                                );
                            } elseif ( $source === 'sale' ) {
                                $sale_ids = wc_get_product_ids_on_sale();
                                $args['post__in'] = ! empty( $sale_ids ) ? $sale_ids : array(0);
                            }
                            $banner_query = new WP_Query( $args );
                            if ( $banner_query->have_posts() ) :
                                while ( $banner_query->have_posts() ) : $banner_query->the_post();
                                    wc_get_template_part( 'content', 'product' );
                                endwhile;
                                wp_reset_postdata();
                            else :
                                echo '<p class="no-products">' . esc_html__( 'Nenhum produto para o carrossel.', 'nosfirnews' ) . '</p>';
                            endif;
                            ?>
                        </div>
                    </section>
                    <?php endif; ?>

                    <?php if ( class_exists('WooCommerce') && get_theme_mod('nosfirnews_woo_search_enable', true) ) : ?>
                    <section class="woo-advanced-search">
                        <div class="section-header">
                            <h2 class="section-title">
                                <i class="fas fa-search" aria-hidden="true"></i>
                                <?php esc_html_e( 'Buscar Produtos', 'nosfirnews' ); ?>
                            </h2>
                        </div>
                        <form class="woo-search-form" action="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" method="get">
                            <div class="woo-search-fields">
                                <input type="search" name="s" placeholder="<?php esc_attr_e( 'Buscar por nome...', 'nosfirnews' ); ?>" />
                                <?php if ( get_theme_mod( 'nosfirnews_woo_search_filter_category', true ) ) : 
                                    wp_dropdown_categories( array(
                                        'taxonomy'        => 'product_cat',
                                        'name'            => 'product_cat',
                                        'show_option_all' => esc_html__( 'Todas categorias', 'nosfirnews' ),
                                        'hide_empty'      => true,
                                        'class'           => 'woo-search-category'
                                    ) );
                                endif; ?>
                                <?php if ( get_theme_mod( 'nosfirnews_woo_search_filter_price', true ) ) : ?>
                                    <input type="number" name="min_price" min="0" step="1" placeholder="<?php esc_attr_e( 'Preço mínimo', 'nosfirnews' ); ?>" />
                                    <input type="number" name="max_price" min="0" step="1" placeholder="<?php esc_attr_e( 'Preço máximo', 'nosfirnews' ); ?>" />
                                <?php endif; ?>
                                <?php if ( get_theme_mod( 'nosfirnews_woo_search_filter_tag', false ) ) : 
                                    $tags = get_terms( array( 'taxonomy' => 'product_tag', 'hide_empty' => true ) );
                                ?>
                                    <select name="product_tag" class="woo-search-tag">
                                        <option value=""><?php esc_html_e( 'Todas tags', 'nosfirnews' ); ?></option>
                                        <?php foreach ( $tags as $tag ) : ?>
                                            <option value="<?php echo esc_attr( $tag->slug ); ?>"><?php echo esc_html( $tag->name ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php endif; ?>
                                <button type="submit" class="btn btn-primary"><?php esc_html_e( 'Buscar', 'nosfirnews' ); ?></button>
                            </div>
                        </form>
                    </section>
                    <?php endif; ?>

                    <!-- Latest News Section -->
                    <section class="latest-news-section">
                        <div class="section-header">
                            <h2 class="section-title">
                                <i class="fas fa-newspaper" aria-hidden="true"></i>
                                <?php esc_html_e( 'Últimas Notícias', 'nosfirnews' ); ?>
                            </h2>
                            <a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>" class="section-link">
                                <?php esc_html_e( 'Ver todas', 'nosfirnews' ); ?>
                                <i class="fas fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>
                        
                        <div class="latest-posts-grid">
                            <?php
                            $latest_posts = new WP_Query( array(
                                'posts_per_page' => 6,
                                'post__not_in' => array( get_option( 'sticky_posts' ) )
                            ) );
                            
                            if ( $latest_posts->have_posts() ) :
                                while ( $latest_posts->have_posts() ) : $latest_posts->the_post();
                            ?>
                                <article class="latest-post-item">
                                    <?php echo nosfirnews_get_post_card(get_the_ID(), 'grid', array(
                                        'show_category' => true,
                                        'show_date' => true,
                                        'show_excerpt' => true,
                                        'show_reading_time' => true,
                                        'show_stats' => true,
                                        'excerpt_length' => 20,
                                        'thumbnail_size' => 'medium'
                                    )); ?>
                                </article>
                            <?php
                                endwhile;
                                wp_reset_postdata();
                            endif;
                            ?>
                        </div>
                    </section>
                    
                    <!-- Categories Showcase -->
                    <section class="categories-showcase">
                        <div class="section-header">
                            <h2 class="section-title">
                                <i class="fas fa-layer-group" aria-hidden="true"></i>
                                <?php esc_html_e( 'Explore por Categoria', 'nosfirnews' ); ?>
                            </h2>
                        </div>
                        
                        <div class="categories-grid">
                            <?php
                            $featured_categories = get_categories( array(
                                'number' => 6,
                                'orderby' => 'count',
                                'order' => 'DESC',
                                'hide_empty' => true
                            ) );
                            
                            foreach ( $featured_categories as $category ) :
                                // Get latest post from this category
                                $cat_post = new WP_Query( array(
                                    'cat' => $category->term_id,
                                    'posts_per_page' => 1
                                ) );
                            ?>
                                <div class="category-showcase-item">
                                    <div class="category-background">
                                        <?php if ( $cat_post->have_posts() ) : ?>
                                            <?php while ( $cat_post->have_posts() ) : $cat_post->the_post(); ?>
                                                <?php if ( has_post_thumbnail() ) : ?>
                                                    <?php the_post_thumbnail( 'medium', array( 'class' => 'category-bg-image' ) ); ?>
                                                <?php endif; ?>
                                            <?php endwhile; ?>
                                            <?php wp_reset_postdata(); ?>
                                        <?php endif; ?>
                                        <div class="category-overlay"></div>
                                    </div>
                                    <div class="category-content">
                                        <div class="category-icon">
                                            <i class="fas fa-folder" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="category-name">
                                            <a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>">
                                                <?php echo esc_html( $category->name ); ?>
                                            </a>
                                        </h3>
                                        <div class="category-stats">
                                            <span class="category-count">
                                                <?php
                                                printf( 
                                                    esc_html( _n( '%s post', '%s posts', $category->count, 'nosfirnews' ) ), 
                                                    number_format_i18n( $category->count ) 
                                                );
                                                ?>
                                            </span>
                                        </div>
                                        <?php if ( $category->description ) : ?>
                                            <div class="category-description">
                                                <?php echo wp_trim_words( $category->description, 10, '...' ); ?>
                                            </div>
                                        <?php endif; ?>
                                        <a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" class="category-link">
                                            <?php esc_html_e( 'Explorar', 'nosfirnews' ); ?>
                                            <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                    
                    <!-- Products Showcase -->
                    <?php if ( class_exists('WooCommerce') ) : ?>
                    <section class="products-showcase">
                        <div class="section-header">
                            <h2 class="section-title">
                                <i class="fas fa-shopping-bag" aria-hidden="true"></i>
                                <?php esc_html_e( 'Produtos em Destaque', 'nosfirnews' ); ?>
                            </h2>
                        </div>
                        <?php
                        $products_query = new WP_Query( array(
                            'post_type'      => 'product',
                            'posts_per_page' => 8,
                            'post_status'    => 'publish',
                        ) );
                        ?>
                        <?php if ( $products_query->have_posts() ) : ?>
                            <?php woocommerce_product_loop_start(); ?>
                            <?php while ( $products_query->have_posts() ) : $products_query->the_post(); ?>
                                <?php wc_get_template_part( 'content', 'product' ); ?>
                            <?php endwhile; ?>
                            <?php woocommerce_product_loop_end(); ?>
                            <?php wp_reset_postdata(); ?>
                            <div class="section-footer">
                                <?php if ( function_exists('wc_get_page_id') ) : 
                                    $shop_id = wc_get_page_id( 'shop' );
                                    if ( $shop_id && $shop_id > 0 ) : ?>
                                        <a class="btn btn-primary" href="<?php echo esc_url( get_permalink( $shop_id ) ); ?>">
                                            <?php esc_html_e( 'Ver Loja', 'nosfirnews' ); ?>
                                            <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                        </a>
                                    <?php endif; endif; ?>
                            </div>
                        <?php else : ?>
                            <p class="no-products"><?php esc_html_e( 'Nenhum produto disponível no momento.', 'nosfirnews' ); ?></p>
                        <?php endif; ?>
                    </section>
                    <?php endif; ?>
                    
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4 col-md-12">
                    <aside class="front-page-sidebar">
                        
                        <!-- Trending Posts Widget -->
                        <div class="widget trending-posts-widget">
                            <h3 class="widget-title">
                                <i class="fas fa-fire" aria-hidden="true"></i>
                                <?php esc_html_e( 'Em Alta', 'nosfirnews' ); ?>
                            </h3>
                            <div class="trending-posts">
                                <?php
                                $trending_posts = new WP_Query( array(
                                    'posts_per_page' => 5,
                                    'meta_key' => 'post_views',
                                    'orderby' => 'meta_value_num',
                                    'order' => 'DESC',
                                    'date_query' => array(
                                        array(
                                            'after' => '1 week ago'
                                        )
                                    )
                                ) );
                                
                                if ( $trending_posts->have_posts() ) :
                                    $counter = 1;
                                    while ( $trending_posts->have_posts() ) : $trending_posts->the_post();
                                ?>
                                    <article class="trending-post-item">
                                        <div class="trending-number">
                                            <?php echo $counter; ?>
                                        </div>
                                        <div class="trending-thumbnail">
                                            <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                                <?php if ( has_post_thumbnail() ) : ?>
                                                    <?php the_post_thumbnail( 'thumbnail', array( 'class' => 'img-fluid' ) ); ?>
                                                <?php else : ?>
                                                    <div class="no-thumbnail">
                                                        <i class="fas fa-image" aria-hidden="true"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                        <div class="trending-content">
                                            <div class="trending-meta">
                                                <span class="trending-category">
                                                    <?php
                                                    $categories = get_the_category();
                                                    if ( $categories ) {
                                                        echo esc_html( $categories[0]->name );
                                                    }
                                                    ?>
                                                </span>
                                                <span class="trending-date">
                                                    <?php echo human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' ' . esc_html__( 'atrás', 'nosfirnews' ); ?>
                                                </span>
                                            </div>
                                            <h4 class="trending-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h4>
                                            <div class="trending-stats">
                                                <span class="trending-views">
                                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                                    <?php
                                                    $views = get_post_meta( get_the_ID(), 'post_views', true );
                                                    echo $views ? number_format_i18n( $views ) : '0';
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                    </article>
                                <?php
                                        $counter++;
                                    endwhile;
                                    wp_reset_postdata();
                                endif;
                                ?>
                            </div>
                        </div>
                        
                        <!-- Newsletter Signup Widget -->
                        <div class="widget newsletter-widget">
                            <h3 class="widget-title">
                                <i class="fas fa-envelope" aria-hidden="true"></i>
                                <?php esc_html_e( 'Newsletter', 'nosfirnews' ); ?>
                            </h3>
                            <div class="newsletter-content">
                                <p><?php esc_html_e( 'Receba as principais notícias diretamente no seu e-mail.', 'nosfirnews' ); ?></p>
                                <form class="newsletter-form" action="#" method="post">
                                    <div class="form-group">
                                        <input type="email" name="newsletter_email" placeholder="<?php esc_attr_e( 'Seu e-mail', 'nosfirnews' ); ?>" required>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane" aria-hidden="true"></i>
                                            <?php esc_html_e( 'Inscrever', 'nosfirnews' ); ?>
                                        </button>
                                    </div>
                                    <div class="newsletter-privacy">
                                        <small><?php esc_html_e( 'Respeitamos sua privacidade. Sem spam.', 'nosfirnews' ); ?></small>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Social Media Widget -->
                        <div class="widget social-media-widget">
                            <h3 class="widget-title">
                                <i class="fas fa-share-alt" aria-hidden="true"></i>
                                <?php esc_html_e( 'Siga-nos', 'nosfirnews' ); ?>
                            </h3>
                            <div class="social-links">
                                <a href="#" class="social-link facebook" aria-label="Facebook">
                                    <i class="fab fa-facebook-f" aria-hidden="true"></i>
                                    <span>Facebook</span>
                                </a>
                                <a href="#" class="social-link twitter" aria-label="Twitter">
                                    <i class="fab fa-twitter" aria-hidden="true"></i>
                                    <span>Twitter</span>
                                </a>
                                <a href="#" class="social-link instagram" aria-label="Instagram">
                                    <i class="fab fa-instagram" aria-hidden="true"></i>
                                    <span>Instagram</span>
                                </a>
                                <a href="#" class="social-link youtube" aria-label="YouTube">
                                    <i class="fab fa-youtube" aria-hidden="true"></i>
                                    <span>YouTube</span>
                                </a>
                                <a href="#" class="social-link linkedin" aria-label="LinkedIn">
                                    <i class="fab fa-linkedin-in" aria-hidden="true"></i>
                                    <span>LinkedIn</span>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Regular Sidebar -->
                        <?php get_sidebar(); ?>
                        
                    </aside>
                </div>
                
            </div>
        </div>
    </div>
    
</div>

<style>
/* Front Page Styles */
.front-page {
    background: #f8f9fa;
}

/* Hero Section */
.hero-section {
    padding: 2rem 0;
    background: linear-gradient(135deg, #007cba 0%, #005a87 100%);
    margin-bottom: 2rem;
}

.hero-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    align-items: start;
}

.hero-main {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.hero-image {
    position: relative;
    height: 500px;
    overflow: hidden;
}

.hero-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.hero-image:hover img {
    transform: scale(1.05);
}

.no-thumbnail {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 4rem;
}

.hero-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
    color: white;
    padding: 3rem 2rem 2rem;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #ffc107;
    color: #333;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.hero-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    opacity: 0.9;
}

.hero-category a {
    color: #ffc107;
    text-decoration: none;
    font-weight: 500;
}

.hero-category a:hover {
    text-decoration: underline;
}

.hero-date {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.hero-title {
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 1rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
}

.hero-title a {
    color: white;
    text-decoration: none;
    transition: color 0.3s ease;
}

.hero-title a:hover {
    color: #ffc107;
}

.hero-excerpt {
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 2rem;
    opacity: 0.95;
}

.hero-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
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
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: #ffc107;
    color: #333;
}

.btn-primary:hover {
    background: #ffcd39;
    color: #333;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 193, 7, 0.4);
}

.hero-stats {
    display: flex;
    gap: 1rem;
    font-size: 0.9rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    opacity: 0.9;
}

/* Hero Secondary */
.hero-secondary {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.secondary-posts {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.secondary-post {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 1rem;
    backdrop-filter: blur(10px);
    transition: background-color 0.3s ease;
}

.secondary-post:hover {
    background: rgba(255, 255, 255, 0.15);
}

.secondary-thumbnail {
    width: 80px;
    height: 60px;
    border-radius: 4px;
    overflow: hidden;
    float: left;
    margin-right: 1rem;
    margin-bottom: 0.5rem;
}

.secondary-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.secondary-thumbnail .no-thumbnail {
    font-size: 1.5rem;
    background: rgba(255, 255, 255, 0.1);
}

.secondary-content {
    overflow: hidden;
}

.secondary-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    font-size: 0.8rem;
    opacity: 0.8;
}

.secondary-category a {
    color: #ffc107;
    text-decoration: none;
}

.secondary-title {
    font-size: 1rem;
    font-weight: 600;
    line-height: 1.3;
    margin-bottom: 0.5rem;
}

.secondary-title a {
    color: white;
    text-decoration: none;
    transition: color 0.3s ease;
}

.secondary-title a:hover {
    color: #ffc107;
}

.secondary-excerpt {
    font-size: 0.9rem;
    line-height: 1.4;
    opacity: 0.9;
}

/* Breaking News */
.breaking-news-section {
    background: #dc3545;
    color: white;
    padding: 1rem 0;
    margin-bottom: 2rem;
}

.breaking-news {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.breaking-header {
    flex-shrink: 0;
}

.breaking-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    font-size: 1.1rem;
    background: rgba(255, 255, 255, 0.2);
    padding: 8px 16px;
    border-radius: 20px;
    backdrop-filter: blur(10px);
}

.breaking-content {
    flex: 1;
    overflow: hidden;
}

.breaking-ticker {
    display: flex;
    gap: 2rem;
    animation: ticker 30s linear infinite;
}

.breaking-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    white-space: nowrap;
    flex-shrink: 0;
}

.breaking-time {
    background: rgba(255, 255, 255, 0.2);
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.8rem;
    font-weight: 500;
}

.breaking-link {
    color: white;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.breaking-link:hover {
    color: #ffc107;
}

@keyframes ticker {
    0% { transform: translateX(100%); }
    100% { transform: translateX(-100%); }
}

/* Main Content */
.main-content {
    padding: 2rem 0;
}

/* Section Headers */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 3px solid #e9ecef;
}

.section-title {
    font-size: 2rem;
    font-weight: 600;
    color: #333;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
}

.section-title i {
    color: var(--color-primary, #007cba);
}

.section-link {
    color: var(--color-primary, #007cba);
    text-decoration: none;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: color 0.3s ease;
}

.section-link:hover {
    color: var(--color-primary-dark, #005a87);
}

/* Latest News */
.latest-news-section {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.latest-posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.latest-post-item {
    background: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.latest-post-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.latest-thumbnail {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.latest-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.latest-thumbnail:hover img {
    transform: scale(1.05);
}

.latest-thumbnail .no-thumbnail {
    background: #e9ecef;
    color: #6c757d;
    font-size: 2rem;
}

.reading-time {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.latest-content {
    padding: 1.5rem;
}

.latest-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    color: #666;
}

.latest-category a {
    color: var(--color-primary, #007cba);
    text-decoration: none;
    font-weight: 500;
}

.latest-category a:hover {
    text-decoration: underline;
}

.latest-title {
    font-size: 1.2rem;
    font-weight: 600;
    line-height: 1.4;
    margin-bottom: 1rem;
}

.latest-title a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.latest-title a:hover {
    color: var(--color-primary, #007cba);
}

.latest-excerpt {
    color: #555;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.latest-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.latest-stats {
    display: flex;
    gap: 1rem;
    font-size: 0.9rem;
    color: #666;
}

.read-more-link {
    color: var(--color-primary, #007cba);
    text-decoration: none;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: color 0.3s ease;
}

.read-more-link:hover {
    color: var(--color-primary-dark, #005a87);
}

/* Categories Showcase */
.categories-showcase {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.category-showcase-item {
    position: relative;
    height: 200px;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.category-showcase-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.category-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
}

.category-bg-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.category-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(0, 124, 186, 0.8) 0%, rgba(0, 90, 135, 0.9) 100%);
}

.category-content {
    position: relative;
    z-index: 2;
    padding: 1.5rem;
    color: white;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    text-align: center;
}

.category-icon {
    font-size: 2rem;
    margin-bottom: 1rem;
    opacity: 0.9;
}

.category-name {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.category-name a {
    color: white;
    text-decoration: none;
    transition: color 0.3s ease;
}

.category-name a:hover {
    color: #ffc107;
}

.category-stats {
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    opacity: 0.9;
}

.category-description {
    font-size: 0.9rem;
    line-height: 1.4;
    margin-bottom: 1rem;
    opacity: 0.9;
}

.category-link {
    color: #ffc107;
    text-decoration: none;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: color 0.3s ease;
}

.category-link:hover {
    color: #ffcd39;
}

/* Sidebar */
.front-page-sidebar {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.widget {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.widget-title {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: #333;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e9ecef;
}

.widget-title i {
    color: var(--color-primary, #007cba);
}

/* Trending Posts */
.trending-posts {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.trending-post-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    transition: background-color 0.3s ease;
}

.trending-post-item:hover {
    background: #e9ecef;
}

.trending-number {
    background: var(--color-primary, #007cba);
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.trending-thumbnail {
    width: 60px;
    height: 45px;
    border-radius: 4px;
    overflow: hidden;
    flex-shrink: 0;
}

.trending-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.trending-thumbnail .no-thumbnail {
    background: #e9ecef;
    color: #6c757d;
    font-size: 1.2rem;
}

.trending-content {
    flex: 1;
    min-width: 0;
}

.trending-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.25rem;
    font-size: 0.8rem;
    color: #666;
}

.trending-category {
    color: var(--color-primary, #007cba);
    font-weight: 500;
}

.trending-title {
    font-size: 0.95rem;
    font-weight: 600;
    line-height: 1.3;
    margin-bottom: 0.25rem;
}

.trending-title a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.trending-title a:hover {
    color: var(--color-primary, #007cba);
}

.trending-stats {
    font-size: 0.8rem;
    color: #666;
}

/* Newsletter Widget */
.newsletter-content p {
    margin-bottom: 1rem;
    color: #555;
    line-height: 1.5;
}

.newsletter-form .form-group {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.newsletter-form input {
    flex: 1;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 0.9rem;
}

.newsletter-form button {
    padding: 10px 16px;
    font-size: 0.9rem;
}

.newsletter-privacy {
    color: #666;
    text-align: center;
}

/* Social Media Widget */
.social-links {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.social-link {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 6px;
    text-decoration: none;
    color: white;
    font-weight: 500;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.social-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    color: white;
}

.social-link.facebook { background: #1877f2; }
.social-link.twitter { background: #1da1f2; }
.social-link.instagram { background: #e4405f; }
.social-link.youtube { background: #ff0000; }
.social-link.linkedin { background: #0077b5; }

.social-link i {
    font-size: 1.2rem;
    width: 20px;
    text-align: center;
}

/* Responsive Design */
@media (max-width: 992px) {
    .hero-content {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .hero-secondary {
        order: -1;
    }
    
    .secondary-posts {
        flex-direction: row;
        overflow-x: auto;
        gap: 1rem;
        padding-bottom: 1rem;
    }
    
    .secondary-post {
        min-width: 250px;
        flex-shrink: 0;
    }
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .hero-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .breaking-news {
        flex-direction: column;
        gap: 0.5rem;
        text-align: center;
    }
    
    .breaking-ticker {
        animation: none;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .latest-posts-grid {
        grid-template-columns: 1fr;
    }
    
    .categories-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
    
    .newsletter-form .form-group {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .hero-section {
        padding: 1rem 0;
    }
    
    .hero-overlay {
        padding: 2rem 1rem 1rem;
    }
    
    .hero-title {
        font-size: 1.5rem;
    }
    
    .section-title {
        font-size: 1.5rem;
    }
    
    .widget {
        padding: 1rem;
    }
    
    .trending-post-item {
        padding: 0.75rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Newsletter form submission
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[name="newsletter_email"]').value;
            
            // Here you would typically send the email to your backend
            alert('Obrigado por se inscrever! Em breve você receberá nossas notícias.');
            this.reset();
        });
    }
    
    // Pause breaking news ticker on hover
    const breakingTicker = document.querySelector('.breaking-ticker');
    if (breakingTicker) {
        breakingTicker.addEventListener('mouseenter', function() {
            this.style.animationPlayState = 'paused';
        });
        
        breakingTicker.addEventListener('mouseleave', function() {
            this.style.animationPlayState = 'running';
        });
    }
    
    // Smooth scroll for anchor links
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
});
</script>

<?php get_footer(); ?>