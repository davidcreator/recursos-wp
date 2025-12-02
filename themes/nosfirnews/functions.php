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
}
add_action( 'widgets_init', 'nosfirnews_widgets_init' );

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
