<?php
namespace NosfirNews\HeaderFooterGrid\Core\Settings;
class Manager {
    public static function boot() {
        foreach ( Defaults::get() as $k => $v ) {
            if ( get_theme_mod( $k, null ) === null ) { set_theme_mod( $k, $v ); }
        }
    }
    public static function set( $key, $value ) { set_theme_mod( $key, $value ); }
    public static function get( $key, $default = null ) { return get_theme_mod( $key, $default ); }
}