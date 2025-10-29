<?php
/**
 * Visual Editor - Colors and Typography
 *
 * @package NosfirNews
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Initialize Visual Editor
 */
function nosfirnews_init_visual_editor() {
    add_action( 'add_meta_boxes', 'nosfirnews_add_visual_editor_metaboxes' );
    add_action( 'save_post', 'nosfirnews_save_visual_editor_data' );
    add_action( 'admin_enqueue_scripts', 'nosfirnews_enqueue_visual_editor_assets' );
    add_action( 'wp_head', 'nosfirnews_output_visual_styles' );
    add_action( 'wp_enqueue_scripts', 'nosfirnews_enqueue_visual_frontend_assets' );
}
add_action( 'init', 'nosfirnews_init_visual_editor' );

/**
 * Add visual editor metaboxes
 */
function nosfirnews_add_visual_editor_metaboxes() {
    $post_types = array( 'post', 'page' );
    
    foreach ( $post_types as $post_type ) {
        add_meta_box(
            'nosfirnews_visual_editor',
            __( 'Visual Editor - Colors & Typography', 'nosfirnews' ),
            'nosfirnews_visual_editor_callback',
            $post_type,
            'normal',
            'high'
        );
    }
}

/**
 * Visual editor metabox callback
 */
function nosfirnews_visual_editor_callback( $post ) {
    wp_nonce_field( 'nosfirnews_visual_editor_nonce', 'nosfirnews_visual_editor_nonce' );
    
    // Get saved values
    $visual_settings = get_post_meta( $post->ID, '_nosfirnews_visual_settings', true );
    
    // Default values
    $defaults = array(
        'primary_color' => '#007cba',
        'secondary_color' => '#6c757d',
        'accent_color' => '#28a745',
        'text_color' => '#333333',
        'background_color' => '#ffffff',
        'link_color' => '#007cba',
        'link_hover_color' => '#005a87',
        'heading_font' => 'inherit',
        'body_font' => 'inherit',
        'heading_size' => '2.5',
        'body_size' => '1',
        'line_height' => '1.6',
        'letter_spacing' => '0',
        'font_weight' => '400',
        'heading_weight' => '700',
        'custom_css' => '',
        'enable_dark_mode' => false,
        'dark_primary_color' => '#1a73e8',
        'dark_background_color' => '#121212',
        'dark_text_color' => '#ffffff'
    );
    
    $visual_settings = wp_parse_args( $visual_settings, $defaults );
    ?>
    
    <div id="nosfirnews-visual-editor" class="nosfirnews-visual-editor">
        <div class="visual-editor-tabs">
            <nav class="tab-nav">
                <button type="button" class="tab-nav-item active" data-tab="colors">
                    <span class="dashicons dashicons-admin-appearance"></span>
                    <?php _e( 'Colors', 'nosfirnews' ); ?>
                </button>
                <button type="button" class="tab-nav-item" data-tab="typography">
                    <span class="dashicons dashicons-editor-textcolor"></span>
                    <?php _e( 'Typography', 'nosfirnews' ); ?>
                </button>
                <button type="button" class="tab-nav-item" data-tab="layout">
                    <span class="dashicons dashicons-layout"></span>
                    <?php _e( 'Layout', 'nosfirnews' ); ?>
                </button>
                <button type="button" class="tab-nav-item" data-tab="custom">
                    <span class="dashicons dashicons-editor-code"></span>
                    <?php _e( 'Custom CSS', 'nosfirnews' ); ?>
                </button>
            </nav>
            
            <div class="tab-content">
                <!-- Colors Tab -->
                <div id="tab-colors" class="tab-panel active">
                    <h3><?php _e( 'Color Scheme', 'nosfirnews' ); ?></h3>
                    
                    <div class="color-grid">
                        <div class="color-field">
                            <label for="primary_color"><?php _e( 'Primary Color', 'nosfirnews' ); ?></label>
                            <input type="color" id="primary_color" name="visual_settings[primary_color]" value="<?php echo esc_attr( $visual_settings['primary_color'] ); ?>" class="color-picker">
                            <input type="text" class="color-text" value="<?php echo esc_attr( $visual_settings['primary_color'] ); ?>">
                        </div>
                        
                        <div class="color-field">
                            <label for="secondary_color"><?php _e( 'Secondary Color', 'nosfirnews' ); ?></label>
                            <input type="color" id="secondary_color" name="visual_settings[secondary_color]" value="<?php echo esc_attr( $visual_settings['secondary_color'] ); ?>" class="color-picker">
                            <input type="text" class="color-text" value="<?php echo esc_attr( $visual_settings['secondary_color'] ); ?>">
                        </div>
                        
                        <div class="color-field">
                            <label for="accent_color"><?php _e( 'Accent Color', 'nosfirnews' ); ?></label>
                            <input type="color" id="accent_color" name="visual_settings[accent_color]" value="<?php echo esc_attr( $visual_settings['accent_color'] ); ?>" class="color-picker">
                            <input type="text" class="color-text" value="<?php echo esc_attr( $visual_settings['accent_color'] ); ?>">
                        </div>
                        
                        <div class="color-field">
                            <label for="text_color"><?php _e( 'Text Color', 'nosfirnews' ); ?></label>
                            <input type="color" id="text_color" name="visual_settings[text_color]" value="<?php echo esc_attr( $visual_settings['text_color'] ); ?>" class="color-picker">
                            <input type="text" class="color-text" value="<?php echo esc_attr( $visual_settings['text_color'] ); ?>">
                        </div>
                        
                        <div class="color-field">
                            <label for="background_color"><?php _e( 'Background Color', 'nosfirnews' ); ?></label>
                            <input type="color" id="background_color" name="visual_settings[background_color]" value="<?php echo esc_attr( $visual_settings['background_color'] ); ?>" class="color-picker">
                            <input type="text" class="color-text" value="<?php echo esc_attr( $visual_settings['background_color'] ); ?>">
                        </div>
                        
                        <div class="color-field">
                            <label for="link_color"><?php _e( 'Link Color', 'nosfirnews' ); ?></label>
                            <input type="color" id="link_color" name="visual_settings[link_color]" value="<?php echo esc_attr( $visual_settings['link_color'] ); ?>" class="color-picker">
                            <input type="text" class="color-text" value="<?php echo esc_attr( $visual_settings['link_color'] ); ?>">
                        </div>
                        
                        <div class="color-field">
                            <label for="link_hover_color"><?php _e( 'Link Hover Color', 'nosfirnews' ); ?></label>
                            <input type="color" id="link_hover_color" name="visual_settings[link_hover_color]" value="<?php echo esc_attr( $visual_settings['link_hover_color'] ); ?>" class="color-picker">
                            <input type="text" class="color-text" value="<?php echo esc_attr( $visual_settings['link_hover_color'] ); ?>">
                        </div>
                    </div>
                    
                    <div class="color-presets">
                        <h4><?php _e( 'Color Presets', 'nosfirnews' ); ?></h4>
                        <div class="preset-buttons">
                            <button type="button" class="preset-btn" data-preset="default">
                                <span class="preset-colors">
                                    <span style="background: #007cba;"></span>
                                    <span style="background: #6c757d;"></span>
                                    <span style="background: #28a745;"></span>
                                </span>
                                <?php _e( 'Default', 'nosfirnews' ); ?>
                            </button>
                            <button type="button" class="preset-btn" data-preset="modern">
                                <span class="preset-colors">
                                    <span style="background: #6366f1;"></span>
                                    <span style="background: #8b5cf6;"></span>
                                    <span style="background: #06b6d4;"></span>
                                </span>
                                <?php _e( 'Modern', 'nosfirnews' ); ?>
                            </button>
                            <button type="button" class="preset-btn" data-preset="warm">
                                <span class="preset-colors">
                                    <span style="background: #f59e0b;"></span>
                                    <span style="background: #ef4444;"></span>
                                    <span style="background: #f97316;"></span>
                                </span>
                                <?php _e( 'Warm', 'nosfirnews' ); ?>
                            </button>
                            <button type="button" class="preset-btn" data-preset="nature">
                                <span class="preset-colors">
                                    <span style="background: #059669;"></span>
                                    <span style="background: #0d9488;"></span>
                                    <span style="background: #84cc16;"></span>
                                </span>
                                <?php _e( 'Nature', 'nosfirnews' ); ?>
                            </button>
                        </div>
                    </div>
                    
                    <div class="dark-mode-section">
                        <h4><?php _e( 'Dark Mode', 'nosfirnews' ); ?></h4>
                        <label class="toggle-switch">
                            <input type="checkbox" name="visual_settings[enable_dark_mode]" value="1" <?php checked( $visual_settings['enable_dark_mode'], 1 ); ?>>
                            <span class="toggle-slider"></span>
                            <?php _e( 'Enable Dark Mode Support', 'nosfirnews' ); ?>
                        </label>
                        
                        <div class="dark-mode-colors" style="<?php echo $visual_settings['enable_dark_mode'] ? '' : 'display: none;'; ?>">
                            <div class="color-field">
                                <label for="dark_primary_color"><?php _e( 'Dark Primary Color', 'nosfirnews' ); ?></label>
                                <input type="color" id="dark_primary_color" name="visual_settings[dark_primary_color]" value="<?php echo esc_attr( $visual_settings['dark_primary_color'] ); ?>" class="color-picker">
                                <input type="text" class="color-text" value="<?php echo esc_attr( $visual_settings['dark_primary_color'] ); ?>">
                            </div>
                            
                            <div class="color-field">
                                <label for="dark_background_color"><?php _e( 'Dark Background Color', 'nosfirnews' ); ?></label>
                                <input type="color" id="dark_background_color" name="visual_settings[dark_background_color]" value="<?php echo esc_attr( $visual_settings['dark_background_color'] ); ?>" class="color-picker">
                                <input type="text" class="color-text" value="<?php echo esc_attr( $visual_settings['dark_background_color'] ); ?>">
                            </div>
                            
                            <div class="color-field">
                                <label for="dark_text_color"><?php _e( 'Dark Text Color', 'nosfirnews' ); ?></label>
                                <input type="color" id="dark_text_color" name="visual_settings[dark_text_color]" value="<?php echo esc_attr( $visual_settings['dark_text_color'] ); ?>" class="color-picker">
                                <input type="text" class="color-text" value="<?php echo esc_attr( $visual_settings['dark_text_color'] ); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Typography Tab -->
                <div id="tab-typography" class="tab-panel">
                    <h3><?php _e( 'Typography Settings', 'nosfirnews' ); ?></h3>
                    
                    <div class="typography-grid">
                        <div class="typography-field">
                            <label for="heading_font"><?php _e( 'Heading Font', 'nosfirnews' ); ?></label>
                            <select id="heading_font" name="visual_settings[heading_font]" class="font-selector">
                                <option value="inherit" <?php selected( $visual_settings['heading_font'], 'inherit' ); ?>><?php _e( 'Inherit from theme', 'nosfirnews' ); ?></option>
                                <option value="Arial, sans-serif" <?php selected( $visual_settings['heading_font'], 'Arial, sans-serif' ); ?>>Arial</option>
                                <option value="Georgia, serif" <?php selected( $visual_settings['heading_font'], 'Georgia, serif' ); ?>>Georgia</option>
                                <option value="'Times New Roman', serif" <?php selected( $visual_settings['heading_font'], "'Times New Roman', serif" ); ?>>Times New Roman</option>
                                <option value="'Helvetica Neue', sans-serif" <?php selected( $visual_settings['heading_font'], "'Helvetica Neue', sans-serif" ); ?>>Helvetica Neue</option>
                                <option value="'Roboto', sans-serif" <?php selected( $visual_settings['heading_font'], "'Roboto', sans-serif" ); ?>>Roboto</option>
                                <option value="'Open Sans', sans-serif" <?php selected( $visual_settings['heading_font'], "'Open Sans', sans-serif" ); ?>>Open Sans</option>
                                <option value="'Lato', sans-serif" <?php selected( $visual_settings['heading_font'], "'Lato', sans-serif" ); ?>>Lato</option>
                                <option value="'Montserrat', sans-serif" <?php selected( $visual_settings['heading_font'], "'Montserrat', sans-serif" ); ?>>Montserrat</option>
                                <option value="'Playfair Display', serif" <?php selected( $visual_settings['heading_font'], "'Playfair Display', serif" ); ?>>Playfair Display</option>
                                <option value="'Source Sans Pro', sans-serif" <?php selected( $visual_settings['heading_font'], "'Source Sans Pro', sans-serif" ); ?>>Source Sans Pro</option>
                            </select>
                        </div>
                        
                        <div class="typography-field">
                            <label for="body_font"><?php _e( 'Body Font', 'nosfirnews' ); ?></label>
                            <select id="body_font" name="visual_settings[body_font]" class="font-selector">
                                <option value="inherit" <?php selected( $visual_settings['body_font'], 'inherit' ); ?>><?php _e( 'Inherit from theme', 'nosfirnews' ); ?></option>
                                <option value="Arial, sans-serif" <?php selected( $visual_settings['body_font'], 'Arial, sans-serif' ); ?>>Arial</option>
                                <option value="Georgia, serif" <?php selected( $visual_settings['body_font'], 'Georgia, serif' ); ?>>Georgia</option>
                                <option value="'Times New Roman', serif" <?php selected( $visual_settings['body_font'], "'Times New Roman', serif" ); ?>>Times New Roman</option>
                                <option value="'Helvetica Neue', sans-serif" <?php selected( $visual_settings['body_font'], "'Helvetica Neue', sans-serif" ); ?>>Helvetica Neue</option>
                                <option value="'Roboto', sans-serif" <?php selected( $visual_settings['body_font'], "'Roboto', sans-serif" ); ?>>Roboto</option>
                                <option value="'Open Sans', sans-serif" <?php selected( $visual_settings['body_font'], "'Open Sans', sans-serif" ); ?>>Open Sans</option>
                                <option value="'Lato', sans-serif" <?php selected( $visual_settings['body_font'], "'Lato', sans-serif" ); ?>>Lato</option>
                                <option value="'Source Sans Pro', sans-serif" <?php selected( $visual_settings['body_font'], "'Source Sans Pro', sans-serif" ); ?>>Source Sans Pro</option>
                            </select>
                        </div>
                        
                        <div class="typography-field">
                            <label for="heading_size"><?php _e( 'Heading Size (rem)', 'nosfirnews' ); ?></label>
                            <input type="range" id="heading_size" name="visual_settings[heading_size]" min="1" max="5" step="0.1" value="<?php echo esc_attr( $visual_settings['heading_size'] ); ?>" class="size-slider">
                            <span class="size-display"><?php echo esc_html( $visual_settings['heading_size'] ); ?>rem</span>
                        </div>
                        
                        <div class="typography-field">
                            <label for="body_size"><?php _e( 'Body Size (rem)', 'nosfirnews' ); ?></label>
                            <input type="range" id="body_size" name="visual_settings[body_size]" min="0.8" max="2" step="0.1" value="<?php echo esc_attr( $visual_settings['body_size'] ); ?>" class="size-slider">
                            <span class="size-display"><?php echo esc_html( $visual_settings['body_size'] ); ?>rem</span>
                        </div>
                        
                        <div class="typography-field">
                            <label for="line_height"><?php _e( 'Line Height', 'nosfirnews' ); ?></label>
                            <input type="range" id="line_height" name="visual_settings[line_height]" min="1" max="3" step="0.1" value="<?php echo esc_attr( $visual_settings['line_height'] ); ?>" class="size-slider">
                            <span class="size-display"><?php echo esc_html( $visual_settings['line_height'] ); ?></span>
                        </div>
                        
                        <div class="typography-field">
                            <label for="letter_spacing"><?php _e( 'Letter Spacing (px)', 'nosfirnews' ); ?></label>
                            <input type="range" id="letter_spacing" name="visual_settings[letter_spacing]" min="-2" max="5" step="0.1" value="<?php echo esc_attr( $visual_settings['letter_spacing'] ); ?>" class="size-slider">
                            <span class="size-display"><?php echo esc_html( $visual_settings['letter_spacing'] ); ?>px</span>
                        </div>
                        
                        <div class="typography-field">
                            <label for="font_weight"><?php _e( 'Body Font Weight', 'nosfirnews' ); ?></label>
                            <select id="font_weight" name="visual_settings[font_weight]">
                                <option value="300" <?php selected( $visual_settings['font_weight'], '300' ); ?>><?php _e( 'Light (300)', 'nosfirnews' ); ?></option>
                                <option value="400" <?php selected( $visual_settings['font_weight'], '400' ); ?>><?php _e( 'Normal (400)', 'nosfirnews' ); ?></option>
                                <option value="500" <?php selected( $visual_settings['font_weight'], '500' ); ?>><?php _e( 'Medium (500)', 'nosfirnews' ); ?></option>
                                <option value="600" <?php selected( $visual_settings['font_weight'], '600' ); ?>><?php _e( 'Semi Bold (600)', 'nosfirnews' ); ?></option>
                                <option value="700" <?php selected( $visual_settings['font_weight'], '700' ); ?>><?php _e( 'Bold (700)', 'nosfirnews' ); ?></option>
                            </select>
                        </div>
                        
                        <div class="typography-field">
                            <label for="heading_weight"><?php _e( 'Heading Font Weight', 'nosfirnews' ); ?></label>
                            <select id="heading_weight" name="visual_settings[heading_weight]">
                                <option value="400" <?php selected( $visual_settings['heading_weight'], '400' ); ?>><?php _e( 'Normal (400)', 'nosfirnews' ); ?></option>
                                <option value="500" <?php selected( $visual_settings['heading_weight'], '500' ); ?>><?php _e( 'Medium (500)', 'nosfirnews' ); ?></option>
                                <option value="600" <?php selected( $visual_settings['heading_weight'], '600' ); ?>><?php _e( 'Semi Bold (600)', 'nosfirnews' ); ?></option>
                                <option value="700" <?php selected( $visual_settings['heading_weight'], '700' ); ?>><?php _e( 'Bold (700)', 'nosfirnews' ); ?></option>
                                <option value="800" <?php selected( $visual_settings['heading_weight'], '800' ); ?>><?php _e( 'Extra Bold (800)', 'nosfirnews' ); ?></option>
                                <option value="900" <?php selected( $visual_settings['heading_weight'], '900' ); ?>><?php _e( 'Black (900)', 'nosfirnews' ); ?></option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="typography-preview">
                        <h4><?php _e( 'Typography Preview', 'nosfirnews' ); ?></h4>
                        <div class="preview-content">
                            <h1 class="preview-heading">Sample Heading Text</h1>
                            <p class="preview-body">This is a sample paragraph to demonstrate how your typography settings will look on the frontend. You can adjust the font family, size, weight, line height, and letter spacing to achieve the perfect look for your content.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Layout Tab -->
                <div id="tab-layout" class="tab-panel">
                    <h3><?php _e( 'Layout Settings', 'nosfirnews' ); ?></h3>
                    <p><?php _e( 'Layout settings will be available in a future update.', 'nosfirnews' ); ?></p>
                </div>
                
                <!-- Custom CSS Tab -->
                <div id="tab-custom" class="tab-panel">
                    <h3><?php _e( 'Custom CSS', 'nosfirnews' ); ?></h3>
                    <p><?php _e( 'Add custom CSS that will be applied to this post/page only.', 'nosfirnews' ); ?></p>
                    
                    <div class="custom-css-editor">
                        <textarea id="custom_css" name="visual_settings[custom_css]" rows="15" placeholder="/* Add your custom CSS here */"><?php echo esc_textarea( $visual_settings['custom_css'] ); ?></textarea>
                    </div>
                    
                    <div class="css-help">
                        <h4><?php _e( 'CSS Selectors Help', 'nosfirnews' ); ?></h4>
                        <ul>
                            <li><code>.entry-content</code> - <?php _e( 'Main content area', 'nosfirnews' ); ?></li>
                            <li><code>.entry-header</code> - <?php _e( 'Post/page header', 'nosfirnews' ); ?></li>
                            <li><code>.entry-title</code> - <?php _e( 'Post/page title', 'nosfirnews' ); ?></li>
                            <li><code>.layout-section</code> - <?php _e( 'Layout sections', 'nosfirnews' ); ?></li>
                            <li><code>.nosfirnews-hero-section</code> - <?php _e( 'Hero sections', 'nosfirnews' ); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="visual-editor-actions">
            <button type="button" class="button button-secondary" id="reset-visual-settings">
                <?php _e( 'Reset to Defaults', 'nosfirnews' ); ?>
            </button>
            <button type="button" class="button button-primary" id="preview-visual-settings">
                <?php _e( 'Preview Changes', 'nosfirnews' ); ?>
            </button>
        </div>
    </div>
    
    <?php
}

