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
 * Helper function to display post cards/templates
 * Consolidates repetitive post display logic across templates
 */
function nosfirnews_get_post_card( $post_id = null, $args = array() ) {
    global $post;
    
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $defaults = array(
        'layout' => 'standard', // standard, featured, compact, grid
        'show_thumbnail' => true,
        'show_category' => true,
        'show_date' => true,
        'show_author' => true,
        'show_excerpt' => true,
        'show_read_more' => true,
        'excerpt_length' => 25,
        'thumbnail_size' => 'medium',
        'item_class' => '',
        'link_wrapper' => true,
    );
    
    $args = wp_parse_args( $args, $defaults );
    
    // Store original post
    $original_post = $post;
    
    // Setup post data
    if ( $post_id !== get_the_ID() ) {
        $post = get_post( $post_id );
        setup_postdata( $post );
    }
    
    $output = '';
    
    // Article wrapper classes
    $classes = array( 'post-card', 'post-card--' . $args['layout'] );
    if ( $args['item_class'] ) {
        $classes[] = $args['item_class'];
    }
    
    $output .= '<article class="' . esc_attr( implode( ' ', $classes ) ) . '">';
    
    // Thumbnail
    if ( $args['show_thumbnail'] && has_post_thumbnail() ) {
        $output .= '<div class="post-card__thumbnail">';
        if ( $args['link_wrapper'] ) {
            $output .= '<a href="' . esc_url( get_permalink() ) . '" aria-hidden="true" tabindex="-1">';
        }
        $output .= get_the_post_thumbnail( $post_id, $args['thumbnail_size'], array( 'class' => 'post-card__image' ) );
        if ( $args['link_wrapper'] ) {
            $output .= '</a>';
        }
        $output .= '</div>';
    }
    
    // Content wrapper
    $output .= '<div class="post-card__content">';
    
    // Category
    if ( $args['show_category'] ) {
        $categories = get_the_category( $post_id );
        if ( ! empty( $categories ) ) {
            $output .= '<div class="post-card__category">';
            $output .= '<a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">';
            $output .= esc_html( $categories[0]->name );
            $output .= '</a>';
            $output .= '</div>';
        }
    }
    
    // Title
    $output .= '<h3 class="post-card__title">';
    if ( $args['link_wrapper'] ) {
        $output .= '<a href="' . esc_url( get_permalink() ) . '">';
    }
    $output .= esc_html( get_the_title( $post_id ) );
    if ( $args['link_wrapper'] ) {
        $output .= '</a>';
    }
    $output .= '</h3>';
    
    // Meta
    if ( $args['show_date'] || $args['show_author'] ) {
        $output .= '<div class="post-card__meta">';
        
        if ( $args['show_author'] ) {
            $output .= '<span class="post-card__author">';
            $output .= esc_html( get_the_author_meta( 'display_name', $post->post_author ) );
            $output .= '</span>';
        }
        
        if ( $args['show_date'] ) {
            $output .= '<time class="post-card__date" datetime="' . esc_attr( get_the_date( 'c', $post_id ) ) . '">';
            $output .= esc_html( get_the_date( '', $post_id ) );
            $output .= '</time>';
        }
        
        $output .= '</div>';
    }
    
    // Excerpt
    if ( $args['show_excerpt'] ) {
        $output .= '<div class="post-card__excerpt">';
        $excerpt = get_the_excerpt( $post_id );
        if ( ! $excerpt ) {
            $excerpt = wp_trim_words( get_the_content( null, false, $post_id ), $args['excerpt_length'] );
        }
        $output .= esc_html( wp_trim_words( $excerpt, $args['excerpt_length'] ) );
        $output .= '</div>';
    }
    
    // Read more
    if ( $args['show_read_more'] ) {
        $output .= '<div class="post-card__read-more">';
        $output .= '<a href="' . esc_url( get_permalink() ) . '" class="post-card__link">';
        $output .= esc_html__( 'Read More', 'nosfirnews' );
        $output .= '</a>';
        $output .= '</div>';
    }
    
    $output .= '</div>'; // .post-card__content
    $output .= '</article>';
    
    // Restore original post
    if ( $original_post && $post_id !== $original_post->ID ) {
        $post = $original_post;
        setup_postdata( $post );
    }
    
    return $output;
}

