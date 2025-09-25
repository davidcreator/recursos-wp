<?php
/**
 * The template for displaying category archive pages
 *
 * @package NosfirNews
 * @since 1.0.0
 */

get_header(); ?>

<div class="category-page">
    <div class="container">
        
        <!-- Category Header -->
        <div class="category-header">
            <div class="row">
                <div class="col-12">
                    <div class="category-hero">
                        
                        <!-- Category Background -->
                        <div class="category-background">
                            <?php
                            // Get category color or use default
                            $category_color = get_term_meta( get_queried_object_id(), 'category_color', true );
                            if ( ! $category_color ) {
                                $category_color = '#007cba';
                            }
                            ?>
                            <div class="category-overlay" style="background: linear-gradient(135deg, <?php echo esc_attr( $category_color ); ?>dd 0%, <?php echo esc_attr( $category_color ); ?>88 100%);"></div>
                        </div>
                        
                        <!-- Category Content -->
                        <div class="category-content">
                            <div class="category-breadcrumb">
                                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                                    <i class="fas fa-home" aria-hidden="true"></i>
                                    <?php esc_html_e( 'Início', 'nosfirnews' ); ?>
                                </a>
                                <span class="breadcrumb-separator">
                                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                                </span>
                                <span class="current-category"><?php single_cat_title(); ?></span>
                            </div>
                            
                            <h1 class="category-title">
                                <i class="fas fa-folder-open" aria-hidden="true"></i>
                                <?php single_cat_title(); ?>
                            </h1>
                            
                            <?php if ( category_description() ) : ?>
                                <div class="category-description">
                                    <?php echo wp_kses_post( category_description() ); ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Category Stats -->
                            <div class="category-stats">
                                <div class="stat-item">
                                    <i class="fas fa-file-alt" aria-hidden="true"></i>
                                    <span class="stat-number">
                                        <?php
                                        global $wp_query;
                                        echo number_format_i18n( $wp_query->found_posts );
                                        ?>
                                    </span>
                                    <span class="stat-label">
                                        <?php
                                        printf( 
                                            esc_html( _n( 'Post', 'Posts', $wp_query->found_posts, 'nosfirnews' ) )
                                        );
                                        ?>
                                    </span>
                                </div>
                                
                                <?php
                                // Get subcategories
                                $subcategories = get_categories( array(
                                    'parent' => get_queried_object_id(),
                                    'hide_empty' => true
                                ) );
                                if ( $subcategories ) :
                                ?>
                                    <div class="stat-item">
                                        <i class="fas fa-sitemap" aria-hidden="true"></i>
                                        <span class="stat-number"><?php echo count( $subcategories ); ?></span>
                                        <span class="stat-label">
                                            <?php
                                            printf( 
                                                esc_html( _n( 'Subcategoria', 'Subcategorias', count( $subcategories ), 'nosfirnews' ) )
                                            );
                                            ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Subcategories Section -->
        <?php if ( $subcategories ) : ?>
            <div class="subcategories-section">
                <div class="row">
                    <div class="col-12">
                        <h2 class="section-title">
                            <i class="fas fa-layer-group" aria-hidden="true"></i>
                            <?php esc_html_e( 'Subcategorias', 'nosfirnews' ); ?>
                        </h2>
                        <div class="subcategories-grid">
                            <?php foreach ( $subcategories as $subcategory ) : ?>
                                <div class="subcategory-item">
                                    <a href="<?php echo esc_url( get_category_link( $subcategory->term_id ) ); ?>" class="subcategory-link">
                                        <div class="subcategory-icon">
                                            <i class="fas fa-folder" aria-hidden="true"></i>
                                        </div>
                                        <div class="subcategory-info">
                                            <h3 class="subcategory-name"><?php echo esc_html( $subcategory->name ); ?></h3>
                                            <span class="subcategory-count">
                                                <?php
                                                printf( 
                                                    esc_html( _n( '%s post', '%s posts', $subcategory->count, 'nosfirnews' ) ), 
                                                    number_format_i18n( $subcategory->count ) 
                                                );
                                                ?>
                                            </span>
                                            <?php if ( $subcategory->description ) : ?>
                                                <p class="subcategory-description">
                                                    <?php echo wp_trim_words( $subcategory->description, 15, '...' ); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="subcategory-arrow">
                                            <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Category Content -->
        <div class="category-content-section">
            <div class="row">
                
                <!-- Main Content -->
                <div class="col-lg-8 col-md-12">
                    <div class="posts-container">
                        
                        <!-- Featured Posts -->
                        <?php
                        // Get featured posts for this category
                        $featured_posts = new WP_Query( array(
                            'cat' => get_queried_object_id(),
                            'posts_per_page' => 3,
                            'meta_query' => array(
                                array(
                                    'key' => '_featured_post',
                                    'value' => '1',
                                    'compare' => '='
                                )
                            )
                        ) );
                        
                        if ( $featured_posts->have_posts() ) : ?>
                            <div class="featured-posts-section">
                                <h2 class="section-title">
                                    <i class="fas fa-star" aria-hidden="true"></i>
                                    <?php esc_html_e( 'Posts em Destaque', 'nosfirnews' ); ?>
                                </h2>
                                <div class="featured-posts-grid">
                                    <?php while ( $featured_posts->have_posts() ) : $featured_posts->the_post(); ?>
                                        <article class="featured-post-item">
                                            <div class="featured-post-thumbnail">
                                                <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                                    <?php if ( has_post_thumbnail() ) : ?>
                                                        <?php the_post_thumbnail( 'large', array( 'class' => 'img-fluid' ) ); ?>
                                                    <?php else : ?>
                                                        <div class="no-thumbnail">
                                                            <i class="fas fa-image" aria-hidden="true"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </a>
                                                <div class="featured-badge">
                                                    <i class="fas fa-star" aria-hidden="true"></i>
                                                    <?php esc_html_e( 'Destaque', 'nosfirnews' ); ?>
                                                </div>
                                            </div>
                                            <div class="featured-post-content">
                                                <div class="post-meta">
                                                    <span class="post-date">
                                                        <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                                                        <time datetime="<?php echo get_the_date( 'c' ); ?>">
                                                            <?php echo get_the_date(); ?>
                                                        </time>
                                                    </span>
                                                    <span class="post-author">
                                                        <i class="fas fa-user" aria-hidden="true"></i>
                                                        <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
                                                            <?php the_author(); ?>
                                                        </a>
                                                    </span>
                                                </div>
                                                <h3 class="featured-post-title">
                                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                </h3>
                                                <div class="featured-post-excerpt">
                                                    <?php echo wp_trim_words( get_the_excerpt(), 20, '...' ); ?>
                                                </div>
                                            </div>
                                        </article>
                                    <?php endwhile; ?>
                                </div>
                                <?php wp_reset_postdata(); ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Filter Options -->
                        <div class="category-filters">
                            <div class="filter-header">
                                <h2 class="section-title">
                                    <i class="fas fa-newspaper" aria-hidden="true"></i>
                                    <?php esc_html_e( 'Todos os Posts', 'nosfirnews' ); ?>
                                </h2>
                                <div class="filter-controls">
                                    <select id="sort-posts" class="sort-select">
                                        <option value="date-desc"><?php esc_html_e( 'Mais recentes', 'nosfirnews' ); ?></option>
                                        <option value="date-asc"><?php esc_html_e( 'Mais antigos', 'nosfirnews' ); ?></option>
                                        <option value="title-asc"><?php esc_html_e( 'Título A-Z', 'nosfirnews' ); ?></option>
                                        <option value="comment-count"><?php esc_html_e( 'Mais comentados', 'nosfirnews' ); ?></option>
                                    </select>
                                    <div class="view-options">
                                        <button class="view-toggle active" data-view="grid" aria-label="<?php esc_attr_e( 'Visualização em grade', 'nosfirnews' ); ?>">
                                            <i class="fas fa-th" aria-hidden="true"></i>
                                        </button>
                                        <button class="view-toggle" data-view="list" aria-label="<?php esc_attr_e( 'Visualização em lista', 'nosfirnews' ); ?>">
                                            <i class="fas fa-list" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Posts Grid -->
                        <?php if ( have_posts() ) : ?>
                            <div class="posts-grid" id="posts-container">
                                <?php while ( have_posts() ) : the_post(); ?>
                                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'post-item' ); ?>>
                                        
                                        <!-- Post Thumbnail -->
                                        <?php if ( has_post_thumbnail() ) : ?>
                                            <div class="post-thumbnail">
                                                <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                                    <?php the_post_thumbnail( 'medium_large', array( 'class' => 'img-fluid' ) ); ?>
                                                </a>
                                                
                                                <!-- Reading Time -->
                                                <div class="reading-time">
                                                    <i class="fas fa-clock" aria-hidden="true"></i>
                                                    <?php
                                                    $content = get_post_field( 'post_content', get_the_ID() );
                                                    $word_count = str_word_count( strip_tags( $content ) );
                                                    $reading_time = ceil( $word_count / 200 );
                                                    printf( esc_html( _n( '%s min', '%s min', $reading_time, 'nosfirnews' ) ), $reading_time );
                                                    ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Post Content -->
                                        <div class="post-content">
                                            
                                            <!-- Post Meta -->
                                            <div class="post-meta">
                                                <span class="post-date">
                                                    <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                                                    <time datetime="<?php echo get_the_date( 'c' ); ?>">
                                                        <?php echo get_the_date(); ?>
                                                    </time>
                                                </span>
                                                <span class="post-author">
                                                    <i class="fas fa-user" aria-hidden="true"></i>
                                                    <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
                                                        <?php the_author(); ?>
                                                    </a>
                                                </span>
                                            </div>
                                            
                                            <!-- Post Title -->
                                            <h2 class="post-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h2>
                                            
                                            <!-- Post Excerpt -->
                                            <div class="post-excerpt">
                                                <?php echo wp_trim_words( get_the_excerpt(), 25, '...' ); ?>
                                            </div>
                                            
                                            <!-- Post Tags -->
                                            <?php
                                            $post_tags = get_the_tags();
                                            if ( $post_tags ) :
                                            ?>
                                                <div class="post-tags">
                                                    <i class="fas fa-tags" aria-hidden="true"></i>
                                                    <?php foreach ( $post_tags as $tag ) : ?>
                                                        <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="tag-link">
                                                            <?php echo esc_html( $tag->name ); ?>
                                                        </a>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <!-- Post Footer -->
                                            <div class="post-footer">
                                                <div class="post-stats">
                                                    <?php if ( comments_open() || get_comments_number() ) : ?>
                                                        <span class="post-comments">
                                                            <i class="fas fa-comments" aria-hidden="true"></i>
                                                            <a href="<?php comments_link(); ?>">
                                                                <?php
                                                                printf( 
                                                                    esc_html( _n( '%s comentário', '%s comentários', get_comments_number(), 'nosfirnews' ) ), 
                                                                    number_format_i18n( get_comments_number() ) 
                                                                );
                                                                ?>
                                                            </a>
                                                        </span>
                                                    <?php endif; ?>
                                                    
                                                    <span class="post-views">
                                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                                        <?php
                                                        $views = get_post_meta( get_the_ID(), 'post_views', true );
                                                        if ( ! $views ) $views = 0;
                                                        printf( esc_html( _n( '%s visualização', '%s visualizações', $views, 'nosfirnews' ) ), number_format_i18n( $views ) );
                                                        ?>
                                                    </span>
                                                </div>
                                                
                                                <a href="<?php the_permalink(); ?>" class="read-more-btn">
                                                    <?php esc_html_e( 'Ler mais', 'nosfirnews' ); ?>
                                                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                                </a>
                                            </div>
                                            
                                        </div>
                                    </article>
                                <?php endwhile; ?>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="category-pagination">
                                <?php
                                the_posts_pagination( array(
                                    'mid_size'  => 2,
                                    'prev_text' => '<i class="fas fa-chevron-left" aria-hidden="true"></i> ' . esc_html__( 'Anterior', 'nosfirnews' ),
                                    'next_text' => esc_html__( 'Próximo', 'nosfirnews' ) . ' <i class="fas fa-chevron-right" aria-hidden="true"></i>',
                                    'screen_reader_text' => esc_html__( 'Navegação de posts', 'nosfirnews' ),
                                ) );
                                ?>
                            </div>
                            
                        <?php else : ?>
                            
                            <!-- No Posts Found -->
                            <div class="no-posts-found">
                                <div class="no-posts-icon">
                                    <i class="fas fa-folder-open" aria-hidden="true"></i>
                                </div>
                                <h2><?php esc_html_e( 'Nenhum post encontrado', 'nosfirnews' ); ?></h2>
                                <p><?php esc_html_e( 'Esta categoria ainda não possui posts publicados.', 'nosfirnews' ); ?></p>
                                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary">
                                    <?php esc_html_e( 'Explorar outras categorias', 'nosfirnews' ); ?>
                                </a>
                            </div>
                            
                        <?php endif; ?>
                        
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4 col-md-12">
                    <aside class="category-sidebar">
                        
                        <!-- Related Categories Widget -->
                        <div class="widget related-categories-widget">
                            <h3 class="widget-title">
                                <i class="fas fa-sitemap" aria-hidden="true"></i>
                                <?php esc_html_e( 'Categorias Relacionadas', 'nosfirnews' ); ?>
                            </h3>
                            <div class="related-categories">
                                <?php
                                $related_categories = get_categories( array(
                                    'exclude' => get_queried_object_id(),
                                    'number' => 6,
                                    'orderby' => 'count',
                                    'order' => 'DESC',
                                    'hide_empty' => true
                                ) );
                                
                                foreach ( $related_categories as $category ) :
                                ?>
                                    <a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" class="related-category-item">
                                        <div class="category-info">
                                            <span class="category-name"><?php echo esc_html( $category->name ); ?></span>
                                            <span class="category-count">
                                                <?php
                                                printf( 
                                                    esc_html( _n( '%s post', '%s posts', $category->count, 'nosfirnews' ) ), 
                                                    number_format_i18n( $category->count ) 
                                                );
                                                ?>
                                            </span>
                                        </div>
                                        <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                    </a>
                                <?php endforeach; ?>
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
/* Category Page Styles */
.category-page {
    background: #f8f9fa;
    min-height: 70vh;
}

