<?php
namespace NosfirNews\HeaderFooterGrid\Core;
class Script_Register {
    public static function init() { add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue' ] ); }
    public static function enqueue() {
        if ( is_rtl() ) {
            $path = get_template_directory() . '/header-footer-grid/assets/css/style-rtl.css';
            $uri  = get_template_directory_uri() . '/header-footer-grid/assets/css/style-rtl.css';
            if ( file_exists( $path ) ) { wp_enqueue_style( 'nosfirnews-hfg-rtl', $uri, [], filemtime( $path ) ); }
        }
    }
}