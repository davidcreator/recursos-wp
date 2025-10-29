<?php
/**
 * Gutenberg Custom Blocks
 *
 * @package NosfirNews
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Initialize Gutenberg blocks
 */
function nosfirnews_init_gutenberg_blocks() {
    // Add theme support for wide and full alignment
    add_theme_support( 'align-wide' );
    
    // Add theme support for responsive embeds
    add_theme_support( 'responsive-embeds' );
    
    // Add theme support for editor styles
    add_theme_support( 'editor-styles' );
    
    // Add editor stylesheet
    add_editor_style( 'assets/css/editor-style.css' );
    
    // Register custom blocks
    nosfirnews_register_custom_blocks();
    
    // Register block patterns
    nosfirnews_register_block_patterns();
}
add_action( 'after_setup_theme', 'nosfirnews_init_gutenberg_blocks' );

/**
 * Register custom blocks
 */
function nosfirnews_register_custom_blocks() {
    // Hero Section Block
    register_block_type( 'nosfirnews/hero-section', array(
        'editor_script' => 'nosfirnews-blocks-js',
        'editor_style'  => 'nosfirnews-blocks-css',
        'style'         => 'nosfirnews-blocks-css',
        'render_callback' => 'nosfirnews_render_hero_block',
        'attributes' => array(
            'title' => array(
                'type' => 'string',
                'default' => 'Hero Title'
            ),
            'subtitle' => array(
                'type' => 'string',
                'default' => 'Hero Subtitle'
            ),
            'backgroundImage' => array(
                'type' => 'string',
                'default' => ''
            ),
            'buttonText' => array(
                'type' => 'string',
                'default' => 'Learn More'
            ),
            'buttonUrl' => array(
                'type' => 'string',
                'default' => '#'
            ),
            'overlayOpacity' => array(
                'type' => 'number',
                'default' => 0.5
            )
        )
    ) );
    
    // Call to Action Block
    register_block_type( 'nosfirnews/call-to-action', array(
        'editor_script' => 'nosfirnews-blocks-js',
        'editor_style'  => 'nosfirnews-blocks-css',
        'style'         => 'nosfirnews-blocks-css',
        'render_callback' => 'nosfirnews_render_cta_block',
        'attributes' => array(
            'title' => array(
                'type' => 'string',
                'default' => 'Call to Action'
            ),
            'description' => array(
                'type' => 'string',
                'default' => 'Description text'
            ),
            'buttonText' => array(
                'type' => 'string',
                'default' => 'Get Started'
            ),
            'buttonUrl' => array(
                'type' => 'string',
                'default' => '#'
            ),
            'backgroundColor' => array(
                'type' => 'string',
                'default' => '#007cba'
            ),
            'textColor' => array(
                'type' => 'string',
                'default' => '#ffffff'
            )
        )
    ) );
    
    // Featured Posts Block
    register_block_type( 'nosfirnews/featured-posts', array(
        'editor_script' => 'nosfirnews-blocks-js',
        'editor_style'  => 'nosfirnews-blocks-css',
        'style'         => 'nosfirnews-blocks-css',
        'render_callback' => 'nosfirnews_render_featured_posts_block',
        'attributes' => array(
            'numberOfPosts' => array(
                'type' => 'number',
                'default' => 3
            ),
            'category' => array(
                'type' => 'string',
                'default' => ''
            ),
            'layout' => array(
                'type' => 'string',
                'default' => 'grid'
            ),
            'showExcerpt' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'showDate' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'showAuthor' => array(
                'type' => 'boolean',
                'default' => true
            )
        )
    ) );
    
    // Testimonial Block
    register_block_type( 'nosfirnews/testimonial', array(
        'editor_script' => 'nosfirnews-blocks-js',
        'editor_style'  => 'nosfirnews-blocks-css',
        'style'         => 'nosfirnews-blocks-css',
        'render_callback' => 'nosfirnews_render_testimonial_block',
        'attributes' => array(
            'quote' => array(
                'type' => 'string',
                'default' => 'This is a testimonial quote.'
            ),
            'author' => array(
                'type' => 'string',
                'default' => 'John Doe'
            ),
            'position' => array(
                'type' => 'string',
                'default' => 'CEO, Company'
            ),
            'avatar' => array(
                'type' => 'string',
                'default' => ''
            ),
            'style' => array(
                'type' => 'string',
                'default' => 'default'
            )
        )
    ) );
}