/**
 * Helper function to get posts with specific meta
 * Consolidates repetitive WP_Query patterns
 */
function nosfirnews_get_posts_by_meta( $meta_key, $meta_value = '1', $args = array() ) {
    $defaults = array(
        'post_type' => 'post',
        'posts_per_page' => 5,
        'meta_query' => array(
            array(
                'key' => $meta_key,
                'value' => $meta_value,
                'compare' => '=',
            ),
        ),
        'orderby' => 'date',
        'order' => 'DESC',
        'post_status' => 'publish',
    );
    
    $args = wp_parse_args( $args, $defaults );
    
    return new WP_Query( $args );
}

/**
 * Helper function to get featured posts
 */
function nosfirnews_get_featured_posts( $args = array() ) {
    $defaults = array(
        'posts_per_page' => 5,
        'meta_key' => '_featured_post',
        'meta_value' => '1',
    );
    
    $args = wp_parse_args( $args, $defaults );
    
    return nosfirnews_get_posts_by_meta( '_featured_post', '1', $args );
}

/**
 * Helper function to get breaking news
 */
function nosfirnews_get_breaking_news( $args = array() ) {
    $defaults = array(
        'posts_per_page' => 5,
        'meta_key' => '_breaking_news',
        'meta_value' => '1',
    );
    
    $args = wp_parse_args( $args, $defaults );
    
    return nosfirnews_get_posts_by_meta( '_breaking_news', '1', $args );
}

/**
 * Helper function to get hero/destaque posts
 */
function nosfirnews_get_hero_posts( $args = array() ) {
    $defaults = array(
        'posts_per_page' => 1,
        'meta_key' => '_hero_post',
        'meta_value' => '1',
    );
    
    $args = wp_parse_args( $args, $defaults );
    
    return nosfirnews_get_posts_by_meta( '_hero_post', '1', $args );
}

/**
 * Helper function to get trending posts by views
 */
function nosfirnews_get_trending_posts( $args = array() ) {
    $defaults = array(
        'posts_per_page' => 5,
        'meta_key' => '_nosfirnews_views',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
    );
    
    $args = wp_parse_args( $args, $defaults );
    
    return new WP_Query( $args );
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

// 1. Implementar cache transiente para queries pesadas
class NosfirNews_Cache_Manager {
    
    private static $cache_group = 'nosfirnews';
    private static $cache_time = 3600; // 1 hora
    
    /**
     * Obtém dados do cache ou executa callback
     */
    public static function get_or_set($key, $callback, $expiration = null) {
        $expiration = $expiration ?? self::$cache_time;
        $cache_key = self::$cache_group . '_' . $key;
        
        // Tentar obter do cache
        $cached = get_transient($cache_key);
        
        if ($cached !== false) {
            return $cached;
        }
        
        // Executar callback e armazenar
        $data = call_user_func($callback);
        set_transient($cache_key, $data, $expiration);
        
        return $data;
    }
    
    /**
     * Limpa cache específico
     */
    public static function delete($key) {
        $cache_key = self::$cache_group . '_' . $key;
        delete_transient($cache_key);
    }
    
    /**
     * Limpa todo o cache do tema
     */
    public static function flush_all() {
        global $wpdb;
        
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                '%' . $wpdb->esc_like('_transient_' . self::$cache_group) . '%'
            )
        );
    }
    
    /**
     * Invalida cache ao publicar post
     */
    public static function invalidate_on_post_save($post_id) {
        // Limpar cache de posts relacionados
        self::delete('related_posts_' . $post_id);
        self::delete('featured_posts');
        self::delete('recent_posts');
        
        // Limpar cache da categoria do post
        $categories = wp_get_post_categories($post_id);
        foreach ($categories as $cat_id) {
            self::delete('category_posts_' . $cat_id);
        }
    }
}

// Hooks de invalidação - DESATIVADOS temporariamente
// add_action('save_post', array('NosfirNews_Cache_Manager', 'invalidate_on_post_save'));
// add_action('delete_post', array('NosfirNews_Cache_Manager', 'flush_all'));

