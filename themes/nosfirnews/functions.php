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
        
        // Set default thumbnail size (mais apropriado para thumbnails)
        set_post_thumbnail_size( 300, 200, true );
        
        // Add additional image sizes (proporções 16:9 para consistência)
        add_image_size( 'nosfirnews-featured', 1200, 675, true );    // Para posts destacados
        add_image_size( 'nosfirnews-medium', 600, 338, true );      // Para posts médios
        add_image_size( 'nosfirnews-small', 400, 225, true );       // Para cards e widgets
        
        // This theme uses wp_nav_menu() in multiple locations
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
        
        // Add support for core custom logo
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
    wp_enqueue_style( 'nosfirnews-style', get_stylesheet_uri(), array(), NOSFIRNEWS_VERSION );
    
    // Enqueue additional CSS files
    wp_enqueue_style( 'nosfirnews-main', get_template_directory_uri() . '/assets/css/main.css', array(), NOSFIRNEWS_VERSION );
    wp_enqueue_style( 'nosfirnews-responsive', get_template_directory_uri() . '/assets/css/responsive.css', array(), NOSFIRNEWS_VERSION );
    wp_enqueue_style( 'nosfirnews-responsive-images', get_template_directory_uri() . '/assets/css/responsive-images.css', array(), NOSFIRNEWS_VERSION );
    wp_enqueue_style( 'nosfirnews-navigation-enhanced', get_template_directory_uri() . '/assets/css/navigation-enhanced.css', array(), NOSFIRNEWS_VERSION );
    
    // Enqueue Google Fonts
    wp_enqueue_style( 'nosfirnews-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap', array(), null );
    
    // Enqueue template-specific styles
    if ( is_page_template( 'templates/page-templates/page-full-width.php' ) ) {
        wp_enqueue_style( 'nosfirnews-page-full-width', get_template_directory_uri() . '/assets/css/page-full-width.css', array(), NOSFIRNEWS_VERSION );
    }
    
    if ( is_page_template( 'templates/page-templates/page-no-sidebar.php' ) ) {
        wp_enqueue_style( 'nosfirnews-page-no-sidebar', get_template_directory_uri() . '/assets/css/page-no-sidebar.css', array(), NOSFIRNEWS_VERSION );
    }
    
    // Enqueue main JavaScript file
    wp_enqueue_script( 'nosfirnews-main', get_template_directory_uri() . '/assets/js/main.js', array( 'jquery' ), NOSFIRNEWS_VERSION, true );
    
    // Enqueue navigation script
    wp_enqueue_script( 'nosfirnews-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array( 'jquery' ), NOSFIRNEWS_VERSION, true );
    
    // Enqueue mobile menu script
    wp_enqueue_script( 'nosfirnews-mobile-menu', get_template_directory_uri() . '/assets/js/mobile-menu.js', array(), NOSFIRNEWS_VERSION, true );
    
    // Enqueue PWA script
    wp_enqueue_script( 'nosfirnews-pwa', get_template_directory_uri() . '/assets/js/pwa.js', array( 'jquery' ), NOSFIRNEWS_VERSION, true );
    
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

// Body classes are handled in inc/template-functions.php

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
// Disable file editing from admin
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
    define( 'DISALLOW_FILE_EDIT', true );
}

// Remove WordPress version from RSS feeds
function nosfirnews_remove_wp_version_rss() {
    return '';
}
add_filter( 'the_generator', 'nosfirnews_remove_wp_version_rss' );

// Disable pingbacks
function nosfirnews_disable_pingback( &$links ) {
    foreach ( $links as $l => $link ) {
        if ( 0 === strpos( $link, get_option( 'home' ) ) ) {
            unset( $links[$l] );
        }
    }
}
add_action( 'pre_ping', 'nosfirnews_disable_pingback' );

// Remove pingback header
function nosfirnews_remove_x_pingback( $headers ) {
    unset( $headers['X-Pingback'] );
    return $headers;
}
add_filter( 'wp_headers', 'nosfirnews_remove_x_pingback' );

