<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function nosfirnews_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
    add_theme_support( 'custom-logo', [ 'height' => 100, 'width' => 300, 'flex-height' => true, 'flex-width' => true ] );
    register_nav_menus( [
        'primary'   => __( 'Primary Menu', 'nosfirnews' ),
        'secondary' => __( 'Secondary Menu', 'nosfirnews' ),
        'footer'    => __( 'Footer Menu', 'nosfirnews' ),
        'mobile'    => __( 'Mobile Menu', 'nosfirnews' ),
        'sidebar'   => __( 'Sidebar Menu', 'nosfirnews' ),
        'social'    => __( 'Social Menu', 'nosfirnews' ),
    ] );
    load_theme_textdomain( 'nosfirnews', get_template_directory() . '/language' );
    $w = (int) get_theme_mod( 'nn_thumb_width', 320 );
    $h = (int) get_theme_mod( 'nn_thumb_height', 180 );
    $crop = (bool) get_theme_mod( 'nn_thumb_crop', false );
    add_image_size( 'nn_thumb_standard', max(1,$w), max(1,$h), $crop );
    $sw = (int) get_theme_mod( 'nn_single_cover_width', 1200 );
    $sh = (int) get_theme_mod( 'nn_single_cover_height', 480 );
    $scrop = (bool) get_theme_mod( 'nn_single_cover_crop', false );
    add_image_size( 'nn_single_cover', max(1,$sw), max(1,$sh), $scrop );
}
add_action( 'after_setup_theme', 'nosfirnews_setup' );

