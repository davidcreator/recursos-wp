<?php
/**
 * Custom Meta Boxes for NosfirNews
 *
 * @package NosfirNews
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add meta boxes
 */
function nosfirnews_add_meta_boxes() {
    // Post meta box
    add_meta_box(
        'nosfirnews_post_settings',
        __( 'Post Settings', 'nosfirnews' ),
        'nosfirnews_post_settings_callback',
        'post',
        'side',
        'high'
    );
    
    // Page meta box
    add_meta_box(
        'nosfirnews_page_settings',
        __( 'Page Settings', 'nosfirnews' ),
        'nosfirnews_page_settings_callback',
        'page',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'nosfirnews_add_meta_boxes' );

/**
 * Post settings meta box callback
 */
function nosfirnews_post_settings_callback( $post ) {
    // Add nonce for security
    wp_nonce_field( 'nosfirnews_post_settings_nonce', 'nosfirnews_post_settings_nonce' );
    
    // Get current values
    $featured = get_post_meta( $post->ID, '_nosfirnews_featured_post', true );
    $hide_featured_image = get_post_meta( $post->ID, '_nosfirnews_hide_featured_image', true );
    $custom_excerpt = get_post_meta( $post->ID, '_nosfirnews_custom_excerpt', true );
    $reading_time = get_post_meta( $post->ID, '_nosfirnews_custom_reading_time', true );
    $post_subtitle = get_post_meta( $post->ID, '_nosfirnews_post_subtitle', true );
    $hide_author = get_post_meta( $post->ID, '_nosfirnews_hide_author', true );
    $hide_date = get_post_meta( $post->ID, '_nosfirnews_hide_date', true );
    
    ?>
    <table class="form-table">
        <tr>
            <td>
                <label for="nosfirnews_post_subtitle">
                    <strong><?php _e( 'Post Subtitle', 'nosfirnews' ); ?></strong>
                </label>
                <br>
                <input type="text" id="nosfirnews_post_subtitle" name="nosfirnews_post_subtitle" value="<?php echo esc_attr( $post_subtitle ); ?>" style="width: 100%;" />
                <p class="description"><?php _e( 'Optional subtitle to display below the main title.', 'nosfirnews' ); ?></p>
            </td>
        </tr>
        
        <tr>
            <td>
                <label for="nosfirnews_custom_excerpt">
                    <strong><?php _e( 'Custom Excerpt', 'nosfirnews' ); ?></strong>
                </label>
                <br>
                <textarea id="nosfirnews_custom_excerpt" name="nosfirnews_custom_excerpt" rows="3" style="width: 100%;"><?php echo esc_textarea( $custom_excerpt ); ?></textarea>
                <p class="description"><?php _e( 'Custom excerpt for this post. Leave empty to use automatic excerpt.', 'nosfirnews' ); ?></p>
            </td>
        </tr>
        
        <tr>
            <td>
                <label for="nosfirnews_custom_reading_time">
                    <strong><?php _e( 'Custom Reading Time', 'nosfirnews' ); ?></strong>
                </label>
                <br>
                <input type="number" id="nosfirnews_custom_reading_time" name="nosfirnews_custom_reading_time" value="<?php echo esc_attr( $reading_time ); ?>" min="1" max="60" style="width: 100px;" />
                <span><?php _e( 'minutes', 'nosfirnews' ); ?></span>
                <p class="description"><?php _e( 'Override automatic reading time calculation.', 'nosfirnews' ); ?></p>
            </td>
        </tr>
        
        <tr>
            <td>
                <label>
                    <input type="checkbox" id="nosfirnews_featured_post" name="nosfirnews_featured_post" value="1" <?php checked( $featured, '1' ); ?> />
                    <strong><?php _e( 'Featured Post', 'nosfirnews' ); ?></strong>
                </label>
                <p class="description"><?php _e( 'Mark this post as featured to highlight it on the homepage.', 'nosfirnews' ); ?></p>
            </td>
        </tr>
        
        <tr>
            <td>
                <label>
                    <input type="checkbox" id="nosfirnews_hide_featured_image" name="nosfirnews_hide_featured_image" value="1" <?php checked( $hide_featured_image, '1' ); ?> />
                    <strong><?php _e( 'Hide Featured Image', 'nosfirnews' ); ?></strong>
                </label>
                <p class="description"><?php _e( 'Hide the featured image from the single post view.', 'nosfirnews' ); ?></p>
            </td>
        </tr>
        
        <tr>
            <td>
                <label>
                    <input type="checkbox" id="nosfirnews_hide_author" name="nosfirnews_hide_author" value="1" <?php checked( $hide_author, '1' ); ?> />
                    <strong><?php _e( 'Hide Author Info', 'nosfirnews' ); ?></strong>
                </label>
                <p class="description"><?php _e( 'Hide author information from this post.', 'nosfirnews' ); ?></p>
            </td>
        </tr>
        
        <tr>
            <td>
                <label>
                    <input type="checkbox" id="nosfirnews_hide_date" name="nosfirnews_hide_date" value="1" <?php checked( $hide_date, '1' ); ?> />
                    <strong><?php _e( 'Hide Date', 'nosfirnews' ); ?></strong>
                </label>
                <p class="description"><?php _e( 'Hide publication date from this post.', 'nosfirnews' ); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Save post meta box data
 */
function nosfirnews_save_post_meta( $post_id ) {
    // Check if nonce is valid
    if ( ! isset( $_POST['nosfirnews_post_settings_nonce'] ) || 
         ! wp_verify_nonce( $_POST['nosfirnews_post_settings_nonce'], 'nosfirnews_post_settings_nonce' ) ) {
        return;
    }
    
    // Check if user has permissions
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    
    // Check if this is an autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    
    // Save post subtitle
    if ( isset( $_POST['nosfirnews_post_subtitle'] ) ) {
        update_post_meta( $post_id, '_nosfirnews_post_subtitle', sanitize_text_field( $_POST['nosfirnews_post_subtitle'] ) );
    }
    
    // Save custom excerpt
    if ( isset( $_POST['nosfirnews_custom_excerpt'] ) ) {
        update_post_meta( $post_id, '_nosfirnews_custom_excerpt', wp_kses_post( $_POST['nosfirnews_custom_excerpt'] ) );
    }
    
    // Save custom reading time
    if ( isset( $_POST['nosfirnews_custom_reading_time'] ) ) {
        $reading_time = intval( $_POST['nosfirnews_custom_reading_time'] );
        if ( $reading_time > 0 ) {
            update_post_meta( $post_id, '_nosfirnews_custom_reading_time', $reading_time );
        } else {
            delete_post_meta( $post_id, '_nosfirnews_custom_reading_time' );
        }
    }
    
    // Save featured post option
    if ( isset( $_POST['nosfirnews_featured_post'] ) ) {
        update_post_meta( $post_id, '_nosfirnews_featured_post', '1' );
    } else {
        delete_post_meta( $post_id, '_nosfirnews_featured_post' );
    }
    
    // Save hide featured image option
    if ( isset( $_POST['nosfirnews_hide_featured_image'] ) ) {
        update_post_meta( $post_id, '_nosfirnews_hide_featured_image', '1' );
    } else {
        delete_post_meta( $post_id, '_nosfirnews_hide_featured_image' );
    }
    
    // Save hide author option
    if ( isset( $_POST['nosfirnews_hide_author'] ) ) {
        update_post_meta( $post_id, '_nosfirnews_hide_author', '1' );
    } else {
        delete_post_meta( $post_id, '_nosfirnews_hide_author' );
    }
    
    // Save hide date option
    if ( isset( $_POST['nosfirnews_hide_date'] ) ) {
        update_post_meta( $post_id, '_nosfirnews_hide_date', '1' );
    } else {
        delete_post_meta( $post_id, '_nosfirnews_hide_date' );
    }
}
add_action( 'save_post', 'nosfirnews_save_post_meta' );

/**
 * Save page meta box data
 */
function nosfirnews_save_page_meta( $post_id ) {
    // Check if nonce is valid
    if ( ! isset( $_POST['nosfirnews_page_settings_nonce'] ) || 
         ! wp_verify_nonce( $_POST['nosfirnews_page_settings_nonce'], 'nosfirnews_page_settings_nonce' ) ) {
        return;
    }
    
    // Check if user has permissions
    if ( ! current_user_can( 'edit_page', $post_id ) ) {
        return;
    }
    
    // Check if this is an autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    
    // Save page subtitle
    if ( isset( $_POST['nosfirnews_page_subtitle'] ) ) {
        update_post_meta( $post_id, '_nosfirnews_page_subtitle', sanitize_text_field( $_POST['nosfirnews_page_subtitle'] ) );
    }
    
    // Save custom header image
    if ( isset( $_POST['nosfirnews_custom_header_image'] ) ) {
        update_post_meta( $post_id, '_nosfirnews_custom_header_image', esc_url_raw( $_POST['nosfirnews_custom_header_image'] ) );
    }
    
    // Save hide title option
    if ( isset( $_POST['nosfirnews_hide_title'] ) ) {
        update_post_meta( $post_id, '_nosfirnews_hide_title', '1' );
    } else {
        delete_post_meta( $post_id, '_nosfirnews_hide_title' );
    }
    
    // Save full width option
    if ( isset( $_POST['nosfirnews_full_width_page'] ) ) {
        update_post_meta( $post_id, '_nosfirnews_full_width_page', '1' );
    } else {
        delete_post_meta( $post_id, '_nosfirnews_full_width_page' );
    }
    
    // Save hide sidebar option
    if ( isset( $_POST['nosfirnews_hide_sidebar'] ) ) {
        update_post_meta( $post_id, '_nosfirnews_hide_sidebar', '1' );
    } else {
        delete_post_meta( $post_id, '_nosfirnews_hide_sidebar' );
    }
}
add_action( 'save_post', 'nosfirnews_save_page_meta' );

/**
 * Add custom CSS classes based on page settings
 */
function nosfirnews_page_body_classes( $classes ) {
    if ( is_page() ) {
        global $post;
        
        // Add full width class
        if ( get_post_meta( $post->ID, '_nosfirnews_full_width_page', true ) ) {
            $classes[] = 'full-width-page';
        }
        
        // Add no sidebar class
        if ( get_post_meta( $post->ID, '_nosfirnews_hide_sidebar', true ) ) {
            $classes[] = 'no-sidebar-page';
        }
        
        // Add hide title class
        if ( get_post_meta( $post->ID, '_nosfirnews_hide_title', true ) ) {
            $classes[] = 'hide-page-title';
        }
    }
    
    if ( is_single() ) {
        global $post;
        
        // Add featured post class
        if ( get_post_meta( $post->ID, '_nosfirnews_featured_post', true ) ) {
            $classes[] = 'featured-post';
        }
        
        // Add hide featured image class
        if ( get_post_meta( $post->ID, '_nosfirnews_hide_featured_image', true ) ) {
            $classes[] = 'hide-featured-image';
        }
    }
    
    return $classes;
}
add_filter( 'body_class', 'nosfirnews_page_body_classes' );

/**
 * Helper functions to get meta values
 */
function nosfirnews_get_post_subtitle( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    return get_post_meta( $post_id, '_nosfirnews_post_subtitle', true );
}

function nosfirnews_get_page_subtitle( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    return get_post_meta( $post_id, '_nosfirnews_page_subtitle', true );
}

function nosfirnews_get_custom_excerpt( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $custom_excerpt = get_post_meta( $post_id, '_nosfirnews_custom_excerpt', true );
    
    if ( ! empty( $custom_excerpt ) ) {
        return $custom_excerpt;
    }
    
    return get_the_excerpt( $post_id );
}

function nosfirnews_get_reading_time( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    // Check for custom reading time first
    $custom_time = get_post_meta( $post_id, '_nosfirnews_custom_reading_time', true );
    
    if ( ! empty( $custom_time ) ) {
        return intval( $custom_time );
    }
    
    // Calculate reading time
    $content = get_post_field( 'post_content', $post_id );
    $word_count = str_word_count( strip_tags( $content ) );
    $reading_time = ceil( $word_count / 200 ); // Average reading speed: 200 words per minute
    
    return max( 1, $reading_time ); // Minimum 1 minute
}

function nosfirnews_should_hide_author( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    return get_post_meta( $post_id, '_nosfirnews_hide_author', true ) === '1';
}

function nosfirnews_should_hide_date( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    return get_post_meta( $post_id, '_nosfirnews_hide_date', true ) === '1';
}

function nosfirnews_should_hide_page_title( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    return get_post_meta( $post_id, '_nosfirnews_hide_title', true ) === '1';
}

function nosfirnews_is_full_width_page( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    return get_post_meta( $post_id, '_nosfirnews_full_width_page', true ) === '1';
}

function nosfirnews_should_hide_page_sidebar( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    return get_post_meta( $post_id, '_nosfirnews_hide_sidebar', true ) === '1';
}

function nosfirnews_get_custom_header_image( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    return get_post_meta( $post_id, '_nosfirnews_custom_header_image', true );
}

/**
 * Page settings meta box callback
 */
function nosfirnews_page_settings_callback( $post ) {
    // Add nonce for security
    wp_nonce_field( 'nosfirnews_page_settings_nonce', 'nosfirnews_page_settings_nonce' );
    
    // Get current values
    $hide_title = get_post_meta( $post->ID, '_nosfirnews_hide_title', true );
    $full_width = get_post_meta( $post->ID, '_nosfirnews_full_width_page', true );
    $hide_sidebar = get_post_meta( $post->ID, '_nosfirnews_hide_sidebar', true );
    $custom_header_image = get_post_meta( $post->ID, '_nosfirnews_custom_header_image', true );
    $page_subtitle = get_post_meta( $post->ID, '_nosfirnews_page_subtitle', true );
    
    ?>
    <table class="form-table">
        <tr>
            <td>
                <label for="nosfirnews_page_subtitle">
                    <strong><?php _e( 'Page Subtitle', 'nosfirnews' ); ?></strong>
                </label>
                <br>
                <input type="text" id="nosfirnews_page_subtitle" name="nosfirnews_page_subtitle" value="<?php echo esc_attr( $page_subtitle ); ?>" style="width: 100%;" />
                <p class="description"><?php _e( 'Optional subtitle to display below the main title.', 'nosfirnews' ); ?></p>
            </td>
        </tr>
        
        <tr>
            <td>
                <label for="nosfirnews_custom_header_image">
                    <strong><?php _e( 'Custom Header Image', 'nosfirnews' ); ?></strong>
                </label>
                <br>
                <input type="text" id="nosfirnews_custom_header_image" name="nosfirnews_custom_header_image" value="<?php echo esc_url( $custom_header_image ); ?>" style="width: 100%;" />
                <button type="button" class="button" id="upload_header_image"><?php _e( 'Upload Image', 'nosfirnews' ); ?></button>
                <p class="description"><?php _e( 'Custom header image for this page.', 'nosfirnews' ); ?></p>
            </td>
        </tr>
        
        <tr>
            <td>
                <label>
                    <input type="checkbox" id="nosfirnews_hide_title" name="nosfirnews_hide_title" value="1" <?php checked( $hide_title, '1' ); ?> />
                    <strong><?php _e( 'Hide Page Title', 'nosfirnews' ); ?></strong>
                </label>
                <p class="description"><?php _e( 'Hide the page title from the front-end display.', 'nosfirnews' ); ?></p>
            </td>
        </tr>
        
        <tr>
            <td>
                <label>
                    <input type="checkbox" id="nosfirnews_full_width_page" name="nosfirnews_full_width_page" value="1" <?php checked( $full_width, '1' ); ?> />
                    <strong><?php _e( 'Full Width Page', 'nosfirnews' ); ?></strong>
                </label>
                <p class="description"><?php _e( 'Display the page content without a sidebar.', 'nosfirnews' ); ?></p>
            </td>
        </tr>
        
        <tr>
            <td>
                <label>
                    <input type="checkbox" id="nosfirnews_hide_sidebar" name="nosfirnews_hide_sidebar" value="1" <?php checked( $hide_sidebar, '1' ); ?> />
                    <strong><?php _e( 'Hide Sidebar', 'nosfirnews' ); ?></strong>
                </label>
                <p class="description"><?php _e( 'Hide the sidebar from this page.', 'nosfirnews' ); ?></p>
            </td>
        </tr>
        <!-- Add more rows as needed -->
    </table>
    <?php
}

/**
 * Add meta boxes for pages
 */
function nosfirnews_add_page_metaboxes() {
    add_meta_box(
        'nosfirnews_page_settings',
        esc_html__( 'Page Settings', 'nosfirnews' ),
        'nosfirnews_page_settings_callback',
        'page',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'nosfirnews_add_page_metaboxes' );