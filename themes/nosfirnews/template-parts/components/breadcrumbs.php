<?php
/**
 * Breadcrumbs Component
 *
 * @package NosfirNews
 * @since 1.0.0
 */

// Don't show breadcrumbs on the front page
if ( is_front_page() ) {
    return;
}

// Get breadcrumb items
$breadcrumbs = nosfirnews_get_breadcrumbs();

if ( empty( $breadcrumbs ) ) {
    return;
}
?>

<nav class="breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb Navigation', 'nosfirnews' ); ?>">
    <div class="container">
        <ol class="breadcrumb-list" itemscope itemtype="https://schema.org/BreadcrumbList">
            <?php foreach ( $breadcrumbs as $index => $breadcrumb ) : ?>
                <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <?php if ( ! empty( $breadcrumb['url'] ) && ! $breadcrumb['is_current'] ) : ?>
                        <a href="<?php echo esc_url( $breadcrumb['url'] ); ?>" itemprop="item">
                            <span itemprop="name"><?php echo esc_html( $breadcrumb['title'] ); ?></span>
                        </a>
                    <?php else : ?>
                        <span itemprop="name" aria-current="page"><?php echo esc_html( $breadcrumb['title'] ); ?></span>
                    <?php endif; ?>
                    <meta itemprop="position" content="<?php echo esc_attr( $index + 1 ); ?>" />
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</nav>

<?php
/**
 * Generate breadcrumb items
 *
 * @return array Array of breadcrumb items
 */
function nosfirnews_get_breadcrumbs() {
    $breadcrumbs = array();
    
    // Home link
    $breadcrumbs[] = array(
        'title' => __( 'Home', 'nosfirnews' ),
        'url' => home_url( '/' ),
        'is_current' => false
    );
    
    if ( is_category() || is_tag() || is_tax() ) {
        // Archive pages
        $term = get_queried_object();
        
        if ( is_category() ) {
            // Add parent categories
            $parents = get_category_parents( $term->term_id, true, '|||' );
            if ( $parents && ! is_wp_error( $parents ) ) {
                $parent_cats = explode( '|||', rtrim( $parents, '|||' ) );
                foreach ( $parent_cats as $parent_cat ) {
                    if ( ! empty( $parent_cat ) ) {
                        // Extract title and URL from the link
                        preg_match( '/<a[^>]*href="([^"]*)"[^>]*>([^<]*)<\/a>/', $parent_cat, $matches );
                        if ( isset( $matches[1] ) && isset( $matches[2] ) ) {
                            $breadcrumbs[] = array(
                                'title' => $matches[2],
                                'url' => $matches[1],
                                'is_current' => false
                            );
                        }
                    }
                }
            }
        } else {
            $breadcrumbs[] = array(
                'title' => $term->name,
                'url' => get_term_link( $term ),
                'is_current' => true
            );
        }
        
    } elseif ( is_single() ) {
        // Single post
        $post = get_queried_object();
        
        if ( $post->post_type === 'post' ) {
            // Add primary category
            $categories = get_the_category( $post->ID );
            if ( ! empty( $categories ) ) {
                $primary_cat = $categories[0];
                
                // Add parent categories
                $parents = get_category_parents( $primary_cat->term_id, true, '|||' );
                if ( $parents && ! is_wp_error( $parents ) ) {
                    $parent_cats = explode( '|||', rtrim( $parents, '|||' ) );
                    foreach ( $parent_cats as $parent_cat ) {
                        if ( ! empty( $parent_cat ) ) {
                            preg_match( '/<a[^>]*href="([^"]*)"[^>]*>([^<]*)<\/a>/', $parent_cat, $matches );
                            if ( isset( $matches[1] ) && isset( $matches[2] ) ) {
                                $breadcrumbs[] = array(
                                    'title' => $matches[2],
                                    'url' => $matches[1],
                                    'is_current' => false
                                );
                            }
                        }
                    }
                }
            }
        } else {
            // Custom post type
            $post_type_object = get_post_type_object( $post->post_type );
            if ( $post_type_object && $post_type_object->has_archive ) {
                $breadcrumbs[] = array(
                    'title' => $post_type_object->labels->name,
                    'url' => get_post_type_archive_link( $post->post_type ),
                    'is_current' => false
                );
            }
        }
        
        // Add current post
        $breadcrumbs[] = array(
            'title' => get_the_title( $post->ID ),
            'url' => '',
            'is_current' => true
        );
        
    } elseif ( is_page() ) {
        // Page
        $page = get_queried_object();
        
        // Add parent pages
        if ( $page->post_parent ) {
            $parent_ids = array_reverse( get_post_ancestors( $page->ID ) );
            foreach ( $parent_ids as $parent_id ) {
                $breadcrumbs[] = array(
                    'title' => get_the_title( $parent_id ),
                    'url' => get_permalink( $parent_id ),
                    'is_current' => false
                );
            }
        }
        
        // Add current page
        $breadcrumbs[] = array(
            'title' => get_the_title( $page->ID ),
            'url' => '',
            'is_current' => true
        );
        
    } elseif ( is_search() ) {
        // Search results
        $breadcrumbs[] = array(
            'title' => sprintf( __( 'Search Results for: %s', 'nosfirnews' ), get_search_query() ),
            'url' => '',
            'is_current' => true
        );
        
    } elseif ( is_404() ) {
        // 404 page
        $breadcrumbs[] = array(
            'title' => __( '404 - Page Not Found', 'nosfirnews' ),
            'url' => '',
            'is_current' => true
        );
        
    } elseif ( is_author() ) {
        // Author archive
        $author = get_queried_object();
        $breadcrumbs[] = array(
            'title' => sprintf( __( 'Author: %s', 'nosfirnews' ), $author->display_name ),
            'url' => '',
            'is_current' => true
        );
        
    } elseif ( is_date() ) {
        // Date archive
        if ( is_year() ) {
            $breadcrumbs[] = array(
                'title' => get_the_date( 'Y' ),
                'url' => '',
                'is_current' => true
            );
        } elseif ( is_month() ) {
            $breadcrumbs[] = array(
                'title' => get_the_date( 'Y' ),
                'url' => get_year_link( get_the_date( 'Y' ) ),
                'is_current' => false
            );
            $breadcrumbs[] = array(
                'title' => get_the_date( 'F' ),
                'url' => '',
                'is_current' => true
            );
        } elseif ( is_day() ) {
            $breadcrumbs[] = array(
                'title' => get_the_date( 'Y' ),
                'url' => get_year_link( get_the_date( 'Y' ) ),
                'is_current' => false
            );
            $breadcrumbs[] = array(
                'title' => get_the_date( 'F' ),
                'url' => get_month_link( get_the_date( 'Y' ), get_the_date( 'm' ) ),
                'is_current' => false
            );
            $breadcrumbs[] = array(
                'title' => get_the_date( 'd' ),
                'url' => '',
                'is_current' => true
            );
        }
        
    } elseif ( is_post_type_archive() ) {
        // Custom post type archive
        $post_type = get_query_var( 'post_type' );
        $post_type_object = get_post_type_object( $post_type );
        
        if ( $post_type_object ) {
            $breadcrumbs[] = array(
                'title' => $post_type_object->labels->name,
                'url' => '',
                'is_current' => true
            );
        }
    }
    
    return apply_filters( 'nosfirnews_breadcrumbs', $breadcrumbs );
}