function nosfirnews_scripts() {
    wp_enqueue_style( 'nosfirnews-style', get_stylesheet_uri(), [], filemtime( get_template_directory() . '/style.css' ) );
    $main_css = get_template_directory() . '/style-main-nosfirnews.css';
    if ( file_exists( $main_css ) ) {
        wp_enqueue_style( 'nosfirnews-main', get_template_directory_uri() . '/style-main-nosfirnews.css', [ 'nosfirnews-style' ], filemtime( $main_css ) );
    }
    if ( is_rtl() ) {
        $rtl_css = get_template_directory() . '/style-main-nosfirnews-rtl.css';
        if ( file_exists( $rtl_css ) ) {
            wp_enqueue_style( 'nosfirnews-main-rtl', get_template_directory_uri() . '/style-main-nosfirnews-rtl.css', [ 'nosfirnews-main' ], filemtime( $rtl_css ) );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'nosfirnews_scripts' );

add_action( 'customize_register', function( $wp_customize ) {
    $wp_customize->add_section( 'nosfirnews_menu_options', [
        'title'    => __( 'Menu Options', 'nosfirnews' ),
        'priority' => 160,
    ] );
    $wp_customize->add_setting( 'nosfirnews_enable_menu_search', [
        'default'           => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'nosfirnews_enable_menu_search', [
        'type'    => 'checkbox',
        'section' => 'nosfirnews_menu_options',
        'label'   => __( 'Enable Search in Menu', 'nosfirnews' ),
    ] );
    $wp_customize->add_setting( 'nosfirnews_enable_menu_social', [
        'default'           => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'nosfirnews_enable_menu_social', [
        'type'    => 'checkbox',
        'section' => 'nosfirnews_menu_options',
        'label'   => __( 'Enable Social Links in Menu', 'nosfirnews' ),
    ] );
    $wp_customize->add_setting( 'nosfirnews_menu_social_append_to', [
        'default'           => 'primary',
        'sanitize_callback' => function( $val ) { $allowed = [ 'primary','secondary','footer' ]; return in_array( $val, $allowed, true ) ? $val : 'primary'; },
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'nosfirnews_menu_social_append_to', [
        'type'    => 'select',
        'section' => 'nosfirnews_menu_options',
        'label'   => __( 'Append Social Links To', 'nosfirnews' ),
        'choices' => [
            'primary'   => __( 'Primary Menu', 'nosfirnews' ),
            'secondary' => __( 'Secondary Menu', 'nosfirnews' ),
            'footer'    => __( 'Footer Menu', 'nosfirnews' ),
        ],
    ] );
    $wp_customize->add_setting( 'nosfirnews_primary_menu_location', [
        'default'           => 'primary',
        'sanitize_callback' => function( $val ) { $allowed = [ 'primary','mobile','sidebar' ]; return in_array( $val, $allowed, true ) ? $val : 'primary'; },
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'nosfirnews_primary_menu_location', [
        'type'    => 'select',
        'section' => 'nosfirnews_menu_options',
        'label'   => __( 'Primary Menu Location', 'nosfirnews' ),
        'choices' => [
            'primary' => __( 'Primary', 'nosfirnews' ),
            'mobile'  => __( 'Mobile', 'nosfirnews' ),
            'sidebar' => __( 'Sidebar', 'nosfirnews' ),
        ],
    ] );
    $wp_customize->add_section( 'nn_woocommerce', [ 'title' => __( 'WooCommerce', 'nosfirnews' ), 'panel' => 'nosfirnews_site_options' ] );
    $wp_customize->add_setting( 'nn_wc_enable_carousel', [ 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_wc_enable_carousel', [ 'type' => 'checkbox', 'section' => 'nn_woocommerce', 'label' => __( 'Enable product carousel shortcode', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_wc_carousel_items', [ 'default' => 4, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_wc_carousel_items', [ 'type' => 'number', 'section' => 'nn_woocommerce', 'label' => __( 'Carousel items per view', 'nosfirnews' ), 'input_attrs' => [ 'min' => 2, 'max' => 6 ] ] );
    $wp_customize->add_setting( 'nn_wc_carousel_autoplay', [ 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_wc_carousel_autoplay', [ 'type' => 'checkbox', 'section' => 'nn_woocommerce', 'label' => __( 'Autoplay carousel', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_wc_enable_featured_block', [ 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_wc_enable_featured_block', [ 'type' => 'checkbox', 'section' => 'nn_woocommerce', 'label' => __( 'Show featured products block on shop', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_wc_featured_count', [ 'default' => 8, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_wc_featured_count', [ 'type' => 'number', 'section' => 'nn_woocommerce', 'label' => __( 'Featured products count', 'nosfirnews' ), 'input_attrs' => [ 'min' => 3, 'max' => 12 ] ] );
    $wp_customize->add_setting( 'nn_wc_popup_enable', [ 'default' => false, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_wc_popup_enable', [ 'type' => 'checkbox', 'section' => 'nn_woocommerce', 'label' => __( 'Enable WooCommerce popup', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_wc_popup_scope', [ 'default' => 'shop', 'sanitize_callback' => function( $v ){ $a = [ 'shop','product','all' ]; return in_array( $v, $a, true ) ? $v : 'shop'; }, 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_wc_popup_scope', [ 'type' => 'select', 'section' => 'nn_woocommerce', 'label' => __( 'Popup scope', 'nosfirnews' ), 'choices' => [ 'shop' => __( 'Shop and categories', 'nosfirnews' ), 'product' => __( 'Single product pages', 'nosfirnews' ), 'all' => __( 'All WooCommerce pages', 'nosfirnews' ) ] ] );
    $wp_customize->add_setting( 'nn_wc_popup_delay', [ 'default' => 3, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_wc_popup_delay', [ 'type' => 'number', 'section' => 'nn_woocommerce', 'label' => __( 'Popup delay (seconds)', 'nosfirnews' ), 'input_attrs' => [ 'min' => 0, 'max' => 30 ] ] );
    $wp_customize->add_setting( 'nn_wc_popup_content', [ 'default' => '', 'sanitize_callback' => 'wp_kses_post', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_wc_popup_content', [ 'type' => 'textarea', 'section' => 'nn_woocommerce', 'label' => __( 'Popup content (HTML allowed)', 'nosfirnews' ) ] );
} );

add_action( 'customize_register', function( $wp_customize ) {
    $wp_customize->add_panel( 'nosfirnews_site_options', [ 'title' => __( 'NosfirNews Options', 'nosfirnews' ), 'priority' => 150 ] );

    // Global settings
    $wp_customize->add_section( 'nn_global', [ 'title' => __( 'Global Settings', 'nosfirnews' ), 'panel' => 'nosfirnews_site_options' ] );
    $wp_customize->add_setting( 'nn_container_max_width', [ 'default' => 1200, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_container_max_width', [ 'type' => 'number', 'section' => 'nn_global', 'label' => __( 'Container Max Width (px)', 'nosfirnews' ), 'input_attrs' => [ 'min' => 800, 'max' => 1800 ] ] );
    $wp_customize->add_setting( 'nn_boxed_layout', [ 'default' => false, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_boxed_layout', [ 'type' => 'checkbox', 'section' => 'nn_global', 'label' => __( 'Use boxed layout', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_primary_color', [ 'default' => '#0073aa', 'sanitize_callback' => 'sanitize_hex_color', 'transport' => 'refresh' ] );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'nn_primary_color', [ 'section' => 'nn_global', 'label' => __( 'Primary Color', 'nosfirnews' ) ] ) );

    // Typography
    $wp_customize->add_section( 'nn_typography', [ 'title' => __( 'Typography', 'nosfirnews' ), 'panel' => 'nosfirnews_site_options' ] );
    $wp_customize->add_setting( 'nn_base_font_family', [ 'default' => 'system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_base_font_family', [ 'type' => 'text', 'section' => 'nn_typography', 'label' => __( 'Base Font Family', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_base_font_size', [ 'default' => 16, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_base_font_size', [ 'type' => 'number', 'section' => 'nn_typography', 'label' => __( 'Base Font Size (px)', 'nosfirnews' ), 'input_attrs' => [ 'min' => 12, 'max' => 22 ] ] );
    $wp_customize->add_setting( 'nn_headings_scale', [ 'default' => 1.0, 'sanitize_callback' => 'floatval', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_headings_scale', [ 'type' => 'number', 'section' => 'nn_typography', 'label' => __( 'Headings Scale', 'nosfirnews' ), 'input_attrs' => [ 'min' => 0.8, 'max' => 1.6, 'step' => 0.05 ] ] );

    // Blog settings
    $wp_customize->add_section( 'nn_blog', [ 'title' => __( 'Blog Settings', 'nosfirnews' ), 'panel' => 'nosfirnews_site_options' ] );
    $wp_customize->add_setting( 'nosfirnews_pagination_type', [ 'default' => 'number', 'sanitize_callback' => function( $v ){ $a = [ 'number','infinite' ]; return in_array( $v, $a, true ) ? $v : 'number'; }, 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nosfirnews_pagination_type', [ 'type' => 'select', 'section' => 'nn_blog', 'label' => __( 'Pagination', 'nosfirnews' ), 'choices' => [ 'number' => __( 'Numbers', 'nosfirnews' ), 'infinite' => __( 'Infinite Scroll', 'nosfirnews' ) ] ] );
    $wp_customize->add_setting( 'nn_excerpt_length', [ 'default' => 25, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_excerpt_length', [ 'type' => 'number', 'section' => 'nn_blog', 'label' => __( 'Excerpt Length (words)', 'nosfirnews' ), 'input_attrs' => [ 'min' => 10, 'max' => 80 ] ] );
    $wp_customize->add_setting( 'nosfirnews_show_featured_image', [ 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nosfirnews_show_featured_image', [ 'type' => 'checkbox', 'section' => 'nn_blog', 'label' => __( 'Show featured image', 'nosfirnews' ) ] );

    // Homepage settings
    $wp_customize->add_section( 'nn_home', [ 'title' => __( 'Homepage Settings', 'nosfirnews' ), 'panel' => 'nosfirnews_site_options' ] );
    $wp_customize->add_setting( 'nn_home_layout', [ 'default' => 'grid', 'sanitize_callback' => function( $v ){ $a = [ 'list','grid','masonry' ]; return in_array( $v, $a, true ) ? $v : 'grid'; }, 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_home_layout', [ 'type' => 'select', 'section' => 'nn_home', 'label' => __( 'Posts Layout', 'nosfirnews' ), 'choices' => [ 'list' => __( 'List', 'nosfirnews' ), 'grid' => __( 'Grid', 'nosfirnews' ), 'masonry' => __( 'Masonry', 'nosfirnews' ) ] ] );
    $wp_customize->add_setting( 'nn_home_columns', [ 'default' => 3, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_home_columns', [ 'type' => 'number', 'section' => 'nn_home', 'label' => __( 'Grid Columns', 'nosfirnews' ), 'input_attrs' => [ 'min' => 2, 'max' => 4 ] ] );
    $wp_customize->add_setting( 'nn_home_show_hero', [ 'default' => false, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_home_show_hero', [ 'type' => 'checkbox', 'section' => 'nn_home', 'label' => __( 'Show Hero Section', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_home_hero_title', [ 'default' => __( 'Bem-vindo', 'nosfirnews' ), 'sanitize_callback' => 'wp_filter_nohtml_kses', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_home_hero_title', [ 'type' => 'text', 'section' => 'nn_home', 'label' => __( 'Hero Title', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_home_hero_subtitle', [ 'default' => '', 'sanitize_callback' => 'wp_filter_nohtml_kses', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_home_hero_subtitle', [ 'type' => 'text', 'section' => 'nn_home', 'label' => __( 'Hero Subtitle', 'nosfirnews' ) ] );

    // 404 settings
    $wp_customize->add_section( 'nn_404', [ 'title' => __( '404 Page', 'nosfirnews' ), 'panel' => 'nosfirnews_site_options' ] );
    $wp_customize->add_setting( 'nn_404_title', [ 'default' => __( 'Página não encontrada', 'nosfirnews' ), 'sanitize_callback' => 'wp_filter_nohtml_kses', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_404_title', [ 'type' => 'text', 'section' => 'nn_404', 'label' => __( 'Title', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_404_message', [ 'default' => __( 'Tente buscar novamente.', 'nosfirnews' ), 'sanitize_callback' => 'wp_filter_nohtml_kses', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_404_message', [ 'type' => 'text', 'section' => 'nn_404', 'label' => __( 'Message', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_404_show_search', [ 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_404_show_search', [ 'type' => 'checkbox', 'section' => 'nn_404', 'label' => __( 'Show search', 'nosfirnews' ) ] );

    // 500 settings
    $wp_customize->add_section( 'nn_500', [ 'title' => __( '500 Page', 'nosfirnews' ), 'panel' => 'nosfirnews_site_options' ] );
    $wp_customize->add_setting( 'nn_500_title', [ 'default' => __( 'Erro interno do servidor', 'nosfirnews' ), 'sanitize_callback' => 'wp_filter_nohtml_kses', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_500_title', [ 'type' => 'text', 'section' => 'nn_500', 'label' => __( 'Title', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_500_message', [ 'default' => __( 'Algo deu errado. Tente novamente mais tarde.', 'nosfirnews' ), 'sanitize_callback' => 'wp_filter_nohtml_kses', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_500_message', [ 'type' => 'text', 'section' => 'nn_500', 'label' => __( 'Message', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_500_show_search', [ 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_500_show_search', [ 'type' => 'checkbox', 'section' => 'nn_500', 'label' => __( 'Show search', 'nosfirnews' ) ] );
    $wp_customize->add_section( 'nn_header', [ 'title' => __( 'Header', 'nosfirnews' ), 'panel' => 'nosfirnews_site_options' ] );
    $wp_customize->add_setting( 'nn_logo_alignment', [ 'default' => 'left', 'sanitize_callback' => function( $v ){ $a = [ 'left','center','right' ]; return in_array( $v, $a, true ) ? $v : 'left'; }, 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_logo_alignment', [ 'type' => 'select', 'section' => 'nn_header', 'label' => __( 'Logo Alignment', 'nosfirnews' ), 'choices' => [ 'left' => __( 'Left', 'nosfirnews' ), 'center' => __( 'Center', 'nosfirnews' ), 'right' => __( 'Right', 'nosfirnews' ) ] ] );
    $wp_customize->add_setting( 'nn_mobile_menu_location', [ 'default' => 'mobile', 'sanitize_callback' => function( $v ){ $a = [ 'mobile','primary','sidebar' ]; return in_array( $v, $a, true ) ? $v : 'mobile'; }, 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_mobile_menu_location', [ 'type' => 'select', 'section' => 'nn_header', 'label' => __( 'Mobile Menu Location', 'nosfirnews' ), 'choices' => [ 'mobile' => __( 'Mobile', 'nosfirnews' ), 'primary' => __( 'Primary', 'nosfirnews' ), 'sidebar' => __( 'Sidebar', 'nosfirnews' ) ] ] );
    $wp_customize->add_setting( 'nn_mobile_breakpoint', [ 'default' => 998, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_mobile_breakpoint', [ 'type' => 'number', 'section' => 'nn_header', 'label' => __( 'Mobile Breakpoint (px)', 'nosfirnews' ), 'input_attrs' => [ 'min' => 480, 'max' => 1200 ] ] );
    $wp_customize->add_setting( 'nn_nav_alignment', [ 'default' => 'right', 'sanitize_callback' => function( $v ){ $a = [ 'left','center','right' ]; return in_array( $v, $a, true ) ? $v : 'right'; }, 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_nav_alignment', [ 'type' => 'select', 'section' => 'nn_header', 'label' => __( 'Navigation Alignment', 'nosfirnews' ), 'choices' => [ 'left' => __( 'Left', 'nosfirnews' ), 'center' => __( 'Center', 'nosfirnews' ), 'right' => __( 'Right', 'nosfirnews' ) ] ] );
    $wp_customize->add_setting( 'nn_header_order', [ 'default' => 'logo_first', 'sanitize_callback' => function( $v ){ $a = [ 'logo_first','nav_first' ]; return in_array( $v, $a, true ) ? $v : 'logo_first'; }, 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_header_order', [ 'type' => 'select', 'section' => 'nn_header', 'label' => __( 'Header Order', 'nosfirnews' ), 'choices' => [ 'logo_first' => __( 'Logo first, Nav after', 'nosfirnews' ), 'nav_first' => __( 'Nav first, Logo after', 'nosfirnews' ) ] ] );
    $wp_customize->add_setting( 'nn_header_logo_height', [ 'default' => 48, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_header_logo_height', [ 'type' => 'number', 'section' => 'nn_header', 'label' => __( 'Header Logo Height (px)', 'nosfirnews' ), 'input_attrs' => [ 'min' => 24, 'max' => 160 ] ] );
    $wp_customize->add_section( 'nn_footer', [ 'title' => __( 'Footer', 'nosfirnews' ), 'panel' => 'nosfirnews_site_options' ] );
    $wp_customize->add_setting( 'nn_footer_show_logo', [ 'default' => false, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_footer_show_logo', [ 'type' => 'checkbox', 'section' => 'nn_footer', 'label' => __( 'Show footer logo', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_footer_logo', [ 'default' => '', 'sanitize_callback' => 'esc_url_raw', 'transport' => 'refresh' ] );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'nn_footer_logo', [ 'section' => 'nn_footer', 'label' => __( 'Footer Logo', 'nosfirnews' ) ] ) );
    $wp_customize->add_setting( 'nn_footer_description', [ 'default' => '', 'sanitize_callback' => 'wp_kses_post', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_footer_description', [ 'type' => 'textarea', 'section' => 'nn_footer', 'label' => __( 'Footer Description', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_footer_show_social', [ 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_footer_show_social', [ 'type' => 'checkbox', 'section' => 'nn_footer', 'label' => __( 'Show social menu', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_footer_links_menu_location', [ 'default' => 'footer', 'sanitize_callback' => function( $v ){ $a = [ 'footer','primary','secondary' ]; return in_array( $v, $a, true ) ? $v : 'footer'; }, 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_footer_links_menu_location', [ 'type' => 'select', 'section' => 'nn_footer', 'label' => __( 'Links Menu Location', 'nosfirnews' ), 'choices' => [ 'footer' => __( 'Footer', 'nosfirnews' ), 'primary' => __( 'Primary', 'nosfirnews' ), 'secondary' => __( 'Secondary', 'nosfirnews' ) ] ] );
    $wp_customize->add_setting( 'nn_footer_columns', [ 'default' => 2, 'sanitize_callback' => function( $v ){ $v = absint( $v ); if ( $v < 1 ) $v = 1; if ( $v > 4 ) $v = 4; return $v; }, 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_footer_columns', [ 'type' => 'number', 'section' => 'nn_footer', 'label' => __( 'Footer Columns', 'nosfirnews' ), 'input_attrs' => [ 'min' => 1, 'max' => 4 ] ] );
    $wp_customize->add_setting( 'nn_footer_gap', [ 'default' => 20, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_footer_gap', [ 'type' => 'number', 'section' => 'nn_footer', 'label' => __( 'Footer Gap (px)', 'nosfirnews' ), 'input_attrs' => [ 'min' => 0, 'max' => 40 ] ] );
    $wp_customize->add_setting( 'nn_footer_align', [ 'default' => 'stretch', 'sanitize_callback' => function( $v ){ $a = [ 'left','center','right','space-between','stretch' ]; return in_array( $v, $a, true ) ? $v : 'stretch'; }, 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_footer_align', [ 'type' => 'select', 'section' => 'nn_footer', 'label' => __( 'Footer Alignment', 'nosfirnews' ), 'choices' => [ 'left' => __( 'Left', 'nosfirnews' ), 'center' => __( 'Center', 'nosfirnews' ), 'right' => __( 'Right', 'nosfirnews' ), 'space-between' => __( 'Space between', 'nosfirnews' ), 'stretch' => __( 'Stretch', 'nosfirnews' ) ] ] );
    $wp_customize->add_setting( 'nn_footer_logo_height', [ 'default' => 50, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_footer_logo_height', [ 'type' => 'number', 'section' => 'nn_footer', 'label' => __( 'Footer Logo Height (px)', 'nosfirnews' ), 'input_attrs' => [ 'min' => 24, 'max' => 160 ] ] );
    $wp_customize->add_section( 'nn_media', [ 'title' => __( 'Media', 'nosfirnews' ), 'panel' => 'nosfirnews_site_options' ] );
    $wp_customize->add_setting( 'nn_post_thumb_show', [ 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_post_thumb_show', [ 'type' => 'checkbox', 'section' => 'nn_media', 'label' => __( 'Show post thumbnails (archive)', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_single_thumb_show', [ 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_single_thumb_show', [ 'type' => 'checkbox', 'section' => 'nn_media', 'label' => __( 'Show featured image on single posts', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_page_featured_show', [ 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_page_featured_show', [ 'type' => 'checkbox', 'section' => 'nn_media', 'label' => __( 'Show featured image on pages', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_archive_show_excerpt', [ 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_archive_show_excerpt', [ 'type' => 'checkbox', 'section' => 'nn_media', 'label' => __( 'Show excerpt on archive cards', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_archive_show_read_more', [ 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_archive_show_read_more', [ 'type' => 'checkbox', 'section' => 'nn_media', 'label' => __( 'Show "Read more" button on archive cards', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_read_more_text', [ 'default' => __( 'Leia mais', 'nosfirnews' ), 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_read_more_text', [ 'type' => 'text', 'section' => 'nn_media', 'label' => __( 'Read more text', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_read_more_style', [ 'default' => 'primary', 'sanitize_callback' => function( $v ){ $a = [ 'primary','link' ]; return in_array( $v, $a, true ) ? $v : 'primary'; }, 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_read_more_style', [ 'type' => 'select', 'section' => 'nn_media', 'label' => __( 'Read more style', 'nosfirnews' ), 'choices' => [ 'primary' => __( 'Primary button', 'nosfirnews' ), 'link' => __( 'Link', 'nosfirnews' ) ] ] );
    $wp_customize->add_setting( 'nn_thumb_width', [ 'default' => 320, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_thumb_width', [ 'type' => 'number', 'section' => 'nn_media', 'label' => __( 'Thumbnail width (px)', 'nosfirnews' ), 'input_attrs' => [ 'min' => 120, 'max' => 800 ] ] );
    $wp_customize->add_setting( 'nn_thumb_height', [ 'default' => 180, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_thumb_height', [ 'type' => 'number', 'section' => 'nn_media', 'label' => __( 'Thumbnail height (px)', 'nosfirnews' ), 'input_attrs' => [ 'min' => 90, 'max' => 600 ] ] );
    $wp_customize->add_setting( 'nn_thumb_crop', [ 'default' => false, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_thumb_crop', [ 'type' => 'checkbox', 'section' => 'nn_media', 'label' => __( 'Crop padronizado (cortar para preencher)', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_thumb_size', [ 'default' => 'large', 'sanitize_callback' => function( $v ){ $a = [ 'thumbnail','medium','large','full' ]; return in_array( $v, $a, true ) ? $v : 'large'; }, 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_thumb_size', [ 'type' => 'select', 'section' => 'nn_media', 'label' => __( 'WordPress image size to use', 'nosfirnews' ), 'choices' => [ 'thumbnail' => 'thumbnail', 'medium' => 'medium', 'large' => 'large', 'full' => 'full' ] ] );
    $wp_customize->add_setting( 'nn_use_standard_thumb', [ 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_use_standard_thumb', [ 'type' => 'checkbox', 'section' => 'nn_media', 'label' => __( 'Usar tamanho padrão "nn_thumb_standard"', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_thumb_fit', [ 'default' => 'contain', 'sanitize_callback' => function( $v ){ $a = [ 'cover','contain','auto' ]; return in_array( $v, $a, true ) ? $v : 'contain'; }, 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_thumb_fit', [ 'type' => 'select', 'section' => 'nn_media', 'label' => __( 'Thumb fit mode', 'nosfirnews' ), 'choices' => [ 'cover' => __( 'Crop (cover)', 'nosfirnews' ), 'contain' => __( 'Contain without crop', 'nosfirnews' ), 'auto' => __( 'Auto height', 'nosfirnews' ) ] ] );
    $wp_customize->add_setting( 'nn_thumb_border_radius', [ 'default' => 8, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_thumb_border_radius', [ 'type' => 'number', 'section' => 'nn_media', 'label' => __( 'Thumb border radius (px)', 'nosfirnews' ), 'input_attrs' => [ 'min' => 0, 'max' => 50 ] ] );
    $wp_customize->add_setting( 'nn_thumb_shadow', [ 'default' => 'none', 'sanitize_callback' => function( $v ){ $a = [ 'none','soft','medium','hard' ]; return in_array( $v, $a, true ) ? $v : 'none'; }, 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_thumb_shadow', [ 'type' => 'select', 'section' => 'nn_media', 'label' => __( 'Thumb shadow', 'nosfirnews' ), 'choices' => [ 'none' => __( 'None', 'nosfirnews' ), 'soft' => __( 'Soft', 'nosfirnews' ), 'medium' => __( 'Medium', 'nosfirnews' ), 'hard' => __( 'Hard', 'nosfirnews' ) ] ] );
    $wp_customize->add_setting( 'nn_thumb_filter', [ 'default' => 'none', 'sanitize_callback' => function( $v ){ $a = [ 'none','grayscale','sepia','saturate','contrast','brightness','blur' ]; return in_array( $v, $a, true ) ? $v : 'none'; }, 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_thumb_filter', [ 'type' => 'select', 'section' => 'nn_media', 'label' => __( 'Thumb filter effect', 'nosfirnews' ), 'choices' => [ 'none' => __( 'None', 'nosfirnews' ), 'grayscale' => __( 'Grayscale', 'nosfirnews' ), 'sepia' => __( 'Sepia', 'nosfirnews' ), 'saturate' => __( 'Saturate', 'nosfirnews' ), 'contrast' => __( 'Contrast', 'nosfirnews' ), 'brightness' => __( 'Brightness', 'nosfirnews' ), 'blur' => __( 'Blur', 'nosfirnews' ) ] ] );
    $wp_customize->add_setting( 'nn_thumb_hover_effect', [ 'default' => 'none', 'sanitize_callback' => function( $v ){ $a = [ 'none','zoom','lift' ]; return in_array( $v, $a, true ) ? $v : 'none'; }, 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_thumb_hover_effect', [ 'type' => 'select', 'section' => 'nn_media', 'label' => __( 'Thumb hover effect', 'nosfirnews' ), 'choices' => [ 'none' => __( 'None', 'nosfirnews' ), 'zoom' => __( 'Zoom', 'nosfirnews' ), 'lift' => __( 'Lift', 'nosfirnews' ) ] ] );
    $wp_customize->add_setting( 'nn_thumb_quality', [ 'default' => 90, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_thumb_quality', [ 'type' => 'number', 'section' => 'nn_media', 'label' => __( 'Qualidade das imagens (%)', 'nosfirnews' ), 'input_attrs' => [ 'min' => 60, 'max' => 100, 'step' => 1 ] ] );
    $wp_customize->add_setting( 'nn_disallow_upscale', [ 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_disallow_upscale', [ 'type' => 'checkbox', 'section' => 'nn_media', 'label' => __( 'Evitar upscaling (não ampliar além do original)', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_cover_fetchpriority', [ 'default' => 'high', 'sanitize_callback' => function( $v ){ $a = [ 'auto','high' ]; return in_array( $v, $a, true ) ? $v : 'high'; }, 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_cover_fetchpriority', [ 'type' => 'select', 'section' => 'nn_media', 'label' => __( 'Fetch Priority da capa', 'nosfirnews' ), 'choices' => [ 'auto' => __( 'Auto', 'nosfirnews' ), 'high' => __( 'High', 'nosfirnews' ) ] ] );
    $wp_customize->add_setting( 'nn_cover_eager_load', [ 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_cover_eager_load', [ 'type' => 'checkbox', 'section' => 'nn_media', 'label' => __( 'Carregar capa com eager (melhor LCP)', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_convert_webp', [ 'default' => false, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_convert_webp', [ 'type' => 'checkbox', 'section' => 'nn_media', 'label' => __( 'Converter miniaturas para WebP quando suportado', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_single_use_cover', [ 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_single_use_cover', [ 'type' => 'checkbox', 'section' => 'nn_media', 'label' => __( 'Usar tamanho dedicado de capa (single/page)', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_single_cover_width', [ 'default' => 1200, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_single_cover_width', [ 'type' => 'number', 'section' => 'nn_media', 'label' => __( 'Largura da capa (px)', 'nosfirnews' ), 'input_attrs' => [ 'min' => 600, 'max' => 2400 ] ] );
    $wp_customize->add_setting( 'nn_single_cover_height', [ 'default' => 480, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_single_cover_height', [ 'type' => 'number', 'section' => 'nn_media', 'label' => __( 'Altura da capa (px)', 'nosfirnews' ), 'input_attrs' => [ 'min' => 240, 'max' => 1200 ] ] );
    $wp_customize->add_setting( 'nn_single_cover_crop', [ 'default' => false, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_single_cover_crop', [ 'type' => 'checkbox', 'section' => 'nn_media', 'label' => __( 'Cortar a capa para preencher', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_single_fit', [ 'default' => 'auto', 'sanitize_callback' => function( $v ){ $a = [ 'cover','contain','auto' ]; return in_array( $v, $a, true ) ? $v : 'auto'; }, 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_single_fit', [ 'type' => 'select', 'section' => 'nn_media', 'label' => __( 'Ajuste da capa (single/page)', 'nosfirnews' ), 'choices' => [ 'cover' => __( 'Crop (cover)', 'nosfirnews' ), 'contain' => __( 'Contain', 'nosfirnews' ), 'auto' => __( 'Auto height', 'nosfirnews' ) ] ] );
    $wp_customize->add_setting( 'nn_single_focus_x', [ 'default' => 50, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_single_focus_x', [ 'type' => 'range', 'section' => 'nn_media', 'label' => __( 'Foco horizontal (%)', 'nosfirnews' ), 'input_attrs' => [ 'min' => 0, 'max' => 100, 'step' => 1 ] ] );
    $wp_customize->add_setting( 'nn_single_focus_y', [ 'default' => 50, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_single_focus_y', [ 'type' => 'range', 'section' => 'nn_media', 'label' => __( 'Foco vertical (%)', 'nosfirnews' ), 'input_attrs' => [ 'min' => 0, 'max' => 100, 'step' => 1 ] ] );
    $wp_customize->add_setting( 'nn_single_overlay_enable', [ 'default' => false, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_single_overlay_enable', [ 'type' => 'checkbox', 'section' => 'nn_media', 'label' => __( 'Ativar overlay em capa', 'nosfirnews' ) ] );
    $wp_customize->add_setting( 'nn_single_overlay_top', [ 'default' => 0.15, 'sanitize_callback' => 'floatval', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_single_overlay_top', [ 'type' => 'number', 'section' => 'nn_media', 'label' => __( 'Overlay topo (opacidade 0–1)', 'nosfirnews' ), 'input_attrs' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ] );
    $wp_customize->add_setting( 'nn_single_overlay_bottom', [ 'default' => 0.35, 'sanitize_callback' => 'floatval', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_single_overlay_bottom', [ 'type' => 'number', 'section' => 'nn_media', 'label' => __( 'Overlay base (opacidade 0–1)', 'nosfirnews' ), 'input_attrs' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ] );
    $wp_customize->add_section( 'nn_theme', [ 'title' => __( 'Theme', 'nosfirnews' ), 'panel' => 'nosfirnews_site_options' ] );
    $wp_customize->add_setting( 'nn_enable_bootstrap', [ 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_enable_bootstrap', [ 'type' => 'checkbox', 'section' => 'nn_theme', 'label' => __( 'Enable Bootstrap 5', 'nosfirnews' ) ] );
} );

add_filter( 'nosfirnews_container_class_filter', function( $classes, $context ) {
    $boxed = (bool) get_theme_mod( 'nn_boxed_layout', false );
    return $boxed ? $classes . ' boxed' : $classes;
}, 10, 2 );

add_filter( 'nosfirnews_posts_wrapper_class', function( $classes, $context ) {
    $layout = get_theme_mod( 'nn_home_layout', 'grid' );
    $cols = max( 1, (int) get_theme_mod( 'nn_home_columns', 3 ) );
    if ( is_home() || is_front_page() ) {
        if ( $layout === 'grid' || $layout === 'masonry' ) { $classes[] = 'nn-grid'; $classes[] = 'nn-cols-' . $cols; }
        if ( $layout === 'masonry' ) { $classes[] = 'nn-masonry'; }
    }
    return $classes;
}, 10, 2 );

add_filter( 'excerpt_length', function( $length ) { return (int) get_theme_mod( 'nn_excerpt_length', 25 ); } );

add_action( 'nosfirnews_page_header', function( $ctx ) {
    if ( ( is_home() || is_front_page() ) && (bool) get_theme_mod( 'nn_home_show_hero', false ) ) {
        echo '<section class="nn-hero container"><h1>' . esc_html( get_theme_mod( 'nn_home_hero_title', __( 'Bem-vindo', 'nosfirnews' ) ) ) . '</h1>';
        $sub = get_theme_mod( 'nn_home_hero_subtitle', '' ); if ( $sub ) { echo '<p class="nn-hero-sub">' . esc_html( $sub ) . '</p>'; }
        echo '</section>';
    }
}, 10, 1 );

add_action( 'wp_footer', function(){
    $maxw = (int) get_theme_mod( 'nn_container_max_width', 1200 );
    $primary = get_theme_mod( 'nn_primary_color', '#0073aa' );
    $ff = get_theme_mod( 'nn_base_font_family', 'system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial' );
    $fs = (int) get_theme_mod( 'nn_base_font_size', 16 );
    $hs = floatval( get_theme_mod( 'nn_headings_scale', 1.0 ) );
    $hlh = (int) get_theme_mod( 'nn_header_logo_height', 48 );
    $flh = (int) get_theme_mod( 'nn_footer_logo_height', 50 );
    $tw = (int) get_theme_mod( 'nn_thumb_width', 320 );
    $th = (int) get_theme_mod( 'nn_thumb_height', 180 );
    $tbr = (int) get_theme_mod( 'nn_thumb_border_radius', 8 );
    $tsh = get_theme_mod( 'nn_thumb_shadow', 'none' );
    $tfl = get_theme_mod( 'nn_thumb_filter', 'none' );
    $hover = get_theme_mod( 'nn_thumb_hover_effect', 'none' );
    $shadow_css = '0 0 0 rgba(0,0,0,0)';
    if ( $tsh === 'soft' ) { $shadow_css = '0 4px 12px rgba(0,0,0,.08)'; }
    if ( $tsh === 'medium' ) { $shadow_css = '0 8px 24px rgba(0,0,0,.12)'; }
    if ( $tsh === 'hard' ) { $shadow_css = '0 12px 32px rgba(0,0,0,.18)'; }
    $filter_css = 'none';
    if ( $tfl === 'grayscale' ) { $filter_css = 'grayscale(1)'; }
    if ( $tfl === 'sepia' ) { $filter_css = 'sepia(1)'; }
    if ( $tfl === 'saturate' ) { $filter_css = 'saturate(1.6)'; }
    if ( $tfl === 'contrast' ) { $filter_css = 'contrast(1.2)'; }
    if ( $tfl === 'brightness' ) { $filter_css = 'brightness(1.1)'; }
    if ( $tfl === 'blur' ) { $filter_css = 'blur(2px)'; }
    ?>
    <style>
    .container { max-width: <?php echo esc_html( $maxw ); ?>px; }
    body { font-family: <?php echo esc_html( $ff ); ?>; font-size: <?php echo esc_html( $fs ); ?>px; }
    h1 { font-size: calc(2.2rem * <?php echo esc_html( $hs ); ?>); }
    h2 { font-size: calc(1.8rem * <?php echo esc_html( $hs ); ?>); }
    a { color: <?php echo esc_html( $primary ); ?>; }
    .site-header .custom-logo { max-height: <?php echo esc_html( $hlh ); ?>px; height: auto; width: auto; }
    .nn-grid { display: grid; gap: 20px; }
    .nn-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .nn-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .nn-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .boxed { padding-left: 15px; padding-right: 15px; }
    .nn-hero { margin: 20px 0; }
    .site-nav { display:flex; align-items:center; }
    .site-nav ul { list-style:none; margin:0; padding:0; }
    .site-nav .nav-menu { display:flex; gap:16px; align-items:center; }
    .site-nav .menu > ul { display:flex; gap:16px; align-items:center; }
    .site-nav .nav-menu > li > a, .site-nav .menu > ul > li > a { display:block; padding:8px 10px; text-decoration:none; border-radius:6px; }
    .site-nav .nav-menu > li > a:hover, .site-nav .menu > ul > li > a:hover { background: rgba(0,0,0,.05); }
    .site-nav .nav-menu > li.current-menu-item > a, .site-nav .nav-menu > li.current_page_item > a,
    .site-nav .menu > ul > li.current-menu-item > a, .site-nav .menu > ul > li.current_page_item > a { background: rgba(0,0,0,.08); font-weight:600; }
    .site-nav .nav-menu li.menu-item-has-children { position:relative; }
    .site-nav .nav-menu li .sub-menu { position:absolute; left:0; top:100%; min-width:200px; background:#fff; box-shadow: 0 6px 18px rgba(0,0,0,.08); border:1px solid rgba(0,0,0,.06); padding:8px 0; display:none; z-index:10; }
    .site-nav .nav-menu li .sub-menu.open { display:block; }
    .site-nav .nav-menu li .sub-menu li a { padding:8px 12px; display:block; }
    .site-nav .nav-menu .submenu-toggle { margin-left:6px; background:none; border:0; color: inherit; cursor:pointer; }
    .menu-item-search form { display:flex; align-items:center; gap:8px; }
    .menu-item-search input[type="search"] { height:32px; padding:0 10px; border-radius:6px; border:1px solid rgba(0,0,0,.2); }
    .menu-item-social a { opacity:.85; }
    .menu-item-social a:hover { opacity:1; }
    .footer-brand { display:flex; align-items:flex-start; gap:16px; margin:20px 0; }
    .footer-logo img, .site-footer .custom-logo { max-height: <?php echo esc_html( $flh ); ?>px; height:auto; width:auto; }
    .footer-desc { color:#5e5e5e; }
    .footer-links { list-style:none; margin:10px 0; padding:0; display:flex; flex-wrap:wrap; gap:10px; }
    .footer-links a { text-decoration:none; padding:4px 6px; border-radius:4px; }
    .footer-links a:hover { background: rgba(0,0,0,.05); }
    .footer-social { list-style:none; margin:10px 0; padding:0; display:flex; gap:10px; }
    .footer-social a { opacity:.85; }
    .footer-social a:hover { opacity:1; }
    .entry-thumb { width: var(--nn-thumb-w, <?php echo esc_html( $tw ); ?>px); overflow:hidden; border-radius: var(--nn-thumb-br, <?php echo esc_html( $tbr ); ?>px); box-shadow: var(--nn-thumb-shadow, <?php echo esc_html( $shadow_css ); ?>); }
    .single-featured, .page-featured { position:relative; overflow:hidden; border-radius: var(--nn-thumb-br, <?php echo esc_html( $tbr ); ?>px); box-shadow: var(--nn-thumb-shadow, <?php echo esc_html( $shadow_css ); ?>); }
    .entry-thumb img { width:100%; display:block; filter: var(--nn-thumb-filter, <?php echo esc_html( $filter_css ); ?>); }
    .single-featured img, .page-featured img { width:100%; height:auto; display:block; filter: var(--nn-thumb-filter, <?php echo esc_html( $filter_css ); ?>); object-position: calc(var(--nn-focus-x, <?php echo esc_html( (int) get_theme_mod('nn_single_focus_x', 50) ); ?>) * 1%) calc(var(--nn-focus-y, <?php echo esc_html( (int) get_theme_mod('nn_single_focus_y', 50) ); ?>) * 1%); }
    .thumb-fit-cover .entry-thumb { height: var(--nn-thumb-h, <?php echo esc_html( $th ); ?>px); }
    .thumb-fit-cover .entry-thumb img { height:100%; object-fit: cover; }
    .thumb-fit-contain .entry-thumb { height: var(--nn-thumb-h, <?php echo esc_html( $th ); ?>px); }
    .thumb-fit-contain .entry-thumb img { height:100%; object-fit: contain; background:#fff; }
    .thumb-fit-auto .entry-thumb { height: auto; }
    .thumb-fit-auto .entry-thumb img { height:auto; object-fit: contain; }
    .thumb-fit-cover .single-featured, .thumb-fit-contain .single-featured,
    .thumb-fit-cover .page-featured, .thumb-fit-contain .page-featured { height: var(--nn-single-h, <?php echo esc_html( (int) get_theme_mod('nn_single_cover_height', 480) ); ?>px); }
    .thumb-fit-cover .single-featured img, .thumb-fit-cover .page-featured img { height:100%; object-fit: cover; }
    .thumb-fit-contain .single-featured img, .thumb-fit-contain .page-featured img { height:100%; object-fit: contain; background:#fff; }
    .thumb-fit-auto .single-featured, .thumb-fit-auto .page-featured { height: auto; }
    .thumb-fit-auto .single-featured img, .thumb-fit-auto .page-featured img { height:auto; object-fit: contain; }
    .single-featured::before, .page-featured::before { content:""; position:absolute; inset:0; background: linear-gradient(to bottom, rgba(0,0,0,var(--nn-ov-top, <?php echo esc_html( max(0, min(1, floatval(get_theme_mod('nn_single_overlay_top', 0.15))) ) ); ?>)) 0%, rgba(0,0,0,var(--nn-ov-bot, <?php echo esc_html( max(0, min(1, floatval(get_theme_mod('nn_single_overlay_bottom', 0.35))) ) ); ?>)) 100%); pointer-events:none; opacity: var(--nn-ov-en, <?php echo (bool) get_theme_mod('nn_single_overlay_enable', false) ? '1' : '0'; ?>); }
    .thumb-effect-zoom .entry-thumb:hover, .thumb-effect-zoom .single-featured:hover, .thumb-effect-zoom .page-featured:hover { transform: scale(1.03); }
    .thumb-effect-lift .entry-thumb:hover, .thumb-effect-lift .single-featured:hover, .thumb-effect-lift .page-featured:hover { box-shadow: 0 16px 38px rgba(0,0,0,.16); }
    .entry-thumb, .single-featured, .page-featured { transition: transform .2s ease, box-shadow .2s ease; }
    .nn-read-more { display:inline-block; margin-top:10px; text-decoration:none; }
    .nn-read-more.primary { background: <?php echo esc_html( $primary ); ?>; color:#fff; padding:8px 12px; border-radius:6px; }
    .nn-read-more.link { color: <?php echo esc_html( $primary ); ?>; }
    .single { max-width: <?php echo esc_html( $maxw ); ?>px; }
    .single-header { margin-bottom: 16px; }
    .entry-title { margin: 0 0 8px; line-height: 1.2; }
    .post-meta { display:flex; gap:12px; color:#666; font-size:.95rem; }
    .single-featured { margin: 16px 0; }
    .entry-content { font-size: 1.05rem; line-height: 1.75; }
    .entry-content > p { margin: 0 0 1.2em; }
    .entry-content img { max-width:100%; height:auto; }
    .single .entry-content { max-width: 70ch; margin-left: auto; margin-right: auto; }
    .entry-content h1, .entry-content h2, .entry-content h3, .entry-content h4, .entry-content h5, .entry-content h6 { line-height: 1.25; margin: 1.6em 0 .8em; }
    .entry-content a { text-decoration: underline; text-underline-offset: 2px; }
    .entry-content blockquote { border-left: 3px solid <?php echo esc_html( $primary ); ?>; padding-left: 1rem; margin: 1.2em 0; color: #444; font-style: italic; }
    .entry-content pre { background: #f6f6f6; border-radius: 8px; padding: 12px; overflow: auto; }
    .entry-content code { background: #f6f6f6; border-radius: 6px; padding: 2px 6px; }
    .entry-content table { width: 100%; border-collapse: collapse; margin: 1.2em 0; }
    .entry-content th, .entry-content td { padding: 8px 10px; border-bottom: 1px solid rgba(0,0,0,.08); }
    .entry-content thead th { border-bottom: 2px solid rgba(0,0,0,.12); }
    .entry-content ul, .entry-content ol { margin: 0 0 1.2em 1.2em; }
    .entry-content li { margin: .4em 0; }
    .entry-content figure { margin: 1.2em 0; }
    .entry-content figcaption { font-size: .9rem; color: #666; text-align: center; margin-top: .5em; }
    .post-tags a { display:inline-block; padding:6px 10px; margin:4px; border-radius:16px; background: rgba(0,0,0,.05); text-decoration:none; font-size:.9rem; }
    .post-tags a:hover { background: rgba(0,0,0,.1); }
    .post-nav { display:grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 20px 0; }
    .post-nav .prev a, .post-nav .next a { display:block; padding:10px; border-radius:8px; background: rgba(0,0,0,.04); text-decoration:none; }
    .post-nav .prev a:hover, .post-nav .next a:hover { background: rgba(0,0,0,.08); }
    .author-box { display:flex; gap:12px; padding:14px; border:1px solid rgba(0,0,0,.08); border-radius:10px; background:#fff; }
    .author-avatar { border-radius:50%; }
    .author-name { margin:0 0 6px; font-size:1rem; }
    .author-bio { margin:0; color:#555; }
    .comments-area { margin-top: 24px; }
    .comment-list { list-style:none; margin:0; padding:0; }
    .comment-list .comment { margin-bottom:14px; }
    .comment-body { padding:12px; border:1px solid rgba(0,0,0,.08); border-radius:10px; background:#fff; }
    .comment-meta { font-size:.9rem; color:#666; margin-bottom:8px; display:flex; gap:8px; align-items:center; }
    .comment-author .avatar { border-radius:50%; }
    .comment-content { line-height:1.6; }
    .reply a { text-decoration:none; font-size:.9rem; color: <?php echo esc_html( $primary ); ?>; }
    .comment-respond { margin-top: 20px; padding:14px; border:1px solid rgba(0,0,0,.08); border-radius:10px; background:#fff; }
    .comment-respond input[type="text"], .comment-respond input[type="email"], .comment-respond textarea { width:100%; border:1px solid rgba(0,0,0,.2); border-radius:8px; padding:8px 10px; }
    .comment-respond .submit { background: <?php echo esc_html( $primary ); ?>; color:#fff; border:0; border-radius:8px; padding:8px 14px; cursor:pointer; }
    .post-card { background:#fff; border:1px solid rgba(0,0,0,.08); border-radius:10px; padding:16px; }
    .post-card .entry-title { font-size: 1.4rem; margin: 0 0 .5rem; }
    .post-card .entry-title a { color: inherit; text-decoration: none; }
    .post-card .entry-title a:hover { text-decoration: underline; text-underline-offset: 2px; }
    .post-card .entry-summary { color:#555; line-height:1.7; }
    .header-inner { display:grid; grid-template-columns: 1fr 1fr 1fr; align-items:center; gap:20px; }
    .branding-pos-left { grid-column: 1; justify-self: start; }
    .branding-pos-center { grid-column: 2; justify-self: center; }
    .branding-pos-right { grid-column: 3; justify-self: end; }
    .nav-pos-left { grid-column: 1; justify-self: start; }
    .nav-pos-center { grid-column: 2; justify-self: center; }
    .nav-pos-right { grid-column: 3; justify-self: end; }
    .header-inner.toggle-pos-left .nav-toggle { grid-column: 1; justify-self: start; }
    .header-inner.toggle-pos-center .nav-toggle { grid-column: 2; justify-self: center; }
    .header-inner.toggle-pos-right .nav-toggle { grid-column: 3; justify-self: end; }
    .nav-toggle { display:none; margin-right:8px; font-size:22px; line-height:1; border:0; background:none; cursor:pointer; order:-1; }
    .nn-mobile-drawer { display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:999; }
    .nn-mobile-drawer.open { display:block; }
    .nn-mobile-drawer nav, .nn-mobile-drawer ul { list-style:none; margin:0; padding:0; }
    .nn-mobile-drawer .mobile-nav-menu { position:absolute; top:0; right:0; width:80%; max-width:360px; height:100%; background:#fff; box-shadow:-6px 0 18px rgba(0,0,0,.12); padding:20px; overflow:auto; transform: translateX(100%); transition: transform .25s ease; }
    .nn-mobile-drawer.open .mobile-nav-menu { transform: translateX(0); }
    .nn-mobile-drawer .mobile-nav li > a { display:block; padding:12px 8px; border-bottom:1px solid rgba(0,0,0,.06); }
    .nn-mobile-drawer .sub-menu { padding-left:12px; }
    .nn-mobile-drawer .drawer-close { position:absolute; top:10px; right:10px; background:none; border:0; font-size:24px; cursor:pointer; }
    .site-header { position: sticky; top: 0; z-index: 1000; background: #fff; }
    .site-header { transition: box-shadow .2s ease, padding .2s ease; }
    .site-header.header-scrolled { box-shadow: 0 4px 16px rgba(0,0,0,.08); }
    .nn-lock { overflow:hidden; }
    @media (max-width: <?php echo (int) get_theme_mod('nn_mobile_breakpoint', 998 ); ?>px) {
        .site-nav { display:none !important; }
        .nav-toggle { display:inline-block !important; }
    }
    </style>
    <script>
    (function(){
      var btn = document.querySelector('.nav-toggle');
      var drawer = document.getElementById('mobile-menu');
      var closeBtn = drawer ? drawer.querySelector('.drawer-close') : null;
      if (!btn || !drawer) return;
      function toggle(){ var open = drawer.classList.toggle('open'); btn.setAttribute('aria-expanded', open ? 'true' : 'false'); drawer.setAttribute('aria-hidden', open ? 'false' : 'true'); document.body.classList.toggle('nn-lock', open); }
      btn.addEventListener('click', toggle);
      drawer.addEventListener('click', function(e){ if (e.target === drawer) toggle(); });
      document.addEventListener('keydown', function(e){ if(e.key === 'Escape' && drawer.classList.contains('open')) toggle(); });
      if (closeBtn) closeBtn.addEventListener('click', toggle);
      function onScroll(){ var y = window.scrollY || document.documentElement.scrollTop; var header = document.querySelector('.site-header'); if (!header) return; header.classList.toggle('header-scrolled', y > 10); }
      window.addEventListener('scroll', onScroll, { passive: true }); onScroll();
    })();
    </script>
    <?php
} );

add_shortcode( 'nn_wc_carousel', function( $atts ){
    if ( ! class_exists( 'WooCommerce' ) ) return '';
    $atts = shortcode_atts( [ 'featured' => 'false', 'limit' => 12 ], $atts, 'nn_wc_carousel' );
    $items = max( 2, (int) get_theme_mod( 'nn_wc_carousel_items', 4 ) );
    $autoplay = (bool) get_theme_mod( 'nn_wc_carousel_autoplay', true );
    $args = [ 'limit' => (int) $atts['limit'], 'status' => 'publish', 'orderby' => 'date', 'order' => 'DESC' ];
    if ( $atts['featured'] === 'true' ) { $args['featured'] = true; }
    $products = function_exists( 'wc_get_products' ) ? wc_get_products( $args ) : [];
    if ( empty( $products ) ) return '';
    $out = '<div class="nn-wc-carousel" data-items="' . esc_attr( $items ) . '" data-autoplay="' . ( $autoplay ? '1' : '0' ) . '"><div class="nn-wc-track">';
    foreach ( $products as $p ) {
        $link = get_permalink( $p->get_id() );
        $img = wp_get_attachment_image( $p->get_image_id(), 'medium' );
        $title = esc_html( $p->get_name() );
        $price = wp_kses_post( $p->get_price_html() );
        $out .= '<div class="nn-wc-item"><a class="nn-wc-thumb" href="' . esc_url( $link ) . '">' . $img . '</a><div class="nn-wc-info"><a class="nn-wc-title" href="' . esc_url( $link ) . '">' . $title . '</a><div class="nn-wc-price">' . $price . '</div></div></div>';
    }
    $out .= '</div><button class="nn-wc-prev" aria-label="Anterior">‹</button><button class="nn-wc-next" aria-label="Próximo">›</button></div>';
    return $out;
} );

add_action( 'woocommerce_before_shop_loop', function(){
    if ( ! class_exists( 'WooCommerce' ) ) return;
    if ( ! (bool) get_theme_mod( 'nn_wc_enable_featured_block', true ) ) return;
    $count = max( 1, (int) get_theme_mod( 'nn_wc_featured_count', 8 ) );
    $products = function_exists( 'wc_get_products' ) ? wc_get_products( [ 'featured' => true, 'limit' => $count ] ) : [];
    if ( empty( $products ) ) return;
    echo '<section class="nn-wc-featured container">';
    echo '<h2>' . esc_html__( 'Produtos em destaque', 'nosfirnews' ) . '</h2>';
    echo '<div class="nn-wc-featured-grid">';
    foreach ( $products as $p ) {
        $link = get_permalink( $p->get_id() ); $img = wp_get_attachment_image( $p->get_image_id(), 'medium' ); $title = esc_html( $p->get_name() ); $price = wp_kses_post( $p->get_price_html() );
        echo '<div class="nn-wc-card"><a href="' . esc_url( $link ) . '" class="nn-wc-thumb">' . $img . '</a><a href="' . esc_url( $link ) . '" class="nn-wc-title">' . $title . '</a><div class="nn-wc-price">' . $price . '</div></div>';
    }
    echo '</div></section>';
} );

add_action( 'wp_footer', function(){
    if ( ! class_exists( 'WooCommerce' ) ) return;
    ?>
    <style>
    .nn-wc-carousel { position: relative; overflow: hidden; }
    .nn-wc-track { display: flex; gap: 16px; transition: transform .25s ease; will-change: transform; }
    .nn-wc-item { min-width: calc(100% / var(--nn-wc-items, 4)); background:#fff; border:1px solid rgba(0,0,0,.08); border-radius:8px; overflow:hidden; }
    .nn-wc-thumb img { width:100%; height:auto; display:block; }
    .nn-wc-info { padding:10px; }
    .nn-wc-title { display:block; text-decoration:none; color:inherit; margin-bottom:6px; }
    .nn-wc-price { font-weight:600; }
    .nn-wc-prev, .nn-wc-next { position:absolute; top:50%; transform:translateY(-50%); border:0; background:#fff; box-shadow:0 2px 6px rgba(0,0,0,.12); width:32px; height:32px; border-radius:16px; cursor:pointer; }
    .nn-wc-prev { left:8px; }
    .nn-wc-next { right:8px; }
    .nn-wc-featured { margin: 20px 0; }
    .nn-wc-featured-grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap:16px; }
    .nn-wc-card { background:#fff; border:1px solid rgba(0,0,0,.08); border-radius:8px; overflow:hidden; padding-bottom:10px; }
    .nn-wc-popup { display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:9999; }
    .nn-wc-popup.open { display:block; }
    .nn-wc-popup .nn-wc-popup-inner { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:90%; max-width:520px; background:#fff; border-radius:10px; box-shadow:0 10px 30px rgba(0,0,0,.2); padding:20px; }
    .nn-wc-popup .nn-wc-popup-close { position:absolute; top:10px; right:10px; background:none; border:0; font-size:24px; cursor:pointer; }
    </style>
    <script>
    (function(){
      document.querySelectorAll('.nn-wc-carousel').forEach(function(car){
        var items = parseInt(car.dataset.items||'4',10); car.style.setProperty('--nn-wc-items', items);
        var track = car.querySelector('.nn-wc-track'); var idx = 0; var autoplay = car.dataset.autoplay==='1';
        function update(){ var w = car.clientWidth; var step = w / items; track.style.transform = 'translateX(' + (-idx*step) + 'px)'; }
        function next(){ idx = Math.min(idx+1, Math.max(0, track.children.length - items)); update(); }
        function prev(){ idx = Math.max(idx-1, 0); update(); }
        car.querySelector('.nn-wc-next')?.addEventListener('click', next);
        car.querySelector('.nn-wc-prev')?.addEventListener('click', prev);
        window.addEventListener('resize', update); update();
        var t; function start(){ if(!autoplay) return; clearInterval(t); t = setInterval(function(){ if(idx >= Math.max(0, track.children.length - items)) idx = 0; else idx++; update(); }, 4000); }
        start();
      });
      var enable = <?php echo json_encode( (bool) get_theme_mod('nn_wc_popup_enable', false) ); ?>;
      if(enable){
        var scope = <?php echo json_encode( get_theme_mod('nn_wc_popup_scope','shop') ); ?>;
        var delay = <?php echo (int) get_theme_mod('nn_wc_popup_delay', 3 ); ?> * 1000;
        var isShop = document.body.classList.contains('woocommerce') || document.body.classList.contains('archive') || document.body.classList.contains('tax-product_cat');
        var isProduct = document.body.classList.contains('single-product');
        var show = (scope==='all') || (scope==='shop' && isShop) || (scope==='product' && isProduct);
        if(show){
          var pop = document.getElementById('nn-wc-popup');
          if(pop){ setTimeout(function(){ pop.classList.add('open'); }, delay); var close = pop.querySelector('.nn-wc-popup-close'); if(close) close.addEventListener('click', function(){ pop.classList.remove('open'); }); pop.addEventListener('click', function(e){ if(e.target===pop) pop.classList.remove('open'); }); }
        }
      }
    })();
    </script>
    <?php
} );

add_action( 'wp_footer', function(){
    if ( ! class_exists( 'WooCommerce' ) ) return;
    $enabled = (bool) get_theme_mod( 'nn_wc_popup_enable', false );
    if ( ! $enabled ) return;
    $content = get_theme_mod( 'nn_wc_popup_content', '' );
    if ( ! $content ) return;
    echo '<div id="nn-wc-popup" class="nn-wc-popup"><div class="nn-wc-popup-inner">';
    echo '<button class="nn-wc-popup-close" aria-label="Fechar">&times;</button>';
    echo wp_kses_post( $content );
    echo '</div></div>';
} );
function nosfirnews_social_menu_items_markup() {
    $locations = get_nav_menu_locations();
    if ( empty( $locations['social'] ) ) return '';
    $items = wp_get_nav_menu_items( $locations['social'] );
    if ( empty( $items ) ) return '';
    $out = '';
    foreach ( $items as $item ) {
        $label = trim( strtolower( $item->title ) );
        $service = 'link';
        foreach ( [ 'facebook','twitter','instagram','linkedin','youtube','tiktok','github','whatsapp','telegram' ] as $s ) {
            if ( strpos( $label, $s ) !== false || strpos( strtolower( $item->url ), $s ) !== false ) { $service = $s; break; }
        }
        $out .= '<li class="menu-item menu-item-social"><a class="social-link social-' . esc_attr( $service ) . '" href="' . esc_url( $item->url ) . '" target="_blank" rel="noopener">' . esc_html( ucfirst( $service ) ) . '</a></li>';
    }
    return $out;
}

add_filter( 'wp_nav_menu_items', function( $items, $args ) {
    $append_to = get_theme_mod( 'nosfirnews_menu_social_append_to', 'primary' );
    $enable_search = (bool) get_theme_mod( 'nosfirnews_enable_menu_search', false );
    $enable_social = (bool) get_theme_mod( 'nosfirnews_enable_menu_social', false );
    if ( isset( $args->theme_location ) && $args->theme_location === $append_to ) {
        $append = '';
        if ( $enable_search ) { $append .= '<li class="menu-item menu-item-search">' . get_search_form( false ) . '</li>'; }
        if ( $enable_social ) { $append .= nosfirnews_social_menu_items_markup(); }
        if ( $append ) { $items .= $append; }
    }
    return $items;
}, 10, 2 );

add_action( 'wp_footer', function() {
    ?>
    <style>
    .nav-menu li { position: relative; }
    .nav-menu .submenu-toggle { background: none; border: 0; cursor: pointer; padding: 0 6px; }
    .nav-menu .sub-menu { display: none; }
    .nav-menu .sub-menu.open { display: block; }
    .menu-item-social a { display: inline-block; padding: 0 6px; }
    @media (min-width: 961px) { .nav-menu .sub-menu { display: block; } }
    </style>
    <script>
    (function(){
        function init(){
            document.querySelectorAll('.nav-menu li.menu-item-has-children').forEach(function(li){
                var link = li.querySelector(':scope > a');
                var btn = li.querySelector(':scope > button.submenu-toggle');
                if(!btn){
                    btn = document.createElement('button');
                    btn.className = 'submenu-toggle';
                    btn.setAttribute('aria-expanded','false');
                    btn.innerHTML = '\u25BC';
                    link && link.parentNode.insertBefore(btn, link.nextSibling);
                }
                btn.addEventListener('click', function(){
                    var sub = li.querySelector(':scope > .sub-menu');
                    if(!sub) return;
                    var isOpen = sub.classList.contains('open');
                    sub.classList.toggle('open');
                    btn.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                });
            });
        }
        if(document.readyState==='loading'){ document.addEventListener('DOMContentLoaded', init); } else { init(); }
    })();
    </script>
    <?php
} );

require_once get_template_directory() . '/header-footer-grid/loader.php';
\NosfirNews\HeaderFooterGrid\load();

// Core
$core_loader = get_template_directory() . '/inc/core/core_loader.php';
if ( file_exists( $core_loader ) ) require_once $core_loader;

// Customizer
$customizer_loader = get_template_directory() . '/inc/customizer/loader.php';
if ( file_exists( $customizer_loader ) ) require_once $customizer_loader;

// Admin pages
$admin_dashboard = get_template_directory() . '/inc/admin/dashboard/main.php';
if ( file_exists( $admin_dashboard ) ) require_once $admin_dashboard;
$admin_changelog = get_template_directory() . '/inc/admin/dashboard/changelog_handler.php';
if ( file_exists( $admin_changelog ) ) require_once $admin_changelog;
$admin_plugin_helper = get_template_directory() . '/inc/admin/dashboard/plugin_helper.php';
if ( file_exists( $admin_plugin_helper ) ) require_once $admin_plugin_helper;
$admin_troubleshoot = get_template_directory() . '/inc/admin/troubleshoot/main.php';
if ( file_exists( $admin_troubleshoot ) ) require_once $admin_troubleshoot;
$admin_upsell = get_template_directory() . '/inc/admin/hooks_upsells.php';
if ( file_exists( $admin_upsell ) ) require_once $admin_upsell;

function nosfirnews_widgets_init() {
    register_sidebar( [
        'name' => __( 'Main Sidebar', 'nosfirnews' ),
        'id'   => 'sidebar-1',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ] );
    register_sidebar( [
        'name' => __( 'Shop Sidebar', 'nosfirnews' ),
        'id'   => 'sidebar-shop',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ] );
    register_sidebar( [
        'name' => __( 'Left Sidebar', 'nosfirnews' ),
        'id'   => 'sidebar-left',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ] );
    register_sidebar( [
        'name' => __( 'Right Sidebar', 'nosfirnews' ),
        'id'   => 'sidebar-right',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ] );
    register_sidebar( [
        'name' => __( 'Header Widgets', 'nosfirnews' ),
        'id'   => 'header-widgets',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ] );
    register_sidebar( [
        'name' => __( 'Footer Column 1', 'nosfirnews' ),
        'id'   => 'footer-1',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ] );
    register_sidebar( [
        'name' => __( 'Footer Column 2', 'nosfirnews' ),
        'id'   => 'footer-2',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ] );
    register_sidebar( [
        'name' => __( 'Footer Column 3', 'nosfirnews' ),
        'id'   => 'footer-3',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ] );
    register_sidebar( [
        'name' => __( 'Footer Column 4', 'nosfirnews' ),
        'id'   => 'footer-4',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ] );
    register_sidebar( [
        'name' => __( 'Bottom Widgets', 'nosfirnews' ),
        'id'   => 'bottom-widgets',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ] );
}
add_action( 'widgets_init', 'nosfirnews_widgets_init' );

add_action( 'nosfirnews_do_sidebar', function( $context, $position ) {
    $pos = get_theme_mod( 'nosfirnews_sidebar_position', 'right' );
    if ( $pos === 'none' ) { return; }
    if ( $pos === 'left' && $position !== 'left' ) { return; }
    if ( $pos === 'right' && $position !== 'right' ) { return; }
    $id = $position === 'left' ? 'sidebar-left' : 'sidebar-right';
    if ( ! is_active_sidebar( $id ) ) { return; }
    $handle = get_theme_mod( 'nosfirnews_sidebar_resizable', false ) ? '<div class="nn-resize-handle" data-side="' . esc_attr( $position ) . '"></div>' : '';
    echo '<aside class="nn-sidebar nn-sidebar-' . esc_attr( $position ) . ' col">' . $handle;
    dynamic_sidebar( $id );
    echo '</aside>';
}, 10, 2 );

add_action( 'nosfirnews_after_posts_loop', function(){
    if ( is_active_sidebar( 'bottom-widgets' ) ) {
        echo '<div class="nn-bottom-widgets container">';
        dynamic_sidebar( 'bottom-widgets' );
        echo '</div>';
    }
} );

add_action( 'customize_register', function( $wp_customize ) {
    $wp_customize->add_section( 'nosfirnews_sidebar_options', [ 'title' => __( 'Sidebar Options', 'nosfirnews' ), 'priority' => 155 ] );
    $wp_customize->add_setting( 'nosfirnews_sidebar_position', [ 'default' => 'right', 'sanitize_callback' => function( $v ){ $allowed = [ 'none','left','right','both' ]; return in_array( $v, $allowed, true ) ? $v : 'right'; }, 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nosfirnews_sidebar_position', [ 'type' => 'select', 'section' => 'nosfirnews_sidebar_options', 'label' => __( 'Sidebar Position', 'nosfirnews' ), 'choices' => [ 'none' => __( 'None', 'nosfirnews' ), 'left' => __( 'Left', 'nosfirnews' ), 'right' => __( 'Right', 'nosfirnews' ), 'both' => __( 'Both', 'nosfirnews' ) ] ] );
    $wp_customize->add_setting( 'nosfirnews_sidebar_left_width', [ 'default' => 25, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nosfirnews_sidebar_left_width', [ 'type' => 'range', 'section' => 'nosfirnews_sidebar_options', 'label' => __( 'Left Sidebar Width (%)', 'nosfirnews' ), 'input_attrs' => [ 'min' => 10, 'max' => 40, 'step' => 1 ] ] );
    $wp_customize->add_setting( 'nosfirnews_sidebar_right_width', [ 'default' => 25, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nosfirnews_sidebar_right_width', [ 'type' => 'range', 'section' => 'nosfirnews_sidebar_options', 'label' => __( 'Right Sidebar Width (%)', 'nosfirnews' ), 'input_attrs' => [ 'min' => 10, 'max' => 40, 'step' => 1 ] ] );
    $wp_customize->add_setting( 'nosfirnews_sidebar_resizable', [ 'default' => false, 'sanitize_callback' => 'rest_sanitize_boolean', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nosfirnews_sidebar_resizable', [ 'type' => 'checkbox', 'section' => 'nosfirnews_sidebar_options', 'label' => __( 'Enable drag resize for sidebars', 'nosfirnews' ) ] );
} );

add_action( 'widgets_init', function(){
    if ( class_exists( 'WP_Widget' ) ) {
        class NosfirNews_Social_Menu_Widget extends WP_Widget {
            public function __construct(){ parent::__construct( 'nn_social_menu', __( 'NosfirNews Social Menu', 'nosfirnews' ) ); }
            public function form( $instance ){ $t = isset( $instance['title'] ) ? $instance['title'] : ''; echo '<p><label>' . esc_html__( 'Title', 'nosfirnews' ) . '</label><input class="widefat" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" type="text" value="' . esc_attr( $t ) . '" /></p>'; }
            public function update( $new_instance, $old_instance ){ $old_instance['title'] = sanitize_text_field( $new_instance['title'] ); return $old_instance; }
            public function widget( $args, $instance ){ echo $args['before_widget']; if ( ! empty( $instance['title'] ) ) echo $args['before_title'] . esc_html( $instance['title'] ) . $args['after_title']; wp_nav_menu( [ 'theme_location' => 'social', 'container' => 'ul', 'menu_class' => 'nn-social-widget' ] ); echo $args['after_widget']; }
        }
        register_widget( 'NosfirNews_Social_Menu_Widget' );
    }
} );

add_action( 'add_meta_boxes', function(){
    foreach ( [ 'post', 'page' ] as $pt ) {
        add_meta_box( 'nn_media_options', __( 'NosfirNews: Media Options', 'nosfirnews' ), function( $post ){
            wp_nonce_field( 'nn_media_opts', 'nn_media_opts_nonce' );
            $br = get_post_meta( $post->ID, 'nn_meta_thumb_border_radius', true );
            $shadow = get_post_meta( $post->ID, 'nn_meta_thumb_shadow', true );
            $filter = get_post_meta( $post->ID, 'nn_meta_thumb_filter', true );
            $hover = get_post_meta( $post->ID, 'nn_meta_thumb_hover', true );
            $fit = get_post_meta( $post->ID, 'nn_meta_thumb_fit', true );
            $hide_thumb = (bool) get_post_meta( $post->ID, 'nn_meta_hide_thumb', false );
            $hide_ex = (bool) get_post_meta( $post->ID, 'nn_meta_hide_excerpt', false );
            $hide_rm = (bool) get_post_meta( $post->ID, 'nn_meta_hide_read_more', false );
            $rm_text = get_post_meta( $post->ID, 'nn_meta_read_more_text', true );
            echo '<p><label>' . esc_html__( 'Thumb border radius (px)', 'nosfirnews' ) . '</label><input type="number" name="nn_meta_thumb_border_radius" value="' . esc_attr( $br ) . '" min="0" max="50" class="widefat" /></p>';
            echo '<p><label>' . esc_html__( 'Thumb shadow', 'nosfirnews' ) . '</label><select name="nn_meta_thumb_shadow" class="widefat">';
            foreach ( [ 'default' => __( 'Use global', 'nosfirnews' ), 'none' => __( 'None', 'nosfirnews' ), 'soft' => __( 'Soft', 'nosfirnews' ), 'medium' => __( 'Medium', 'nosfirnews' ), 'hard' => __( 'Hard', 'nosfirnews' ) ] as $k => $lbl ) { $sel = ( $shadow === $k ) ? ' selected' : ''; echo '<option value="' . esc_attr( $k ) . '"' . $sel . '>' . esc_html( $lbl ) . '</option>'; } echo '</select></p>';
            echo '<p><label>' . esc_html__( 'Thumb filter', 'nosfirnews' ) . '</label><select name="nn_meta_thumb_filter" class="widefat">';
            foreach ( [ 'default' => __( 'Use global', 'nosfirnews' ), 'none' => __( 'None', 'nosfirnews' ), 'grayscale' => __( 'Grayscale', 'nosfirnews' ), 'sepia' => __( 'Sepia', 'nosfirnews' ), 'saturate' => __( 'Saturate', 'nosfirnews' ), 'contrast' => __( 'Contrast', 'nosfirnews' ), 'brightness' => __( 'Brightness', 'nosfirnews' ), 'blur' => __( 'Blur', 'nosfirnews' ) ] as $k => $lbl ) { $sel = ( $filter === $k ) ? ' selected' : ''; echo '<option value="' . esc_attr( $k ) . '"' . $sel . '>' . esc_html( $lbl ) . '</option>'; } echo '</select></p>';
            echo '<p><label>' . esc_html__( 'Hover effect', 'nosfirnews' ) . '</label><select name="nn_meta_thumb_hover" class="widefat">';
            foreach ( [ 'default' => __( 'Use global', 'nosfirnews' ), 'none' => __( 'None', 'nosfirnews' ), 'zoom' => __( 'Zoom', 'nosfirnews' ), 'lift' => __( 'Lift', 'nosfirnews' ) ] as $k => $lbl ) { $sel = ( $hover === $k ) ? ' selected' : ''; echo '<option value="' . esc_attr( $k ) . '"' . $sel . '>' . esc_html( $lbl ) . '</option>'; } echo '</select></p>';
            echo '<p><label>' . esc_html__( 'Thumb fit mode', 'nosfirnews' ) . '</label><select name="nn_meta_thumb_fit" class="widefat">';
            foreach ( [ 'default' => __( 'Use global', 'nosfirnews' ), 'cover' => __( 'Crop (cover)', 'nosfirnews' ), 'contain' => __( 'Contain without crop', 'nosfirnews' ), 'auto' => __( 'Auto height', 'nosfirnews' ) ] as $k => $lbl ) { $sel = ( $fit === $k ) ? ' selected' : ''; echo '<option value="' . esc_attr( $k ) . '"' . $sel . '>' . esc_html( $lbl ) . '</option>'; } echo '</select></p>';
            echo '<p><label><input type="checkbox" name="nn_meta_hide_thumb" value="1"' . ( $hide_thumb ? ' checked' : '' ) . ' /> ' . esc_html__( 'Hide featured image', 'nosfirnews' ) . '</label></p>';
            echo '<p><label><input type="checkbox" name="nn_meta_hide_excerpt" value="1"' . ( $hide_ex ? ' checked' : '' ) . ' /> ' . esc_html__( 'Hide excerpt (archive)', 'nosfirnews' ) . '</label></p>';
            echo '<p><label><input type="checkbox" name="nn_meta_hide_read_more" value="1"' . ( $hide_rm ? ' checked' : '' ) . ' /> ' . esc_html__( 'Hide "Read more" (archive)', 'nosfirnews' ) . '</label></p>';
            echo '<p><label>' . esc_html__( 'Read more text (override)', 'nosfirnews' ) . '</label><input type="text" name="nn_meta_read_more_text" value="' . esc_attr( $rm_text ) . '" class="widefat" /></p>';
            $fx = get_post_meta( $post->ID, 'nn_meta_single_focus_x', true );
            $fy = get_post_meta( $post->ID, 'nn_meta_single_focus_y', true );
            $ov_en = (bool) get_post_meta( $post->ID, 'nn_meta_single_overlay_en', false );
            $ov_top = get_post_meta( $post->ID, 'nn_meta_single_overlay_top', true );
            $ov_bot = get_post_meta( $post->ID, 'nn_meta_single_overlay_bottom', true );
            echo '<hr />';
            echo '<p><strong>' . esc_html__( 'Single/Page cover options', 'nosfirnews' ) . '</strong></p>';
            echo '<p><label>' . esc_html__( 'Foco horizontal (%)', 'nosfirnews' ) . '</label><input type="range" name="nn_meta_single_focus_x" min="0" max="100" step="1" value="' . esc_attr( $fx ) . '" /></p>';
            echo '<p><label>' . esc_html__( 'Foco vertical (%)', 'nosfirnews' ) . '</label><input type="range" name="nn_meta_single_focus_y" min="0" max="100" step="1" value="' . esc_attr( $fy ) . '" /></p>';
            echo '<p><label><input type="checkbox" name="nn_meta_single_overlay_en" value="1"' . ( $ov_en ? ' checked' : '' ) . ' /> ' . esc_html__( 'Ativar overlay em capa', 'nosfirnews' ) . '</label></p>';
            echo '<p><label>' . esc_html__( 'Overlay topo (0–1)', 'nosfirnews' ) . '</label><input type="number" name="nn_meta_single_overlay_top" min="0" max="1" step="0.05" value="' . esc_attr( $ov_top ) . '" class="widefat" /></p>';
            echo '<p><label>' . esc_html__( 'Overlay base (0–1)', 'nosfirnews' ) . '</label><input type="number" name="nn_meta_single_overlay_bottom" min="0" max="1" step="0.05" value="' . esc_attr( $ov_bot ) . '" class="widefat" /></p>';
        }, $pt, 'side' );
    }
} );

add_action( 'save_post', function( $post_id ){
    if ( ! isset( $_POST['nn_media_opts_nonce'] ) || ! wp_verify_nonce( $_POST['nn_media_opts_nonce'], 'nn_media_opts' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;
    $fields = [
        'nn_meta_thumb_border_radius' => function( $v ){ return absint( $v ); },
        'nn_meta_thumb_shadow' => function( $v ){ $a = [ 'default','none','soft','medium','hard' ]; return in_array( $v, $a, true ) ? $v : 'default'; },
        'nn_meta_thumb_filter' => function( $v ){ $a = [ 'default','none','grayscale','sepia','saturate','contrast','brightness','blur' ]; return in_array( $v, $a, true ) ? $v : 'default'; },
        'nn_meta_thumb_hover' => function( $v ){ $a = [ 'default','none','zoom','lift' ]; return in_array( $v, $a, true ) ? $v : 'default'; },
        'nn_meta_hide_thumb' => function( $v ){ return $v ? '1' : ''; },
        'nn_meta_hide_excerpt' => function( $v ){ return $v ? '1' : ''; },
        'nn_meta_hide_read_more' => function( $v ){ return $v ? '1' : ''; },
        'nn_meta_read_more_text' => function( $v ){ return sanitize_text_field( $v ); },
        'nn_meta_thumb_fit' => function( $v ){ $a = [ 'default','cover','contain','auto' ]; return in_array( $v, $a, true ) ? $v : 'default'; },
        'nn_meta_single_focus_x' => function( $v ){ return max( 0, min( 100, absint( $v ) ) ); },
        'nn_meta_single_focus_y' => function( $v ){ return max( 0, min( 100, absint( $v ) ) ); },
        'nn_meta_single_overlay_en' => function( $v ){ return $v ? '1' : ''; },
        'nn_meta_single_overlay_top' => function( $v ){ $f = floatval( $v ); return ( $f >= 0 && $f <= 1 ) ? $f : ''; },
        'nn_meta_single_overlay_bottom' => function( $v ){ $f = floatval( $v ); return ( $f >= 0 && $f <= 1 ) ? $f : ''; },
    ];
    foreach ( $fields as $key => $san ) {
        $val = isset( $_POST[ $key ] ) ? $san( $_POST[ $key ] ) : '';
        if ( $val === '' ) { delete_post_meta( $post_id, $key ); } else { update_post_meta( $post_id, $key, $val ); }
    }
} );

add_action( 'wp_footer', function(){
    $left = (int) get_theme_mod( 'nosfirnews_sidebar_left_width', 25 );
    $right = (int) get_theme_mod( 'nosfirnews_sidebar_right_width', 25 );
    $pos = get_theme_mod( 'nosfirnews_sidebar_position', 'right' );
    $resizable = (bool) get_theme_mod( 'nosfirnews_sidebar_resizable', false );
    ?>
    <style>
    .archive-container .row { display: flex; gap: 20px; }
    .nn-sidebar-left { flex: 0 0 calc(var(--nn-sidebar-left, <?php echo esc_html( $left ); ?>%) - 10px); }
    .nn-sidebar-right { flex: 0 0 calc(var(--nn-sidebar-right, <?php echo esc_html( $right ); ?>%) - 10px); }
    .nn-index-posts.blog.col { flex: 1 1 auto; }
    .nn-resize-handle { width: 6px; cursor: col-resize; align-self: stretch; background: rgba(0,0,0,.1); margin: 0 4px; }
    .nn-bottom-widgets { margin-top: 30px; }
    .footer-widgets { display:grid; margin-bottom:20px; gap: var(--nn-footer-gap, 20px); }
    .footer-widgets.footer-cols-1 { grid-template-columns: 1fr; }
    .footer-widgets.footer-cols-2 { grid-template-columns: repeat(2, 1fr); }
    .footer-widgets.footer-cols-3 { grid-template-columns: repeat(3, 1fr); }
    .footer-widgets.footer-cols-4 { grid-template-columns: repeat(4, 1fr); }
    .footer-widgets.footer-align-left { justify-items: start; }
    .footer-widgets.footer-align-center { justify-items: center; }
    .footer-widgets.footer-align-right { justify-items: end; }
    .footer-widgets.footer-align-stretch { justify-items: stretch; }
    .footer-widgets.footer-align-space-between { justify-content: space-between; }
    .footer-widgets .footer-col { min-width: 0; }
    .widget { background:#fff; border:1px solid rgba(0,0,0,.08); border-radius:10px; padding:12px; margin-bottom:16px; }
    .widget-title { font-size:1rem; margin:0 0 10px; padding-bottom:6px; border-bottom:1px solid rgba(0,0,0,.06); }
    .widget ul { list-style:none; margin:0; padding:0; }
    .widget ul li { padding:6px 0; border-bottom:1px solid rgba(0,0,0,.06); }
    .widget ul li:last-child { border-bottom:0; }
    .widget a { text-decoration:none; }
    .nn-social-widget { display:flex; gap:8px; flex-wrap:wrap; padding:0; margin:0; }
    .nn-social-widget li { list-style:none; }
    .nn-social-widget a { display:inline-block; padding:6px 10px; border-radius:6px; background: rgba(0,0,0,.05); }
    .nn-social-widget a:hover { background: rgba(0,0,0,.1); }
    .comment-list .children { margin-left:20px; padding-left:10px; border-left:2px solid rgba(0,0,0,.06); }
    .comment-author .fn { font-weight:600; }
    .comment-meta time { color:#888; }
    .reply a { display:inline-block; padding:6px 10px; border-radius:16px; border:1px solid currentColor; }
    </style>
    <?php if ( $resizable ) { ?>
    <script>
    (function(){
      function clamp(v,min,max){ return Math.max(min, Math.min(max, v)); }
      function attach(side){
        document.querySelectorAll('.nn-sidebar-' + side + ' .nn-resize-handle').forEach(function(h){
          h.addEventListener('mousedown', function(e){
            e.preventDefault();
            var startX = e.clientX;
            var root = document.documentElement;
            var start = parseInt(getComputedStyle(root).getPropertyValue('--nn-sidebar-' + side)) || (side==='left'?<?php echo (int) $left; ?>:<?php echo (int) $right; ?>);
            function onMove(me){
              var dx = me.clientX - startX;
              var w = window.innerWidth || document.body.clientWidth;
              var deltaPct = (dx / w) * 100 * (side==='left'?1:-1);
              var val = clamp(start + deltaPct, 10, 40);
              root.style.setProperty('--nn-sidebar-' + side, val + '%');
            }
            function onUp(){ document.removeEventListener('mousemove', onMove); document.removeEventListener('mouseup', onUp); }
            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup', onUp);
          });
        });
      }
      attach('left'); attach('right');
    })();
    </script>
    <?php } ?>
    <?php
} );

function nosfirnews_do_loop_hook( $position ) {
    do_action( 'nosfirnews_loop_' . $position );
}
$views_dirs = [ '/inc/views', '/inc/views/inline', '/inc/views/layouts', '/inc/views/partials', '/inc/views/pluggable' ];
foreach ( $views_dirs as $sub ) { foreach ( glob( get_template_directory() . $sub . '/*.php' ) as $f ) { require_once $f; } }

function nosfirnews_register_block_patterns() {
    if ( function_exists( 'register_block_pattern' ) ) {
        register_block_pattern_category( 'nosfirnews', [ 'label' => __( 'NosfirNews', 'nosfirnews' ) ] );
        foreach ( glob( get_template_directory() . '/inc/compability/block-patterns/*.php' ) as $file ) {
            $pattern = require $file;
            if ( is_array( $pattern ) && isset( $pattern['title'], $pattern['content'] ) ) {
                register_block_pattern( 'nosfirnews/' . basename( $file, '.php' ), $pattern );
            }
        }
    }
}
add_action( 'init', 'nosfirnews_register_block_patterns' );

function nosfirnews_register_starter_content() {
    $dir = get_template_directory() . '/inc/compability/starter-content';
    if ( is_dir( $dir ) ) {
        $mods = file_exists( $dir . '/theme-mods.php' ) ? require $dir . '/theme-mods.php' : [];
        $posts = [];
        foreach ( [ 'home', 'about', 'contact', 'portofolio', 'project-details' ] as $p ) {
            $file = $dir . '/' . $p . '.php'; if ( file_exists( $file ) ) { $posts[ $p ] = require $file; }
        }
        add_theme_support( 'starter-content', [ 'theme_mods' => $mods, 'posts' => $posts ] );
    }
}
add_action( 'after_setup_theme', 'nosfirnews_register_starter_content' );
add_action( 'wp_enqueue_scripts', function(){
    if ( (bool) get_theme_mod( 'nn_enable_bootstrap', true ) ) {
        wp_enqueue_style( 'bootstrap5', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css', [], '5.3.2' );
        wp_enqueue_script( 'bootstrap5', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', [], '5.3.2', true );
    }
} );

add_filter( 'wp_editor_set_quality', function( $quality ){
    return max( 60, min( 100, (int) get_theme_mod( 'nn_thumb_quality', 90 ) ) );
} );

add_filter( 'wp_image_editors', function( $editors ){
    if ( class_exists( 'Imagick' ) ) { return [ 'WP_Image_Editor_Imagick', 'WP_Image_Editor_GD' ]; }
    return $editors;
} );

add_filter( 'image_resize_dimensions', function( $payload, $orig_w, $orig_h, $dest_w, $dest_h, $crop ){
    if ( (bool) get_theme_mod( 'nn_disallow_upscale', true ) ) {
        if ( $dest_w > $orig_w || $dest_h > $orig_h ) { return false; }
    }
    return $payload;
}, 10, 6 );

add_filter( 'image_editor_output_format', function( $formats ){
    $enable = (bool) get_theme_mod( 'nn_convert_webp', false );
    $supports = function_exists( 'wp_image_editor_supports' ) ? wp_image_editor_supports( [ 'mime_type' => 'image/webp' ] ) : false;
    if ( $enable && $supports ) { $formats['image/jpeg'] = 'image/webp'; $formats['image/png'] = 'image/webp'; }
    return $formats;
} );

add_filter( 'wp_get_attachment_image_attributes', function( $attr, $attachment, $size ){
    if ( $size === 'nn_single_cover' ) {
        $attr['sizes'] = '100vw';
        $attr['decoding'] = 'async';
        if ( (bool) get_theme_mod( 'nn_cover_eager_load', true ) ) { $attr['loading'] = 'eager'; }
        $fp = get_theme_mod( 'nn_cover_fetchpriority', 'high' );
        if ( $fp === 'high' ) { $attr['fetchpriority'] = 'high'; }
    } elseif ( $size === 'nn_thumb_standard' ) {
        $attr['sizes'] = '(min-width: 992px) 320px, 50vw';
        $attr['decoding'] = 'async';
    }
    return $attr;
}, 10, 3 );

add_action( 'admin_menu', function(){
    add_theme_page( __( 'NosfirNews Thumbs', 'nosfirnews' ), __( 'Thumbs', 'nosfirnews' ), 'manage_options', 'nn-thumbs', function(){
        if ( isset( $_POST['nn_regen'] ) ) {
            check_admin_referer( 'nn_thumbs' );
            @set_time_limit( 0 );
            $q = new WP_Query( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => -1, 'fields' => 'ids' ] );
            $ok = 0; $fail = 0;
            foreach ( $q->posts as $id ) {
                $file = get_attached_file( $id );
                if ( ! $file || ! file_exists( $file ) ) { $fail++; continue; }
                $meta = wp_generate_attachment_metadata( $id, $file );
                if ( is_array( $meta ) ) { wp_update_attachment_metadata( $id, $meta ); $ok++; } else { $fail++; }
            }
            echo '<div class="notice notice-success"><p>' . esc_html( sprintf( __( 'Regenerado: %d anexos, falhas: %d', 'nosfirnews' ), $ok, $fail ) ) . '</p></div>';
        }
        echo '<div class="wrap"><h1>' . esc_html__( 'Thumbs padrão', 'nosfirnews' ) . '</h1>';
        echo '<p>' . esc_html__( 'Defina largura/altura e qualidade nas opções do tema. Use o botão abaixo para regenerar as miniaturas com o padrão atual.', 'nosfirnews' ) . '</p>';
        echo '<form method="post">';
        wp_nonce_field( 'nn_thumbs' );
        echo '<p><button class="button button-primary" name="nn_regen" value="1">' . esc_html__( 'Regenerar thumbnails', 'nosfirnews' ) . '</button></p>';
        echo '</form></div>';
    } );
} );

add_filter( 'comment_form_defaults', function( $d ){
    $d['class_form'] = trim( ( $d['class_form'] ?? '' ) . ' needs-validation' );
    $d['submit_button'] = '<button name="%1$s" type="submit" id="%2$s" class="submit btn btn-primary">%4$s</button>';
    return $d;
} );

add_filter( 'comment_form_fields', function( $fields ){
    $add_class = function( $html ){
        $html = preg_replace( '/<input(.*?)class="([^"]*)"/i', '<input$1class="$2 form-control"', $html );
        $html = preg_replace( '/<input(?!.*class=)/i', '<input class="form-control"', $html );
        $html = preg_replace( '/<textarea(.*?)class="([^"]*)"/i', '<textarea$1class="$2 form-control"', $html );
        $html = preg_replace( '/<textarea(?!.*class=)/i', '<textarea class="form-control"', $html );
        return $html;
    };
    foreach ( $fields as $k => $html ) { $fields[ $k ] = $add_class( $html ); }
    return $fields;
} );
