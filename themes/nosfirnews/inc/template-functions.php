<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package NosfirNews
 * @since 1.0.0
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function nosfirnews_body_classes( $classes ) {
    // Adds a class of hfeed to non-singular pages.
    if ( ! is_singular() ) {
        $classes[] = 'hfeed';
    }
    
    // Add class for custom logo
    if ( has_custom_logo() ) {
        $classes[] = 'has-custom-logo';
    }
    
    // Add class for sidebar
    if ( is_active_sidebar( 'sidebar-1' ) ) {
        $classes[] = 'has-sidebar';
    } else {
        $classes[] = 'no-sidebar';
    }
    
    // Add class for singular posts/pages
    if ( is_singular() ) {
        $classes[] = 'singular';
    }
    
    return $classes;
}
add_filter( 'body_class', 'nosfirnews_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function nosfirnews_pingback_header() {
    if ( is_singular() && pings_open() ) {
        printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
    }
}
add_action( 'wp_head', 'nosfirnews_pingback_header' );

/**
 * Changes comment form default fields.
 *
 * @param array $defaults The default comment form fields.
 * @return array
 */
function nosfirnews_comment_form_defaults( $defaults ) {
    $comment_field = $defaults['comment_field'];

    // Adjust height of comment form.
    $defaults['comment_field'] = preg_replace( '/rows="\d+"/', 'rows="5"', $comment_field );

    return $defaults;
}
add_filter( 'comment_form_defaults', 'nosfirnews_comment_form_defaults' );

/**
 * Filters the default archive titles.
 */
function nosfirnews_get_the_archive_title( $title ) {
    if ( is_category() ) {
        $title = single_cat_title( '', false );
    } elseif ( is_tag() ) {
        $title = single_tag_title( '', false );
    } elseif ( is_author() ) {
        $title = '<span class="vcard">' . get_the_author() . '</span>';
    } elseif ( is_year() ) {
        $title = get_the_date( _x( 'Y', 'yearly archives date format', 'nosfirnews' ) );
    } elseif ( is_month() ) {
        $title = get_the_date( _x( 'F Y', 'monthly archives date format', 'nosfirnews' ) );
    } elseif ( is_day() ) {
        $title = get_the_date( _x( 'F j, Y', 'daily archives date format', 'nosfirnews' ) );
    } elseif ( is_tax( 'post_format' ) ) {
        if ( is_tax( 'post_format', 'post-format-aside' ) ) {
            $title = _x( 'Asides', 'post format archive title', 'nosfirnews' );
        } elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
            $title = _x( 'Galleries', 'post format archive title', 'nosfirnews' );
        } elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
            $title = _x( 'Links', 'post format archive title', 'nosfirnews' );
        } elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
            $title = _x( 'Images', 'post format archive title', 'nosfirnews' );
        } elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
            $title = _x( 'Quotes', 'post format archive title', 'nosfirnews' );
        } elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
            $title = _x( 'Statuses', 'post format archive title', 'nosfirnews' );
        } elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
            $title = _x( 'Videos', 'post format archive title', 'nosfirnews' );
        } elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
            $title = _x( 'Audios', 'post format archive title', 'nosfirnews' );
        } elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
            $title = _x( 'Chats', 'post format archive title', 'nosfirnews' );
        }
    } elseif ( is_post_type_archive() ) {
        $title = post_type_archive_title( '', false );
    } elseif ( is_tax() ) {
        $title = single_term_title( '', false );
    }

    return $title;
}
add_filter( 'get_the_archive_title', 'nosfirnews_get_the_archive_title' );

/**
 * Custom breadcrumbs function
 */
function nosfirnews_breadcrumbs() {
    if ( ! is_front_page() ) {
        echo '<nav class="breadcrumbs" aria-label="Breadcrumb">';
        echo '<ul>';
        
        // Home link
        echo '<li><a href="' . home_url() . '">' . __( 'Home', 'nosfirnews' ) . '</a></li>';
        
        if ( is_category() || is_single() ) {
            // Category breadcrumb
            if ( is_single() ) {
                $category = get_the_category();
                if ( ! empty( $category ) ) {
                    $category_link = get_category_link( $category[0]->term_id );
                    echo '<li><a href="' . $category_link . '">' . $category[0]->name . '</a></li>';
                }
                echo '<li>' . get_the_title() . '</li>';
            } else {
                echo '<li>' . single_cat_title( '', false ) . '</li>';
            }
        } elseif ( is_page() ) {
            // Page breadcrumb
            $ancestors = get_post_ancestors( get_the_ID() );
            if ( $ancestors ) {
                $ancestors = array_reverse( $ancestors );
                foreach ( $ancestors as $ancestor ) {
                    echo '<li><a href="' . get_permalink( $ancestor ) . '">' . get_the_title( $ancestor ) . '</a></li>';
                }
            }
            echo '<li>' . get_the_title() . '</li>';
        } elseif ( is_search() ) {
            echo '<li>' . __( 'Search Results', 'nosfirnews' ) . '</li>';
        } elseif ( is_404() ) {
            echo '<li>' . __( '404 Error', 'nosfirnews' ) . '</li>';
        }
        
        echo '</ul>';
        echo '</nav>';
    }
}

