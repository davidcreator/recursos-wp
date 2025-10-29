<?php
/**
 * Custom Post Types and Taxonomies for NosfirNews
 *
 * @package NosfirNews
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Custom Post Type - Portfolio (exemplo)
 * Remova esta seção se não precisar de portfolio
 */
function nosfirnews_register_portfolio_post_type() {
    $labels = array(
        'name'                  => _x( 'Portfolio', 'Post type general name', 'nosfirnews' ),
        'singular_name'         => _x( 'Portfolio Item', 'Post type singular name', 'nosfirnews' ),
        'menu_name'             => _x( 'Portfolio', 'Admin Menu text', 'nosfirnews' ),
        'name_admin_bar'        => _x( 'Portfolio Item', 'Add New on Toolbar', 'nosfirnews' ),
        'add_new'               => __( 'Add New', 'nosfirnews' ),
        'add_new_item'          => __( 'Add New Portfolio Item', 'nosfirnews' ),
        'new_item'              => __( 'New Portfolio Item', 'nosfirnews' ),
        'edit_item'             => __( 'Edit Portfolio Item', 'nosfirnews' ),
        'view_item'             => __( 'View Portfolio Item', 'nosfirnews' ),
        'all_items'             => __( 'All Portfolio Items', 'nosfirnews' ),
        'search_items'          => __( 'Search Portfolio Items', 'nosfirnews' ),
        'parent_item_colon'     => __( 'Parent Portfolio Items:', 'nosfirnews' ),
        'not_found'             => __( 'No portfolio items found.', 'nosfirnews' ),
        'not_found_in_trash'    => __( 'No portfolio items found in Trash.', 'nosfirnews' ),
        'featured_image'        => _x( 'Portfolio Featured Image', 'Overrides the "Featured Image" phrase', 'nosfirnews' ),
        'set_featured_image'    => _x( 'Set featured image', 'Overrides the "Set featured image" phrase', 'nosfirnews' ),
        'remove_featured_image' => _x( 'Remove featured image', 'Overrides the "Remove featured image" phrase', 'nosfirnews' ),
        'use_featured_image'    => _x( 'Use as featured image', 'Overrides the "Use as featured image" phrase', 'nosfirnews' ),
        'archives'              => _x( 'Portfolio archives', 'The post type archive label', 'nosfirnews' ),
        'insert_into_item'      => _x( 'Insert into portfolio item', 'Overrides the "Insert into post"/"Insert into page" phrase', 'nosfirnews' ),
        'uploaded_to_this_item' => _x( 'Uploaded to this portfolio item', 'Overrides the "Uploaded to this post"/"Uploaded to this page" phrase', 'nosfirnews' ),
        'filter_items_list'     => _x( 'Filter portfolio items list', 'Screen reader text for the filter links', 'nosfirnews' ),
        'items_list_navigation' => _x( 'Portfolio items list navigation', 'Screen reader text for the pagination', 'nosfirnews' ),
        'items_list'            => _x( 'Portfolio items list', 'Screen reader text for the items list', 'nosfirnews' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true, // Enable Gutenberg editor
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'portfolio' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 6,
        'menu_icon'          => 'dashicons-portfolio',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
        'taxonomies'         => array( 'portfolio_category' ),
    );

    // Descomente a linha abaixo se quiser ativar o portfolio
    // register_post_type( 'portfolio', $args );
}
// add_action( 'init', 'nosfirnews_register_portfolio_post_type' );

/**
 * Register Custom Taxonomy - Portfolio Category
 * Remova esta seção se não precisar de portfolio
 */
function nosfirnews_register_portfolio_taxonomy() {
    $labels = array(
        'name'                       => _x( 'Portfolio Categories', 'Taxonomy General Name', 'nosfirnews' ),
        'singular_name'              => _x( 'Portfolio Category', 'Taxonomy Singular Name', 'nosfirnews' ),
        'menu_name'                  => __( 'Portfolio Categories', 'nosfirnews' ),
        'all_items'                  => __( 'All Categories', 'nosfirnews' ),
        'parent_item'                => __( 'Parent Category', 'nosfirnews' ),
        'parent_item_colon'          => __( 'Parent Category:', 'nosfirnews' ),
        'new_item_name'              => __( 'New Category Name', 'nosfirnews' ),
        'add_new_item'               => __( 'Add New Category', 'nosfirnews' ),
        'edit_item'                  => __( 'Edit Category', 'nosfirnews' ),
        'update_item'                => __( 'Update Category', 'nosfirnews' ),
        'view_item'                  => __( 'View Category', 'nosfirnews' ),
        'separate_items_with_commas' => __( 'Separate categories with commas', 'nosfirnews' ),
        'add_or_remove_items'        => __( 'Add or remove categories', 'nosfirnews' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'nosfirnews' ),
        'popular_items'              => __( 'Popular Categories', 'nosfirnews' ),
        'search_items'               => __( 'Search Categories', 'nosfirnews' ),
        'not_found'                  => __( 'Not Found', 'nosfirnews' ),
        'no_terms'                   => __( 'No categories', 'nosfirnews' ),
        'items_list'                 => __( 'Categories list', 'nosfirnews' ),
        'items_list_navigation'      => __( 'Categories list navigation', 'nosfirnews' ),
    );

    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'show_in_rest'               => true,
        'rewrite'                    => array( 'slug' => 'portfolio-category' ),
    );

    // Descomente a linha abaixo se quiser ativar o portfolio
    // register_taxonomy( 'portfolio_category', array( 'portfolio' ), $args );
}
// add_action( 'init', 'nosfirnews_register_portfolio_taxonomy', 0 );

