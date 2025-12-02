<?php
namespace NosfirNews\HeaderFooterGrid\Core;
class Script_Register {
    public static function init() { add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue' ] ); }
    public static function enqueue() {
        $rtl = get_template_directory() . '/header-footer-grid/assets/css/style-rtl.css';
        if ( is_rtl() && file_exists( $rtl ) ) {
            wp_enqueue_style( 'nosfirnews-hfg-rtl', get_template_directory_uri() . '/header-footer-grid/assets/css/style-rtl.css', [], filemtime( $rtl ) );
        }
    }
}
