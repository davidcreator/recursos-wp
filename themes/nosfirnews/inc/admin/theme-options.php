<?php
/**
 * Theme Options Panel for NosfirNews
 *
 * @package NosfirNews
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add theme options menu page
 */
function nosfirnews_add_theme_options_page() {
    add_theme_page(
        __( 'NosfirNews Options', 'nosfirnews' ),
        __( 'Theme Options', 'nosfirnews' ),
        'manage_options',
        'nosfirnews-theme-options',
        'nosfirnews_theme_options_page'
    );
}
add_action( 'admin_menu', 'nosfirnews_add_theme_options_page' );

/**
 * Theme options page callback
 */
function nosfirnews_theme_options_page() {
    // Check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    
    // Save options if form submitted
    if ( isset( $_POST['submit'] ) && wp_verify_nonce( $_POST['nosfirnews_options_nonce'], 'nosfirnews_save_options' ) ) {
        nosfirnews_save_theme_options();
        echo '<div class="notice notice-success"><p>' . __( 'Settings saved successfully!', 'nosfirnews' ) . '</p></div>';
    }
    
    // Get current options
    $options = get_option( 'nosfirnews_theme_options', nosfirnews_get_default_options() );
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field( 'nosfirnews_save_options', 'nosfirnews_options_nonce' ); ?>
            
            <table class="form-table">
                
                <!-- General Settings -->
                <tr>
                    <th colspan="2">
                        <h2><?php _e( 'General Settings', 'nosfirnews' ); ?></h2>
                    </th>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="site_layout"><?php _e( 'Site Layout', 'nosfirnews' ); ?></label>
                    </th>
                    <td>
                        <select id="site_layout" name="nosfirnews_options[site_layout]">
                            <option value="boxed" <?php selected( $options['site_layout'], 'boxed' ); ?>><?php _e( 'Boxed', 'nosfirnews' ); ?></option>
                            <option value="full-width" <?php selected( $options['site_layout'], 'full-width' ); ?>><?php _e( 'Full Width', 'nosfirnews' ); ?></option>
                        </select>
                        <p class="description"><?php _e( 'Choose the overall layout style for your site.', 'nosfirnews' ); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="posts_per_page"><?php _e( 'Posts per Page', 'nosfirnews' ); ?></label>
                    </th>
                    <td>
                        <input type="number" id="posts_per_page" name="nosfirnews_options[posts_per_page]" value="<?php echo esc_attr( $options['posts_per_page'] ); ?>" min="1" max="50" />
                        <p class="description"><?php _e( 'Number of posts to display on archive pages.', 'nosfirnews' ); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="excerpt_length"><?php _e( 'Excerpt Length', 'nosfirnews' ); ?></label>
                    </th>
                    <td>
                        <input type="number" id="excerpt_length" name="nosfirnews_options[excerpt_length]" value="<?php echo esc_attr( $options['excerpt_length'] ); ?>" min="10" max="100" />
                        <p class="description"><?php _e( 'Number of words in post excerpts.', 'nosfirnews' ); ?></p>
                    </td>
                </tr>
                
                <!-- Header Settings -->
                <tr>
                    <th colspan="2">
                        <h2><?php _e( 'Header Settings', 'nosfirnews' ); ?></h2>
                    </th>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="show_search"><?php _e( 'Show Search in Header', 'nosfirnews' ); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="show_search" name="nosfirnews_options[show_search]" value="1" <?php checked( $options['show_search'], 1 ); ?> />
                        <p class="description"><?php _e( 'Display search form in the header area.', 'nosfirnews' ); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="sticky_header"><?php _e( 'Sticky Header', 'nosfirnews' ); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="sticky_header" name="nosfirnews_options[sticky_header]" value="1" <?php checked( $options['sticky_header'], 1 ); ?> />
                        <p class="description"><?php _e( 'Make the header stick to the top when scrolling.', 'nosfirnews' ); ?></p>
                    </td>
                </tr>
                
                <!-- Footer Settings -->
                <tr>
                    <th colspan="2">
                        <h2><?php _e( 'Footer Settings', 'nosfirnews' ); ?></h2>
                    </th>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="footer_text"><?php _e( 'Footer Text', 'nosfirnews' ); ?></label>
                    </th>
                    <td>
                        <textarea id="footer_text" name="nosfirnews_options[footer_text]" rows="3" cols="50"><?php echo esc_textarea( $options['footer_text'] ); ?></textarea>
                        <p class="description"><?php _e( 'Custom text to display in the footer. HTML allowed.', 'nosfirnews' ); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="show_back_to_top"><?php _e( 'Show Back to Top Button', 'nosfirnews' ); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="show_back_to_top" name="nosfirnews_options[show_back_to_top]" value="1" <?php checked( $options['show_back_to_top'], 1 ); ?> />
                        <p class="description"><?php _e( 'Display a floating back to top button.', 'nosfirnews' ); ?></p>
                    </td>
                </tr>
                
                <!-- Social Media Settings -->
                <tr>
                    <th colspan="2">
                        <h2><?php _e( 'Social Media', 'nosfirnews' ); ?></h2>
                    </th>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="facebook_url"><?php _e( 'Facebook URL', 'nosfirnews' ); ?></label>
                    </th>
                    <td>
                        <input type="url" id="facebook_url" name="nosfirnews_options[facebook_url]" value="<?php echo esc_url( $options['facebook_url'] ); ?>" class="regular-text" />
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="twitter_url"><?php _e( 'Twitter URL', 'nosfirnews' ); ?></label>
                    </th>
                    <td>
                        <input type="url" id="twitter_url" name="nosfirnews_options[twitter_url]" value="<?php echo esc_url( $options['twitter_url'] ); ?>" class="regular-text" />
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="instagram_url"><?php _e( 'Instagram URL', 'nosfirnews' ); ?></label>
                    </th>
                    <td>
                        <input type="url" id="instagram_url" name="nosfirnews_options[instagram_url]" value="<?php echo esc_url( $options['instagram_url'] ); ?>" class="regular-text" />
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="linkedin_url"><?php _e( 'LinkedIn URL', 'nosfirnews' ); ?></label>
                    </th>
                    <td>
                        <input type="url" id="linkedin_url" name="nosfirnews_options[linkedin_url]" value="<?php echo esc_url( $options['linkedin_url'] ); ?>" class="regular-text" />
                    </td>
                </tr>
                
                <!-- Performance Settings -->
                <tr>
                    <th colspan="2">
                        <h2><?php _e( 'Performance', 'nosfirnews' ); ?></h2>
                    </th>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="enable_lazy_loading"><?php _e( 'Enable Lazy Loading', 'nosfirnews' ); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="enable_lazy_loading" name="nosfirnews_options[enable_lazy_loading]" value="1" <?php checked( $options['enable_lazy_loading'], 1 ); ?> />
                        <p class="description"><?php _e( 'Enable lazy loading for images to improve page load speed.', 'nosfirnews' ); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="minify_css"><?php _e( 'Minify CSS', 'nosfirnews' ); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="minify_css" name="nosfirnews_options[minify_css]" value="1" <?php checked( $options['minify_css'], 1 ); ?> />
                        <p class="description"><?php _e( 'Minify CSS files to reduce file size.', 'nosfirnews' ); ?></p>
                    </td>
                </tr>
                
            </table>
            
            <?php submit_button(); ?>
        </form>
        
        <!-- Theme Info -->
        <div class="theme-info" style="background: #fff; padding: 20px; margin-top: 20px; border: 1px solid #ccd0d4;">
            <h3><?php _e( 'Theme Information', 'nosfirnews' ); ?></h3>
            <p><strong><?php _e( 'Theme:', 'nosfirnews' ); ?></strong> NosfirNews v<?php echo NOSFIRNEWS_VERSION; ?></p>
            <p><strong><?php _e( 'Author:', 'nosfirnews' ); ?></strong> David L. Almeida</p>
            <p><strong><?php _e( 'Email:', 'nosfirnews' ); ?></strong> <a href="mailto:contato@davidalmeida.xyz">contato@davidalmeida.xyz</a></p>
            <p><strong><?php _e( 'Documentation:', 'nosfirnews' ); ?></strong> <a href="#" target="_blank"><?php _e( 'View Documentation', 'nosfirnews' ); ?></a></p>
        </div>
        
    </div>
    <?php
}