/* Category Header */
.category-header {
    margin-bottom: 2rem;
}

.category-hero {
    position: relative;
    background: linear-gradient(135deg, #007cba 0%, #005a87 100%);
    border-radius: 12px;
    overflow: hidden;
    color: white;
    min-height: 300px;
    display: flex;
    align-items: center;
}

.category-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
}

.category-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
}

.category-content {
    position: relative;
    z-index: 2;
    padding: 3rem 2rem;
    text-align: center;
    width: 100%;
}

.category-breadcrumb {
    margin-bottom: 1rem;
    font-size: 0.9rem;
    opacity: 0.9;
}

.category-breadcrumb a {
    color: white;
    text-decoration: none;
}

.category-breadcrumb a:hover {
    text-decoration: underline;
}

.breadcrumb-separator {
    margin: 0 0.5rem;
    opacity: 0.7;
}

.category-title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.category-title i {
    margin-right: 1rem;
    opacity: 0.8;
}

.category-description {
    font-size: 1.2rem;
    margin-bottom: 2rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    opacity: 0.95;
    line-height: 1.6;
}

.category-stats {
    display: flex;
    justify-content: center;
    gap: 2rem;
    flex-wrap: wrap;
}

.stat-item {
    text-align: center;
    background: rgba(255, 255, 255, 0.1);
    padding: 1rem 1.5rem;
    border-radius: 8px;
    backdrop-filter: blur(10px);
}