// Disable REST API for non-authenticated users (optional)
function nosfirnews_disable_rest_api( $access ) {
    if ( ! is_user_logged_in() ) {
        return new WP_Error( 'rest_disabled', __( 'REST API disabled.', 'nosfirnews' ), array( 'status' => 401 ) );
    }
    return $access;
}
// Uncomment the line below if you want to disable REST API for non-authenticated users
// add_filter( 'rest_authentication_errors', 'nosfirnews_disable_rest_api' );

// Hide login errors
function nosfirnews_hide_login_errors() {
    return __( 'Something is wrong!', 'nosfirnews' );
}
add_filter( 'login_errors', 'nosfirnews_hide_login_errors' );

// Add security headers
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

/**
 * Performance optimizations
 */
function nosfirnews_performance_optimizations() {
    // Remove query strings from static resources
    if ( ! is_admin() ) {
        add_filter( 'script_loader_src', 'nosfirnews_remove_query_strings' );
        add_filter( 'style_loader_src', 'nosfirnews_remove_query_strings' );
    }
}
add_action( 'init', 'nosfirnews_performance_optimizations' );

function nosfirnews_remove_query_strings( $src ) {
    $output = preg_split( "/(&ver|\?ver)/", $src );
    return $output[0];
}

/**
 * Enhanced performance optimizations
 */
// Preload critical resources
function nosfirnews_preload_resources() {
    // Preload main CSS
    echo '<link rel="preload" href="' . esc_url( get_template_directory_uri() . '/assets/css/main.css' ) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
    
    // Preload main JS
    echo '<link rel="preload" href="' . esc_url( get_template_directory_uri() . '/assets/js/main.js' ) . '" as="script">';
    
    // Preload Google Fonts
    echo '<link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
}
add_action( 'wp_head', 'nosfirnews_preload_resources', 1 );

// Defer non-critical CSS
function nosfirnews_defer_css( $html, $handle, $href, $media ) {
    // List of non-critical CSS handles
    $defer_handles = array( 'nosfirnews-responsive', 'nosfirnews-page-full-width', 'nosfirnews-page-no-sidebar' );
    
    if ( in_array( $handle, $defer_handles, true ) ) {
        $html = '<link rel="preload" href="' . esc_url( $href ) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
        $html .= '<noscript><link rel="stylesheet" href="' . esc_url( $href ) . '"></noscript>';
    }
    
    return $html;
}
add_filter( 'style_loader_tag', 'nosfirnews_defer_css', 10, 4 );



// Optimize database queries
function nosfirnews_optimize_queries() {
    // Remove unnecessary queries
    remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
    
    // Limit post revisions
    if ( ! defined( 'WP_POST_REVISIONS' ) ) {
        define( 'WP_POST_REVISIONS', 3 );
    }
    
    // Increase memory limit if needed
    if ( ! defined( 'WP_MEMORY_LIMIT' ) ) {
        define( 'WP_MEMORY_LIMIT', '256M' );
    }
}
add_action( 'init', 'nosfirnews_optimize_queries' );

// Enable Gzip compression
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

/**
 * Remove default WooCommerce wrapper
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

/**
 * Add custom WooCommerce wrapper
 */
add_action( 'woocommerce_before_main_content', 'nosfirnews_wrapper_start', 10 );
add_action( 'woocommerce_after_main_content', 'nosfirnews_wrapper_end', 10 );

function nosfirnews_wrapper_start() {
    echo '<div id="primary" class="content-area woocommerce-content">';
    echo '<main id="main" class="site-main">';
}

function nosfirnews_wrapper_end() {
    echo '</main><!-- #main -->';
    echo '</div><!-- #primary -->';
}

/**
 * Change number of products per row
 */
function nosfirnews_loop_columns() {
    return 3; // 3 products per row
}
add_filter( 'loop_shop_columns', 'nosfirnews_loop_columns' );

