<?php
/**
 * Advanced Custom Fields (ACF-like) System
 *
 * @package NosfirNews
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Initialize Advanced Custom Fields
 */
function nosfirnews_init_advanced_fields() {
    add_action( 'add_meta_boxes', 'nosfirnews_add_advanced_meta_boxes' );
    add_action( 'save_post', 'nosfirnews_save_advanced_fields' );
    add_action( 'admin_enqueue_scripts', 'nosfirnews_enqueue_advanced_fields_scripts' );
}
add_action( 'init', 'nosfirnews_init_advanced_fields' );

/**
 * Add advanced meta boxes
 */
function nosfirnews_add_advanced_meta_boxes() {
    // Advanced Content Fields
    add_meta_box(
        'nosfirnews_advanced_content',
        __( 'Advanced Content Fields', 'nosfirnews' ),
        'nosfirnews_advanced_content_callback',
        array( 'post', 'page' ),
        'normal',
        'high'
    );
    
    // Layout Builder
    add_meta_box(
        'nosfirnews_layout_builder',
        __( 'Layout Builder', 'nosfirnews' ),
        'nosfirnews_layout_builder_callback',
        array( 'post', 'page' ),
        'normal',
        'high'
    );
    
    // Media Gallery
    add_meta_box(
        'nosfirnews_media_gallery',
        __( 'Media Gallery', 'nosfirnews' ),
        'nosfirnews_media_gallery_callback',
        array( 'post', 'page' ),
        'normal',
        'default'
    );
}

/**
 * Advanced Content Fields Callback
 */