.stat-item i {
    display: block;
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    opacity: 0.8;
}

.stat-number {
    display: block;
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

/* Subcategories Section */
.subcategories-section {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.section-title {
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: #333;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-title i {
    color: var(--color-primary, #007cba);
}

.subcategories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.subcategory-item {
    background: #f8f9fa;
    border-radius: 8px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.subcategory-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.subcategory-link {
    display: flex;
    align-items: center;
    padding: 1.5rem;
    text-decoration: none;
    color: inherit;
    gap: 1rem;
}

.subcategory-icon {
    background: var(--color-primary, #007cba);
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.subcategory-info {
    flex: 1;
}

.subcategory-name {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: #333;
}

.subcategory-count {
    font-size: 0.9rem;
    color: #666;
    display: block;
    margin-bottom: 0.5rem;
}

.subcategory-description {
    font-size: 0.9rem;
    color: #555;
    margin: 0;
    line-height: 1.4;
}

.subcategory-arrow {
    color: var(--color-primary, #007cba);
    font-size: 1.2rem;
    transition: transform 0.3s ease;
}

.subcategory-item:hover .subcategory-arrow {
    transform: translateX(5px);
}

/* Featured Posts */
.featured-posts-section {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.featured-posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.featured-post-item {
    background: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.featured-post-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.featured-post-thumbnail {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.featured-post-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.featured-post-thumbnail:hover img {
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

.featured-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #ffc107;
    color: #333;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.featured-post-content {
    padding: 1.5rem;
}

.featured-post-title {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    line-height: 1.4;
}

.featured-post-title a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.featured-post-title a:hover {
    color: var(--color-primary, #007cba);
}

.featured-post-excerpt {
    color: #555;
    line-height: 1.5;
    font-size: 0.95rem;
}

/* Category Filters */
.category-filters {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.filter-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.filter-controls {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.sort-select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
    font-size: 0.9rem;
}

.view-options {
    display: flex;
    gap: 0.5rem;
}

.view-toggle {
    padding: 8px 12px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.view-toggle.active,
.view-toggle:hover {
    background: var(--color-primary, #007cba);
    color: white;
    border-color: var(--color-primary, #007cba);
}

/* Posts Grid */
.posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.posts-grid.list-view {
    grid-template-columns: 1fr;
}

.post-item {
    background: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.post-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.posts-grid.list-view .post-item {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.post-thumbnail {
    position: relative;
    overflow: hidden;
    height: 250px;
}

.posts-grid.list-view .post-thumbnail {
    width: 200px;
    height: 150px;
    flex-shrink: 0;
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

.post-content {
    padding: 1.5rem;
}

.posts-grid.list-view .post-content {
    padding: 1rem;
    flex: 1;
}

.post-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    color: #666;
}

.post-meta a {
    color: inherit;
    text-decoration: none;
}

.post-meta a:hover {
    color: var(--color-primary, #007cba);
}

.post-meta i {
    margin-right: 0.25rem;
}

.post-title {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 1rem;
    line-height: 1.4;
}

.posts-grid.list-view .post-title {
    font-size: 1.2rem;
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

.post-excerpt {
    color: #555;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.posts-grid.list-view .post-excerpt {
    margin-bottom: 0.5rem;
}

.post-tags {
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

.post-tags i {
    color: #666;
    margin-right: 0.5rem;
}

.tag-link {
    display: inline-block;
    background: #e9ecef;
    color: #495057;
    padding: 2px 6px;
    border-radius: 3px;
    text-decoration: none;
    font-size: 0.8rem;
    margin-right: 0.5rem;
    margin-bottom: 0.25rem;
    transition: background-color 0.3s ease;
}

.tag-link:hover {
    background: var(--color-primary, #007cba);
    color: white;
}

.post-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.post-stats {
    display: flex;
    gap: 1rem;
    font-size: 0.9rem;
    color: #666;
}

.post-stats a {
    color: inherit;
    text-decoration: none;
}

.post-stats a:hover {
    color: var(--color-primary, #007cba);
}

.post-stats i {
    margin-right: 0.25rem;
}

.read-more-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--color-primary, #007cba);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.read-more-btn:hover {
    color: var(--color-primary-dark, #005a87);
}

/* No Posts Found */
.no-posts-found {
    text-align: center;
    padding: 3rem 2rem;
    color: #666;
}

.no-posts-icon {
    font-size: 4rem;
    color: #ddd;
    margin-bottom: 1rem;
}

.no-posts-found h2 {
    font-size: 1.8rem;
    margin-bottom: 1rem;
    color: #333;
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
    background: var(--color-primary, #007cba);
    color: white;
}

.btn-primary:hover {
    background: var(--color-primary-dark, #005a87);
    color: white;
    transform: translateY(-2px);
}

/* Pagination */
.category-pagination {
    margin-top: 2rem;
    text-align: center;
}

.category-pagination .nav-links {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.category-pagination a,
.category-pagination span {
    display: inline-flex;
    align-items: center;
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    color: #333;
    transition: all 0.3s ease;
}

.category-pagination a:hover,
.category-pagination .current {
    background: var(--color-primary, #007cba);
    color: white;
    border-color: var(--color-primary, #007cba);
}

/* Sidebar */
.category-sidebar {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    height: fit-content;
    position: sticky;
    top: 2rem;
}

.widget {
    margin-bottom: 2rem;
}

.widget-title {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 1rem;
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

.related-categories {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.related-category-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 6px;
    text-decoration: none;
    color: inherit;
    transition: background-color 0.3s ease;
}

.related-category-item:hover {
    background: #e9ecef;
}

.category-info {
    flex: 1;
}

.category-name {
    font-weight: 500;
    color: #333;
    display: block;
    margin-bottom: 0.25rem;
}

.category-count {
    font-size: 0.9rem;
    color: #666;
}

.related-category-item i {
    color: var(--color-primary, #007cba);
    transition: transform 0.3s ease;
}

.related-category-item:hover i {
    transform: translateX(3px);
}

/* Responsive Design */
@media (max-width: 768px) {
    .category-title {
        font-size: 2rem;
    }
    
    .category-stats {
        gap: 1rem;
    }
    
    .stat-item {
        padding: 0.75rem 1rem;
    }
    
    .filter-header {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-controls {
        justify-content: space-between;
    }
    
    .posts-grid {
        grid-template-columns: 1fr;
    }
    
    .posts-grid.list-view .post-item {
        flex-direction: column;
    }
    
    .posts-grid.list-view .post-thumbnail {
        width: 100%;
        height: 200px;
    }
    
    .subcategories-grid {
        grid-template-columns: 1fr;
    }
    
    .featured-posts-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .category-content {
        padding: 2rem 1rem;
    }
    
    .category-title {
        font-size: 1.8rem;
    }
    
    .category-stats {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .subcategories-section,
    .featured-posts-section,
    .category-filters,
    .category-sidebar {
        padding: 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // View toggle functionality
    const viewToggles = document.querySelectorAll('.view-toggle');
    const postsContainer = document.getElementById('posts-container');
    
    viewToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            viewToggles.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            const view = this.dataset.view;
            if (view === 'list') {
                postsContainer.classList.add('list-view');
            } else {
                postsContainer.classList.remove('list-view');
            }
        });
    });
    
    // Sort functionality
    const sortSelect = document.getElementById('sort-posts');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const sortValue = this.value;
            const url = new URL(window.location);
            
            switch(sortValue) {
                case 'date-desc':
                    url.searchParams.set('orderby', 'date');
                    url.searchParams.set('order', 'DESC');
                    break;
                case 'date-asc':
                    url.searchParams.set('orderby', 'date');
                    url.searchParams.set('order', 'ASC');
                    break;
                case 'title-asc':
                    url.searchParams.set('orderby', 'title');
                    url.searchParams.set('order', 'ASC');
                    break;
                case 'comment-count':
                    url.searchParams.set('orderby', 'comment_count');
                    url.searchParams.set('order', 'DESC');
                    break;
            }
            
            window.location.href = url.toString();
        });
    }
});
</script>

<?php get_footer(); ?>