/**
 * Change number of products per page
 */
function nosfirnews_products_per_page() {
    return 12; // 12 products per page
}
add_filter( 'loop_shop_per_page', 'nosfirnews_products_per_page', 20 );

/**
 * Customize WooCommerce breadcrumbs
 */
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



// Adicionar ícone de carrinho ao cabeçalho
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

// Carregar estilos e scripts do WooCommerce
function nosfirnews_woocommerce_assets() {
    if (class_exists('WooCommerce')) {
        // Carregar CSS
        wp_enqueue_style(
            'nosfirnews-woocommerce',
            get_template_directory_uri() . '/assets/css/woocommerce.css',
            array('woocommerce-general'),
            wp_get_theme()->get('Version')
        );
        
        // Carregar JavaScript
        wp_enqueue_script(
            'nosfirnews-woocommerce',
            get_template_directory_uri() . '/assets/js/woocommerce.js',
            array('jquery', 'woocommerce'),
            wp_get_theme()->get('Version'),
            true
        );
        
        // Localizar script para AJAX
        wp_localize_script('nosfirnews-woocommerce', 'nosfirnews_woo_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nosfirnews_woo_nonce'),
            'currency_symbol' => get_woocommerce_currency_symbol(),
            'currency_position' => get_option('woocommerce_currency_pos'),
            'thousand_separator' => wc_get_price_thousand_separator(),
            'decimal_separator' => wc_get_price_decimal_separator(),
            'decimals' => wc_get_price_decimals(),
        ));
    }
}
add_action('wp_enqueue_scripts', 'nosfirnews_woocommerce_assets');

// Hooks e filtros personalizados do WooCommerce

// Personalizar o número de produtos relacionados
function nosfirnews_related_products_args($args) {
    $args['posts_per_page'] = 4;
    $args['columns'] = 4;
    return $args;
}
add_filter('woocommerce_output_related_products_args', 'nosfirnews_related_products_args');

// Personalizar o número de produtos em vendas cruzadas
function nosfirnews_cross_sells_display() {
    woocommerce_cross_sell_display(4, 4);
}
remove_action('woocommerce_cart_collaterals', 'woocommerce_cross_sell_display');
add_action('woocommerce_cart_collaterals', 'nosfirnews_cross_sells_display');

// Adicionar classe CSS personalizada aos produtos
function nosfirnews_product_class($classes, $product) {
    if ($product->is_on_sale()) {
        $classes[] = 'on-sale';
    }
    if ($product->is_featured()) {
        $classes[] = 'featured';
    }
    if (!$product->is_in_stock()) {
        $classes[] = 'out-of-stock';
    }
    return $classes;
}
add_filter('woocommerce_post_class', 'nosfirnews_product_class', 10, 2);

// Personalizar o texto do botão "Adicionar ao carrinho"
function nosfirnews_add_to_cart_text($text, $product) {
    if ($product->get_type() === 'simple') {
        return 'Comprar Agora';
    }
    if ($product->get_type() === 'variable') {
        return 'Selecionar Opções';
    }
    if ($product->get_type() === 'grouped') {
        return 'Ver Produtos';
    }
    if ($product->get_type() === 'external') {
        return 'Comprar no Site';
    }
    return $text;
}
add_filter('woocommerce_product_add_to_cart_text', 'nosfirnews_add_to_cart_text', 10, 2);

// Personalizar placeholder da imagem do produto
function nosfirnews_custom_product_placeholder($image_html, $size, $dimensions) {
    $placeholder_url = get_template_directory_uri() . '/assets/images/product-placeholder.svg';
    if (file_exists(get_template_directory() . '/assets/images/product-placeholder.svg')) {
        return '<img src="' . esc_url($placeholder_url) . '" alt="' . esc_attr__('Placeholder', 'nosfirnews') . '" class="woocommerce-placeholder wp-post-image" />';
    }
    return $image_html;
}
add_filter('woocommerce_single_product_image_thumbnail_html', 'nosfirnews_custom_product_placeholder', 10, 3);

