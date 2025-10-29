<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package NosfirNews
 * @since 2.0.0
 */

get_header(); ?>

<div class="site-content-wrapper">
    <div class="content-layout">
        <main id="main" class="site-main" role="main" aria-label="<?php esc_attr_e( 'Archive page', 'nosfirnews' ); ?>">
            
            <?php if ( have_posts() ) : ?>
                
                <header class="page-header archive-header" role="banner" itemscope itemtype="https://schema.org/CollectionPage">
                    <?php
                    // Get archive information
                    $archive_title = get_the_archive_title();
                    $archive_description = get_the_archive_description();
                    $archive_type = '';
                    $archive_object = get_queried_object();
                    
                    // Determine archive type for microdata
                    if ( is_category() ) {
                        $archive_type = 'CategoryPage';
                    } elseif ( is_tag() ) {
                        $archive_type = 'TagPage';
                    } elseif ( is_author() ) {
                        $archive_type = 'ProfilePage';
                    } elseif ( is_date() ) {
                        $archive_type = 'CollectionPage';
                    } else {
                        $archive_type = 'CollectionPage';
                    }
                    ?>
                    
                    <div class="archive-header-content" itemscope itemtype="https://schema.org/<?php echo esc_attr( $archive_type ); ?>">
                        <?php if ( $archive_title ) : ?>
                            <h1 class="page-title archive-title" itemprop="name"><?php echo wp_kses_post( $archive_title ); ?></h1>
                        <?php endif; ?>
                        
                        <?php if ( $archive_description ) : ?>
                            <div class="archive-description" itemprop="description"><?php echo wp_kses_post( $archive_description ); ?></div>
                        <?php endif; ?>
                        
                        <!-- Archive meta information -->
                        <div class="archive-meta">
                            <?php
                            global $wp_query;
                            $total_posts = $wp_query->found_posts;
                            $posts_per_page = get_option( 'posts_per_page' );
                            $current_page = max( 1, get_query_var( 'paged' ) );
                            $total_pages = ceil( $total_posts / $posts_per_page );
                            ?>
                            
                            <div class="archive-stats">
                                <span class="posts-count">
                                    <?php
                                    /* translators: %d: number of posts */
                                    printf( 
                                        esc_html( _n( '%d post', '%d posts', $total_posts, 'nosfirnews' ) ), 
                                        number_format_i18n( $total_posts ) 
                                    );
                                    ?>
                                </span>
                                
                                <?php if ( $total_pages > 1 ) : ?>
                                    <span class="page-info">
                                        <?php
                                        /* translators: %1$d: current page, %2$d: total pages */
                                        printf( 
                                            esc_html__( 'Page %1$d of %2$d', 'nosfirnews' ), 
                                            $current_page, 
                                            $total_pages 
                                        );
                                        ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Archive actions -->
                            <div class="archive-actions">
                                <?php if ( is_category() || is_tag() ) : ?>
                                    <div class="archive-feed">
                                        <a href="<?php echo esc_url( get_term_feed_link( $archive_object->term_id, $archive_object->taxonomy ) ); ?>" 
                                           class="feed-link" 
                                           aria-label="<?php esc_attr_e( 'Subscribe to RSS feed', 'nosfirnews' ); ?>"
                                           title="<?php esc_attr_e( 'Subscribe to RSS feed', 'nosfirnews' ); ?>">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path d="M4 11C6.38695 11 8.67613 11.9482 10.364 13.636C12.0518 15.3239 13 17.6131 13 20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M4 4C8.24346 4 12.3131 5.68571 15.3137 8.68629C18.3143 11.6869 20 15.7565 20 20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <circle cx="5" cy="19" r="1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <?php esc_html_e( 'RSS Feed', 'nosfirnews' ); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Sort options -->
                                <div class="archive-sort">
                                    <label for="archive-sort-select" class="screen-reader-text"><?php esc_html_e( 'Sort posts by', 'nosfirnews' ); ?></label>
                                    <select id="archive-sort-select" class="archive-sort-select" onchange="location = this.value;">
                                        <option value="<?php echo esc_url( remove_query_arg( 'orderby' ) ); ?>" <?php selected( ! get_query_var( 'orderby' ) ); ?>>
                                            <?php esc_html_e( 'Latest first', 'nosfirnews' ); ?>
                                        </option>
                                        <option value="<?php echo esc_url( add_query_arg( 'orderby', 'title' ) ); ?>" <?php selected( get_query_var( 'orderby' ), 'title' ); ?>>
                                            <?php esc_html_e( 'Alphabetical', 'nosfirnews' ); ?>
                                        </option>
                                        <option value="<?php echo esc_url( add_query_arg( 'orderby', 'comment_count' ) ); ?>" <?php selected( get_query_var( 'orderby' ), 'comment_count' ); ?>>
                                            <?php esc_html_e( 'Most commented', 'nosfirnews' ); ?>
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hidden microdata -->
                        <div style="display: none;">
                            <span itemprop="url"><?php echo esc_url( get_pagenum_link() ); ?></span>
                            <?php if ( is_category() && $archive_object ) : ?>
                                <span itemprop="about" itemscope itemtype="https://schema.org/Thing">
                                    <span itemprop="name"><?php echo esc_html( $archive_object->name ); ?></span>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </header><!-- .page-header -->
                
                <section class="archive-content" role="region" aria-labelledby="archive-title">
                    <div class="posts-grid" role="list" aria-label="<?php esc_attr_e( 'Archive posts', 'nosfirnews' ); ?>">
                        <?php
                        // Start the Loop.
                        while ( have_posts() ) :
                            the_post();
                            
                            echo '<div role="listitem">';
                            get_template_part( 'template-parts/content/content', get_post_type() );
                            echo '</div>';
                            
                        endwhile;
                        ?>
                    </div>
                    
                    <?php get_template_part( 'template-parts/components/pagination' ); ?>
                </section>
                
                <!-- Archive structured data -->
                <script type="application/ld+json">
                {
                    "@context": "https://schema.org",
                    "@type": "<?php echo esc_js( $archive_type ); ?>",
                    "url": "<?php echo esc_url( get_pagenum_link() ); ?>",
                    "name": "<?php echo esc_js( wp_strip_all_tags( $archive_title ) ); ?>",
                    <?php if ( $archive_description ) : ?>
                    "description": "<?php echo esc_js( wp_strip_all_tags( $archive_description ) ); ?>",
                    <?php endif; ?>
                    "mainEntity": {
                        "@type": "ItemList",
                        "numberOfItems": <?php echo esc_js( $total_posts ); ?>,
                        "itemListElement": [
                            <?php
                            $items = array();
                            $position = ( $current_page - 1 ) * $posts_per_page;
                            while ( have_posts() ) :
                                the_post();
                                $position++;
                                $items[] = sprintf(
                                    '{
                                        "@type": "ListItem",
                                        "position": %d,
                                        "url": "%s",
                                        "name": "%s"
                                    }',
                                    $position,
                                    esc_url( get_permalink() ),
                                    esc_js( get_the_title() )
                                );
                            endwhile;
                            echo implode( ',', $items );
                            wp_reset_postdata();
                            ?>
                        ]
                    }
                }
                </script>
                
            <?php else : ?>
                
                <section class="no-posts-found" role="region" aria-labelledby="no-posts-title">
                    <header class="page-header">
                        <h1 id="no-posts-title" class="page-title">
                            <?php esc_html_e( 'Nothing found', 'nosfirnews' ); ?>
                        </h1>
                    </header>
                    
                    <?php get_template_part( 'template-parts/content/content', 'none' ); ?>
                </section>
                
            <?php endif; ?>
            
        </main><!-- #main -->
        
        <?php get_sidebar(); ?>
        
    </div><!-- .content-layout -->
</div><!-- .site-content-wrapper -->

<?php
get_footer();