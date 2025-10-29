<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package NosfirNews
 * @since 2.0.0
 */

get_header(); ?>

<div class="site-content-wrapper">
    <div class="content-layout">
        <main id="main" class="site-main" role="main" aria-label="<?php esc_attr_e( 'Search results', 'nosfirnews' ); ?>">
            
            <?php if ( have_posts() ) : ?>
                
                <header class="page-header search-header" role="banner">
                    <div class="search-results-info">
                        <h1 class="page-title" id="search-title">
                            <?php
                            /* translators: %s: search query. */
                            printf( esc_html__( 'Search Results for: %s', 'nosfirnews' ), '<span class="search-query">' . get_search_query() . '</span>' );
                            ?>
                        </h1>
                        
                        <div class="search-meta">
                            <p class="search-results-count">
                                <?php
                                global $wp_query;
                                $total_results = $wp_query->found_posts;
                                /* translators: %d: number of search results */
                                printf( 
                                    esc_html( _n( 'Found %d result', 'Found %d results', $total_results, 'nosfirnews' ) ), 
                                    number_format_i18n( $total_results ) 
                                );
                                ?>
                            </p>
                            
                            <!-- Search refinement -->
                            <div class="search-refinement">
                                <details class="search-filters">
                                    <summary><?php esc_html_e( 'Refine your search', 'nosfirnews' ); ?></summary>
                                    <div class="search-filters-content">
                                        <?php get_search_form(); ?>
                                        
                                        <!-- Search suggestions -->
                                        <?php if ( strlen( get_search_query() ) > 3 ) : ?>
                                            <div class="search-suggestions">
                                                <h3><?php esc_html_e( 'Search in categories', 'nosfirnews' ); ?></h3>
                                                <div class="category-filters">
                                                    <?php
                                                    $categories = get_categories( array(
                                                        'orderby' => 'count',
                                                        'order'   => 'DESC',
                                                        'number'  => 6,
                                                    ) );
                                                    
                                                    foreach ( $categories as $category ) :
                                                        $search_url = add_query_arg( array(
                                                            's' => get_search_query(),
                                                            'cat' => $category->term_id,
                                                        ), home_url( '/' ) );
                                                    ?>
                                                        <a href="<?php echo esc_url( $search_url ); ?>" class="category-filter">
                                                            <?php echo esc_html( $category->name ); ?>
                                                            <span class="count">(<?php echo esc_html( $category->count ); ?>)</span>
                                                        </a>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </details>
                            </div>
                        </div>
                    </div>
                </header><!-- .page-header -->
                
                <section class="search-results" role="region" aria-labelledby="search-title">
                    <div class="posts-grid" role="list" aria-label="<?php esc_attr_e( 'Search results', 'nosfirnews' ); ?>">
                        <?php
                        // Start the Loop.
                        while ( have_posts() ) :
                            the_post();
                            
                            echo '<div role="listitem">';
                            get_template_part( 'template-parts/content/content', 'search' );
                            echo '</div>';
                            
                        endwhile;
                        ?>
                    </div>
                    
                    <?php get_template_part( 'template-parts/components/pagination' ); ?>
                </section>
                
                <!-- Search analytics (hidden) -->
                <script type="application/ld+json">
                {
                    "@context": "https://schema.org",
                    "@type": "SearchResultsPage",
                    "url": "<?php echo esc_url( home_url( add_query_arg( null, null ) ) ); ?>",
                    "mainEntity": {
                        "@type": "ItemList",
                        "numberOfItems": <?php echo esc_js( $wp_query->found_posts ); ?>,
                        "itemListElement": [
                            <?php
                            $items = array();
                            while ( have_posts() ) :
                                the_post();
                                $items[] = sprintf(
                                    '{
                                        "@type": "ListItem",
                                        "position": %d,
                                        "url": "%s",
                                        "name": "%s"
                                    }',
                                    get_query_var( 'paged' ) ? ( ( get_query_var( 'paged' ) - 1 ) * get_option( 'posts_per_page' ) ) + $wp_query->current_post + 1 : $wp_query->current_post + 1,
                                    esc_url( get_permalink() ),
                                    esc_js( get_the_title() )
                                );
                            endwhile;
                            echo implode( ',', array_slice( $items, 0, 10 ) ); // Limit to first 10 for performance
                            wp_reset_postdata();
                            ?>
                        ]
                    }
                }
                </script>
                
            <?php else : ?>
                
                <section class="no-search-results" role="region" aria-labelledby="no-results-title">
                    <header class="page-header search-header">
                        <h1 id="no-results-title" class="page-title">
                            <?php
                            /* translators: %s: search query. */
                            printf( esc_html__( 'No results found for: %s', 'nosfirnews' ), '<span class="search-query">' . get_search_query() . '</span>' );
                            ?>
                        </h1>
                    </header>
                    
                    <div class="no-results-content">
                        <div class="search-suggestions-wrapper">
                            <h2><?php esc_html_e( 'Try a different search', 'nosfirnews' ); ?></h2>
                            <?php get_search_form(); ?>
                            
                            <div class="search-tips">
                                <h3><?php esc_html_e( 'Search tips:', 'nosfirnews' ); ?></h3>
                                <ul>
                                    <li><?php esc_html_e( 'Check your spelling', 'nosfirnews' ); ?></li>
                                    <li><?php esc_html_e( 'Try different keywords', 'nosfirnews' ); ?></li>
                                    <li><?php esc_html_e( 'Use more general terms', 'nosfirnews' ); ?></li>
                                    <li><?php esc_html_e( 'Try fewer keywords', 'nosfirnews' ); ?></li>
                                </ul>
                            </div>
                        </div>
                        
                        <?php get_template_part( 'template-parts/content/content', 'none' ); ?>
                    </div>
                </section>
                
            <?php endif; ?>
            
        </main><!-- #main -->
        
        <?php get_sidebar(); ?>
        
    </div><!-- .content-layout -->
</div><!-- .site-content-wrapper -->

<?php
get_footer();