// 2. Lazy Loading de imagens otimizado - DESATIVADO temporariamente
function nosfirnews_lazy_load_images_optimized($content) {
    // Já tem lazy loading nativo do WP 5.5+
    if (version_compare(get_bloginfo('version'), '5.5', '>=')) {
        return $content;
    }
    
    // Adicionar loading="lazy" para versões antigas
    $content = preg_replace(
        '/<img((?![^>]*loading=)[^>]*)>/i',
        '<img loading="lazy"$1>',
        $content
    );
    
    return $content;
}
// add_filter('the_content', 'nosfirnews_lazy_load_images_optimized', 20);

// 3. Preload de recursos críticos - DESATIVADO temporariamente
function nosfirnews_preload_critical_resources() {
    // Preload de fontes
    echo '<link rel="preload" href="' . get_template_directory_uri() . '/assets/fonts/main.woff2" as="font" type="font/woff2" crossorigin>' . "\n";
    
    // Preload de CSS crítico
    echo '<link rel="preload" href="' . get_stylesheet_uri() . '" as="style">' . "\n";
    
    // DNS prefetch para recursos externos
    echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">' . "\n";
    echo '<link rel="dns-prefetch" href="//fonts.gstatic.com">' . "\n";
    
    // Preconnect para recursos críticos
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
// add_action('wp_head', 'nosfirnews_preload_critical_resources', 1);

// 4. Otimizar queries de menu - DESATIVADO temporariamente
function nosfirnews_optimize_menu_queries() {
    // Cache de menus
    add_filter('wp_nav_menu_args', function($args) {
        if (!isset($args['echo'])) {
            $args['echo'] = false;
        }
        return $args;
    });
    
    // Limitar profundidade de menus
    add_filter('wp_nav_menu_args', function($args) {
        if (!isset($args['depth'])) {
            $args['depth'] = 3; // Máximo 3 níveis
        }
        return $args;
    });
}
// add_action('after_setup_theme', 'nosfirnews_optimize_menu_queries');

// 5. Otimizar queries de widgets - DESATIVADO temporariamente
function nosfirnews_optimize_widget_queries() {
    // Limitar posts em widgets
    add_filter('widget_posts_args', function($args) {
        $args['posts_per_page'] = min($args['posts_per_page'] ?? 5, 10);
        $args['no_found_rows'] = true; // Evita COUNT(*)
        $args['update_post_meta_cache'] = false;
        $args['update_post_term_cache'] = false;
        return $args;
    });
}
// add_action('widgets_init', 'nosfirnews_optimize_widget_queries');

// 6. Defer/Async de scripts não críticos - DESATIVADO temporariamente
function nosfirnews_defer_non_critical_scripts($tag, $handle, $src) {
    // Scripts que podem ser defer
    $defer_scripts = array(
        'nosfirnews-navigation',
        'nosfirnews-responsive',
        'comment-reply'
    );
    
    // Scripts que podem ser async
    $async_scripts = array(
        'google-analytics',
        'facebook-sdk'
    );
    
    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }
    
    if (in_array($handle, $async_scripts)) {
        return str_replace(' src', ' async src', $tag);
    }
    
    return $tag;
}
// add_filter('script_loader_tag', 'nosfirnews_defer_non_critical_scripts', 10, 3);

// 7. Otimizar revisões de posts - DESATIVADO temporariamente
function nosfirnews_optimize_revisions() {
    // Limitar revisões via código (backup do que está no metabox)
    if (!defined('WP_POST_REVISIONS')) {
        define('WP_POST_REVISIONS', 5);
    }
    
    // Auto-cleanup de revisões antigas - DESATIVADO temporariamente
    // if (!wp_next_scheduled('nosfirnews_cleanup_revisions')) {
    //     wp_schedule_event(time(), 'weekly', 'nosfirnews_cleanup_revisions');
    // }
}
// add_action('init', 'nosfirnews_optimize_revisions');

