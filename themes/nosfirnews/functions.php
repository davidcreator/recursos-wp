<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function nosfirnews_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
    register_nav_menus( [ 'primary' => __( 'Primary Menu', 'nosfirnews' ) ] );
}
add_action( 'after_setup_theme', 'nosfirnews_setup' );

function nosfirnews_scripts() {
    wp_enqueue_style( 'nosfirnews-style', get_stylesheet_uri(), [], filemtime( get_template_directory() . '/style.css' ) );
}
add_action( 'wp_enqueue_scripts', 'nosfirnews_scripts' );

require_once get_template_directory() . '/header-footer-grid/loader.php';
\NosfirNews\HeaderFooterGrid\load();

function nosfirnews_widgets_init() {
    register_sidebar( [
        'name' => __( 'Main Sidebar', 'nosfirnews' ),
        'id'   => 'sidebar-1',
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