/**
 * Register Custom Post Type - Testimonials
 * Exemplo de outro tipo de post personalizado
 */
function nosfirnews_register_testimonial_post_type() {
    $labels = array(
        'name'                  => _x( 'Testimonials', 'Post type general name', 'nosfirnews' ),
        'singular_name'         => _x( 'Testimonial', 'Post type singular name', 'nosfirnews' ),
        'menu_name'             => _x( 'Testimonials', 'Admin Menu text', 'nosfirnews' ),
        'name_admin_bar'        => _x( 'Testimonial', 'Add New on Toolbar', 'nosfirnews' ),
        'add_new'               => __( 'Add New', 'nosfirnews' ),
        'add_new_item'          => __( 'Add New Testimonial', 'nosfirnews' ),
        'new_item'              => __( 'New Testimonial', 'nosfirnews' ),
        'edit_item'             => __( 'Edit Testimonial', 'nosfirnews' ),
        'view_item'             => __( 'View Testimonial', 'nosfirnews' ),
        'all_items'             => __( 'All Testimonials', 'nosfirnews' ),
        'search_items'          => __( 'Search Testimonials', 'nosfirnews' ),
        'not_found'             => __( 'No testimonials found.', 'nosfirnews' ),
        'not_found_in_trash'    => __( 'No testimonials found in Trash.', 'nosfirnews' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'query_var'          => true,
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 7,
        'menu_icon'          => 'dashicons-format-quote',
        'supports'           => array( 'title', 'editor', 'thumbnail' ),
    );

    // Descomente a linha abaixo se quiser ativar os depoimentos
    // register_post_type( 'testimonial', $args );
}
// add_action( 'init', 'nosfirnews_register_testimonial_post_type' );

/**
 * Add custom fields support for posts
 */
function nosfirnews_add_post_type_support() {
    // Add custom fields support to posts and pages
    add_post_type_support( 'post', 'custom-fields' );
    add_post_type_support( 'page', 'custom-fields' );
}
add_action( 'init', 'nosfirnews_add_post_type_support' );

/**
 * Custom meta boxes for posts
 */
function nosfirnews_add_post_meta_boxes() {
    add_meta_box(
        'nosfirnews_post_options',
        __( 'Post Options', 'nosfirnews' ),
        'nosfirnews_post_options_callback',
        'post',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'nosfirnews_add_post_meta_boxes' );

/**
 * Post options meta box callback
 */
function nosfirnews_post_options_callback( $post ) {
    // Add nonce for security
    wp_nonce_field( 'nosfirnews_post_options_nonce', 'nosfirnews_post_options_nonce' );
    
    // Get current values
    $featured = get_post_meta( $post->ID, '_nosfirnews_featured_post', true );
    $hide_featured_image = get_post_meta( $post->ID, '_nosfirnews_hide_featured_image', true );
    
    ?>
    <table class="form-table">
        <tr>
            <td>
                <label for="nosfirnews_featured_post">
                    <input type="checkbox" id="nosfirnews_featured_post" name="nosfirnews_featured_post" value="1" <?php checked( $featured, '1' ); ?> />
                    <?php _e( 'Featured Post', 'nosfirnews' ); ?>
                </label>
                <p class="description"><?php _e( 'Mark this post as featured to highlight it on the homepage.', 'nosfirnews' ); ?></p>
            </td>
        </tr>
        <tr>
            <td>
                <label for="nosfirnews_hide_featured_image">
                    <input type="checkbox" id="nosfirnews_hide_featured_image" name="nosfirnews_hide_featured_image" value="1" <?php checked( $hide_featured_image, '1' ); ?> />
                    <?php _e( 'Hide Featured Image', 'nosfirnews' ); ?>
                </label>
                <p class="description"><?php _e( 'Hide the featured image from the single post view.', 'nosfirnews' ); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Save post options meta box data
 */
function nosfirnews_save_post_options_meta( $post_id ) {
    // Check if nonce is valid
    if ( ! isset( $_POST['nosfirnews_post_options_nonce'] ) || 
         ! wp_verify_nonce( $_POST['nosfirnews_post_options_nonce'], 'nosfirnews_post_options_nonce' ) ) {
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
}
add_action( 'save_post', 'nosfirnews_save_post_options_meta' );