function nosfirnews_advanced_content_callback( $post ) {
    wp_nonce_field( 'nosfirnews_advanced_fields_nonce', 'nosfirnews_advanced_fields_nonce' );
    
    // Get current values
    $hero_section = get_post_meta( $post->ID, '_nosfirnews_hero_section', true );
    $call_to_action = get_post_meta( $post->ID, '_nosfirnews_call_to_action', true );
    $testimonials = get_post_meta( $post->ID, '_nosfirnews_testimonials', true );
    $features = get_post_meta( $post->ID, '_nosfirnews_features', true );
    $custom_css = get_post_meta( $post->ID, '_nosfirnews_custom_css', true );
    $custom_js = get_post_meta( $post->ID, '_nosfirnews_custom_js', true );
    
    // Default values
    if ( empty( $hero_section ) ) {
        $hero_section = array(
            'enabled' => false,
            'title' => '',
            'subtitle' => '',
            'background_image' => '',
            'button_text' => '',
            'button_url' => '',
            'overlay_opacity' => 0.5
        );
    }
    
    if ( empty( $call_to_action ) ) {
        $call_to_action = array(
            'enabled' => false,
            'title' => '',
            'description' => '',
            'button_text' => '',
            'button_url' => '',
            'background_color' => '#007cba',
            'text_color' => '#ffffff'
        );
    }
    
    if ( empty( $testimonials ) ) {
        $testimonials = array();
    }
    
    if ( empty( $features ) ) {
        $features = array();
    }
    
    ?>
    <div class="nosfirnews-advanced-fields">
        <!-- Tabs Navigation -->
        <div class="nosfirnews-tabs">
            <ul class="nosfirnews-tab-nav">
                <li><a href="#tab-hero" class="active"><?php _e( 'Hero Section', 'nosfirnews' ); ?></a></li>
                <li><a href="#tab-cta"><?php _e( 'Call to Action', 'nosfirnews' ); ?></a></li>
                <li><a href="#tab-testimonials"><?php _e( 'Testimonials', 'nosfirnews' ); ?></a></li>
                <li><a href="#tab-features"><?php _e( 'Features', 'nosfirnews' ); ?></a></li>
                <li><a href="#tab-custom"><?php _e( 'Custom Code', 'nosfirnews' ); ?></a></li>
            </ul>
            
            <!-- Hero Section Tab -->
            <div id="tab-hero" class="nosfirnews-tab-content active">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="hero_enabled"><?php _e( 'Enable Hero Section', 'nosfirnews' ); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="hero_enabled" name="nosfirnews_hero_section[enabled]" value="1" <?php checked( $hero_section['enabled'], true ); ?> />
                            <p class="description"><?php _e( 'Display a hero section at the top of this page/post.', 'nosfirnews' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="hero_title"><?php _e( 'Hero Title', 'nosfirnews' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="hero_title" name="nosfirnews_hero_section[title]" value="<?php echo esc_attr( $hero_section['title'] ); ?>" class="large-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="hero_subtitle"><?php _e( 'Hero Subtitle', 'nosfirnews' ); ?></label>
                        </th>
                        <td>
                            <textarea id="hero_subtitle" name="nosfirnews_hero_section[subtitle]" rows="3" class="large-text"><?php echo esc_textarea( $hero_section['subtitle'] ); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="hero_background_image"><?php _e( 'Background Image', 'nosfirnews' ); ?></label>
                        </th>
                        <td>
                            <div class="nosfirnews-media-upload">
                                <input type="hidden" id="hero_background_image" name="nosfirnews_hero_section[background_image]" value="<?php echo esc_attr( $hero_section['background_image'] ); ?>" />
                                <div class="media-preview">
                                    <?php if ( $hero_section['background_image'] ) : ?>
                                        <img src="<?php echo esc_url( $hero_section['background_image'] ); ?>" style="max-width: 200px; height: auto;" />
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="button nosfirnews-upload-media" data-target="#hero_background_image"><?php _e( 'Select Image', 'nosfirnews' ); ?></button>
                                <button type="button" class="button nosfirnews-remove-media" data-target="#hero_background_image"><?php _e( 'Remove', 'nosfirnews' ); ?></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="hero_button_text"><?php _e( 'Button Text', 'nosfirnews' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="hero_button_text" name="nosfirnews_hero_section[button_text]" value="<?php echo esc_attr( $hero_section['button_text'] ); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="hero_button_url"><?php _e( 'Button URL', 'nosfirnews' ); ?></label>
                        </th>
                        <td>
                            <input type="url" id="hero_button_url" name="nosfirnews_hero_section[button_url]" value="<?php echo esc_attr( $hero_section['button_url'] ); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="hero_overlay_opacity"><?php _e( 'Overlay Opacity', 'nosfirnews' ); ?></label>
                        </th>
                        <td>
                            <input type="range" id="hero_overlay_opacity" name="nosfirnews_hero_section[overlay_opacity]" value="<?php echo esc_attr( $hero_section['overlay_opacity'] ); ?>" min="0" max="1" step="0.1" />
                            <span class="range-value"><?php echo esc_html( $hero_section['overlay_opacity'] ); ?></span>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Call to Action Tab -->
            <div id="tab-cta" class="nosfirnews-tab-content">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="cta_enabled"><?php _e( 'Enable Call to Action', 'nosfirnews' ); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="cta_enabled" name="nosfirnews_call_to_action[enabled]" value="1" <?php checked( $call_to_action['enabled'], true ); ?> />
                            <p class="description"><?php _e( 'Display a call to action section.', 'nosfirnews' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="cta_title"><?php _e( 'CTA Title', 'nosfirnews' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="cta_title" name="nosfirnews_call_to_action[title]" value="<?php echo esc_attr( $call_to_action['title'] ); ?>" class="large-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="cta_description"><?php _e( 'CTA Description', 'nosfirnews' ); ?></label>
                        </th>
                        <td>
                            <textarea id="cta_description" name="nosfirnews_call_to_action[description]" rows="3" class="large-text"><?php echo esc_textarea( $call_to_action['description'] ); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="cta_button_text"><?php _e( 'Button Text', 'nosfirnews' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="cta_button_text" name="nosfirnews_call_to_action[button_text]" value="<?php echo esc_attr( $call_to_action['button_text'] ); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="cta_button_url"><?php _e( 'Button URL', 'nosfirnews' ); ?></label>
                        </th>
                        <td>
                            <input type="url" id="cta_button_url" name="nosfirnews_call_to_action[button_url]" value="<?php echo esc_attr( $call_to_action['button_url'] ); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="cta_background_color"><?php _e( 'Background Color', 'nosfirnews' ); ?></label>
                        </th>
                        <td>
                            <input type="color" id="cta_background_color" name="nosfirnews_call_to_action[background_color]" value="<?php echo esc_attr( $call_to_action['background_color'] ); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="cta_text_color"><?php _e( 'Text Color', 'nosfirnews' ); ?></label>
                        </th>
                        <td>
                            <input type="color" id="cta_text_color" name="nosfirnews_call_to_action[text_color]" value="<?php echo esc_attr( $call_to_action['text_color'] ); ?>" />
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Testimonials Tab -->
            <div id="tab-testimonials" class="nosfirnews-tab-content">
                <div class="nosfirnews-repeater" data-field="testimonials">
                    <div class="repeater-header">
                        <h4><?php _e( 'Testimonials', 'nosfirnews' ); ?></h4>
                        <button type="button" class="button add-repeater-item"><?php _e( 'Add Testimonial', 'nosfirnews' ); ?></button>
                    </div>
                    
                    <div class="repeater-items">
                        <?php if ( ! empty( $testimonials ) ) : ?>
                            <?php foreach ( $testimonials as $index => $testimonial ) : ?>
                                <div class="repeater-item">
                                    <div class="repeater-item-header">
                                        <span class="item-title"><?php echo esc_html( $testimonial['author'] ?: __( 'Testimonial', 'nosfirnews' ) ); ?></span>
                                        <button type="button" class="remove-repeater-item">&times;</button>
                                    </div>
                                    <div class="repeater-item-content">
                                        <table class="form-table">
                                            <tr>
                                                <th><label><?php _e( 'Quote', 'nosfirnews' ); ?></label></th>
                                                <td>
                                                    <textarea name="nosfirnews_testimonials[<?php echo $index; ?>][quote]" rows="3" class="large-text"><?php echo esc_textarea( $testimonial['quote'] ); ?></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><label><?php _e( 'Author', 'nosfirnews' ); ?></label></th>
                                                <td>
                                                    <input type="text" name="nosfirnews_testimonials[<?php echo $index; ?>][author]" value="<?php echo esc_attr( $testimonial['author'] ); ?>" class="regular-text" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><label><?php _e( 'Position', 'nosfirnews' ); ?></label></th>
                                                <td>
                                                    <input type="text" name="nosfirnews_testimonials[<?php echo $index; ?>][position]" value="<?php echo esc_attr( $testimonial['position'] ); ?>" class="regular-text" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><label><?php _e( 'Avatar', 'nosfirnews' ); ?></label></th>
                                                <td>
                                                    <div class="nosfirnews-media-upload">
                                                        <input type="hidden" name="nosfirnews_testimonials[<?php echo $index; ?>][avatar]" value="<?php echo esc_attr( $testimonial['avatar'] ); ?>" />
                                                        <div class="media-preview">
                                                            <?php if ( $testimonial['avatar'] ) : ?>
                                                                <img src="<?php echo esc_url( $testimonial['avatar'] ); ?>" style="max-width: 100px; height: auto;" />
                                                            <?php endif; ?>
                                                        </div>
                                                        <button type="button" class="button nosfirnews-upload-media"><?php _e( 'Select Avatar', 'nosfirnews' ); ?></button>
                                                        <button type="button" class="button nosfirnews-remove-media"><?php _e( 'Remove', 'nosfirnews' ); ?></button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Features Tab -->
            <div id="tab-features" class="nosfirnews-tab-content">
                <div class="nosfirnews-repeater" data-field="features">
                    <div class="repeater-header">
                        <h4><?php _e( 'Features', 'nosfirnews' ); ?></h4>
                        <button type="button" class="button add-repeater-item"><?php _e( 'Add Feature', 'nosfirnews' ); ?></button>
                    </div>
                    
                    <div class="repeater-items">
                        <?php if ( ! empty( $features ) ) : ?>
                            <?php foreach ( $features as $index => $feature ) : ?>
                                <div class="repeater-item">
                                    <div class="repeater-item-header">
                                        <span class="item-title"><?php echo esc_html( $feature['title'] ?: __( 'Feature', 'nosfirnews' ) ); ?></span>
                                        <button type="button" class="remove-repeater-item">&times;</button>
                                    </div>
                                    <div class="repeater-item-content">
                                        <table class="form-table">
                                            <tr>
                                                <th><label><?php _e( 'Title', 'nosfirnews' ); ?></label></th>
                                                <td>
                                                    <input type="text" name="nosfirnews_features[<?php echo $index; ?>][title]" value="<?php echo esc_attr( $feature['title'] ); ?>" class="large-text" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><label><?php _e( 'Description', 'nosfirnews' ); ?></label></th>
                                                <td>
                                                    <textarea name="nosfirnews_features[<?php echo $index; ?>][description]" rows="3" class="large-text"><?php echo esc_textarea( $feature['description'] ); ?></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><label><?php _e( 'Icon', 'nosfirnews' ); ?></label></th>
                                                <td>
                                                    <input type="text" name="nosfirnews_features[<?php echo $index; ?>][icon]" value="<?php echo esc_attr( $feature['icon'] ); ?>" class="regular-text" placeholder="<?php _e( 'Font Awesome class (e.g., fas fa-star)', 'nosfirnews' ); ?>" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><label><?php _e( 'Link URL', 'nosfirnews' ); ?></label></th>
                                                <td>
                                                    <input type="url" name="nosfirnews_features[<?php echo $index; ?>][link]" value="<?php echo esc_attr( $feature['link'] ); ?>" class="regular-text" />
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Custom Code Tab -->
            <div id="tab-custom" class="nosfirnews-tab-content">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="custom_css"><?php _e( 'Custom CSS', 'nosfirnews' ); ?></label>
                        </th>
                        <td>
                            <textarea id="custom_css" name="nosfirnews_custom_css" rows="10" class="large-text code"><?php echo esc_textarea( $custom_css ); ?></textarea>
                            <p class="description"><?php _e( 'Add custom CSS for this page/post only.', 'nosfirnews' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="custom_js"><?php _e( 'Custom JavaScript', 'nosfirnews' ); ?></label>
                        </th>
                        <td>
                            <textarea id="custom_js" name="nosfirnews_custom_js" rows="10" class="large-text code"><?php echo esc_textarea( $custom_js ); ?></textarea>
                            <p class="description"><?php _e( 'Add custom JavaScript for this page/post only. Do not include &lt;script&gt; tags.', 'nosfirnews' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Repeater Templates -->
    <script type="text/template" id="testimonial-template">
        <div class="repeater-item">
            <div class="repeater-item-header">
                <span class="item-title"><?php _e( 'New Testimonial', 'nosfirnews' ); ?></span>
                <button type="button" class="remove-repeater-item">&times;</button>
            </div>
            <div class="repeater-item-content">
                <table class="form-table">
                    <tr>
                        <th><label><?php _e( 'Quote', 'nosfirnews' ); ?></label></th>
                        <td>
                            <textarea name="nosfirnews_testimonials[{{INDEX}}][quote]" rows="3" class="large-text"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th><label><?php _e( 'Author', 'nosfirnews' ); ?></label></th>
                        <td>
                            <input type="text" name="nosfirnews_testimonials[{{INDEX}}][author]" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th><label><?php _e( 'Position', 'nosfirnews' ); ?></label></th>
                        <td>
                            <input type="text" name="nosfirnews_testimonials[{{INDEX}}][position]" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th><label><?php _e( 'Avatar', 'nosfirnews' ); ?></label></th>
                        <td>
                            <div class="nosfirnews-media-upload">
                                <input type="hidden" name="nosfirnews_testimonials[{{INDEX}}][avatar]" />
                                <div class="media-preview"></div>
                                <button type="button" class="button nosfirnews-upload-media"><?php _e( 'Select Avatar', 'nosfirnews' ); ?></button>
                                <button type="button" class="button nosfirnews-remove-media"><?php _e( 'Remove', 'nosfirnews' ); ?></button>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </script>
    
    <script type="text/template" id="feature-template">
        <div class="repeater-item">
            <div class="repeater-item-header">
                <span class="item-title"><?php _e( 'New Feature', 'nosfirnews' ); ?></span>
                <button type="button" class="remove-repeater-item">&times;</button>
            </div>
            <div class="repeater-item-content">
                <table class="form-table">
                    <tr>
                        <th><label><?php _e( 'Title', 'nosfirnews' ); ?></label></th>
                        <td>
                            <input type="text" name="nosfirnews_features[{{INDEX}}][title]" class="large-text" />
                        </td>
                    </tr>
                    <tr>
                        <th><label><?php _e( 'Description', 'nosfirnews' ); ?></label></th>
                        <td>
                            <textarea name="nosfirnews_features[{{INDEX}}][description]" rows="3" class="large-text"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th><label><?php _e( 'Icon', 'nosfirnews' ); ?></label></th>
                        <td>
                            <input type="text" name="nosfirnews_features[{{INDEX}}][icon]" class="regular-text" placeholder="<?php _e( 'Font Awesome class (e.g., fas fa-star)', 'nosfirnews' ); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><label><?php _e( 'Link URL', 'nosfirnews' ); ?></label></th>
                        <td>
                            <input type="url" name="nosfirnews_features[{{INDEX}}][link]" class="regular-text" />
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </script>
    <?php
}

/**
 * Layout Builder Callback
 */
function nosfirnews_layout_builder_callback( $post ) {
    $layout_sections = get_post_meta( $post->ID, '_nosfirnews_layout_sections', true );
    
    if ( empty( $layout_sections ) ) {
        $layout_sections = array();
    }
    
    ?>
    <div class="nosfirnews-layout-builder">
        <div class="layout-builder-header">
            <h4><?php _e( 'Layout Sections', 'nosfirnews' ); ?></h4>
            <div class="section-types">
                <button type="button" class="button add-section" data-type="text"><?php _e( 'Add Text Section', 'nosfirnews' ); ?></button>
                <button type="button" class="button add-section" data-type="image"><?php _e( 'Add Image Section', 'nosfirnews' ); ?></button>
                <button type="button" class="button add-section" data-type="columns"><?php _e( 'Add Columns', 'nosfirnews' ); ?></button>
                <button type="button" class="button add-section" data-type="spacer"><?php _e( 'Add Spacer', 'nosfirnews' ); ?></button>
            </div>
        </div>
        
        <div class="layout-sections" id="layout-sections">
            <?php if ( ! empty( $layout_sections ) ) : ?>
                <?php foreach ( $layout_sections as $index => $section ) : ?>
                    <?php nosfirnews_render_layout_section( $section, $index ); ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Section Templates -->
    <?php nosfirnews_layout_section_templates(); ?>
    <?php
}

/**
 * Media Gallery Callback
 */
function nosfirnews_media_gallery_callback( $post ) {
    $gallery_images = get_post_meta( $post->ID, '_nosfirnews_gallery_images', true );
    $gallery_type = get_post_meta( $post->ID, '_nosfirnews_gallery_type', true );
    $gallery_columns = get_post_meta( $post->ID, '_nosfirnews_gallery_columns', true );
    
    if ( empty( $gallery_images ) ) {
        $gallery_images = array();
    }
    
    if ( empty( $gallery_type ) ) {
        $gallery_type = 'grid';
    }
    
    if ( empty( $gallery_columns ) ) {
        $gallery_columns = 3;
    }
    
    ?>
    <div class="nosfirnews-media-gallery">
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="gallery_type"><?php _e( 'Gallery Type', 'nosfirnews' ); ?></label>
                </th>
                <td>
                    <select id="gallery_type" name="nosfirnews_gallery_type">
                        <option value="grid" <?php selected( $gallery_type, 'grid' ); ?>><?php _e( 'Grid', 'nosfirnews' ); ?></option>
                        <option value="masonry" <?php selected( $gallery_type, 'masonry' ); ?>><?php _e( 'Masonry', 'nosfirnews' ); ?></option>
                        <option value="carousel" <?php selected( $gallery_type, 'carousel' ); ?>><?php _e( 'Carousel', 'nosfirnews' ); ?></option>
                        <option value="lightbox" <?php selected( $gallery_type, 'lightbox' ); ?>><?php _e( 'Lightbox', 'nosfirnews' ); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="gallery_columns"><?php _e( 'Columns', 'nosfirnews' ); ?></label>
                </th>
                <td>
                    <select id="gallery_columns" name="nosfirnews_gallery_columns">
                        <option value="2" <?php selected( $gallery_columns, 2 ); ?>>2</option>
                        <option value="3" <?php selected( $gallery_columns, 3 ); ?>>3</option>
                        <option value="4" <?php selected( $gallery_columns, 4 ); ?>>4</option>
                        <option value="5" <?php selected( $gallery_columns, 5 ); ?>>5</option>
                        <option value="6" <?php selected( $gallery_columns, 6 ); ?>>6</option>
                    </select>
                </td>
            </tr>
        </table>
        
        <div class="gallery-manager">
            <div class="gallery-header">
                <h4><?php _e( 'Gallery Images', 'nosfirnews' ); ?></h4>
                <button type="button" class="button button-primary add-gallery-images"><?php _e( 'Add Images', 'nosfirnews' ); ?></button>
            </div>
            
            <div class="gallery-images" id="gallery-images">
                <?php if ( ! empty( $gallery_images ) ) : ?>
                    <?php foreach ( $gallery_images as $index => $image ) : ?>
                        <div class="gallery-image-item" data-index="<?php echo $index; ?>">
                            <div class="image-preview">
                                <img src="<?php echo esc_url( wp_get_attachment_image_url( $image['id'], 'thumbnail' ) ); ?>" alt="" />
                            </div>
                            <div class="image-controls">
                                <input type="hidden" name="nosfirnews_gallery_images[<?php echo $index; ?>][id]" value="<?php echo esc_attr( $image['id'] ); ?>" />
                                <input type="text" name="nosfirnews_gallery_images[<?php echo $index; ?>][caption]" value="<?php echo esc_attr( $image['caption'] ); ?>" placeholder="<?php _e( 'Caption', 'nosfirnews' ); ?>" class="regular-text" />
                                <button type="button" class="button remove-gallery-image"><?php _e( 'Remove', 'nosfirnews' ); ?></button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Render layout section
 */
function nosfirnews_render_layout_section( $section, $index ) {
    $type = $section['type'];
    ?>
    <div class="layout-section" data-type="<?php echo esc_attr( $type ); ?>" data-index="<?php echo $index; ?>">
        <div class="section-header">
            <span class="section-title"><?php echo esc_html( ucfirst( $type ) . ' Section' ); ?></span>
            <div class="section-controls">
                <button type="button" class="button move-section-up">↑</button>
                <button type="button" class="button move-section-down">↓</button>
                <button type="button" class="button remove-section">×</button>
            </div>
        </div>
        <div class="section-content">
            <?php
            switch ( $type ) {
                case 'text':
                    ?>
                    <table class="form-table">
                        <tr>
                            <th><label><?php _e( 'Content', 'nosfirnews' ); ?></label></th>
                            <td>
                                <?php
                                wp_editor( $section['content'], 'section_content_' . $index, array(
                                    'textarea_name' => 'nosfirnews_layout_sections[' . $index . '][content]',
                                    'textarea_rows' => 5,
                                    'media_buttons' => true,
                                    'teeny' => false
                                ) );
                                ?>
                            </td>
                        </tr>
                    </table>
                    <?php
                    break;
                    
                case 'image':
                    ?>
                    <table class="form-table">
                        <tr>
                            <th><label><?php _e( 'Image', 'nosfirnews' ); ?></label></th>
                            <td>
                                <div class="nosfirnews-media-upload">
                                    <input type="hidden" name="nosfirnews_layout_sections[<?php echo $index; ?>][image]" value="<?php echo esc_attr( $section['image'] ); ?>" />
                                    <div class="media-preview">
                                        <?php if ( $section['image'] ) : ?>
                                            <img src="<?php echo esc_url( $section['image'] ); ?>" style="max-width: 200px; height: auto;" />
                                        <?php endif; ?>
                                    </div>
                                    <button type="button" class="button nosfirnews-upload-media"><?php _e( 'Select Image', 'nosfirnews' ); ?></button>
                                    <button type="button" class="button nosfirnews-remove-media"><?php _e( 'Remove', 'nosfirnews' ); ?></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><label><?php _e( 'Caption', 'nosfirnews' ); ?></label></th>
                            <td>
                                <input type="text" name="nosfirnews_layout_sections[<?php echo $index; ?>][caption]" value="<?php echo esc_attr( $section['caption'] ); ?>" class="large-text" />
                            </td>
                        </tr>
                        <tr>
                            <th><label><?php _e( 'Alignment', 'nosfirnews' ); ?></label></th>
                            <td>
                                <select name="nosfirnews_layout_sections[<?php echo $index; ?>][alignment]">
                                    <option value="left" <?php selected( $section['alignment'], 'left' ); ?>><?php _e( 'Left', 'nosfirnews' ); ?></option>
                                    <option value="center" <?php selected( $section['alignment'], 'center' ); ?>><?php _e( 'Center', 'nosfirnews' ); ?></option>
                                    <option value="right" <?php selected( $section['alignment'], 'right' ); ?>><?php _e( 'Right', 'nosfirnews' ); ?></option>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <?php
                    break;
                    
                case 'columns':
                    ?>
                    <table class="form-table">
                        <tr>
                            <th><label><?php _e( 'Number of Columns', 'nosfirnews' ); ?></label></th>
                            <td>
                                <select name="nosfirnews_layout_sections[<?php echo $index; ?>][columns]" class="columns-selector">
                                    <option value="2" <?php selected( $section['columns'], 2 ); ?>>2</option>
                                    <option value="3" <?php selected( $section['columns'], 3 ); ?>>3</option>
                                    <option value="4" <?php selected( $section['columns'], 4 ); ?>>4</option>
                                </select>
                            </td>
                        </tr>
                        <?php
                        $columns = isset( $section['columns'] ) ? intval( $section['columns'] ) : 2;
                        for ( $i = 1; $i <= $columns; $i++ ) :
                        ?>
                        <tr>
                            <th><label><?php printf( __( 'Column %d Content', 'nosfirnews' ), $i ); ?></label></th>
                            <td>
                                <?php
                                $content = isset( $section['column_' . $i] ) ? $section['column_' . $i] : '';
                                wp_editor( $content, 'section_column_' . $i . '_' . $index, array(
                                    'textarea_name' => 'nosfirnews_layout_sections[' . $index . '][column_' . $i . ']',
                                    'textarea_rows' => 3,
                                    'media_buttons' => false,
                                    'teeny' => true
                                ) );
                                ?>
                            </td>
                        </tr>
                        <?php endfor; ?>
                    </table>
                    <?php
                    break;
                    
                case 'spacer':
                    ?>
                    <table class="form-table">
                        <tr>
                            <th><label><?php _e( 'Height (px)', 'nosfirnews' ); ?></label></th>
                            <td>
                                <input type="number" name="nosfirnews_layout_sections[<?php echo $index; ?>][height]" value="<?php echo esc_attr( $section['height'] ); ?>" min="10" max="500" class="small-text" />
                            </td>
                        </tr>
                    </table>
                    <?php
                    break;
            }
            ?>
            <input type="hidden" name="nosfirnews_layout_sections[<?php echo $index; ?>][type]" value="<?php echo esc_attr( $type ); ?>" />
        </div>
    </div>
    <?php
}

/**
 * Layout section templates
 */
function nosfirnews_layout_section_templates() {
    ?>
    <!-- Text Section Template -->
    <script type="text/template" id="text-section-template">
        <div class="layout-section" data-type="text" data-index="{{INDEX}}">
            <div class="section-header">
                <span class="section-title"><?php _e( 'Text Section', 'nosfirnews' ); ?></span>
                <div class="section-controls">
                    <button type="button" class="button move-section-up">↑</button>
                    <button type="button" class="button move-section-down">↓</button>
                    <button type="button" class="button remove-section">×</button>
                </div>
            </div>
            <div class="section-content">
                <table class="form-table">
                    <tr>
                        <th><label><?php _e( 'Content', 'nosfirnews' ); ?></label></th>
                        <td>
                            <textarea name="nosfirnews_layout_sections[{{INDEX}}][content]" rows="5" class="large-text"></textarea>
                        </td>
                    </tr>
                </table>
                <input type="hidden" name="nosfirnews_layout_sections[{{INDEX}}][type]" value="text" />
            </div>
        </div>
    </script>
    
    <!-- Image Section Template -->
    <script type="text/template" id="image-section-template">
        <div class="layout-section" data-type="image" data-index="{{INDEX}}">
            <div class="section-header">
                <span class="section-title"><?php _e( 'Image Section', 'nosfirnews' ); ?></span>
                <div class="section-controls">
                    <button type="button" class="button move-section-up">↑</button>
                    <button type="button" class="button move-section-down">↓</button>
                    <button type="button" class="button remove-section">×</button>
                </div>
            </div>
            <div class="section-content">
                <table class="form-table">
                    <tr>
                        <th><label><?php _e( 'Image', 'nosfirnews' ); ?></label></th>
                        <td>
                            <div class="nosfirnews-media-upload">
                                <input type="hidden" name="nosfirnews_layout_sections[{{INDEX}}][image]" />
                                <div class="media-preview"></div>
                                <button type="button" class="button nosfirnews-upload-media"><?php _e( 'Select Image', 'nosfirnews' ); ?></button>
                                <button type="button" class="button nosfirnews-remove-media"><?php _e( 'Remove', 'nosfirnews' ); ?></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label><?php _e( 'Caption', 'nosfirnews' ); ?></label></th>
                        <td>
                            <input type="text" name="nosfirnews_layout_sections[{{INDEX}}][caption]" class="large-text" />
                        </td>
                    </tr>
                    <tr>
                        <th><label><?php _e( 'Alignment', 'nosfirnews' ); ?></label></th>
                        <td>
                            <select name="nosfirnews_layout_sections[{{INDEX}}][alignment]">
                                <option value="left"><?php _e( 'Left', 'nosfirnews' ); ?></option>
                                <option value="center"><?php _e( 'Center', 'nosfirnews' ); ?></option>
                                <option value="right"><?php _e( 'Right', 'nosfirnews' ); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
                <input type="hidden" name="nosfirnews_layout_sections[{{INDEX}}][type]" value="image" />
            </div>
        </div>
    </script>
    
    <!-- Columns Section Template -->
    <script type="text/template" id="columns-section-template">
        <div class="layout-section" data-type="columns" data-index="{{INDEX}}">
            <div class="section-header">
                <span class="section-title"><?php _e( 'Columns Section', 'nosfirnews' ); ?></span>
                <div class="section-controls">
                    <button type="button" class="button move-section-up">↑</button>
                    <button type="button" class="button move-section-down">↓</button>
                    <button type="button" class="button remove-section">×</button>
                </div>
            </div>
            <div class="section-content">
                <table class="form-table">
                    <tr>
                        <th><label><?php _e( 'Number of Columns', 'nosfirnews' ); ?></label></th>
                        <td>
                            <select name="nosfirnews_layout_sections[{{INDEX}}][columns]" class="columns-selector">
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </td>
                    </tr>
                    <tr class="column-content" data-column="1">
                        <th><label><?php _e( 'Column 1 Content', 'nosfirnews' ); ?></label></th>
                        <td>
                            <textarea name="nosfirnews_layout_sections[{{INDEX}}][column_1]" rows="3" class="large-text"></textarea>
                        </td>
                    </tr>
                    <tr class="column-content" data-column="2">
                        <th><label><?php _e( 'Column 2 Content', 'nosfirnews' ); ?></label></th>
                        <td>
                            <textarea name="nosfirnews_layout_sections[{{INDEX}}][column_2]" rows="3" class="large-text"></textarea>
                        </td>
                    </tr>
                </table>
                <input type="hidden" name="nosfirnews_layout_sections[{{INDEX}}][type]" value="columns" />
            </div>
        </div>
    </script>
    
    <!-- Spacer Section Template -->
    <script type="text/template" id="spacer-section-template">
        <div class="layout-section" data-type="spacer" data-index="{{INDEX}}">
            <div class="section-header">
                <span class="section-title"><?php _e( 'Spacer Section', 'nosfirnews' ); ?></span>
                <div class="section-controls">
                    <button type="button" class="button move-section-up">↑</button>
                    <button type="button" class="button move-section-down">↓</button>
                    <button type="button" class="button remove-section">×</button>
                </div>
            </div>
            <div class="section-content">
                <table class="form-table">
                    <tr>
                        <th><label><?php _e( 'Height (px)', 'nosfirnews' ); ?></label></th>
                        <td>
                            <input type="number" name="nosfirnews_layout_sections[{{INDEX}}][height]" value="50" min="10" max="500" class="small-text" />
                        </td>
                    </tr>
                </table>
                <input type="hidden" name="nosfirnews_layout_sections[{{INDEX}}][type]" value="spacer" />
            </div>
        </div>
    </script>
    <?php
}

/**
 * Save advanced fields
 */
function nosfirnews_save_advanced_fields( $post_id ) {
    // Check if nonce is valid
    if ( ! isset( $_POST['nosfirnews_advanced_fields_nonce'] ) || ! wp_verify_nonce( $_POST['nosfirnews_advanced_fields_nonce'], 'nosfirnews_advanced_fields_nonce' ) ) {
        return;
    }
    
    // Check if user has permission to edit the post
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    
    // Check if not an autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    
    // Save hero section
    if ( isset( $_POST['nosfirnews_hero_section'] ) ) {
        $hero_section = array_map( 'sanitize_text_field', $_POST['nosfirnews_hero_section'] );
        $hero_section['enabled'] = isset( $_POST['nosfirnews_hero_section']['enabled'] );
        update_post_meta( $post_id, '_nosfirnews_hero_section', $hero_section );
    }
    
    // Save call to action
    if ( isset( $_POST['nosfirnews_call_to_action'] ) ) {
        $call_to_action = array_map( 'sanitize_text_field', $_POST['nosfirnews_call_to_action'] );
        $call_to_action['enabled'] = isset( $_POST['nosfirnews_call_to_action']['enabled'] );
        update_post_meta( $post_id, '_nosfirnews_call_to_action', $call_to_action );
    }
    
    // Save testimonials
    if ( isset( $_POST['nosfirnews_testimonials'] ) ) {
        $testimonials = array();
        foreach ( $_POST['nosfirnews_testimonials'] as $testimonial ) {
            $testimonials[] = array_map( 'sanitize_text_field', $testimonial );
        }
        update_post_meta( $post_id, '_nosfirnews_testimonials', $testimonials );
    }
    
    // Save features
    if ( isset( $_POST['nosfirnews_features'] ) ) {
        $features = array();
        foreach ( $_POST['nosfirnews_features'] as $feature ) {
            $features[] = array_map( 'sanitize_text_field', $feature );
        }
        update_post_meta( $post_id, '_nosfirnews_features', $features );
    }
    
    // Save layout sections
    if ( isset( $_POST['nosfirnews_layout_sections'] ) ) {
        $layout_sections = array();
        foreach ( $_POST['nosfirnews_layout_sections'] as $section ) {
            $clean_section = array();
            $clean_section['type'] = sanitize_text_field( $section['type'] );
            
            switch ( $clean_section['type'] ) {
                case 'text':
                    $clean_section['content'] = wp_kses_post( $section['content'] );
                    break;
                case 'image':
                    $clean_section['image'] = esc_url_raw( $section['image'] );
                    $clean_section['caption'] = sanitize_text_field( $section['caption'] );
                    $clean_section['alignment'] = sanitize_text_field( $section['alignment'] );
                    break;
                case 'columns':
                    $clean_section['columns'] = intval( $section['columns'] );
                    for ( $i = 1; $i <= $clean_section['columns']; $i++ ) {
                        if ( isset( $section['column_' . $i] ) ) {
                            $clean_section['column_' . $i] = wp_kses_post( $section['column_' . $i] );
                        }
                    }
                    break;
                case 'spacer':
                    $clean_section['height'] = intval( $section['height'] );
                    break;
            }
            
            $layout_sections[] = $clean_section;
        }
        update_post_meta( $post_id, '_nosfirnews_layout_sections', $layout_sections );
    }
    
    // Save gallery
    if ( isset( $_POST['nosfirnews_gallery_images'] ) ) {
        $gallery_images = array();
        foreach ( $_POST['nosfirnews_gallery_images'] as $image ) {
            $gallery_images[] = array(
                'id' => intval( $image['id'] ),
                'caption' => sanitize_text_field( $image['caption'] )
            );
        }
        update_post_meta( $post_id, '_nosfirnews_gallery_images', $gallery_images );
    }
    
    if ( isset( $_POST['nosfirnews_gallery_type'] ) ) {
        update_post_meta( $post_id, '_nosfirnews_gallery_type', sanitize_text_field( $_POST['nosfirnews_gallery_type'] ) );
    }
    
    if ( isset( $_POST['nosfirnews_gallery_columns'] ) ) {
        update_post_meta( $post_id, '_nosfirnews_gallery_columns', intval( $_POST['nosfirnews_gallery_columns'] ) );
    }
    
    // Save custom CSS and JS
    if ( isset( $_POST['nosfirnews_custom_css'] ) ) {
        update_post_meta( $post_id, '_nosfirnews_custom_css', sanitize_textarea_field( $_POST['nosfirnews_custom_css'] ) );
    }
    
    if ( isset( $_POST['nosfirnews_custom_js'] ) ) {
        update_post_meta( $post_id, '_nosfirnews_custom_js', sanitize_textarea_field( $_POST['nosfirnews_custom_js'] ) );
    }
}

/**
 * Enqueue advanced fields scripts
 */
function nosfirnews_enqueue_advanced_fields_scripts( $hook ) {
    if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
        return;
    }
    
    wp_enqueue_media();
    wp_enqueue_script( 'jquery-ui-sortable' );
    
    wp_enqueue_script(
        'nosfirnews-advanced-fields',
        get_template_directory_uri() . '/assets/js/advanced-fields.js',
        array( 'jquery', 'jquery-ui-sortable' ),
        '1.0.0',
        true
    );
    
    wp_enqueue_style(
        'nosfirnews-advanced-fields',
        get_template_directory_uri() . '/assets/css/advanced-fields.css',
        array(),
        '1.0.0'
    );
    
    wp_localize_script( 'nosfirnews-advanced-fields', 'nosfirnews_advanced', array(
        'confirm_remove' => __( 'Are you sure you want to remove this item?', 'nosfirnews' ),
        'select_images' => __( 'Select Images', 'nosfirnews' ),
        'use_images' => __( 'Use Images', 'nosfirnews' )
    ) );
}