/**
 * Get default theme options
 */
function nosfirnews_get_default_options() {
    return array(
        'site_layout'        => 'full-width',
        'posts_per_page'     => 10,
        'excerpt_length'     => 30,
        'show_search'        => 1,
        'sticky_header'      => 1,
        'footer_text'        => sprintf( __( '© %s. All rights reserved.', 'nosfirnews' ), date( 'Y' ) ),
        'show_back_to_top'   => 1,
        'facebook_url'       => '',
        'twitter_url'        => '',
        'instagram_url'      => '',
        'linkedin_url'       => '',
        'enable_lazy_loading' => 1,
        'minify_css'         => 0,
    );
}

/**
 * Save theme options
 */
function nosfirnews_save_theme_options() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    
    $options = array();
    
    if ( isset( $_POST['nosfirnews_options'] ) ) {
        $posted_options = $_POST['nosfirnews_options'];
        
        // Sanitize options
        $options['site_layout']        = sanitize_text_field( $posted_options['site_layout'] );
        $options['posts_per_page']     = intval( $posted_options['posts_per_page'] );
        $options['excerpt_length']     = intval( $posted_options['excerpt_length'] );
        $options['show_search']        = isset( $posted_options['show_search'] ) ? 1 : 0;
        $options['sticky_header']      = isset( $posted_options['sticky_header'] ) ? 1 : 0;
        $options['footer_text']        = wp_kses_post( $posted_options['footer_text'] );
        $options['show_back_to_top']   = isset( $posted_options['show_back_to_top'] ) ? 1 : 0;
        $options['facebook_url']       = esc_url_raw( $posted_options['facebook_url'] );
        $options['twitter_url']        = esc_url_raw( $posted_options['twitter_url'] );
        $options['instagram_url']      = esc_url_raw( $posted_options['instagram_url'] );
        $options['linkedin_url']       = esc_url_raw( $posted_options['linkedin_url'] );
        $options['enable_lazy_loading'] = isset( $posted_options['enable_lazy_loading'] ) ? 1 : 0;
        $options['minify_css']         = isset( $posted_options['minify_css'] ) ? 1 : 0;
    }
    
    update_option( 'nosfirnews_theme_options', $options );
}

/**
 * Get theme option value
 */
function nosfirnews_get_option( $option, $default = null ) {
    $options = get_option( 'nosfirnews_theme_options', nosfirnews_get_default_options() );
    
    if ( isset( $options[ $option ] ) ) {
        return $options[ $option ];
    }
    
    return $default;
}