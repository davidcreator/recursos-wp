<?php
function nosfirnews_asset_version( $relative_path ) {
    $file = trailingslashit( get_template_directory() ) . ltrim( $relative_path, '/\\' );
    return file_exists( $file ) ? filemtime( $file ) : wp_get_theme()->get( 'Version' );
}
function nosfirnews_is_rtl() { return is_rtl(); }
function nosfirnews_get_container_class( $context = '' ) { return 'container'; }
function nosfirnews_safe_get( $array, $key, $default = null ) { return isset( $array[ $key ] ) ? $array[ $key ] : $default; }