add_action('nosfirnews_cleanup_revisions', function() {
    global $wpdb;
    
    // Deletar revisões além do limite
    $wpdb->query("
        DELETE FROM {$wpdb->posts}
        WHERE post_type = 'revision'
        AND post_date < DATE_SUB(NOW(), INTERVAL 90 DAY)
    ");
});

// 8. Otimizar carregamento de imagens - DESATIVADO temporariamente
function nosfirnews_optimize_image_sizes() {
    // Adicionar tamanhos responsivos
    update_option('medium_large_size_w', 768);
    update_option('medium_large_size_h', 0);
    
    // Registrar tamanhos otimizados
    add_image_size('nosfirnews-hero', 1920, 1080, true);
    add_image_size('nosfirnews-card', 600, 400, true);
    add_image_size('nosfirnews-thumb', 300, 200, true);
}
// add_action('after_setup_theme', 'nosfirnews_optimize_image_sizes');

// 9. Remover query strings de recursos estáticos (para melhor cache) - DESATIVADO temporariamente
function nosfirnews_remove_script_version($src) {
    if (strpos($src, 'ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
// add_filter('script_loader_src', 'nosfirnews_remove_script_version', 15);
// add_filter('style_loader_src', 'nosfirnews_remove_script_version', 15);

// 10. Otimizar heartbeat do WordPress - DESATIVADO temporariamente
function nosfirnews_optimize_heartbeat($settings) {
    // Desabilitar no frontend
    if (!is_admin()) {
        wp_deregister_script('heartbeat');
    }
    
    // Aumentar intervalo no admin
    $settings['interval'] = 60; // 60 segundos
    
    return $settings;
}
// add_filter('heartbeat_settings', 'nosfirnews_optimize_heartbeat');

// 11. Implementar object cache (se disponível) - DESATIVADO temporariamente
function nosfirnews_setup_object_cache() {
    // Verificar se object cache está disponível
    if (function_exists('wp_cache_add_global_groups')) {
        wp_cache_add_global_groups(array(
            'nosfirnews',
            'nosfirnews_options',
            'nosfirnews_menus'
        ));
    }
}
// add_action('init', 'nosfirnews_setup_object_cache');

// 12. Otimizar queries de customizer - DESATIVADO temporariamente
function nosfirnews_optimize_customizer_queries($wp_customize) {
    // Desabilitar refresh automático para performance
    $wp_customize->get_setting('blogname')->transport = 'postMessage';
    $wp_customize->get_setting('blogdescription')->transport = 'postMessage';
}
// add_action('customize_register', 'nosfirnews_optimize_customizer_queries', 999);

// 13. Cleanup de dados temporários
function nosfirnews_cleanup_temp_data() {
    global $wpdb;
    
    // Limpar transientes expirados
    $wpdb->query("
        DELETE FROM {$wpdb->options}
        WHERE option_name LIKE '_transient_timeout_%'
        AND option_value < UNIX_TIMESTAMP()
    ");
    
    // Limpar transientes órfãos
    $wpdb->query("
        DELETE FROM {$wpdb->options}
        WHERE option_name LIKE '_transient_%'
        AND option_name NOT LIKE '_transient_timeout_%'
        AND NOT EXISTS (
            SELECT 1 FROM {$wpdb->options} t2
            WHERE t2.option_name = CONCAT('_transient_timeout_', SUBSTRING({$wpdb->options}.option_name, 12))
        )
    ");
}

// Agendar cleanup semanal - DESATIVADO temporariamente
// if (!wp_next_scheduled('nosfirnews_weekly_cleanup')) {
//     wp_schedule_event(time(), 'weekly', 'nosfirnews_weekly_cleanup');
// }
// add_action('nosfirnews_weekly_cleanup', 'nosfirnews_cleanup_temp_data');

// 14. Exemplo de uso otimizado de queries - DESATIVADO temporariamente
function nosfirnews_get_featured_posts_optimized($limit = 5) {
    // return NosfirNews_Cache_Manager::get_or_set(
    //     'featured_posts_' . $limit,
    //     function() use ($limit) {
    //         $args = array(
    //             'post_type' => 'post',
    //             'posts_per_page' => $limit,
    //             'meta_key' => '_nosfirnews_featured_post',
    //             'meta_value' => '1',
    //             'no_found_rows' => true,
    //             'update_post_meta_cache' => false,
    //             'update_post_term_cache' => false,
    //             'fields' => 'ids' // Retornar apenas IDs se não precisar de dados completos
    //         );
    //         
    //         return get_posts($args);
    //     },
    //     HOUR_IN_SECONDS
    // );
    
    // Versão sem cache
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $limit,
        'meta_key' => '_nosfirnews_featured_post',
        'meta_value' => '1',
        'no_found_rows' => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
        'fields' => 'ids'
    );
    
    return get_posts($args);
}