/**
 * Render Hero Block
 */
function nosfirnews_render_hero_block( $attributes ) {
    $title = esc_html( $attributes['title'] );
    $subtitle = esc_html( $attributes['subtitle'] );
    $background_image = esc_url( $attributes['backgroundImage'] );
    $button_text = esc_html( $attributes['buttonText'] );
    $button_url = esc_url( $attributes['buttonUrl'] );
    $overlay_opacity = floatval( $attributes['overlayOpacity'] );
    
    $background_style = $background_image ? "background-image: url('{$background_image}');" : '';
    $overlay_style = "opacity: {$overlay_opacity};";
    
    ob_start();
    ?>
    <div class="nosfirnews-hero-block" style="<?php echo $background_style; ?>">
        <div class="hero-overlay" style="<?php echo $overlay_style; ?>"></div>
        <div class="hero-content">
            <div class="container">
                <h1 class="hero-title"><?php echo $title; ?></h1>
                <p class="hero-subtitle"><?php echo $subtitle; ?></p>
                <?php if ( $button_text && $button_url ) : ?>
                    <a href="<?php echo $button_url; ?>" class="hero-button btn btn-primary">
                        <?php echo $button_text; ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render Call to Action Block
 */
function nosfirnews_render_cta_block( $attributes ) {
    $title = esc_html( $attributes['title'] );
    $description = esc_html( $attributes['description'] );
    $button_text = esc_html( $attributes['buttonText'] );
    $button_url = esc_url( $attributes['buttonUrl'] );
    $bg_color = esc_attr( $attributes['backgroundColor'] );
    $text_color = esc_attr( $attributes['textColor'] );
    
    $style = "background-color: {$bg_color}; color: {$text_color};";
    
    ob_start();
    ?>
    <div class="nosfirnews-cta-block" style="<?php echo $style; ?>">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title"><?php echo $title; ?></h2>
                <p class="cta-description"><?php echo $description; ?></p>
                <?php if ( $button_text && $button_url ) : ?>
                    <a href="<?php echo $button_url; ?>" class="cta-button btn btn-secondary">
                        <?php echo $button_text; ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render Featured Posts Block
 */
function nosfirnews_render_featured_posts_block( $attributes ) {
    $number_of_posts = intval( $attributes['numberOfPosts'] );
    $category = sanitize_text_field( $attributes['category'] );
    $layout = sanitize_text_field( $attributes['layout'] );
    $show_excerpt = (bool) $attributes['showExcerpt'];
    $show_date = (bool) $attributes['showDate'];
    $show_author = (bool) $attributes['showAuthor'];
    
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $number_of_posts,
        'post_status' => 'publish'
    );
    
    if ( $category ) {
        $args['category_name'] = $category;
    }
    
    $query = new WP_Query( $args );
    
    if ( ! $query->have_posts() ) {
        return '<p>' . __( 'No posts found.', 'nosfirnews' ) . '</p>';
    }
    
    ob_start();
    ?>
    <div class="nosfirnews-featured-posts-block layout-<?php echo esc_attr( $layout ); ?>">
        <div class="posts-grid">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                <article class="featured-post-item">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="post-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail( 'medium' ); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="post-content">
                        <h3 class="post-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        
                        <?php if ( $show_date || $show_author ) : ?>
                            <div class="post-meta">
                                <?php if ( $show_date ) : ?>
                                    <span class="post-date"><?php echo get_the_date(); ?></span>
                                <?php endif; ?>
                                <?php if ( $show_author ) : ?>
                                    <span class="post-author"><?php echo get_the_author(); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ( $show_excerpt ) : ?>
                            <div class="post-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
    </div>
    <?php
    wp_reset_postdata();
    return ob_get_clean();
}

/**
 * Render Testimonial Block
 */
function nosfirnews_render_testimonial_block( $attributes ) {
    $quote = esc_html( $attributes['quote'] );
    $author = esc_html( $attributes['author'] );
    $position = esc_html( $attributes['position'] );
    $avatar = esc_url( $attributes['avatar'] );
    $style = esc_attr( $attributes['style'] );
    
    ob_start();
    ?>
    <div class="nosfirnews-testimonial-block style-<?php echo $style; ?>">
        <div class="testimonial-content">
            <blockquote class="testimonial-quote">
                "<?php echo $quote; ?>"
            </blockquote>
            
            <div class="testimonial-author">
                <?php if ( $avatar ) : ?>
                    <img src="<?php echo $avatar; ?>" alt="<?php echo $author; ?>" class="author-avatar">
                <?php endif; ?>
                
                <div class="author-info">
                    <cite class="author-name"><?php echo $author; ?></cite>
                    <?php if ( $position ) : ?>
                        <span class="author-position"><?php echo $position; ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Register block patterns
 */
function nosfirnews_register_block_patterns() {
    // Hero + Featured Posts Pattern
    register_block_pattern(
        'nosfirnews/hero-with-posts',
        array(
            'title'       => __( 'Hero with Featured Posts', 'nosfirnews' ),
            'description' => __( 'A hero section followed by featured posts grid.', 'nosfirnews' ),
            'content'     => '<!-- wp:nosfirnews/hero-section {"title":"Welcome to Our Site","subtitle":"Discover amazing content and stories"} /-->

<!-- wp:nosfirnews/featured-posts {"numberOfPosts":3,"layout":"grid"} /-->',
            'categories'  => array( 'nosfirnews' ),
        )
    );
    
    // Call to Action Pattern
    register_block_pattern(
        'nosfirnews/cta-section',
        array(
            'title'       => __( 'Call to Action Section', 'nosfirnews' ),
            'description' => __( 'A call to action section with testimonial.', 'nosfirnews' ),
            'content'     => '<!-- wp:nosfirnews/call-to-action {"title":"Ready to Get Started?","description":"Join thousands of satisfied customers today."} /-->

<!-- wp:nosfirnews/testimonial {"quote":"This service has transformed our business completely.","author":"Jane Smith","position":"Marketing Director"} /-->',
            'categories'  => array( 'nosfirnews' ),
        )
    );
}

/**
 * Register block pattern category
 */
function nosfirnews_register_block_pattern_category() {
    register_block_pattern_category(
        'nosfirnews',
        array( 'label' => __( 'NosfirNews', 'nosfirnews' ) )
    );
}
add_action( 'init', 'nosfirnews_register_block_pattern_category' );

/**
 * Enqueue block editor assets
 */
function nosfirnews_enqueue_block_editor_assets() {
    wp_enqueue_script(
        'nosfirnews-blocks-js',
        get_template_directory_uri() . '/assets/js/blocks.js',
        array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n' ),
        function_exists( 'nosfirnews_asset_version' ) ? nosfirnews_asset_version( 'assets/js/blocks.js' ) : '1.0.0',
        true
    );
    
    wp_enqueue_style(
        'nosfirnews-blocks-css',
        get_template_directory_uri() . '/assets/css/blocks.css',
        array(),
        function_exists( 'nosfirnews_asset_version' ) ? nosfirnews_asset_version( 'assets/css/blocks.css' ) : '1.0.0'
    );
}
add_action( 'enqueue_block_editor_assets', 'nosfirnews_enqueue_block_editor_assets' );

/**
 * Enqueue block assets for frontend
 */
function nosfirnews_enqueue_block_assets() {
    wp_enqueue_style(
        'nosfirnews-blocks-css',
        get_template_directory_uri() . '/assets/css/blocks.css',
        array(),
        function_exists( 'nosfirnews_asset_version' ) ? nosfirnews_asset_version( 'assets/css/blocks.css' ) : '1.0.0'
    );
}
add_action( 'enqueue_block_assets', 'nosfirnews_enqueue_block_assets' );