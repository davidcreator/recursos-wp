<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function nosfirnews_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
    register_nav_menus( [
        'primary'   => __( 'Primary Menu', 'nosfirnews' ),
        'secondary' => __( 'Secondary Menu', 'nosfirnews' ),
        'footer'    => __( 'Footer Menu', 'nosfirnews' ),
    ] );
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
}
add_action( 'widgets_init', 'nosfirnews_widgets_init' );

function nosfirnews_do_loop_hook( $position ) {
    do_action( 'nosfirnews_loop_' . $position );
}
