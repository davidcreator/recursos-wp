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
 * Output JSON-LD Schema for SEO
 */
function nosfirnews_output_json_ld_schema() {
    // Get SEO options
    $seo = get_option( 'nosfirnews_seo_options', array() );
    $enable_schema = isset( $seo['enable_schema'] ) ? (int) $seo['enable_schema'] : 1;
    if ( ! $enable_schema ) {
        return;
    }

    $organization_name = ! empty( $seo['organization_name'] ) ? $seo['organization_name'] : get_bloginfo( 'name' );
    $organization_logo = ! empty( $seo['organization_logo'] ) ? $seo['organization_logo'] : ( function_exists( 'get_site_icon_url' ) && get_site_icon_url() ? get_site_icon_url() : '' );

    $schemas = array();

    // Organization
    $org = array(
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => $organization_name,
        'url'      => home_url( '/' ),
    );
    if ( $organization_logo ) {
        $org['logo'] = array(
            '@type' => 'ImageObject',
            'url'   => esc_url( $organization_logo ),
        );
    }
    $schemas[] = $org;

    // WebSite with SearchAction
    $schemas[] = array(
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        'url'      => home_url( '/' ),
        'name'     => get_bloginfo( 'name' ),
        'potentialAction' => array(
            '@type' => 'SearchAction',
            'target' => add_query_arg( 's', '{search_term_string}', home_url( '/' ) ),
            'query-input' => 'required name=search_term_string',
        ),
    );

    // Breadcrumbs (basic)
    if ( ! is_front_page() ) {
        $breadcrumb_items = array();
        $breadcrumb_items[] = array(
            '@type' => 'ListItem',
            'position' => 1,
            'name' => __( 'Home', 'nosfirnews' ),
            'item' => home_url( '/' ),
        );

        $position = 2;
        if ( is_single() ) {
            $cats = get_the_category();
            if ( ! empty( $cats ) ) {
                $breadcrumb_items[] = array(
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $cats[0]->name,
                    'item' => get_category_link( $cats[0]->term_id ),
                );
            }
            $breadcrumb_items[] = array(
                '@type' => 'ListItem',
                'position' => $position,
                'name' => get_the_title(),
                'item' => get_permalink(),
            );
        } elseif ( is_page() ) {
            $ancestors = array_reverse( get_post_ancestors( get_the_ID() ) );
            foreach ( $ancestors as $ancestor ) {
                $breadcrumb_items[] = array(
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => get_the_title( $ancestor ),
                    'item' => get_permalink( $ancestor ),
                );
            }
            $breadcrumb_items[] = array(
                '@type' => 'ListItem',
                'position' => $position,
                'name' => get_the_title(),
                'item' => get_permalink(),
            );
        } elseif ( is_category() ) {
            $cat = get_queried_object();
            if ( $cat && isset( $cat->term_id ) ) {
                $breadcrumb_items[] = array(
                    '@type' => 'ListItem',
                    'position' => $position,
                    'name' => single_cat_title( '', false ),
                    'item' => get_category_link( $cat->term_id ),
                );
            }
        }

        if ( count( $breadcrumb_items ) > 1 ) {
            $schemas[] = array(
                '@context' => 'https://schema.org',
                '@type'    => 'BreadcrumbList',
                'itemListElement' => $breadcrumb_items,
            );
        }
    }

    // Article/BlogPosting for single posts
    if ( is_single() && get_post_type() === 'post' ) {
        $image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
        $author_id = get_post_field( 'post_author', get_the_ID() );
        $schemas[] = array(
            '@context' => 'https://schema.org',
            '@type'    => 'Article',
            'headline' => get_the_title(),
            'datePublished' => get_the_date( 'c' ),
            'dateModified'  => get_the_modified_date( 'c' ),
            'author' => array(
                '@type' => 'Person',
                'name'  => get_the_author_meta( 'display_name', $author_id ),
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name'  => $organization_name,
                'logo'  => $organization_logo ? array(
                    '@type' => 'ImageObject',
                    'url'   => esc_url( $organization_logo ),
                ) : null,
            ),
            'mainEntityOfPage' => get_permalink(),
            'image' => $image ? esc_url( $image ) : null,
        );
    }

    // Output all schemas in a single script tag
    if ( ! empty( $schemas ) ) {
        echo "\n<script type=\"application/ld+json\">" . wp_json_encode( $schemas ) . "</script>\n";
    }
}
add_action( 'wp_head', 'nosfirnews_output_json_ld_schema', 99 );

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