<?php
/**
 * Layout Renderer
 *
 * @package NosfirNews
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Initialize Layout Renderer
 */
function nosfirnews_init_layout_renderer() {
    add_action( 'wp_enqueue_scripts', 'nosfirnews_enqueue_layout_styles' );
    add_action( 'wp_head', 'nosfirnews_output_custom_styles' );
    add_action( 'wp_footer', 'nosfirnews_output_custom_scripts' );
}
add_action( 'init', 'nosfirnews_init_layout_renderer' );

/**
 * Enqueue layout styles
 */
function nosfirnews_enqueue_layout_styles() {
    wp_enqueue_style(
        'nosfirnews-layout-sections',
        get_template_directory_uri() . '/assets/css/layout-sections.css',
        array(),
        '1.0.0'
    );
    
    wp_enqueue_script(
        'nosfirnews-layout-sections',
        get_template_directory_uri() . '/assets/js/layout-sections.js',
        array( 'jquery' ),
        '1.0.0',
        true
    );
}

/**
 * Render layout sections
 */
function nosfirnews_render_layout_sections( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $layout_sections = get_post_meta( $post_id, '_nosfirnews_layout_sections', true );
    
    if ( empty( $layout_sections ) ) {
        return;
    }
    
    echo '<div class="nosfirnews-layout-sections">';
    
    foreach ( $layout_sections as $index => $section ) {
        nosfirnews_render_single_section( $section, $index );
    }
    
    echo '</div>';
}

/**
 * Render single layout section
 */
function nosfirnews_render_single_section( $section, $index ) {
    $type = $section['type'];
    $section_id = 'section-' . $index;
    $section_class = 'layout-section layout-section-' . $type;
    
    echo '<div id="' . esc_attr( $section_id ) . '" class="' . esc_attr( $section_class ) . '">';
    
    switch ( $type ) {
        case 'text':
            nosfirnews_render_text_section( $section );
            break;
        case 'image':
            nosfirnews_render_image_section( $section );
            break;
        case 'columns':
            nosfirnews_render_columns_section( $section );
            break;
        case 'spacer':
            nosfirnews_render_spacer_section( $section );
            break;
        default:
            do_action( 'nosfirnews_render_custom_section', $section, $type );
            break;
    }
    
    echo '</div>';
}

/**
 * Render text section
 */
function nosfirnews_render_text_section( $section ) {
    if ( empty( $section['content'] ) ) {
        return;
    }
    
    echo '<div class="section-content text-section-content">';
    echo wp_kses_post( $section['content'] );
    echo '</div>';
}

/**
 * Render image section
 */
function nosfirnews_render_image_section( $section ) {
    if ( empty( $section['image'] ) ) {
        return;
    }
    
    $alignment = isset( $section['alignment'] ) ? $section['alignment'] : 'center';
    $caption = isset( $section['caption'] ) ? $section['caption'] : '';
    
    echo '<div class="section-content image-section-content align-' . esc_attr( $alignment ) . '">';
    echo '<div class="image-wrapper">';
    echo '<img src="' . esc_url( $section['image'] ) . '" alt="' . esc_attr( $caption ) . '" class="section-image" />';
    
    if ( $caption ) {
        echo '<div class="image-caption">' . esc_html( $caption ) . '</div>';
    }
    
    echo '</div>';
    echo '</div>';
}

/**
 * Render columns section
 */
function nosfirnews_render_columns_section( $section ) {
    $columns = isset( $section['columns'] ) ? intval( $section['columns'] ) : 2;
    
    echo '<div class="section-content columns-section-content columns-' . esc_attr( $columns ) . '">';
    echo '<div class="columns-wrapper">';
    
    for ( $i = 1; $i <= $columns; $i++ ) {
        $column_content = isset( $section['column_' . $i] ) ? $section['column_' . $i] : '';
        
        if ( $column_content ) {
            echo '<div class="column column-' . esc_attr( $i ) . '">';
            echo wp_kses_post( $column_content );
            echo '</div>';
        }
    }
    
    echo '</div>';
    echo '</div>';
}

/**
 * Render spacer section
 */
function nosfirnews_render_spacer_section( $section ) {
    $height = isset( $section['height'] ) ? intval( $section['height'] ) : 50;
    
    echo '<div class="section-content spacer-section-content" style="height: ' . esc_attr( $height ) . 'px;"></div>';
}

/**
 * Render hero section
 */