// Adicionar informações extras na página do produto
function nosfirnews_product_extra_info() {
    global $product;
    
    echo '<div class="product-extra-info">';
    
    // Mostrar SKU se disponível
    if ($product->get_sku()) {
        echo '<div class="product-sku"><strong>' . esc_html__('SKU:', 'nosfirnews') . '</strong> ' . esc_html($product->get_sku()) . '</div>';
    }
    
    // Mostrar categorias
    $categories = get_the_terms($product->get_id(), 'product_cat');
    if ($categories && !is_wp_error($categories)) {
        echo '<div class="product-categories"><strong>' . esc_html__('Categorias:', 'nosfirnews') . '</strong> ';
        $category_links = array();
        foreach ($categories as $category) {
            $category_links[] = '<a href="' . esc_url(get_term_link($category)) . '">' . esc_html($category->name) . '</a>';
        }
        echo implode(', ', $category_links);
        echo '</div>';
    }
    
    // Mostrar tags
    $tags = get_the_terms($product->get_id(), 'product_tag');
    if ($tags && !is_wp_error($tags)) {
        echo '<div class="product-tags"><strong>' . esc_html__('Tags:', 'nosfirnews') . '</strong> ';
        $tag_links = array();
        foreach ($tags as $tag) {
            $tag_links[] = '<a href="' . esc_url(get_term_link($tag)) . '">' . esc_html($tag->name) . '</a>';
        }
        echo implode(', ', $tag_links);
        echo '</div>';
    }
    
    echo '</div>';
}
add_action('woocommerce_single_product_summary', 'nosfirnews_product_extra_info', 25);

// Personalizar as abas do produto
function nosfirnews_product_tabs($tabs) {
    // Renomear aba de descrição
    if (isset($tabs['description'])) {
        $tabs['description']['title'] = __('Detalhes do Produto', 'nosfirnews');
    }
    
    // Renomear aba de informações adicionais
    if (isset($tabs['additional_information'])) {
        $tabs['additional_information']['title'] = __('Especificações', 'nosfirnews');
    }
    
    // Adicionar aba personalizada
    $tabs['shipping_info'] = array(
        'title'    => __('Entrega e Devolução', 'nosfirnews'),
        'priority' => 25,
        'callback' => 'nosfirnews_shipping_info_tab_content'
    );
    
    return $tabs;
}
add_filter('woocommerce_product_tabs', 'nosfirnews_product_tabs');

// Conteúdo da aba personalizada
function nosfirnews_shipping_info_tab_content() {
    echo '<div class="shipping-info-content">';
    echo '<h3>' . esc_html__('Informações de Entrega', 'nosfirnews') . '</h3>';
    echo '<ul>';
    echo '<li>' . esc_html__('Entrega grátis para compras acima de R$ 99,00', 'nosfirnews') . '</li>';
    echo '<li>' . esc_html__('Prazo de entrega: 3 a 7 dias úteis', 'nosfirnews') . '</li>';
    echo '<li>' . esc_html__('Entrega expressa disponível', 'nosfirnews') . '</li>';
    echo '</ul>';
    
    echo '<h3>' . esc_html__('Política de Devolução', 'nosfirnews') . '</h3>';
    echo '<ul>';
    echo '<li>' . esc_html__('30 dias para devolução ou troca', 'nosfirnews') . '</li>';
    echo '<li>' . esc_html__('Produto deve estar em perfeitas condições', 'nosfirnews') . '</li>';
    echo '<li>' . esc_html__('Devolução gratuita em caso de defeito', 'nosfirnews') . '</li>';
    echo '</ul>';
    echo '</div>';
}

