<?php
/**
 * AMP Support for NosfirNews Theme
 * 
 * @package NosfirNews
 * @since 2.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * AMP Support Class
 */
class NosfirNews_AMP_Support {
    
    /**
     * Initialize AMP support
     */
    public static function init() {
        add_action( 'init', array( __CLASS__, 'setup_amp_support' ) );
        add_action( 'wp_head', array( __CLASS__, 'add_amp_meta_tags' ) );
        add_filter( 'amp_post_template_data', array( __CLASS__, 'customize_amp_template_data' ) );
        add_filter( 'amp_post_template_metadata', array( __CLASS__, 'customize_amp_metadata' ) );
        add_action( 'amp_post_template_head', array( __CLASS__, 'add_amp_custom_styles' ) );
        add_filter( 'amp_content_sanitizers', array( __CLASS__, 'add_custom_sanitizers' ) );
        add_action( 'amp_post_template_footer', array( __CLASS__, 'add_amp_analytics' ) );
    }
    
    /**
     * Setup AMP support
     */
    public static function setup_amp_support() {
        // Check if AMP plugin is active
        if ( ! function_exists( 'amp_is_request' ) ) {
            return;
        }
        
        // Add theme support for AMP
        add_theme_support( 'amp', array(
            'paired' => true,
            'template_dir' => get_template_directory() . '/amp-templates/',
        ) );
        
        // Add AMP support for post types
        add_post_type_support( 'post', 'amp' );
        add_post_type_support( 'page', 'amp' );
        
        // Custom post types support
        $custom_post_types = get_post_types( array( 'public' => true, '_builtin' => false ) );
        foreach ( $custom_post_types as $post_type ) {
            add_post_type_support( $post_type, 'amp' );
        }
    }
    
    /**
     * Add AMP meta tags
     */
    public static function add_amp_meta_tags() {
        if ( function_exists( 'amp_is_request' ) && amp_is_request() ) {
            echo '<meta name="amp-google-client-id-api" content="googleanalytics">' . "\n";
            echo '<meta name="amp-link-variable-allowed-origin" content="' . home_url() . '">' . "\n";
        }
    }
    
    /**
     * Customize AMP template data
     */
    public static function customize_amp_template_data( $data ) {
        $data['site_name'] = get_bloginfo( 'name' );
        $data['site_description'] = get_bloginfo( 'description' );
        $data['site_url'] = home_url();
        $data['theme_color'] = get_theme_mod( 'nosfirnews_primary_color', '#2196F3' );
        
        // Add custom logo
        $custom_logo_id = get_theme_mod( 'custom_logo' );
        if ( $custom_logo_id ) {
            $logo_data = wp_get_attachment_image_src( $custom_logo_id, 'full' );
            $data['site_logo'] = array(
                'url' => $logo_data[0],
                'width' => $logo_data[1],
                'height' => $logo_data[2],
            );
        }
        
        return $data;
    }
    
    /**
     * Customize AMP metadata
     */
    public static function customize_amp_metadata( $metadata ) {
        global $post;
        
        if ( ! $post ) {
            return $metadata;
        }
        
        // Add structured data
        $metadata['@type'] = 'NewsArticle';
        $metadata['mainEntityOfPage'] = get_permalink( $post->ID );
        $metadata['headline'] = get_the_title( $post->ID );
        $metadata['description'] = wp_trim_words( get_the_excerpt( $post->ID ), 20 );
        $metadata['datePublished'] = get_the_date( 'c', $post->ID );
        $metadata['dateModified'] = get_the_modified_date( 'c', $post->ID );
        
        // Author information
        $author_id = $post->post_author;
        $metadata['author'] = array(
            '@type' => 'Person',
            'name' => get_the_author_meta( 'display_name', $author_id ),
            'url' => get_author_posts_url( $author_id ),
        );
        
        // Publisher information
        $metadata['publisher'] = array(
            '@type' => 'Organization',
            'name' => get_bloginfo( 'name' ),
            'url' => home_url(),
        );
        
        // Add logo to publisher
        $custom_logo_id = get_theme_mod( 'custom_logo' );
        if ( $custom_logo_id ) {
            $logo_data = wp_get_attachment_image_src( $custom_logo_id, 'full' );
            $metadata['publisher']['logo'] = array(
                '@type' => 'ImageObject',
                'url' => $logo_data[0],
                'width' => $logo_data[1],
                'height' => $logo_data[2],
            );
        }
        
        // Featured image
        if ( has_post_thumbnail( $post->ID ) ) {
            $image_data = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
            $metadata['image'] = array(
                '@type' => 'ImageObject',
                'url' => $image_data[0],
                'width' => $image_data[1],
                'height' => $image_data[2],
            );
        }
        
        return $metadata;
    }
    