/**
 * Save visual editor data
 */
function nosfirnews_save_visual_editor_data( $post_id ) {
    // Check nonce
    if ( ! isset( $_POST['nosfirnews_visual_editor_nonce'] ) || 
         ! wp_verify_nonce( $_POST['nosfirnews_visual_editor_nonce'], 'nosfirnews_visual_editor_nonce' ) ) {
        return;
    }
    
    // Check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    
    // Check permissions
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    
    // Save visual settings
    if ( isset( $_POST['visual_settings'] ) ) {
        $visual_settings = array_map( 'sanitize_text_field', $_POST['visual_settings'] );
        
        // Sanitize custom CSS separately
        if ( isset( $_POST['visual_settings']['custom_css'] ) ) {
            $visual_settings['custom_css'] = wp_strip_all_tags( $_POST['visual_settings']['custom_css'] );
        }
        
        update_post_meta( $post_id, '_nosfirnews_visual_settings', $visual_settings );
    }
}

/**
 * Enqueue visual editor assets
 */
function nosfirnews_enqueue_visual_editor_assets( $hook ) {
    if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
        return;
    }
    
    wp_enqueue_style(
        'nosfirnews-visual-editor',
        get_template_directory_uri() . '/assets/css/visual-editor.css',
        array(),
        '1.0.0'
    );
    
    wp_enqueue_script(
        'nosfirnews-visual-editor',
        get_template_directory_uri() . '/assets/js/visual-editor.js',
        array( 'jquery', 'wp-color-picker' ),
        '1.0.0',
        true
    );
    
    wp_enqueue_style( 'wp-color-picker' );
    
    // Add Google Fonts
    wp_enqueue_style(
        'nosfirnews-google-fonts',
        'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Open+Sans:wght@300;400;600;700&family=Lato:wght@300;400;700&family=Montserrat:wght@400;500;600;700&family=Playfair+Display:wght@400;700&family=Source+Sans+Pro:wght@300;400;600;700&display=swap',
        array(),
        '1.0.0'
    );
}