// Personalizar mensagens de estoque
function nosfirnews_stock_messages($html, $product) {
    $availability = $product->get_availability();
    
    if ($product->is_in_stock()) {
        if ($product->managing_stock() && $product->get_stock_quantity() <= get_option('woocommerce_notify_low_stock_amount')) {
            $html = '<p class="stock in-stock low-stock">' . esc_html__('Últimas unidades!', 'nosfirnews') . '</p>';
        } else {
            $html = '<p class="stock in-stock">' . esc_html__('Em estoque', 'nosfirnews') . '</p>';
        }
    } else {
        $html = '<p class="stock out-of-stock">' . esc_html__('Produto esgotado', 'nosfirnews') . '</p>';
    }
    
    return $html;
}
add_filter('woocommerce_get_stock_html', 'nosfirnews_stock_messages', 10, 2);

// Adicionar botão de lista de desejos (simulado)
function nosfirnews_wishlist_button() {
    global $product;
    
    echo '<div class="wishlist-button-wrapper">';
    echo '<button type="button" class="wishlist-button" data-product-id="' . esc_attr($product->get_id()) . '">';
    echo '<i class="far fa-heart"></i> ' . esc_html__('Adicionar aos Favoritos', 'nosfirnews');
    echo '</button>';
    echo '</div>';
}
add_action('woocommerce_single_product_summary', 'nosfirnews_wishlist_button', 35);

// Personalizar o loop de produtos no arquivo
function nosfirnews_shop_loop_item_title() {
    echo '<h2 class="woocommerce-loop-product__title"><a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a></h2>';
}
remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
add_action('woocommerce_shop_loop_item_title', 'nosfirnews_shop_loop_item_title', 10);

// Adicionar badge de desconto personalizado
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

// Personalizar o resultado da busca de produtos
function nosfirnews_shop_page_title($title) {
    if (is_search() && is_post_type_archive('product')) {
        $title = sprintf(__('Resultados da busca por: "%s"', 'nosfirnews'), get_search_query());
    }
    return $title;
}
add_filter('woocommerce_page_title', 'nosfirnews_shop_page_title');

// Adicionar suporte a zoom na galeria de produtos
function nosfirnews_product_gallery_support() {
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'nosfirnews_product_gallery_support');

// Personalizar o fragmento do carrinho para AJAX
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

/**
 * Get the appropriate image size based on context
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

/**
 * Custom image quality filter
 */
function nosfirnews_image_quality( $quality, $mime_type ) {
    $custom_quality = get_theme_mod( 'nosfirnews_image_quality', 85 );
    return $custom_quality;
}
add_filter( 'wp_editor_set_quality', 'nosfirnews_image_quality', 10, 2 );
add_filter( 'jpeg_quality', 'nosfirnews_image_quality' );

/**
 * Add lazy loading attributes to images
 */
function nosfirnews_add_lazy_loading( $attr, $attachment, $size ) {
    if ( get_theme_mod( 'nosfirnews_enable_lazy_loading', true ) ) {
        $attr['loading'] = 'lazy';
        $attr['decoding'] = 'async';
    }
    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'nosfirnews_add_lazy_loading', 10, 3 );

/**
 * Enhanced post thumbnail function
 */
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
    
    // Add lazy loading if enabled
    if ( get_theme_mod( 'nosfirnews_enable_lazy_loading', true ) ) {
        $attr['loading'] = 'lazy';
        $attr['decoding'] = 'async';
    }
    
    the_post_thumbnail( $image_size, $attr );
}

/**
 * Get responsive image with multiple sizes
 */
function nosfirnews_get_responsive_image( $attachment_id, $size = 'medium', $attr = array() ) {
    if ( ! $attachment_id ) {
        return '';
    }
    
    $default_attr = array(
        'class' => 'img-responsive',
    );
    
    $attr = wp_parse_args( $attr, $default_attr );
    
    // Add lazy loading if enabled
    if ( get_theme_mod( 'nosfirnews_enable_lazy_loading', true ) ) {
        $attr['loading'] = 'lazy';
        $attr['decoding'] = 'async';
    }
    
    return wp_get_attachment_image( $attachment_id, $size, false, $attr );
}