    /**
     * Add custom AMP styles
     */
    public static function add_amp_custom_styles() {
        $primary_color = get_theme_mod( 'nosfirnews_primary_color', '#2196F3' );
        $secondary_color = get_theme_mod( 'nosfirnews_secondary_color', '#FF5722' );
        $accent_color = get_theme_mod( 'nosfirnews_accent_color', '#FFC107' );
        
        ?>
        <style amp-custom>
            /* NosfirNews AMP Styles */
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                line-height: 1.6;
                color: #333;
                margin: 0;
                padding: 0;
            }
            
            .amp-wp-header {
                background: <?php echo esc_attr( $primary_color ); ?>;
                color: white;
                padding: 1rem;
                text-align: center;
            }
            
            .amp-wp-header h1 {
                margin: 0;
                font-size: 1.5rem;
                font-weight: 700;
            }
            
            .amp-wp-header a {
                color: white;
                text-decoration: none;
            }
            
            .amp-wp-content {
                max-width: 800px;
                margin: 0 auto;
                padding: 2rem 1rem;
            }
            
            .amp-wp-title {
                font-size: 2rem;
                font-weight: 700;
                line-height: 1.2;
                margin-bottom: 1rem;
                color: #333;
            }
            
            .amp-wp-meta {
                color: #666;
                font-size: 0.9rem;
                margin-bottom: 2rem;
                padding-bottom: 1rem;
                border-bottom: 1px solid #eee;
            }
            
            .amp-wp-meta a {
                color: <?php echo esc_attr( $primary_color ); ?>;
                text-decoration: none;
            }
            
            .amp-wp-article-content {
                font-size: 1.1rem;
                line-height: 1.8;
            }
            
            .amp-wp-article-content h2,
            .amp-wp-article-content h3,
            .amp-wp-article-content h4 {
                color: #333;
                margin-top: 2rem;
                margin-bottom: 1rem;
            }
            
            .amp-wp-article-content h2 {
                font-size: 1.5rem;
                border-left: 4px solid <?php echo esc_attr( $primary_color ); ?>;
                padding-left: 1rem;
            }
            
            .amp-wp-article-content p {
                margin-bottom: 1.5rem;
            }
            
            .amp-wp-article-content blockquote {
                background: #f8f9fa;
                border-left: 4px solid <?php echo esc_attr( $accent_color ); ?>;
                margin: 2rem 0;
                padding: 1rem 1.5rem;
                font-style: italic;
            }
            
            .amp-wp-article-content img {
                max-width: 100%;
                height: auto;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            }
            
            .amp-wp-article-content a {
                color: <?php echo esc_attr( $primary_color ); ?>;
                text-decoration: none;
                border-bottom: 1px solid transparent;
                transition: border-color 0.3s ease;
            }
            
            .amp-wp-article-content a:hover {
                border-bottom-color: <?php echo esc_attr( $primary_color ); ?>;
            }
            
            .amp-wp-footer {
                background: #f8f9fa;
                padding: 2rem 1rem;
                text-align: center;
                margin-top: 3rem;
                border-top: 1px solid #eee;
            }
            
            .amp-wp-footer p {
                margin: 0;
                color: #666;
                font-size: 0.9rem;
            }
            
            /* Navigation */
            .amp-wp-nav {
                background: white;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                padding: 1rem;
            }
            
            .amp-wp-nav ul {
                list-style: none;
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                flex-wrap: wrap;
            }
            
            .amp-wp-nav li {
                margin: 0 1rem;
            }
            
            .amp-wp-nav a {
                color: #333;
                text-decoration: none;
                font-weight: 500;
                padding: 0.5rem 0;
                border-bottom: 2px solid transparent;
                transition: border-color 0.3s ease;
            }
            
            .amp-wp-nav a:hover {
                border-bottom-color: <?php echo esc_attr( $primary_color ); ?>;
            }
            
            /* Responsive */
            @media (max-width: 768px) {
                .amp-wp-content {
                    padding: 1rem;
                }
                
                .amp-wp-title {
                    font-size: 1.5rem;
                }
                
                .amp-wp-article-content {
                    font-size: 1rem;
                }
                
                .amp-wp-nav ul {
                    flex-direction: column;
                    align-items: center;
                }
                
                .amp-wp-nav li {
                    margin: 0.5rem 0;
                }
            }
            
            /* AMP Components */
            amp-img {
                background-color: #f8f9fa;
            }
            