function nosfirnews_render_hero_section( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $hero_section = get_post_meta( $post_id, '_nosfirnews_hero_section', true );
    
    if ( empty( $hero_section ) || ! $hero_section['enabled'] ) {
        return;
    }
    
    $background_image = $hero_section['background_image'];
    $overlay_opacity = isset( $hero_section['overlay_opacity'] ) ? $hero_section['overlay_opacity'] : 0.5;
    
    ?>
    <div class="nosfirnews-hero-section" <?php if ( $background_image ) : ?>style="background-image: url('<?php echo esc_url( $background_image ); ?>');"<?php endif; ?>>
        <?php if ( $background_image ) : ?>
            <div class="hero-overlay" style="opacity: <?php echo esc_attr( $overlay_opacity ); ?>;"></div>
        <?php endif; ?>
        
        <div class="hero-content">
            <div class="container">
                <?php if ( $hero_section['title'] ) : ?>
                    <h1 class="hero-title"><?php echo esc_html( $hero_section['title'] ); ?></h1>
                <?php endif; ?>
                
                <?php if ( $hero_section['subtitle'] ) : ?>
                    <p class="hero-subtitle"><?php echo esc_html( $hero_section['subtitle'] ); ?></p>
                <?php endif; ?>
                
                <?php if ( $hero_section['button_text'] && $hero_section['button_url'] ) : ?>
                    <div class="hero-button">
                        <a href="<?php echo esc_url( $hero_section['button_url'] ); ?>" class="btn btn-primary btn-hero">
                            <?php echo esc_html( $hero_section['button_text'] ); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Render call to action section
 */
function nosfirnews_render_cta_section( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $cta_section = get_post_meta( $post_id, '_nosfirnews_call_to_action', true );
    
    if ( empty( $cta_section ) || ! $cta_section['enabled'] ) {
        return;
    }
    
    $background_color = isset( $cta_section['background_color'] ) ? $cta_section['background_color'] : '#007cba';
    $text_color = isset( $cta_section['text_color'] ) ? $cta_section['text_color'] : '#ffffff';
    
    ?>
    <div class="nosfirnews-cta-section" style="background-color: <?php echo esc_attr( $background_color ); ?>; color: <?php echo esc_attr( $text_color ); ?>;">
        <div class="container">
            <div class="cta-content">
                <?php if ( $cta_section['title'] ) : ?>
                    <h2 class="cta-title"><?php echo esc_html( $cta_section['title'] ); ?></h2>
                <?php endif; ?>
                
                <?php if ( $cta_section['description'] ) : ?>
                    <p class="cta-description"><?php echo esc_html( $cta_section['description'] ); ?></p>
                <?php endif; ?>
                
                <?php if ( $cta_section['button_text'] && $cta_section['button_url'] ) : ?>
                    <div class="cta-button">
                        <a href="<?php echo esc_url( $cta_section['button_url'] ); ?>" class="btn btn-secondary btn-cta">
                            <?php echo esc_html( $cta_section['button_text'] ); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Render testimonials section
 */
function nosfirnews_render_testimonials_section( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $testimonials = get_post_meta( $post_id, '_nosfirnews_testimonials', true );
    
    if ( empty( $testimonials ) ) {
        return;
    }
    
    ?>
    <div class="nosfirnews-testimonials-section">
        <div class="container">
            <div class="testimonials-grid">
                <?php foreach ( $testimonials as $testimonial ) : ?>
                    <div class="testimonial-item">
                        <?php if ( $testimonial['quote'] ) : ?>
                            <blockquote class="testimonial-quote">
                                "<?php echo esc_html( $testimonial['quote'] ); ?>"
                            </blockquote>
                        <?php endif; ?>
                        
                        <div class="testimonial-author">
                            <?php if ( $testimonial['avatar'] ) : ?>
                                <div class="author-avatar">
                                    <img src="<?php echo esc_url( $testimonial['avatar'] ); ?>" alt="<?php echo esc_attr( $testimonial['author'] ); ?>" />
                                </div>
                            <?php endif; ?>
                            
                            <div class="author-info">
                                <?php if ( $testimonial['author'] ) : ?>
                                    <div class="author-name"><?php echo esc_html( $testimonial['author'] ); ?></div>
                                <?php endif; ?>
                                
                                <?php if ( $testimonial['position'] ) : ?>
                                    <div class="author-position"><?php echo esc_html( $testimonial['position'] ); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Render features section
 */
function nosfirnews_render_features_section( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $features = get_post_meta( $post_id, '_nosfirnews_features', true );
    
    if ( empty( $features ) ) {
        return;
    }
    
    ?>
    <div class="nosfirnews-features-section">
        <div class="container">
            <div class="features-grid">
                <?php foreach ( $features as $feature ) : ?>
                    <div class="feature-item">
                        <?php if ( $feature['icon'] ) : ?>
                            <div class="feature-icon">
                                <i class="<?php echo esc_attr( $feature['icon'] ); ?>"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="feature-content">
                            <?php if ( $feature['title'] ) : ?>
                                <h3 class="feature-title">
                                    <?php if ( $feature['link'] ) : ?>
                                        <a href="<?php echo esc_url( $feature['link'] ); ?>"><?php echo esc_html( $feature['title'] ); ?></a>
                                    <?php else : ?>
                                        <?php echo esc_html( $feature['title'] ); ?>
                                    <?php endif; ?>
                                </h3>
                            <?php endif; ?>
                            
                            <?php if ( $feature['description'] ) : ?>
                                <p class="feature-description"><?php echo esc_html( $feature['description'] ); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Render media gallery
 */
function nosfirnews_render_media_gallery( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $gallery_images = get_post_meta( $post_id, '_nosfirnews_gallery_images', true );
    $gallery_type = get_post_meta( $post_id, '_nosfirnews_gallery_type', true );
    $gallery_columns = get_post_meta( $post_id, '_nosfirnews_gallery_columns', true );
    
    if ( empty( $gallery_images ) ) {
        return;
    }
    
    if ( empty( $gallery_type ) ) {
        $gallery_type = 'grid';
    }
    
    if ( empty( $gallery_columns ) ) {
        $gallery_columns = 3;
    }
    
    ?>
    <div class="nosfirnews-media-gallery gallery-type-<?php echo esc_attr( $gallery_type ); ?> gallery-columns-<?php echo esc_attr( $gallery_columns ); ?>">
        <div class="container">
            <div class="gallery-wrapper">
                <?php foreach ( $gallery_images as $image ) : ?>
                    <div class="gallery-item">
                        <div class="gallery-image">
                            <?php
                            $image_url = wp_get_attachment_image_url( $image['id'], 'large' );
                            $image_thumb = wp_get_attachment_image_url( $image['id'], 'medium' );
                            ?>
                            
                            <?php if ( $gallery_type === 'lightbox' ) : ?>
                                <a href="<?php echo esc_url( $image_url ); ?>" class="gallery-lightbox" data-lightbox="gallery">
                                    <img src="<?php echo esc_url( $image_thumb ); ?>" alt="<?php echo esc_attr( $image['caption'] ); ?>" />
                                </a>
                            <?php else : ?>
                                <img src="<?php echo esc_url( $image_thumb ); ?>" alt="<?php echo esc_attr( $image['caption'] ); ?>" />
                            <?php endif; ?>
                        </div>
                        
                        <?php if ( $image['caption'] ) : ?>
                            <div class="gallery-caption">
                                <?php echo esc_html( $image['caption'] ); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Output custom CSS for the current post
 */
function nosfirnews_output_custom_styles() {
    if ( ! is_singular() ) {
        return;
    }
    
    $custom_css = get_post_meta( get_the_ID(), '_nosfirnews_custom_css', true );
    
    if ( $custom_css ) {
        echo '<style type="text/css" id="nosfirnews-custom-css">' . wp_strip_all_tags( $custom_css ) . '</style>';
    }
}

/**
 * Output custom JavaScript for the current post
 */
function nosfirnews_output_custom_scripts() {
    if ( ! is_singular() ) {
        return;
    }
    
    $custom_js = get_post_meta( get_the_ID(), '_nosfirnews_custom_js', true );
    
    if ( $custom_js ) {
        echo '<script type="text/javascript" id="nosfirnews-custom-js">' . wp_strip_all_tags( $custom_js ) . '</script>';
    }
}

/**
 * Get layout sections for use in templates
 */
function nosfirnews_get_layout_sections( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    return get_post_meta( $post_id, '_nosfirnews_layout_sections', true );
}

/**
 * Check if post has layout sections
 */
function nosfirnews_has_layout_sections( $post_id = null ) {
    $sections = nosfirnews_get_layout_sections( $post_id );
    return ! empty( $sections );
}

/**
 * Check if post has hero section
 */
function nosfirnews_has_hero_section( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $hero_section = get_post_meta( $post_id, '_nosfirnews_hero_section', true );
    return ! empty( $hero_section ) && $hero_section['enabled'];
}

/**
 * Check if post has CTA section
 */
function nosfirnews_has_cta_section( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $cta_section = get_post_meta( $post_id, '_nosfirnews_call_to_action', true );
    return ! empty( $cta_section ) && $cta_section['enabled'];
}

/**
 * Check if post has testimonials
 */
function nosfirnews_has_testimonials( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $testimonials = get_post_meta( $post_id, '_nosfirnews_testimonials', true );
    return ! empty( $testimonials );
}

/**
 * Check if post has features
 */
function nosfirnews_has_features( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $features = get_post_meta( $post_id, '_nosfirnews_features', true );
    return ! empty( $features );
}

/**
 * Check if post has media gallery
 */
function nosfirnews_has_media_gallery( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $gallery_images = get_post_meta( $post_id, '_nosfirnews_gallery_images', true );
    return ! empty( $gallery_images );
}