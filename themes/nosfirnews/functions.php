<?php
/**
 * NosfirNews functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package NosfirNews
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Define theme constants
 */
define( 'NOSFIRNEWS_VERSION', '2.0.0' );
define( 'NOSFIRNEWS_THEME_DIR', get_template_directory() );
define( 'NOSFIRNEWS_THEME_URI', get_template_directory_uri() );

/**
 * Asset versioning helper (cache busting via filemtime)
 */
if ( ! function_exists( 'nosfirnews_asset_version' ) ) {
    function nosfirnews_asset_version( $relative_path ) {
        $file = trailingslashit( NOSFIRNEWS_THEME_DIR ) . ltrim( $relative_path, '/\\' );
        return file_exists( $file ) ? filemtime( $file ) : NOSFIRNEWS_VERSION;
    }
}

/**
 * Theme setup
 */
if ( ! function_exists( 'nosfirnews_setup' ) ) :
    
    function nosfirnews_setup() {
        
        // Make theme available for translation
        load_theme_textdomain( 'nosfirnews', get_template_directory() . '/languages' );
        
        // Add default posts and comments RSS feed links to head
        add_theme_support( 'automatic-feed-links' );
        
        // Let WordPress manage the document title
        add_theme_support( 'title-tag' );
        
        // Enable support for Post Thumbnails on posts and pages
        add_theme_support( 'post-thumbnails' );
        
        // Set default thumbnail size
        set_post_thumbnail_size( 300, 200, true );
        
        // Add additional image sizes (proporções 16:9 para consistência)
        add_image_size( 'nosfirnews-featured', 1200, 675, true );
        add_image_size( 'nosfirnews-medium', 600, 338, true );
        add_image_size( 'nosfirnews-small', 400, 225, true );
        
        // Register navigation menus
        register_nav_menus( array(
            'primary'   => esc_html__( 'Primary Menu', 'nosfirnews' ),
            'footer'    => esc_html__( 'Footer Menu', 'nosfirnews' ),
            'social'    => esc_html__( 'Social Links Menu', 'nosfirnews' ),
        ) );
        
        // Switch default core markup to output valid HTML5
        add_theme_support( 'html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        ) );
        
        // Set up the WordPress core custom background feature
        add_theme_support( 'custom-background', apply_filters( 'nosfirnews_custom_background_args', array(
            'default-color' => 'ffffff',
            'default-image' => '',
        ) ) );
        
        // Add theme support for selective refresh for widgets
        add_theme_support( 'customize-selective-refresh-widgets' );
        
        // Add support for custom logo
        add_theme_support( 'custom-logo', array(
            'height'      => 250,
            'width'       => 250,
            'flex-width'  => true,
            'flex-height' => true,
        ) );
        
        // Add support for custom header
        add_theme_support( 'custom-header', array(
            'default-image'          => '',
            'random-default'         => false,
            'width'                  => 1200,
            'height'                 => 400,
            'flex-height'            => true,
            'flex-width'             => true,
            'default-text-color'     => '333333',
            'header-text'            => true,
            'uploads'                => true,
        ) );
        
        // Add editor styles
        add_theme_support( 'editor-styles' );
        add_editor_style( 'assets/css/editor-style.css' );
        
        // Add support for responsive embeds
        add_theme_support( 'responsive-embeds' );
        
        // Add support for block styles
        add_theme_support( 'wp-block-styles' );
        
        // Add support for wide and full alignment
        add_theme_support( 'align-wide' );
        
        // Add WooCommerce support
        add_theme_support( 'woocommerce' );
        add_theme_support( 'wc-product-gallery-zoom' );
        add_theme_support( 'wc-product-gallery-lightbox' );
        add_theme_support( 'wc-product-gallery-slider' );
        
        // Add support for editor color palette
        add_theme_support( 'editor-color-palette', array(
            array(
                'name'  => esc_html__( 'Primary', 'nosfirnews' ),
                'slug'  => 'primary',
                'color' => '#1a73e8',
            ),
            array(
                'name'  => esc_html__( 'Secondary', 'nosfirnews' ),
                'slug'  => 'secondary',
                'color' => '#34a853',
            ),
            array(
                'name'  => esc_html__( 'Accent', 'nosfirnews' ),
                'slug'  => 'accent',
                'color' => '#ea4335',
            ),
        ) );
        
        // Disable custom colors
        add_theme_support( 'disable-custom-colors' );
        
        // Add support for editor font sizes
        add_theme_support( 'editor-font-sizes', array(
            array(
                'name' => esc_html__( 'Small', 'nosfirnews' ),
                'size' => 14,
                'slug' => 'small'
            ),
            array(
                'name' => esc_html__( 'Regular', 'nosfirnews' ),
                'size' => 16,
                'slug' => 'regular'
            ),
            array(
                'name' => esc_html__( 'Large', 'nosfirnews' ),
                'size' => 20,
                'slug' => 'large'
            ),
            array(
                'name' => esc_html__( 'Huge', 'nosfirnews' ),
                'size' => 32,
                'slug' => 'huge'
            )
        ) );
        
        // Disable custom font sizes
        add_theme_support( 'disable-custom-font-sizes' );
    }
    
endif;
add_action( 'after_setup_theme', 'nosfirnews_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet
 */
function nosfirnews_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'nosfirnews_content_width', 800 );
}
add_action( 'after_setup_theme', 'nosfirnews_content_width', 0 );