            amp-social-share {
                margin: 0 0.5rem;
            }
            
            .amp-social-share-container {
                text-align: center;
                margin: 2rem 0;
                padding: 1rem;
                background: #f8f9fa;
                border-radius: 8px;
            }
            
            .amp-social-share-container h3 {
                margin-top: 0;
                color: #333;
                font-size: 1.2rem;
            }
            
            /* Loading animation */
            .amp-loading {
                display: inline-block;
                width: 20px;
                height: 20px;
                border: 3px solid #f3f3f3;
                border-top: 3px solid <?php echo esc_attr( $primary_color ); ?>;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }
            
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
        <?php
    }
    
    /**
     * Add custom sanitizers
     */
    public static function add_custom_sanitizers( $sanitizers ) {
        // Add custom sanitizers if needed
        return $sanitizers;
    }
    
    /**
     * Add AMP analytics
     */
    public static function add_amp_analytics() {
        $google_analytics_id = get_theme_mod( 'nosfirnews_google_analytics', '' );
        
        if ( ! empty( $google_analytics_id ) ) {
            ?>
            <amp-analytics type="googleanalytics">
                <script type="application/json">
                {
                    "vars": {
                        "account": "<?php echo esc_js( $google_analytics_id ); ?>"
                    },
                    "triggers": {
                        "trackPageview": {
                            "on": "visible",
                            "request": "pageview"
                        }
                    }
                }
                </script>
            </amp-analytics>
            <?php
        }
    }
    
    /**
     * Check if current request is AMP
     */
    public static function is_amp() {
        return function_exists( 'amp_is_request' ) && amp_is_request();
    }
    
    /**
     * Get AMP URL for current page
     */
    public static function get_amp_url( $post_id = null ) {
        if ( ! function_exists( 'amp_get_permalink' ) ) {
            return '';
        }
        
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }
        
        return amp_get_permalink( $post_id );
    }
    
    /**
     * Add AMP link to non-AMP pages
     */
    public static function add_amp_link() {
        if ( is_singular() && ! self::is_amp() ) {
            $amp_url = self::get_amp_url();
            if ( $amp_url ) {
                echo '<link rel="amphtml" href="' . esc_url( $amp_url ) . '">' . "\n";
            }
        }
    }
}

// Initialize AMP support
NosfirNews_AMP_Support::init();

/**
 * Add AMP link to head
 */
add_action( 'wp_head', array( 'NosfirNews_AMP_Support', 'add_amp_link' ) );

/**
 * Customize AMP content
 */
function nosfirnews_amp_content_filter( $content ) {
    if ( ! NosfirNews_AMP_Support::is_amp() ) {
        return $content;
    }
    
    // Add social sharing buttons
    $social_buttons = '
    <div class="amp-social-share-container">
        <h3>Compartilhar</h3>
        <amp-social-share type="facebook" width="40" height="40"></amp-social-share>
        <amp-social-share type="twitter" width="40" height="40"></amp-social-share>
        <amp-social-share type="whatsapp" width="40" height="40"></amp-social-share>
        <amp-social-share type="email" width="40" height="40"></amp-social-share>
    </div>';
    
    $content .= $social_buttons;
    
    return $content;
}
add_filter( 'the_content', 'nosfirnews_amp_content_filter' );

/**
 * AMP customizer options
 */
function nosfirnews_amp_customizer_options( $wp_customize ) {
    // AMP Section
    $wp_customize->add_section( 'nosfirnews_amp_options', array(
        'title'    => __( 'AMP Options', 'nosfirnews' ),
        'priority' => 160,
    ) );
    
    // Google Analytics for AMP
    $wp_customize->add_setting( 'nosfirnews_google_analytics', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    
    $wp_customize->add_control( 'nosfirnews_google_analytics', array(
        'label'       => __( 'Google Analytics ID', 'nosfirnews' ),
        'description' => __( 'Enter your Google Analytics tracking ID for AMP pages (e.g., UA-XXXXX-X)', 'nosfirnews' ),
        'section'     => 'nosfirnews_amp_options',
        'type'        => 'text',
    ) );
    
    // AMP Logo
    $wp_customize->add_setting( 'nosfirnews_amp_logo', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ) );
    
    $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'nosfirnews_amp_logo', array(
        'label'       => __( 'AMP Logo', 'nosfirnews' ),
        'description' => __( 'Upload a logo specifically for AMP pages (recommended: 600x60px)', 'nosfirnews' ),
        'section'     => 'nosfirnews_amp_options',
        'mime_type'   => 'image',
    ) ) );
}
add_action( 'customize_register', 'nosfirnews_amp_customizer_options' );