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
    $wp_customize->add_setting( 'nn_mobile_breakpoint', [ 'default' => 990, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_mobile_breakpoint', [ 'type' => 'number', 'section' => 'nn_header', 'label' => __( 'Mobile Breakpoint (px)', 'nosfirnews' ), 'input_attrs' => [ 'min' => 480, 'max' => 1200 ] ] );
    $wp_customize->add_setting( 'nn_nav_alignment', [ 'default' => 'right', 'sanitize_callback' => function( $v ){ $a = [ 'left','center','right' ]; return in_array( $v, $a, true ) ? $v : 'right'; }, 'transport' => 'refresh' ] );
    $wp_customize->add_control( 'nn_nav_alignment', [ 'type' => 'select', 'section' => 'nn_header', 'label' => __( 'Navigation Alignment', 'nosfirnews' ), 'choices' => [ 'left' => __( 'Left', 'nosfirnews' ), 'center' => __( 'Center', 'nosfirnews' ), 'right' => __( 'Right', 'nosfirnews' ) ] ] );
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
    ?>
    <style>
    .container { max-width: <?php echo esc_html( $maxw ); ?>px; }
    body { font-family: <?php echo esc_html( $ff ); ?>; font-size: <?php echo esc_html( $fs ); ?>px; }
    h1 { font-size: calc(2.2rem * <?php echo esc_html( $hs ); ?>); }
    h2 { font-size: calc(1.8rem * <?php echo esc_html( $hs ); ?>); }
    a { color: <?php echo esc_html( $primary ); ?>; }
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
    .footer-logo img { max-height:50px; height:auto; width:auto; }
    .footer-desc { color:#5e5e5e; }
    .footer-links { list-style:none; margin:10px 0; padding:0; display:flex; flex-wrap:wrap; gap:10px; }
    .footer-links a { text-decoration:none; padding:4px 6px; border-radius:4px; }
    .footer-links a:hover { background: rgba(0,0,0,.05); }
    .footer-social { list-style:none; margin:10px 0; padding:0; display:flex; gap:10px; }
    .footer-social a { opacity:.85; }
    .footer-social a:hover { opacity:1; }
    .header-inner { display:grid; grid-template-columns: 1fr 1fr 1fr; align-items:center; gap:20px; }
    .branding-pos-left { grid-column: 1; justify-self: start; }
    .branding-pos-center { grid-column: 2; justify-self: center; }
    .branding-pos-right { grid-column: 3; justify-self: end; }
    .nav-pos-left { grid-column: 1; justify-self: start; }
    .nav-pos-center { grid-column: 2; justify-self: center; }
    .nav-pos-right { grid-column: 3; justify-self: end; }
    .nav-toggle { display:none; margin-right:8px; font-size:22px; line-height:1; border:0; background:none; cursor:pointer; order:-1; }
    .nn-mobile-drawer { display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:999; }
    .nn-mobile-drawer.open { display:block; }
    .nn-mobile-drawer nav, .nn-mobile-drawer ul { list-style:none; margin:0; padding:0; }
    .nn-mobile-drawer .mobile-nav-menu { position:absolute; top:0; right:0; width:80%; max-width:360px; height:100%; background:#fff; box-shadow:-6px 0 18px rgba(0,0,0,.12); padding:20px; overflow:auto; transform: translateX(100%); transition: transform .25s ease; }
    .nn-mobile-drawer.open .mobile-nav-menu { transform: translateX(0); }
    .nn-mobile-drawer .mobile-nav-menu > li > a { display:block; padding:12px 8px; border-bottom:1px solid rgba(0,0,0,.06); }
    .nn-mobile-drawer .sub-menu { padding-left:12px; }
    .nn-lock { overflow:hidden; }
    @media (max-width: <?php echo (int) get_theme_mod('nn_mobile_breakpoint', 990 ); ?>px) {
        .site-nav { display:none !important; }
        .nav-toggle { display:inline-block !important; }
    }
    </style>
    <script>
    (function(){
      var btn = document.querySelector('.nav-toggle');
      var drawer = document.getElementById('mobile-menu');
      if (!btn || !drawer) return;
      function toggle(){ var open = drawer.classList.toggle('open'); btn.setAttribute('aria-expanded', open ? 'true' : 'false'); drawer.setAttribute('aria-hidden', open ? 'false' : 'true'); document.body.classList.toggle('nn-lock', open); }
      btn.addEventListener('click', toggle);
      drawer.addEventListener('click', function(e){ if (e.target === drawer) toggle(); });
      document.addEventListener('keydown', function(e){ if(e.key === 'Escape' && drawer.classList.contains('open')) toggle(); });
    })();
    </script>
    <?php
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
    .footer-widgets { display:flex; gap:20px; margin-bottom:20px; }
    .footer-widgets .footer-col { flex:1 1 0; }
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
