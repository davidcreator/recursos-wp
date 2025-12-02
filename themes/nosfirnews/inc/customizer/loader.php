<?php
if ( ! defined( 'ABSPATH' ) ) exit;
require_once get_template_directory() . '/inc/customizer/base_customizer.php';
function nosfirnews_customizer_load_controls() {
    foreach ( glob( get_template_directory() . '/inc/customizer/types/*.php' ) as $file ) { require_once $file; }
    foreach ( glob( get_template_directory() . '/inc/customizer/controls/*.php' ) as $file ) { require_once $file; }
    foreach ( glob( get_template_directory() . '/inc/customizer/controls/react/*.php' ) as $file ) { require_once $file; }
}
add_action( 'customize_register', 'nosfirnews_customizer_load_controls', 0 );

function nosfirnews_customizer_enqueue_controls() {
    $js_controls = get_template_directory() . '/header-footer-grid/assets/js/customizer/customizer.js';
    if ( file_exists( $js_controls ) ) {
        wp_enqueue_script( 'nosfirnews-customizer-controls', get_template_directory_uri() . '/header-footer-grid/assets/js/customizer/customizer.js', [ 'customize-controls' ], filemtime( $js_controls ), true );
    }
}
add_action( 'customize_controls_enqueue_scripts', 'nosfirnews_customizer_enqueue_controls' );

function nosfirnews_customizer_enqueue_preview() {
    $js_preview = get_template_directory() . '/header-footer-grid/assets/js/customizer/builder.js';
    if ( file_exists( $js_preview ) ) {
        wp_enqueue_script( 'nosfirnews-customizer-preview', get_template_directory_uri() . '/header-footer-grid/assets/js/customizer/builder.js', [ 'customize-preview' ], filemtime( $js_preview ), true );
    }
}
add_action( 'customize_preview_init', 'nosfirnews_customizer_enqueue_preview' );