/**
 * Add additional image sizes with customizer integration
 */
function nosfirnews_add_custom_image_sizes() {
    // Add more flexible image sizes (proporções padronizadas)
    add_image_size( 'nosfirnews-hero', 1920, 1080, true );      // 16:9 para hero sections
    add_image_size( 'nosfirnews-card', 400, 225, true );        // 16:9 para cards
    add_image_size( 'nosfirnews-thumbnail', 200, 200, true );   // Quadrado para avatars/thumbs
    add_image_size( 'nosfirnews-gallery', 800, 450, true );     // 16:9 para galerias
    add_image_size( 'nosfirnews-widget', 300, 169, true );      // 16:9 para widgets
}
add_action( 'after_setup_theme', 'nosfirnews_add_custom_image_sizes' );

/**
 * Make custom image sizes available in media library
 */
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

/**
 * Optimize image loading with WebP support
 */
function nosfirnews_webp_support() {
    if ( function_exists( 'imagewebp' ) ) {
        add_filter( 'wp_generate_attachment_metadata', 'nosfirnews_generate_webp_images' );
    }
}
add_action( 'init', 'nosfirnews_webp_support' );

/**
 * Generate WebP versions of uploaded images
 */
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
 * PWA Functions
 */

/**
 * Add PWA manifest link to head
 */
function nosfirnews_add_manifest_link() {
    echo '<link rel="manifest" href="' . get_template_directory_uri() . '/manifest.json">' . "\n";
    echo '<meta name="theme-color" content="#2196F3">' . "\n";
    echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
    echo '<meta name="apple-mobile-web-app-status-bar-style" content="default">' . "\n";
    echo '<meta name="apple-mobile-web-app-title" content="NosfirNews">' . "\n";
    echo '<link rel="apple-touch-icon" href="' . get_template_directory_uri() . '/assets/images/icons/icon-192x192.png">' . "\n";
}
add_action( 'wp_head', 'nosfirnews_add_manifest_link' );

/**
 * Create offline page if it doesn't exist
 */
function nosfirnews_create_offline_page() {
    $offline_page = get_page_by_path( 'offline' );
    
    if ( ! $offline_page ) {
        $page_data = array(
            'post_title'   => 'Offline',
            'post_content' => '',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_name'    => 'offline'
        );
        
        wp_insert_post( $page_data );
    }
}
add_action( 'after_switch_theme', 'nosfirnews_create_offline_page' );

/**
 * Add PWA meta tags for better mobile experience
 */
function nosfirnews_pwa_meta_tags() {
    echo '<meta name="mobile-web-app-capable" content="yes">' . "\n";
    echo '<meta name="application-name" content="NosfirNews">' . "\n";
    echo '<meta name="msapplication-TileColor" content="#2196F3">' . "\n";
    echo '<meta name="msapplication-TileImage" content="' . get_template_directory_uri() . '/assets/images/icons/icon-144x144.png">' . "\n";
    echo '<meta name="msapplication-config" content="' . get_template_directory_uri() . '/browserconfig.xml">' . "\n";
}
add_action( 'wp_head', 'nosfirnews_pwa_meta_tags' );

/**
 * Register REST API endpoint for push notifications
 */
function nosfirnews_register_push_api() {
    register_rest_route( 'nosfirnews/v1', '/push-subscription', array(
        'methods'  => 'POST',
        'callback' => 'nosfirnews_handle_push_subscription',
        'permission_callback' => '__return_true',
    ) );
}
add_action( 'rest_api_init', 'nosfirnews_register_push_api' );

/**
 * Handle push notification subscription
 */