/**
 * Register widget area
 */
function nosfirnews_widgets_init() {
    register_sidebar( array(
        'name'          => esc_html__( 'Main Sidebar', 'nosfirnews' ),
        'id'            => 'sidebar-1',
        'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'nosfirnews' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
    
    register_sidebar( array(
        'name'          => esc_html__( 'Footer Widget Area 1', 'nosfirnews' ),
        'id'            => 'footer-1',
        'description'   => esc_html__( 'Add widgets here to appear in your footer.', 'nosfirnews' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
    
    register_sidebar( array(
        'name'          => esc_html__( 'Footer Widget Area 2', 'nosfirnews' ),
        'id'            => 'footer-2',
        'description'   => esc_html__( 'Add widgets here to appear in your footer.', 'nosfirnews' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
    
    register_sidebar( array(
        'name'          => esc_html__( 'Footer Widget Area 3', 'nosfirnews' ),
        'id'            => 'footer-3',
        'description'   => esc_html__( 'Add widgets here to appear in your footer.', 'nosfirnews' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'nosfirnews_widgets_init' );

/**
 * Enqueue scripts and styles
 */
function nosfirnews_scripts() {
    // Enqueue main stylesheet
    wp_enqueue_style( 'nosfirnews-style', get_stylesheet_uri(), array(), nosfirnews_asset_version( 'style.css' ) );
    
    // Enqueue additional CSS files
    wp_enqueue_style( 'nosfirnews-main', get_template_directory_uri() . '/assets/css/main.css', array(), nosfirnews_asset_version( 'assets/css/main.css' ) );
    wp_enqueue_style( 'nosfirnews-responsive', get_template_directory_uri() . '/assets/css/responsive.css', array(), nosfirnews_asset_version( 'assets/css/responsive.css' ) );
    wp_enqueue_style( 'nosfirnews-responsive-images', get_template_directory_uri() . '/assets/css/responsive-images.css', array(), nosfirnews_asset_version( 'assets/css/responsive-images.css' ) );
    wp_enqueue_style( 'nosfirnews-navigation-unified', get_template_directory_uri() . '/assets/css/navigation-unified.css', array(), nosfirnews_asset_version( 'assets/css/navigation-unified.css' ) );
    
    // Enqueue Google Fonts
    wp_enqueue_style( 'nosfirnews-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap', array(), null );

    // Enqueue icon libraries
    wp_enqueue_style( 'nosfirnews-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', array(), '5.15.4' );
    wp_enqueue_style( 'nosfirnews-bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css', array(), '1.11.1' );
    
    // WooCommerce styles
    if ( class_exists( 'WooCommerce' ) ) {
        wp_enqueue_style( 'nosfirnews-woocommerce', get_template_directory_uri() . '/assets/css/woocommerce.css', array( 'nosfirnews-main' ), nosfirnews_asset_version( 'assets/css/woocommerce.css' ) );
    }
    
    // Enqueue template-specific styles
    if ( is_page_template( 'templates/page-templates/page-full-width.php' ) ) {
        wp_enqueue_style( 'nosfirnews-page-full-width', get_template_directory_uri() . '/assets/css/page-full-width.css', array(), nosfirnews_asset_version( 'assets/css/page-full-width.css' ) );
    }
    
    if ( is_page_template( 'templates/page-templates/page-no-sidebar.php' ) ) {
        wp_enqueue_style( 'nosfirnews-page-no-sidebar', get_template_directory_uri() . '/assets/css/page-no-sidebar.css', array(), nosfirnews_asset_version( 'assets/css/page-no-sidebar.css' ) );
    }
    
    // Enqueue main JavaScript file
    wp_enqueue_script( 'nosfirnews-main', get_template_directory_uri() . '/assets/js/main.js', array( 'jquery' ), nosfirnews_asset_version( 'assets/js/main.js' ), true );
    
    // Enqueue unified navigation script
    wp_enqueue_script( 'nosfirnews-navigation-unified', get_template_directory_uri() . '/assets/js/navigation-unified.js', array(), nosfirnews_asset_version( 'assets/js/navigation-unified.js' ), true );
    
    // Enqueue comment reply script
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
    
    // Localize script for AJAX
    wp_localize_script( 'nosfirnews-main', 'nosfirnews_ajax', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'nosfirnews_nonce' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'nosfirnews_scripts' );

/**
 * Performance optimizations
 */
function nosfirnews_performance_optimizations() {
    $perf_opts = get_option( 'nosfirnews_performance_options', array() );

    // Disable emojis
    if ( isset( $perf_opts['disable_emojis'] ) ? (bool) $perf_opts['disable_emojis'] : true ) {
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_action( 'admin_print_styles', 'print_emoji_styles' );
        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
        remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    }

    // Deregister wp-embed if not needed
    if ( ! is_admin() && ( isset( $perf_opts['disable_wp_embed'] ) ? (bool) $perf_opts['disable_wp_embed'] : true ) ) {
        wp_deregister_script( 'wp-embed' );
    }

    // Remove query strings from static resources - DESATIVADO temporariamente para evitar loop infinito
    // if ( ! is_admin() && ( isset( $perf_opts['remove_query_strings'] ) ? (bool) $perf_opts['remove_query_strings'] : true ) ) {
    //     add_filter( 'script_loader_src', 'nosfirnews_remove_query_strings' );
    //     add_filter( 'style_loader_src', 'nosfirnews_remove_query_strings' );
    // }
}
add_action( 'init', 'nosfirnews_performance_optimizations' );

// Resource hints: preconnect to Google Fonts
function nosfirnews_resource_hints( $hints, $relation_type ) {
    if ( 'preconnect' === $relation_type ) {
        $hints[] = 'https://fonts.gstatic.com';
        $hints[] = 'https://fonts.googleapis.com';
    }
    if ( 'dns-prefetch' === $relation_type ) {
        $hints[] = 'https://fonts.gstatic.com';
        $hints[] = 'https://fonts.googleapis.com';
    }
    return $hints;
}

function nosfirnews_maybe_add_resource_hints( $hints, $relation_type ) {
    $perf_opts = get_option( 'nosfirnews_performance_options', array() );
    $enable_hints = isset( $perf_opts['resource_hints_fonts'] ) ? (bool) $perf_opts['resource_hints_fonts'] : true;
    if ( ! $enable_hints ) {
        return $hints;
    }
    return nosfirnews_resource_hints( $hints, $relation_type );
}
add_filter( 'wp_resource_hints', 'nosfirnews_maybe_add_resource_hints', 10, 2 );

/**
 * Enqueue admin scripts and styles
 */
function nosfirnews_admin_scripts( $hook ) {
    wp_enqueue_style( 'nosfirnews-admin', get_template_directory_uri() . '/assets/css/admin.css', array(), NOSFIRNEWS_VERSION );
    wp_enqueue_script( 'nosfirnews-admin', get_template_directory_uri() . '/assets/js/admin.js', array( 'jquery' ), NOSFIRNEWS_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'nosfirnews_admin_scripts' );

/**
 * Include required files
 */
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/customizer-advanced.php';
require get_template_directory() . '/inc/customizer/navigation-layout.php';
require get_template_directory() . '/inc/dynamic-widgets.php';
require get_template_directory() . '/inc/responsive-system.php';
require get_template_directory() . '/inc/custom-post-types.php';
require get_template_directory() . '/inc/gutenberg-blocks.php';
require get_template_directory() . '/inc/advanced-custom-fields.php';
require get_template_directory() . '/inc/layout-renderer.php';
require get_template_directory() . '/inc/visual-editor.php';
require get_template_directory() . '/inc/custom-templates.php';
require get_template_directory() . '/inc/media-gallery.php';
require get_template_directory() . '/inc/class-walker-nav-menu.php';
require get_template_directory() . '/inc/menu-custom-fields.php';
require get_template_directory() . '/inc/menu-customization.php';
require get_template_directory() . '/inc/page-templates.php';

/**
 * Include admin functions
 */
if ( is_admin() ) {
    require get_template_directory() . '/inc/admin/theme-options.php';
    require get_template_directory() . '/inc/admin/advanced-theme-options.php';
    require get_template_directory() . '/inc/admin/metaboxes.php';
}

/**
 * Custom excerpt length
 */
function nosfirnews_excerpt_length( $length ) {
    return 30;
}
add_filter( 'excerpt_length', 'nosfirnews_excerpt_length', 999 );

/**
 * Custom excerpt more string
 */
function nosfirnews_excerpt_more( $more ) {
    return '...';
}
add_filter( 'excerpt_more', 'nosfirnews_excerpt_more' );

/**
 * Security enhancements
 */
function nosfirnews_remove_version() {
    return '';
}
add_filter( 'the_generator', 'nosfirnews_remove_version' );

/**
 * Disable XML-RPC
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * Remove unnecessary header links
 */
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );

/**
 * Enhanced security measures
 */
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
    define( 'DISALLOW_FILE_EDIT', true );
}

function nosfirnews_remove_wp_version_rss() {
    return '';
}

function nosfirnews_disable_pingback( &$links ) {
    foreach ( $links as $l => $link ) {
        if ( 0 === strpos( $link, get_option( 'home' ) ) ) {
            unset( $links[$l] );
        }
    }
}
add_action( 'pre_ping', 'nosfirnews_disable_pingback' );

function nosfirnews_remove_x_pingback( $headers ) {
    unset( $headers['X-Pingback'] );
    return $headers;
}
add_filter( 'wp_headers', 'nosfirnews_remove_x_pingback' );

function nosfirnews_hide_login_errors() {
    return __( 'Something is wrong!', 'nosfirnews' );
}
add_filter( 'login_errors', 'nosfirnews_hide_login_errors' );

function nosfirnews_add_security_headers() {
    if ( ! is_admin() ) {
        header( 'X-Content-Type-Options: nosniff' );
        header( 'X-Frame-Options: SAMEORIGIN' );
        header( 'X-XSS-Protection: 1; mode=block' );
        header( 'Referrer-Policy: strict-origin-when-cross-origin' );
        header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()' );
    }
}
add_action( 'send_headers', 'nosfirnews_add_security_headers' );

function nosfirnews_remove_query_strings( $src ) {
    if ( empty( $src ) || ! is_string( $src ) ) {
        return $src;
    }
    
    // Previne recursão infinita
    static $processing = array();
    $src_hash = md5( $src );
    
    if ( isset( $processing[ $src_hash ] ) ) {
        return $src;
    }
    
    $processing[ $src_hash ] = true;
    
    $output = preg_split( "/(&ver|\?ver)/", $src );
    $result = $output[0];
    
    unset( $processing[ $src_hash ] );
    
    return $result;
}

/**
 * Enhanced performance optimizations
 */
function nosfirnews_preload_resources() {
    echo '<link rel="preload" href="' . esc_url( get_template_directory_uri() . '/assets/css/main.css' ) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
    echo '<link rel="preload" href="' . esc_url( get_template_directory_uri() . '/assets/js/main.js' ) . '" as="script">';
    echo '<link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
}

function nosfirnews_defer_css( $html, $handle, $href, $media ) {
    $defer_handles = array( 'nosfirnews-responsive', 'nosfirnews-page-full-width', 'nosfirnews-page-no-sidebar' );
    
    if ( in_array( $handle, $defer_handles, true ) ) {
        $html = '<link rel="preload" href="' . esc_url( $href ) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
        $html .= '<noscript><link rel="stylesheet" href="' . esc_url( $href ) . '"></noscript>';
    }
    
    return $html;
}
// add_filter( 'style_loader_tag', 'nosfirnews_defer_css', 10, 4 );

function nosfirnews_optimize_queries() {
    remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
    
    if ( ! defined( 'WP_POST_REVISIONS' ) ) {
        define( 'WP_POST_REVISIONS', 3 );
    }
    
    if ( ! defined( 'WP_MEMORY_LIMIT' ) ) {
        define( 'WP_MEMORY_LIMIT', '256M' );
    }
}
add_action( 'init', 'nosfirnews_optimize_queries' );

function nosfirnews_enable_gzip() {
    if ( ! is_admin() && ! headers_sent() ) {
        if ( function_exists( 'ob_gzhandler' ) && ! ini_get( 'zlib.output_compression' ) ) {
            ob_start( 'ob_gzhandler' );
        }
    }
}
add_action( 'init', 'nosfirnews_enable_gzip' );

/**
 * WooCommerce Support Functions
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

add_action( 'woocommerce_before_main_content', 'nosfirnews_wrapper_start', 10 );
add_action( 'woocommerce_after_main_content', 'nosfirnews_wrapper_end', 10 );

function nosfirnews_wrapper_start() {
    echo '<div id="primary" class="content-area woocommerce-content">';
    echo '<main id="main" class="site-main">';
}

function nosfirnews_wrapper_end() {
    echo '</main>';
    echo '</div>';
}

function nosfirnews_loop_columns() {
    return 3;
}
add_filter( 'loop_shop_columns', 'nosfirnews_loop_columns' );

function nosfirnews_products_per_page() {
    return 12;
}
add_filter( 'loop_shop_per_page', 'nosfirnews_products_per_page', 20 );

function nosfirnews_woocommerce_breadcrumbs() {
    return array(
        'delimiter'   => ' / ',
        'wrap_before' => '<nav class="woocommerce-breadcrumb breadcrumb">',
        'wrap_after'  => '</nav>',
        'before'      => '',
        'after'       => '',
        'home'        => _x( 'Home', 'breadcrumb', 'nosfirnews' ),
    );
}
add_filter( 'woocommerce_breadcrumb_defaults', 'nosfirnews_woocommerce_breadcrumbs' );

function nosfirnews_cart_icon() {
    if (class_exists('WooCommerce')) {
        $cart_count = WC()->cart->get_cart_contents_count();
        $cart_url = wc_get_cart_url();
        
        echo '<div class="header-cart">';
        echo '<a href="' . esc_url($cart_url) . '" class="cart-icon">';
        echo '<i class="fas fa-shopping-cart"></i>';
        if ($cart_count > 0) {
            echo '<span class="cart-count">' . $cart_count . '</span>';
        }
        echo '</a>';
        echo '</div>';
    }
}

function nosfirnews_woocommerce_assets() {
    if (class_exists('WooCommerce')) {
        wp_enqueue_script(
            'nosfirnews-woocommerce',
            get_template_directory_uri() . '/assets/js/woocommerce.js',
            array('jquery'),
            wp_get_theme()->get('Version'),
            true
        );
        wp_localize_script('nosfirnews-woocommerce', 'nosfirnews_woo_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nosfirnews_woo_nonce'),
            'currency_symbol' => get_woocommerce_currency_symbol(),
            'currency_position' => get_option('woocommerce_currency_pos'),
            'thousand_separator' => wc_get_price_thousand_separator(),
            'decimal_separator' => wc_get_price_decimal_separator(),
            'decimals' => wc_get_price_decimals(),
            'banner_autoplay' => (bool) get_theme_mod('nosfirnews_woo_banner_autoplay', true),
            'banner_speed' => (int) get_theme_mod('nosfirnews_woo_banner_speed', 4000),
        ));
    }
}
add_action('wp_enqueue_scripts', 'nosfirnews_woocommerce_assets');

function nosfirnews_enqueue_woo_banner_assets() {
    if ( is_front_page() && class_exists('WooCommerce') && get_theme_mod('nosfirnews_woo_banner_enable', false) ) {
        wp_enqueue_style(
            'slick-css',
            'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css',
            array(),
            '1.8.1'
        );
        wp_enqueue_style(
            'slick-theme',
            'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css',
            array('slick-css'),
            '1.8.1'
        );
        wp_enqueue_script(
            'slick-js',
            'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js',
            array('jquery'),
            '1.8.1',
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'nosfirnews_enqueue_woo_banner_assets');

function nosfirnews_sale_badge() {
    global $product;
    
    if ($product->is_on_sale()) {
        $regular_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();
        
        if ($regular_price && $sale_price) {
            $discount_percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
            echo '<span class="onsale-badge">-' . $discount_percentage . '%</span>';
        } else {
            echo '<span class="onsale-badge">' . esc_html__('Oferta', 'nosfirnews') . '</span>';
        }
    }
}
remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
add_action('woocommerce_before_single_product_summary', 'nosfirnews_sale_badge', 10);
add_action('woocommerce_before_shop_loop_item_title', 'nosfirnews_sale_badge', 10);

function nosfirnews_shop_page_title($title) {
    if (is_search() && is_post_type_archive('product')) {
        $title = sprintf(__('Resultados da busca por: "%s"', 'nosfirnews'), get_search_query());
    }
    return $title;
}
add_filter('woocommerce_page_title', 'nosfirnews_shop_page_title');

function nosfirnews_product_gallery_support() {
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'nosfirnews_product_gallery_support');

function nosfirnews_add_to_cart_fragment($fragments) {
    ob_start();
    nosfirnews_cart_icon();
    $fragments['.header-cart'] = ob_get_clean();
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'nosfirnews_add_to_cart_fragment');

/**
 * Optimize images
 */
function nosfirnews_add_image_responsive_class( $content ) {
    global $post;
    $pattern = "/<img(.*?)class=\"(.*?)\"(.*?)>/i";
    $replacement = '<img$1class="$2 img-responsive"$3>';
    $content = preg_replace( $pattern, $replacement, $content );
    return $content;
}
add_filter( 'the_content', 'nosfirnews_add_image_responsive_class' );

/**
 * Thumbnail and Image Management Functions
 */
function nosfirnews_get_image_size( $context = 'featured' ) {
    $size = '';
    
    switch ( $context ) {
        case 'featured':
            $size = get_theme_mod( 'nosfirnews_featured_image_size', 'nosfirnews-featured' );
            break;
        case 'archive':
            $size = get_theme_mod( 'nosfirnews_archive_image_size', 'nosfirnews-medium' );
            break;
        case 'widget':
            $size = get_theme_mod( 'nosfirnews_widget_image_size', 'nosfirnews-small' );
            break;
        default:
            $size = 'nosfirnews-medium';
    }
    
    return $size;
}

function nosfirnews_image_quality( $quality, $mime_type ) {
    $custom_quality = get_theme_mod( 'nosfirnews_image_quality', 85 );
    return $custom_quality;
}
add_filter( 'wp_editor_set_quality', 'nosfirnews_image_quality', 10, 2 );
add_filter( 'jpeg_quality', 'nosfirnews_image_quality' );

function nosfirnews_add_lazy_loading( $attr, $attachment, $size ) {
    if ( get_theme_mod( 'nosfirnews_enable_lazy_loading', true ) ) {
        $attr['loading'] = 'lazy';
        $attr['decoding'] = 'async';
    }
    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'nosfirnews_add_lazy_loading', 10, 3 );

function nosfirnews_post_thumbnail( $size = 'featured', $attr = array() ) {
    if ( ! has_post_thumbnail() ) {
        return;
    }
    
    $image_size = nosfirnews_get_image_size( $size );
    $default_attr = array(
        'class' => 'post-thumbnail img-responsive',
        'alt'   => get_the_title(),
    );
    
    $attr = wp_parse_args( $attr, $default_attr );
    
    if ( get_theme_mod( 'nosfirnews_enable_lazy_loading', true ) ) {
        $attr['loading'] = 'lazy';
        $attr['decoding'] = 'async';
    }
    
    the_post_thumbnail( $image_size, $attr );
}

function nosfirnews_get_responsive_image( $attachment_id, $size = 'medium', $attr = array() ) {
    if ( ! $attachment_id ) {
        return '';
    }
    
    $default_attr = array(
        'class' => 'img-responsive',
    );
    
    $attr = wp_parse_args( $attr, $default_attr );
    
    if ( get_theme_mod( 'nosfirnews_enable_lazy_loading', true ) ) {
        $attr['loading'] = 'lazy';
        $attr['decoding'] = 'async';
    }
    
    return wp_get_attachment_image( $attachment_id, $size, false, $attr );
}

function nosfirnews_add_custom_image_sizes() {
    add_image_size( 'nosfirnews-hero', 1920, 1080, true );
    add_image_size( 'nosfirnews-card', 400, 225, true );
    add_image_size( 'nosfirnews-thumbnail', 200, 200, true );
    add_image_size( 'nosfirnews-gallery', 800, 450, true );
    add_image_size( 'nosfirnews-widget', 300, 169, true );
}
add_action( 'after_setup_theme', 'nosfirnews_add_custom_image_sizes' );

function nosfirnews_custom_image_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'nosfirnews-hero'      => esc_html__( 'Hero (1920x1080)', 'nosfirnews' ),
        'nosfirnews-featured'  => esc_html__( 'Destacado (1200x675)', 'nosfirnews' ),
        'nosfirnews-medium'    => esc_html__( 'Médio (600x338)', 'nosfirnews' ),
        'nosfirnews-small'     => esc_html__( 'Pequeno (400x225)', 'nosfirnews' ),
        'nosfirnews-card'      => esc_html__( 'Card (400x225)', 'nosfirnews' ),
        'nosfirnews-thumbnail' => esc_html__( 'Miniatura (200x200)', 'nosfirnews' ),
        'nosfirnews-gallery'   => esc_html__( 'Galeria (800x450)', 'nosfirnews' ),
        'nosfirnews-widget'    => esc_html__( 'Widget (300x169)', 'nosfirnews' ),
    ) );
}
add_filter( 'image_size_names_choose', 'nosfirnews_custom_image_sizes' );

function nosfirnews_webp_support() {
    if ( function_exists( 'imagewebp' ) ) {
        add_filter( 'wp_generate_attachment_metadata', 'nosfirnews_generate_webp_images' );
    }
}
add_action( 'init', 'nosfirnews_webp_support' );

function nosfirnews_generate_webp_images( $metadata ) {
    if ( ! isset( $metadata['file'] ) ) {
        return $metadata;
    }
    
    $upload_dir = wp_upload_dir();
    $file_path = $upload_dir['basedir'] . '/' . $metadata['file'];
    
    if ( file_exists( $file_path ) ) {
        $webp_path = preg_replace( '/\.(jpg|jpeg|png)$/i', '.webp', $file_path );
        
        $image_type = wp_check_filetype( $file_path );
        
        switch ( $image_type['type'] ) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg( $file_path );
                break;
            case 'image/png':
                $image = imagecreatefrompng( $file_path );
                break;
            default:
                return $metadata;
        }
        
        if ( $image ) {
            $quality = get_theme_mod( 'nosfirnews_image_quality', 85 );
            imagewebp( $image, $webp_path, $quality );
            imagedestroy( $image );
        }
    }
    
    return $metadata;
}
add_filter( 'wp_generate_attachment_metadata', 'nosfirnews_generate_webp_images' );

/**
 * Include AMP Support
 */
require_once get_template_directory() . '/inc/amp-support.php';

/**
 * Desativar PWA para investigar loop infinito
 */
function nosfirnews_desativar_pwa() {
    // Remover manifest.json do header
    remove_action('wp_head', 'nosfirnews_add_pwa_manifest');
    
    // Adicionar meta tag para impedir registro de service worker
    echo '<meta name="nosfirnews-pwa-disabled" content="true">' . "\n";
    echo '<script>window.nosfirnewsPWAEnabled = false;</script>' . "\n";
    
    // Adicionar script para bloquear carregamento do PWA antes que ele aconteça
    echo '<script>
    // Prevenir carregamento do PWA
    window.nosfirNewsPWAEnabled = false;
    window.nosfirNewsConfig = null;
    
    // Bloquear definição do PWAManager
    Object.defineProperty(window, "PWAManager", {
        get: function() {
            return function() {
                console.log("PWA Manager bloqueado - PWA desativado temporariamente");
                return {
                    isSupported: function() { return false; },
                    getState: function() { return { isInstalled: false, isOnline: navigator.onLine }; },
                    isInstalled: function() { return false; },
                    isOnline: function() { return navigator.onLine; }
                };
            };
        },
        set: function(value) {
            console.log("Tentativa de definir PWAManager bloqueada");
        },
        configurable: false
    });
    </script>' . "\n";
}
add_action('wp_head', 'nosfirnews_desativar_pwa', 1);

/**
 * Remover qualquer manifest.json do header
 */
function nosfirnews_remove_manifest_links() {
    // Remover links de manifest que possam ter sido adicionados
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    
    // Filtrar para remover qualquer link de manifest
    add_filter('site_icon_meta_tags', function($meta_tags) {
        // Remover tags de manifest
        return array_filter($meta_tags, function($tag) {
            return strpos($tag, 'manifest') === false;
        });
    }, 999);
}
add_action('init', 'nosfirnews_remove_manifest_links', 1);

/**
 * Remover script PWA do carregamento
 */
function nosfirnews_remove_pwa_script($tag, $handle, $src) {
    // Remover qualquer script que contenha pwa.js
    if (strpos($src, 'pwa.js') !== false) {
        return ''; // Retornar string vazia remove o script
    }
    return $tag;
}
add_filter('script_loader_tag', 'nosfirnews_remove_pwa_script', 999, 3);

/**
 * Desativar carregamento de scripts PWA via wp_enqueue_scripts
 */
function nosfirnews_disable_pwa_scripts() {
    // Remover qualquer script PWA que possa estar na fila
    wp_dequeue_script('nosfirnews-pwa');
    wp_deregister_script('nosfirnews-pwa');
    
    // Remover outros possíveis scripts PWA
    wp_dequeue_script('pwa-manager');
    wp_deregister_script('pwa-manager');
    
    wp_dequeue_script('pwa');
    wp_deregister_script('pwa');
}
add_action('wp_enqueue_scripts', 'nosfirnews_disable_pwa_scripts', 999);

/**
 * Função original que adiciona o manifest.json (DESATIVADA temporariamente)
 */
function nosfirnews_add_pwa_manifest() {
    // Verificar se está no admin para evitar loops
    if (is_admin()) {
        return;
    }
    
    // Adicionar manifest.json
    echo '<link rel="manifest" href="' . get_template_directory_uri() . '/manifest.json">' . "\n";
    
    // Adicionar meta tags PWA
    echo '<meta name="theme-color" content="#1a1a1a">' . "\n";
    echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
    echo '<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">' . "\n";
    echo '<meta name="apple-mobile-web-app-title" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
}
// add_action('wp_head', 'nosfirnews_add_pwa_manifest', 10); // DESATIVADO temporariamente

/**
 * Função original que adiciona o script PWA (DESATIVADA temporariamente)
 */
function nosfirnews_enqueue_pwa_script() {
    // Verificar se está no admin para evitar loops
    if (is_admin()) {
        return;
    }
    
    // Enfileirar o script PWA
    wp_enqueue_script(
        'nosfirnews-pwa',
        get_template_directory_uri() . '/assets/js/pwa.js',
        array(),
        '1.0.0',
        true
    );
    
    // Adicionar dados de localização
    wp_localize_script('nosfirnews-pwa', 'nosfirnewsPWA', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('nosfirnews_pwa_nonce'),
        'site_name' => get_bloginfo('name'),
        'site_description' => get_bloginfo('description'),
        'theme_color' => '#1a1a1a',
        'background_color' => '#ffffff'
    ));
}
// add_action('wp_enqueue_scripts', 'nosfirnews_enqueue_pwa_script'); // DESATIVADO temporariamente

/**
 * Impedir registro de service worker via JavaScript
 */
function nosfirnews_prevent_service_worker() {
    if (is_user_logged_in() && current_user_can('manage_options')) {
        echo '<script>
        // Desativar Service Worker
        if ("serviceWorker" in navigator) {
            navigator.serviceWorker.getRegistrations().then(function(registrations) {
                for(let registration of registrations) {
                    registration.unregister();
                    console.log("Service Worker desativado:", registration.scope);
                }
            });
        }
        
        // Prevenir registro de novos service workers
        if ("serviceWorker" in navigator) {
            const originalRegister = navigator.serviceWorker.register;
            navigator.serviceWorker.register = function() {
                console.log("Tentativa de registrar Service Worker bloqueada pelo tema");
                return Promise.reject(new Error("Service Worker desativado temporariamente"));
            };
        }
        
        // Prevenir inicialização do PWA
        window.nosfirNewsPWAEnabled = false;
        window.nosfirNewsConfig = null;
        
        // Sobrescrever a função de inicialização do PWA
        if (typeof PWAManager !== "undefined") {
            window.PWAManager = function() {
                console.log("PWA Manager bloqueado - PWA desativado temporariamente");
                return {
                    isSupported: function() { return false; },
                    getState: function() { return { isInstalled: false, isOnline: navigator.onLine }; },
                    isInstalled: function() { return false; },
                    isOnline: function() { return navigator.onLine; }
                };
            };
        }
        </script>' . "\n";
    }
}
add_action('wp_footer', 'nosfirnews_prevent_service_worker', 999);