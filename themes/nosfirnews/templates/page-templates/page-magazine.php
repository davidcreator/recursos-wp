<?php
/**
 * Template Name: Magazine Layout
 * 
 * Advanced magazine-style layout with featured sections and complex grids
 *
 * @package NosfirNews
 * @since 1.0.0
 */

get_header(); ?>

<div id="primary" class="content-area magazine-page">
    <main id="main" class="site-main">

        <?php while ( have_posts() ) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class( 'magazine-content' ); ?>>
                
                <!-- Magazine Header -->
                <section class="magazine-header">
                    <div class="container">
                        <div class="magazine-intro">
                            <h1 class="magazine-title"><?php the_title(); ?></h1>
                            
                            <?php if ( get_the_content() ) : ?>
                                <div class="magazine-description">
                                    <?php the_content(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>

                <!-- Breaking News Ticker -->
                <?php
                $breaking_news = new WP_Query( array(
                    'post_type' => 'post',
                    'posts_per_page' => 5,
                    'meta_query' => array(
                        array(
                            'key' => '_breaking_news',
                            'value' => '1',
                            'compare' => '='
                        )
                    )
                ) );
                
                if ( $breaking_news->have_posts() ) : ?>
                    <section class="breaking-news">
                        <div class="container">
                            <div class="breaking-news-ticker">
                                <div class="breaking-label">
                                    <span><?php esc_html_e( 'Últimas Notícias', 'nosfirnews' ); ?></span>
                                </div>
                                <div class="breaking-content">
                                    <div class="breaking-slider">
                                        <?php while ( $breaking_news->have_posts() ) : $breaking_news->the_post(); ?>
                                            <div class="breaking-item">
                                                <a href="<?php the_permalink(); ?>">
                                                    <?php the_title(); ?>
                                                </a>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <?php wp_reset_postdata(); ?>
                <?php endif; ?>

                <!-- Featured Stories -->
                <section class="featured-stories">
                    <div class="container">
                        <?php
                        $featured_posts = new WP_Query( array(
                            'post_type' => 'post',
                            'posts_per_page' => 5,
                            'meta_query' => array(
                                array(
                                    'key' => '_featured_post',
                                    'value' => '1',
                                    'compare' => '='
                                )
                            )
                        ) );
                        
                        if ( $featured_posts->have_posts() ) : ?>
                            <div class="featured-grid">
                                <?php 
                                $post_count = 0;
                                while ( $featured_posts->have_posts() ) : $featured_posts->the_post(); 
                                    $post_count++;
                                    $item_class = $post_count === 1 ? 'featured-main' : 'featured-secondary';
                                ?>
                                    <article class="featured-item <?php echo esc_attr( $item_class ); ?>">
                                        <?php if ( has_post_thumbnail() ) : ?>
                                            <div class="featured-image">
                                                <a href="<?php the_permalink(); ?>">
                                                    <?php 
                                                    $image_size = $post_count === 1 ? 'large' : 'nosfirnews-featured';
                                                    the_post_thumbnail( $image_size, array( 'class' => 'featured-img' ) ); 
                                                    ?>
                                                </a>
                                                
                                                <div class="featured-overlay">
                                                    <?php
                                                    $categories = get_the_category();
                                                    if ( ! empty( $categories ) ) :
                                                    ?>
                                                        <div class="featured-category">
                                                            <a href="<?php echo esc_url( get_category_link( $categories[0]->term_id ) ); ?>">
                                                                <?php echo esc_html( $categories[0]->name ); ?>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="featured-content">
                                            <h2 class="featured-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h2>
                                            
                                            <?php if ( $post_count === 1 ) : ?>
                                                <div class="featured-excerpt">
                                                    <?php 
                                                    if ( has_excerpt() ) {
                                                        the_excerpt();
                                                    } else {
                                                        echo wp_trim_words( get_the_content(), 30, '...' );
                                                    }
                                                    ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="featured-meta">
                                                <span class="featured-author"><?php the_author(); ?></span>
                                                <span class="featured-date"><?php echo get_the_date(); ?></span>
                                                <?php if ( comments_open() || get_comments_number() ) : ?>
                                                    <span class="featured-comments">
                                                        <?php comments_number( '0', '1', '%' ); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </article>
                                <?php endwhile; ?>
                            </div>
                            <?php wp_reset_postdata(); ?>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Category Sections -->
                <section class="category-sections">
                    <div class="container">
                        <?php
                        $magazine_categories = get_post_meta( get_the_ID(), '_magazine_categories', true );
                        if ( ! $magazine_categories ) {
                            $magazine_categories = get_categories( array( 'number' => 4, 'hide_empty' => true ) );
                        }
                        
                        if ( $magazine_categories ) :
                            foreach ( $magazine_categories as $category ) :
                                $cat_id = is_object( $category ) ? $category->term_id : $category;
                                $cat_obj = get_term( $cat_id, 'category' );
                                
                                if ( ! $cat_obj || is_wp_error( $cat_obj ) ) continue;
                                
                                $cat_posts = new WP_Query( array(
                                    'post_type' => 'post',
                                    'posts_per_page' => 4,
                                    'cat' => $cat_id,
                                    'post_status' => 'publish'
                                ) );
                                
                                if ( $cat_posts->have_posts() ) :
                        ?>
                            <div class="category-section">
                                <div class="category-header">
                                    <h2 class="category-title">
                                        <a href="<?php echo esc_url( get_category_link( $cat_id ) ); ?>">
                                            <?php echo esc_html( $cat_obj->name ); ?>
                                        </a>
                                    </h2>
                                    <a href="<?php echo esc_url( get_category_link( $cat_id ) ); ?>" class="view-all">
                                        <?php esc_html_e( 'Ver Todos', 'nosfirnews' ); ?>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>
                                </div>
                                
                                <div class="category-posts">
                                    <?php 
                                    $post_index = 0;
                                    while ( $cat_posts->have_posts() ) : $cat_posts->the_post(); 
                                        $post_index++;
                                        $post_class = $post_index === 1 ? 'category-main' : 'category-secondary';
                                    ?>
                                        <article class="category-post <?php echo esc_attr( $post_class ); ?>">
                                            <?php if ( has_post_thumbnail() ) : ?>
                                                <div class="category-image">
                                                    <a href="<?php the_permalink(); ?>">
                                                        <?php 
                                                        $image_size = $post_index === 1 ? 'nosfirnews-featured' : 'nosfirnews-medium';
                                                        the_post_thumbnail( $image_size, array( 'class' => 'category-img' ) ); 
                                                        ?>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="category-content">
                                                <h3 class="category-post-title">
                                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                </h3>
                                                
                                                <?php if ( $post_index === 1 ) : ?>
                                                    <div class="category-excerpt">
                                                        <?php 
                                                        if ( has_excerpt() ) {
                                                            the_excerpt();
                                                        } else {
                                                            echo wp_trim_words( get_the_content(), 15, '...' );
                                                        }
                                                        ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <div class="category-meta">
                                                    <span class="category-date"><?php echo get_the_date(); ?></span>
                                                    <?php if ( comments_open() || get_comments_number() ) : ?>
                                                        <span class="category-comments">
                                                            <?php comments_number( '0', '1', '%' ); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </article>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                            <?php 
                                wp_reset_postdata();
                                endif;
                            endforeach;
                        endif; 
                        ?>
                    </div>
                </section>

                <!-- Trending Posts -->
                <section class="trending-posts">
                    <div class="container">
                        <div class="section-header">
                            <h2 class="section-title"><?php esc_html_e( 'Em Alta', 'nosfirnews' ); ?></h2>
                        </div>
                        
                        <?php
                        $trending_posts = new WP_Query( array(
                            'post_type' => 'post',
                            'posts_per_page' => 6,
                            'orderby' => 'comment_count',
                            'order' => 'DESC',
                            'date_query' => array(
                                array(
                                    'after' => '1 week ago'
                                )
                            )
                        ) );
                        
                        if ( $trending_posts->have_posts() ) : ?>
                            <div class="trending-grid">
                                <?php while ( $trending_posts->have_posts() ) : $trending_posts->the_post(); ?>
                                    <article class="trending-item">
                                        <?php if ( has_post_thumbnail() ) : ?>
                                            <div class="trending-image">
                                                <a href="<?php the_permalink(); ?>">
                                                    <?php the_post_thumbnail( 'nosfirnews-medium', array( 'class' => 'trending-img' ) ); ?>
                                                </a>
                                                
                                                <div class="trending-badge">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M13 2L3 14H12L11 22L21 10H12L13 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="trending-content">
                                            <h3 class="trending-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h3>
                                            
                                            <div class="trending-meta">
                                                <span class="trending-date"><?php echo human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' ' . __( 'atrás', 'nosfirnews' ); ?></span>
                                                <span class="trending-views">
                                                    <?php 
                                                    $views = get_post_meta( get_the_ID(), '_post_views', true );
                                                    if ( $views ) {
                                                        echo number_format( $views ) . ' ' . __( 'visualizações', 'nosfirnews' );
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                    </article>
                                <?php endwhile; ?>
                            </div>
                            <?php wp_reset_postdata(); ?>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Newsletter Signup -->
                <?php 
                $newsletter_title = get_post_meta( get_the_ID(), '_magazine_newsletter_title', true );
                $newsletter_content = get_post_meta( get_the_ID(), '_magazine_newsletter_content', true );
                $newsletter_form = get_post_meta( get_the_ID(), '_magazine_newsletter_form', true );
                
                if ( $newsletter_form ) : ?>
                    <section class="newsletter-section">
                        <div class="container">
                            <div class="newsletter-content">
                                <?php if ( $newsletter_title ) : ?>
                                    <h2 class="newsletter-title"><?php echo esc_html( $newsletter_title ); ?></h2>
                                <?php endif; ?>
                                
                                <?php if ( $newsletter_content ) : ?>
                                    <div class="newsletter-description">
                                        <?php echo wp_kses_post( $newsletter_content ); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="newsletter-form">
                                    <?php echo do_shortcode( $newsletter_form ); ?>
                                </div>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Latest Posts -->
                <section class="latest-posts">
                    <div class="container">
                        <div class="section-header">
                            <h2 class="section-title"><?php esc_html_e( 'Últimas Publicações', 'nosfirnews' ); ?></h2>
                        </div>
                        
                        <?php
                        $latest_posts = new WP_Query( array(
                            'post_type' => 'post',
                            'posts_per_page' => 8,
                            'orderby' => 'date',
                            'order' => 'DESC'
                        ) );
                        
                        if ( $latest_posts->have_posts() ) : ?>
                            <div class="latest-grid">
                                <?php while ( $latest_posts->have_posts() ) : $latest_posts->the_post(); ?>
                                    <article class="latest-item">
                                        <?php if ( has_post_thumbnail() ) : ?>
                                            <div class="latest-image">
                                                <a href="<?php the_permalink(); ?>">
                                                    <?php the_post_thumbnail( 'nosfirnews-small', array( 'class' => 'latest-img' ) ); ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="latest-content">
                                            <?php
                                            $categories = get_the_category();
                                            if ( ! empty( $categories ) ) :
                                            ?>
                                                <div class="latest-category">
                                                    <a href="<?php echo esc_url( get_category_link( $categories[0]->term_id ) ); ?>">
                                                        <?php echo esc_html( $categories[0]->name ); ?>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <h3 class="latest-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h3>
                                            
                                            <div class="latest-meta">
                                                <span class="latest-author"><?php the_author(); ?></span>
                                                <span class="latest-date"><?php echo get_the_date(); ?></span>
                                            </div>
                                        </div>
                                    </article>
                                <?php endwhile; ?>
                            </div>
                            <?php wp_reset_postdata(); ?>
                        <?php endif; ?>
                    </div>
                </section>

            </article>

        <?php endwhile; ?>

    </main>
</div>

<?php
get_footer();
?>