function nosfirnews_handle_push_subscription( $request ) {
    $subscription = $request->get_json_params();
    
    if ( ! $subscription ) {
        return new WP_Error( 'invalid_subscription', 'Invalid subscription data', array( 'status' => 400 ) );
    }
    
    // Store subscription in database
    $user_id = get_current_user_id();
    $subscriptions = get_option( 'nosfirnews_push_subscriptions', array() );
    
    $subscription_key = md5( json_encode( $subscription ) );
    $subscriptions[ $subscription_key ] = array(
        'subscription' => $subscription,
        'user_id'      => $user_id,
        'created_at'   => current_time( 'mysql' )
    );
    
    update_option( 'nosfirnews_push_subscriptions', $subscriptions );
    
    return rest_ensure_response( array( 'success' => true ) );
}

/**
 * Send push notification to all subscribers
 */
function nosfirnews_send_push_notification( $title, $body, $url = '' ) {
    $subscriptions = get_option( 'nosfirnews_push_subscriptions', array() );
    
    if ( empty( $subscriptions ) ) {
        return false;
    }
    
    $payload = json_encode( array(
        'title' => $title,
        'body'  => $body,
        'url'   => $url,
        'icon'  => get_template_directory_uri() . '/assets/images/icons/icon-192x192.png',
        'badge' => get_template_directory_uri() . '/assets/images/icons/badge-72x72.png'
    ) );
    
    // Here you would implement the actual push notification sending
    // using a service like Firebase Cloud Messaging or Web Push Protocol
    
    return true;
}

/**
 * Send push notification when new post is published
 */
function nosfirnews_notify_new_post( $post_id, $post ) {
    if ( $post->post_status !== 'publish' || $post->post_type !== 'post' ) {
        return;
    }
    
    $title = 'Nova notícia publicada!';
    $body = wp_trim_words( $post->post_title, 10 );
    $url = get_permalink( $post_id );
    
    nosfirnews_send_push_notification( $title, $body, $url );
}
add_action( 'publish_post', 'nosfirnews_notify_new_post', 10, 2 );

/**
 * Add PWA cache headers
 */
function nosfirnews_add_cache_headers() {
    if ( ! is_admin() ) {
        // Cache static assets for 1 year
        if ( preg_match( '/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$/', $_SERVER['REQUEST_URI'] ) ) {
            header( 'Cache-Control: public, max-age=31536000, immutable' );
        }
        // Cache HTML pages for 1 hour
        else {
            header( 'Cache-Control: public, max-age=3600' );
        }
    }
}
add_action( 'send_headers', 'nosfirnews_add_cache_headers' );

/**
 * Add preload hints for critical resources
 */
function nosfirnews_add_preload_hints() {
    echo '<link rel="preload" href="' . get_template_directory_uri() . '/assets/css/main.css" as="style">' . "\n";
    echo '<link rel="preload" href="' . get_template_directory_uri() . '/assets/js/main.js" as="script">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action( 'wp_head', 'nosfirnews_add_preload_hints', 1 );

/**
 * Add service worker registration script
 */
function nosfirnews_add_sw_registration() {
    if ( ! is_admin() ) {
        echo '<script>
        if ("serviceWorker" in navigator) {
            window.addEventListener("load", function() {
                navigator.serviceWorker.register("' . get_template_directory_uri() . '/sw.js")
                    .then(function(registration) {
                        console.log("SW registered: ", registration);
                    })
                    .catch(function(registrationError) {
                        console.log("SW registration failed: ", registrationError);
                    });
            });
        }
        </script>' . "\n";
    }
}
add_action( 'wp_footer', 'nosfirnews_add_sw_registration' );

/**
 * Handle offline page template
 */
function nosfirnews_offline_page_template( $template ) {
    if ( is_page( 'offline' ) ) {
        $offline_template = get_template_directory() . '/offline.php';
        if ( file_exists( $offline_template ) ) {
            return $offline_template;
        }
    }
    return $template;
}
add_filter( 'page_template', 'nosfirnews_offline_page_template' );

/**
 * Include AMP Support
 */
require_once get_template_directory() . '/inc/amp-support.php';