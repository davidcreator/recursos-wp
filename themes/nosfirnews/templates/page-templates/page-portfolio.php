<?php
/**
 * Template Name: Portfolio
 * 
 * Advanced portfolio template with filtering and grid layouts
 *
 * @package NosfirNews
 * @since 1.0.0
 */

get_header(); ?>

<div id="primary" class="content-area portfolio-page">
    <main id="main" class="site-main">

        <?php while ( have_posts() ) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class( 'portfolio-content' ); ?>>
                
                <!-- Portfolio Header -->
                <section class="portfolio-header">
                    <div class="container">
                        <div class="portfolio-intro">
                            <h1 class="portfolio-title"><?php the_title(); ?></h1>
                            
                            <?php if ( get_the_content() ) : ?>
                                <div class="portfolio-description">
                                    <?php the_content(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>

                <!-- Portfolio Filters -->
                <?php 
                $portfolio_categories = get_terms( array(
                    'taxonomy' => 'portfolio_category',
                    'hide_empty' => true,
                ) );
                
                if ( ! empty( $portfolio_categories ) && ! is_wp_error( $portfolio_categories ) ) : ?>
                    <section class="portfolio-filters">
                        <div class="container">
                            <div class="filter-buttons">
                                <button class="filter-btn active" data-filter="*">
                                    <?php esc_html_e( 'Todos', 'nosfirnews' ); ?>
                                </button>
                                
                                <?php foreach ( $portfolio_categories as $category ) : ?>
                                    <button class="filter-btn" data-filter=".<?php echo esc_attr( $category->slug ); ?>">
                                        <?php echo esc_html( $category->name ); ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Portfolio Grid -->
                <section class="portfolio-grid-section">
                    <div class="container">
                        <?php
                        $portfolio_layout = get_post_meta( get_the_ID(), '_portfolio_layout', true );
                        $portfolio_layout = $portfolio_layout ? $portfolio_layout : 'masonry';
                        
                        $posts_per_page = get_post_meta( get_the_ID(), '_portfolio_posts_per_page', true );
                        $posts_per_page = $posts_per_page ? intval( $posts_per_page ) : 12;
                        
                        $portfolio_query = new WP_Query( array(
                            'post_type' => 'portfolio',
                            'posts_per_page' => $posts_per_page,
                            'post_status' => 'publish',
                            'orderby' => 'menu_order date',
                            'order' => 'ASC',
                        ) );
                        
                        if ( $portfolio_query->have_posts() ) : ?>
                            <div class="portfolio-grid <?php echo esc_attr( $portfolio_layout ); ?>-layout" id="portfolio-grid">
                                <?php while ( $portfolio_query->have_posts() ) : $portfolio_query->the_post(); ?>
                                    <?php
                                    $portfolio_cats = get_the_terms( get_the_ID(), 'portfolio_category' );
                                    $cat_classes = '';
                                    if ( $portfolio_cats && ! is_wp_error( $portfolio_cats ) ) {
                                        $cat_slugs = wp_list_pluck( $portfolio_cats, 'slug' );
                                        $cat_classes = implode( ' ', $cat_slugs );
                                    }
                                    ?>
                                    
                                    <div class="portfolio-item <?php echo esc_attr( $cat_classes ); ?>">
                                        <div class="portfolio-item-inner">
                                            <?php if ( has_post_thumbnail() ) : ?>
                                                <div class="portfolio-image">
                                                    <?php the_post_thumbnail( 'nosfirnews-featured', array( 'class' => 'portfolio-img' ) ); ?>
                                                    
                                                    <div class="portfolio-overlay">
                                                        <div class="portfolio-actions">
                                                            <?php 
                                                            $portfolio_gallery = get_post_meta( get_the_ID(), '_portfolio_gallery', true );
                                                            if ( $portfolio_gallery ) : ?>
                                                                <a href="<?php echo esc_url( wp_get_attachment_url( get_post_thumbnail_id() ) ); ?>" 
                                                                   class="portfolio-lightbox" 
                                                                   data-gallery="portfolio-<?php the_ID(); ?>"
                                                                   title="<?php the_title_attribute(); ?>">
                                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M21 21L16.514 16.506M19 10.5C19 15.194 15.194 19 10.5 19S2 15.194 2 10.5 5.806 2 10.5 2 19 5.806 19 10.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    </svg>
                                                                </a>
                                                            <?php endif; ?>
                                                            
                                                            <a href="<?php the_permalink(); ?>" class="portfolio-link">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M18 13V19C18 19.5304 17.7893 20.0391 17.4142 20.4142C17.0391 20.7893 16.5304 21 16 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V8C3 7.46957 3.21071 6.96086 3.58579 6.58579C3.96086 6.21071 4.46957 6 5 6H11M15 3H21V9M10 14L21 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                </svg>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="portfolio-content">
                                                <h3 class="portfolio-title">
                                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                </h3>
                                                
                                                <?php if ( $portfolio_cats && ! is_wp_error( $portfolio_cats ) ) : ?>
                                                    <div class="portfolio-categories">
                                                        <?php foreach ( $portfolio_cats as $cat ) : ?>
                                                            <span class="portfolio-category"><?php echo esc_html( $cat->name ); ?></span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php 
                                                $show_excerpt = get_post_meta( get_the_ID(), '_portfolio_show_excerpt', true );
                                                if ( $show_excerpt && has_excerpt() ) : ?>
                                                    <div class="portfolio-excerpt">
                                                        <?php the_excerpt(); ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php 
                                                $portfolio_client = get_post_meta( get_the_ID(), '_portfolio_client', true );
                                                $portfolio_date = get_post_meta( get_the_ID(), '_portfolio_date', true );
                                                if ( $portfolio_client || $portfolio_date ) : ?>
                                                    <div class="portfolio-meta">
                                                        <?php if ( $portfolio_client ) : ?>
                                                            <span class="portfolio-client">
                                                                <strong><?php esc_html_e( 'Cliente:', 'nosfirnews' ); ?></strong> 
                                                                <?php echo esc_html( $portfolio_client ); ?>
                                                            </span>
                                                        <?php endif; ?>
                                                        
                                                        <?php if ( $portfolio_date ) : ?>
                                                            <span class="portfolio-date">
                                                                <strong><?php esc_html_e( 'Data:', 'nosfirnews' ); ?></strong> 
                                                                <?php echo esc_html( $portfolio_date ); ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php 
                                    // Hidden gallery images for lightbox
                                    if ( $portfolio_gallery && is_array( $portfolio_gallery ) ) :
                                        foreach ( $portfolio_gallery as $image_id ) : ?>
                                            <a href="<?php echo esc_url( wp_get_attachment_url( $image_id ) ); ?>" 
                                               class="portfolio-lightbox-hidden" 
                                               data-gallery="portfolio-<?php the_ID(); ?>"
                                               style="display: none;">
                                            </a>
                                        <?php endforeach;
                                    endif; ?>
                                    
                                <?php endwhile; ?>
                            </div>
                            
                            <?php 
                            // Load More Button
                            $show_load_more = get_post_meta( get_the_ID(), '_portfolio_load_more', true );
                            if ( $show_load_more && $portfolio_query->max_num_pages > 1 ) : ?>
                                <div class="portfolio-load-more">
                                    <button class="btn btn-outline load-more-btn" 
                                            data-page="1" 
                                            data-max-pages="<?php echo esc_attr( $portfolio_query->max_num_pages ); ?>">
                                        <?php esc_html_e( 'Carregar Mais', 'nosfirnews' ); ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                            
                        <?php else : ?>
                            <div class="no-portfolio-items">
                                <p><?php esc_html_e( 'Nenhum item de portfolio encontrado.', 'nosfirnews' ); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php wp_reset_postdata(); ?>
                    </div>
                </section>

                <!-- Portfolio CTA Section -->
                <?php 
                $cta_title = get_post_meta( get_the_ID(), '_portfolio_cta_title', true );
                $cta_content = get_post_meta( get_the_ID(), '_portfolio_cta_content', true );
                $cta_button_text = get_post_meta( get_the_ID(), '_portfolio_cta_button_text', true );
                $cta_button_url = get_post_meta( get_the_ID(), '_portfolio_cta_button_url', true );
                
                if ( $cta_title || $cta_content ) : ?>
                    <section class="portfolio-cta">
                        <div class="container">
                            <div class="cta-content">
                                <?php if ( $cta_title ) : ?>
                                    <h2 class="cta-title"><?php echo esc_html( $cta_title ); ?></h2>
                                <?php endif; ?>
                                
                                <?php if ( $cta_content ) : ?>
                                    <div class="cta-description">
                                        <?php echo wp_kses_post( $cta_content ); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ( $cta_button_text && $cta_button_url ) : ?>
                                    <div class="cta-button">
                                        <a href="<?php echo esc_url( $cta_button_url ); ?>" class="btn btn-primary btn-lg">
                                            <?php echo esc_html( $cta_button_text ); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

            </article>

        <?php endwhile; ?>

    </main>
</div>

<?php
get_footer();
?>