/**
 * Enqueue frontend visual assets
 */
function nosfirnews_enqueue_visual_frontend_assets() {
    // Add Google Fonts for frontend
    wp_enqueue_style(
        'nosfirnews-google-fonts',
        'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Open+Sans:wght@300;400;600;700&family=Lato:wght@300;400;700&family=Montserrat:wght@400;500;600;700&family=Playfair+Display:wght@400;700&family=Source+Sans+Pro:wght@300;400;600;700&display=swap',
        array(),
        '1.0.0'
    );
}

/**
 * Output visual styles in head
 */
function nosfirnews_output_visual_styles() {
    if ( ! is_singular() ) {
        return;
    }
    
    $visual_settings = get_post_meta( get_the_ID(), '_nosfirnews_visual_settings', true );
    
    if ( empty( $visual_settings ) ) {
        return;
    }
    
    $css = nosfirnews_generate_visual_css( $visual_settings );
    
    if ( $css ) {
        echo '<style type="text/css" id="nosfirnews-visual-styles">' . $css . '</style>';
    }
}

/**
 * Generate CSS from visual settings
 */
function nosfirnews_generate_visual_css( $settings ) {
    $css = '';
    
    // Colors
    if ( ! empty( $settings['primary_color'] ) ) {
        $css .= ':root { --nosfirnews-primary-color: ' . $settings['primary_color'] . '; }';
        $css .= '.btn-primary, .button-primary { background-color: ' . $settings['primary_color'] . '; border-color: ' . $settings['primary_color'] . '; }';
    }
    
    if ( ! empty( $settings['secondary_color'] ) ) {
        $css .= ':root { --nosfirnews-secondary-color: ' . $settings['secondary_color'] . '; }';
        $css .= '.btn-secondary, .button-secondary { background-color: ' . $settings['secondary_color'] . '; border-color: ' . $settings['secondary_color'] . '; }';
    }
    
    if ( ! empty( $settings['accent_color'] ) ) {
        $css .= ':root { --nosfirnews-accent-color: ' . $settings['accent_color'] . '; }';
    }
    
    if ( ! empty( $settings['text_color'] ) ) {
        $css .= 'body, .entry-content { color: ' . $settings['text_color'] . '; }';
    }
    
    if ( ! empty( $settings['background_color'] ) ) {
        $css .= 'body, .site { background-color: ' . $settings['background_color'] . '; }';
    }
    
    if ( ! empty( $settings['link_color'] ) ) {
        $css .= 'a { color: ' . $settings['link_color'] . '; }';
    }
    
    if ( ! empty( $settings['link_hover_color'] ) ) {
        $css .= 'a:hover, a:focus { color: ' . $settings['link_hover_color'] . '; }';
    }
    
    // Typography
    if ( ! empty( $settings['heading_font'] ) && $settings['heading_font'] !== 'inherit' ) {
        $css .= 'h1, h2, h3, h4, h5, h6, .entry-title { font-family: ' . $settings['heading_font'] . '; }';
    }
    
    if ( ! empty( $settings['body_font'] ) && $settings['body_font'] !== 'inherit' ) {
        $css .= 'body, .entry-content { font-family: ' . $settings['body_font'] . '; }';
    }
    
    if ( ! empty( $settings['heading_size'] ) ) {
        $css .= 'h1, .entry-title { font-size: ' . $settings['heading_size'] . 'rem; }';
        $css .= 'h2 { font-size: ' . ( $settings['heading_size'] * 0.8 ) . 'rem; }';
        $css .= 'h3 { font-size: ' . ( $settings['heading_size'] * 0.6 ) . 'rem; }';
    }
    
    if ( ! empty( $settings['body_size'] ) ) {
        $css .= 'body, .entry-content { font-size: ' . $settings['body_size'] . 'rem; }';
    }
    
    if ( ! empty( $settings['line_height'] ) ) {
        $css .= 'body, .entry-content { line-height: ' . $settings['line_height'] . '; }';
    }
    
    if ( ! empty( $settings['letter_spacing'] ) ) {
        $css .= 'body, .entry-content { letter-spacing: ' . $settings['letter_spacing'] . 'px; }';
    }
    
    if ( ! empty( $settings['font_weight'] ) ) {
        $css .= 'body, .entry-content { font-weight: ' . $settings['font_weight'] . '; }';
    }
    
    if ( ! empty( $settings['heading_weight'] ) ) {
        $css .= 'h1, h2, h3, h4, h5, h6, .entry-title { font-weight: ' . $settings['heading_weight'] . '; }';
    }
    
    // Dark mode
    if ( ! empty( $settings['enable_dark_mode'] ) ) {
        $css .= '@media (prefers-color-scheme: dark) {';
        
        if ( ! empty( $settings['dark_primary_color'] ) ) {
            $css .= ':root { --nosfirnews-primary-color: ' . $settings['dark_primary_color'] . '; }';
            $css .= '.btn-primary, .button-primary { background-color: ' . $settings['dark_primary_color'] . '; border-color: ' . $settings['dark_primary_color'] . '; }';
        }
        
        if ( ! empty( $settings['dark_background_color'] ) ) {
            $css .= 'body, .site { background-color: ' . $settings['dark_background_color'] . '; }';
        }
        
        if ( ! empty( $settings['dark_text_color'] ) ) {
            $css .= 'body, .entry-content { color: ' . $settings['dark_text_color'] . '; }';
        }
        
        $css .= '}';
    }
    
    // Custom CSS
    if ( ! empty( $settings['custom_css'] ) ) {
        $css .= $settings['custom_css'];
    }
    
    return $css;
}

/**
 * Get visual settings for a post
 */
function nosfirnews_get_visual_settings( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    return get_post_meta( $post_id, '_nosfirnews_visual_settings', true );
}

/**
 * Check if post has custom visual settings
 */
function nosfirnews_has_visual_settings( $post_id = null ) {
    $settings = nosfirnews_get_visual_settings( $post_id );
    return ! empty( $settings );
}