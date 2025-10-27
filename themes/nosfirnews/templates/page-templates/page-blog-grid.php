<?php
/**
 * Template Name: Blog Grid
 * 
 * Advanced blog grid template with filtering and multiple layouts
 *
 * @package NosfirNews
 * @since 1.0.0
 */

get_header(); ?>

<div id="primary" class="content-area blog-grid-page">
    <main id="main" class="site-main">

        <?php while ( have_posts() ) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class( 'blog-grid-content' ); ?>>
                
                <!-- Blog Header -->
                <section class="blog-header">
                    <div class="container">
                        <div class="blog-intro">
                            <h1 class="blog-title"><?php the_title(); ?></h1>
                            
                            <?php if ( get_the_content() ) : ?>
                                <div class="blog-description">
                                    <?php the_content(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>

                <!-- Blog Filters -->
                <section class="blog-filters">
                    <div class="container">
                        <div class="filter-controls">
                            <!-- Category Filter -->
                            <div class="filter-group">
                                <label for="category-filter"><?php esc_html_e( 'Categoria:', 'nosfirnews' ); ?></label>
                                <select id="category-filter" class="filter-select">
                                    <option value=""><?php esc_html_e( 'Todas as Categorias', 'nosfirnews' ); ?></option>
                                    <?php
                                    $categories = get_categories( array( 'hide_empty' => true ) );
                                    foreach ( $categories as $category ) :
                                    ?>
                                        <option value="<?php echo esc_attr( $category->term_id ); ?>">
                                            <?php echo esc_html( $category->name ); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Tag Filter -->
                            <div class="filter-group">
                                <label for="tag-filter"><?php esc_html_e( 'Tag:', 'nosfirnews' ); ?></label>
                                <select id="tag-filter" class="filter-select">
                                    <option value=""><?php esc_html_e( 'Todas as Tags', 'nosfirnews' ); ?></option>
                                    <?php
                                    $tags = get_tags( array( 'hide_empty' => true ) );
                                    foreach ( $tags as $tag ) :
                                    ?>
                                        <option value="<?php echo esc_attr( $tag->term_id ); ?>">
                                            <?php echo esc_html( $tag->name ); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Date Filter -->
                            <div class="filter-group">
                                <label for="date-filter"><?php esc_html_e( 'Data:', 'nosfirnews' ); ?></label>
                                <select id="date-filter" class="filter-select">
                                    <option value=""><?php esc_html_e( 'Todas as Datas', 'nosfirnews' ); ?></option>
                                    <option value="today"><?php esc_html_e( 'Hoje', 'nosfirnews' ); ?></option>
                                    <option value="week"><?php esc_html_e( 'Esta Semana', 'nosfirnews' ); ?></option>
                                    <option value="month"><?php esc_html_e( 'Este Mês', 'nosfirnews' ); ?></option>
                                    <option value="year"><?php esc_html_e( 'Este Ano', 'nosfirnews' ); ?></option>
                                </select>
                            </div>

                            <!-- Layout Toggle -->
                            <div class="layout-toggle">
                                <button class="layout-btn active" data-layout="grid" title="<?php esc_attr_e( 'Grid', 'nosfirnews' ); ?>">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="3" y="3" width="7" height="7" stroke="currentColor" stroke-width="2"/>
                                        <rect x="14" y="3" width="7" height="7" stroke="currentColor" stroke-width="2"/>
                                        <rect x="3" y="14" width="7" height="7" stroke="currentColor" stroke-width="2"/>
                                        <rect x="14" y="14" width="7" height="7" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                </button>
                                <button class="layout-btn" data-layout="list" title="<?php esc_attr_e( 'Lista', 'nosfirnews' ); ?>">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <line x1="8" y1="6" x2="21" y2="6" stroke="currentColor" stroke-width="2"/>
                                        <line x1="8" y1="12" x2="21" y2="12" stroke="currentColor" stroke-width="2"/>
                                        <line x1="8" y1="18" x2="21" y2="18" stroke="currentColor" stroke-width="2"/>
                                        <line x1="3" y1="6" x2="3.01" y2="6" stroke="currentColor" stroke-width="2"/>
                                        <line x1="3" y1="12" x2="3.01" y2="12" stroke="currentColor" stroke-width="2"/>
                                        <line x1="3" y1="18" x2="3.01" y2="18" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                </button>
                                <button class="layout-btn" data-layout="masonry" title="<?php esc_attr_e( 'Masonry', 'nosfirnews' ); ?>">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="3" y="3" width="7" height="5" stroke="currentColor" stroke-width="2"/>
                                        <rect x="14" y="3" width="7" height="9" stroke="currentColor" stroke-width="2"/>
                                        <rect x="3" y="12" width="7" height="9" stroke="currentColor" stroke-width="2"/>
                                        <rect x="14" y="16" width="7" height="5" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Search -->
                            <div class="search-group">
                                <input type="search" id="blog-search" placeholder="<?php esc_attr_e( 'Buscar posts...', 'nosfirnews' ); ?>" class="search-input">
                                <button type="button" class="search-btn">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                                        <path d="m21 21-4.35-4.35" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Blog Grid -->
                <section class="blog-grid-section">
                    <div class="container">
                        <div class="blog-results-info">
                            <span class="results-count"></span>
                            <div class="loading-indicator" style="display: none;">
                                <span><?php esc_html_e( 'Carregando...', 'nosfirnews' ); ?></span>
                            </div>
                        </div>

                        <?php
                        $grid_layout = get_post_meta( get_the_ID(), '_blog_grid_layout', true );
                        $grid_layout = $grid_layout ? $grid_layout : 'grid';
                        
                        $posts_per_page = get_post_meta( get_the_ID(), '_blog_posts_per_page', true );
                        $posts_per_page = $posts_per_page ? intval( $posts_per_page ) : 12;
                        
                        $blog_query = new WP_Query( array(
                            'post_type' => 'post',
                            'posts_per_page' => $posts_per_page,
                            'post_status' => 'publish',
                            'orderby' => 'date',
                            'order' => 'DESC',
                        ) );
                        
                        if ( $blog_query->have_posts() ) : ?>
                            <div class="blog-grid <?php echo esc_attr( $grid_layout ); ?>-layout" id="blog-grid">
                                <?php while ( $blog_query->have_posts() ) : $blog_query->the_post(); ?>
                                    
                                    <article class="blog-item" data-categories="<?php echo esc_attr( implode( ',', wp_get_post_categories( get_the_ID(), array( 'fields' => 'ids' ) ) ) ); ?>" data-tags="<?php echo esc_attr( implode( ',', wp_get_post_tags( get_the_ID(), array( 'fields' => 'ids' ) ) ) ); ?>" data-date="<?php echo esc_attr( get_the_date( 'Y-m-d' ) ); ?>">
                                        <div class="blog-item-inner">
                                            <?php if ( has_post_thumbnail() ) : ?>
                                                <div class="blog-image">
                                                    <a href="<?php the_permalink(); ?>">
                                                        <?php the_post_thumbnail( 'nosfirnews-featured', array( 'class' => 'blog-img' ) ); ?>
                                                    </a>
                                                    
                                                    <div class="blog-overlay">
                                                        <div class="blog-meta-overlay">
                                                            <span class="blog-date"><?php echo get_the_date(); ?></span>
                                                            <?php if ( comments_open() || get_comments_number() ) : ?>
                                                                <span class="blog-comments">
                                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    </svg>
                                                                    <?php comments_number( '0', '1', '%' ); ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="blog-content">
                                                <div class="blog-meta">
                                                    <?php
                                                    $categories = get_the_category();
                                                    if ( ! empty( $categories ) ) :
                                                    ?>
                                                        <div class="blog-categories">
                                                            <?php foreach ( $categories as $category ) : ?>
                                                                <a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" class="blog-category">
                                                                    <?php echo esc_html( $category->name ); ?>
                                                                </a>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <div class="blog-author">
                                                        <span><?php esc_html_e( 'Por', 'nosfirnews' ); ?> <?php the_author(); ?></span>
                                                    </div>
                                                </div>
                                                
                                                <h2 class="blog-title">
                                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                </h2>
                                                
                                                <div class="blog-excerpt">
                                                    <?php 
                                                    if ( has_excerpt() ) {
                                                        the_excerpt();
                                                    } else {
                                                        echo wp_trim_words( get_the_content(), 20, '...' );
                                                    }
                                                    ?>
                                                </div>
                                                
                                                <div class="blog-footer">
                                                    <a href="<?php the_permalink(); ?>" class="read-more">
                                                        <?php esc_html_e( 'Leia Mais', 'nosfirnews' ); ?>
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </a>
                                                    
                                                    <?php
                                                    $tags = get_the_tags();
                                                    if ( $tags ) :
                                                    ?>
                                                        <div class="blog-tags">
                                                            <?php foreach ( $tags as $tag ) : ?>
                                                                <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="blog-tag">
                                                                    #<?php echo esc_html( $tag->name ); ?>
                                                                </a>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                    
                                <?php endwhile; ?>
                            </div>
                            
                            <?php 
                            // Pagination
                            if ( $blog_query->max_num_pages > 1 ) : ?>
                                <div class="blog-pagination">
                                    <?php
                                    echo paginate_links( array(
                                        'total' => $blog_query->max_num_pages,
                                        'current' => max( 1, get_query_var( 'paged' ) ),
                                        'format' => '?paged=%#%',
                                        'show_all' => false,
                                        'type' => 'list',
                                        'end_size' => 2,
                                        'mid_size' => 1,
                                        'prev_next' => true,
                                        'prev_text' => sprintf( '<span>%1$s</span>', __( 'Anterior', 'nosfirnews' ) ),
                                        'next_text' => sprintf( '<span>%1$s</span>', __( 'Próximo', 'nosfirnews' ) ),
                                        'add_args' => false,
                                        'add_fragment' => '',
                                    ) );
                                    ?>
                                </div>
                            <?php endif; ?>
                            
                        <?php else : ?>
                            <div class="no-blog-posts">
                                <p><?php esc_html_e( 'Nenhum post encontrado.', 'nosfirnews' ); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php wp_reset_postdata(); ?>
                    </div>
                </section>

            </article>

        <?php endwhile; ?>

    </main>
</div>

<?php
get_footer();
?>