/**
 * Get reading time estimate
 */
function nosfirnews_reading_time() {
    $content = get_post_field( 'post_content', get_the_ID() );
    $word_count = str_word_count( strip_tags( $content ) );
    $reading_time = ceil( $word_count / 200 ); // Average reading speed: 200 words per minute
    
    if ( $reading_time === 1 ) {
        return sprintf( __( '%d minute read', 'nosfirnews' ), $reading_time );
    } else {
        return sprintf( __( '%d minutes read', 'nosfirnews' ), $reading_time );
    }
}

/**
 * Custom post meta display
 */
function nosfirnews_post_meta() {
    if ( 'post' === get_post_type() ) {
        echo '<div class="entry-meta">';
        
        // Author
        echo '<span class="author">';
        echo '<i class="fas fa-user"></i> ';
        echo '<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">';
        echo get_the_author();
        echo '</a>';
        echo '</span>';
        
        // Date
        echo '<span class="date">';
        echo '<i class="fas fa-calendar"></i> ';
        echo '<a href="' . esc_url( get_permalink() ) . '">';
        echo get_the_date();
        echo '</a>';
        echo '</span>';
        
        // Categories
        $categories = get_the_category();
        if ( ! empty( $categories ) ) {
            echo '<span class="categories">';
            echo '<i class="fas fa-folder"></i> ';
            $category_links = array();
            foreach ( $categories as $category ) {
                $category_links[] = '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . esc_html( $category->name ) . '</a>';
            }
            echo implode( ', ', $category_links );
            echo '</span>';
        }
        
        // Reading time
        echo '<span class="reading-time">';
        echo '<i class="fas fa-clock"></i> ';
        echo nosfirnews_reading_time();
        echo '</span>';
        
        echo '</div>';
    }
}

/**
 * Social sharing buttons
 */
function nosfirnews_social_share_buttons() {
    $post_url = get_permalink();
    $post_title = get_the_title();
    
    echo '<div class="social-share">';
    echo '<h4>' . __( 'Share this article:', 'nosfirnews' ) . '</h4>';
    echo '<div class="share-buttons">';
    
    // Facebook
    echo '<a href="https://www.facebook.com/sharer/sharer.php?u=' . urlencode( $post_url ) . '" target="_blank" rel="noopener" class="share-facebook">';
    echo '<i class="fab fa-facebook-f"></i> Facebook';
    echo '</a>';
    
    // Twitter
    echo '<a href="https://twitter.com/intent/tweet?url=' . urlencode( $post_url ) . '&text=' . urlencode( $post_title ) . '" target="_blank" rel="noopener" class="share-twitter">';
    echo '<i class="fab fa-twitter"></i> Twitter';
    echo '</a>';
    
    // LinkedIn
    echo '<a href="https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode( $post_url ) . '" target="_blank" rel="noopener" class="share-linkedin">';
    echo '<i class="fab fa-linkedin-in"></i> LinkedIn';
    echo '</a>';
    
    // WhatsApp
    echo '<a href="https://wa.me/?text=' . urlencode( $post_title . ' ' . $post_url ) . '" target="_blank" rel="noopener" class="share-whatsapp">';
    echo '<i class="fab fa-whatsapp"></i> WhatsApp';
    echo '</a>';
    
    // Copy link
    echo '<button class="share-copy" onclick="nosfirnews_copy_link()" title="' . __( 'Copy link', 'nosfirnews' ) . '">';
    echo '<i class="fas fa-link"></i> ' . __( 'Copy Link', 'nosfirnews' );
    echo '</button>';
    
    echo '</div>';
    echo '</div>';
}

/**
 * Related posts function
 */
function nosfirnews_related_posts( $post_id = null, $limit = 3 ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $categories = wp_get_post_categories( $post_id );
    
    if ( empty( $categories ) ) {
        return;
    }
    
    $args = array(
        'category__in'   => $categories,
        'post__not_in'   => array( $post_id ),
        'posts_per_page' => $limit,
        'post_status'    => 'publish',
        'orderby'        => 'rand',
    );
    
    $related_posts = new WP_Query( $args );
    
    if ( $related_posts->have_posts() ) {
        echo '<div class="related-posts">';
        echo '<h3>' . __( 'Related Articles', 'nosfirnews' ) . '</h3>';
        echo '<div class="related-posts-grid">';
        
        while ( $related_posts->have_posts() ) {
            $related_posts->the_post();
            echo '<article class="related-post">';
            
            if ( has_post_thumbnail() ) {
                echo '<div class="related-post-thumbnail">';
                echo '<a href="' . get_permalink() . '">';
                the_post_thumbnail( 'nosfirnews-medium' );
                echo '</a>';
                echo '</div>';
            }
            
            echo '<div class="related-post-content">';
            echo '<h4><a href="' . get_permalink() . '">' . get_the_title() . '</a></h4>';
            echo '<div class="related-post-meta">';
            echo '<span class="date">' . get_the_date() . '</span>';
            echo '</div>';
            echo '</div>';
            
            echo '</article>';
        }
        
        echo '</div>';
        echo '</div>';
    }
    
    wp_reset_postdata();
}