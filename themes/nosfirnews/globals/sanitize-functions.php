<?php
function nosfirnews_sanitize_hex_color( $value ) {
    $value = trim( $value );
    return preg_match( '/^#([A-Fa-f0-9]{3}){1,2}$/', $value ) ? $value : '';
}
function nosfirnews_sanitize_select( $value, $choices ) {
    return in_array( $value, (array) $choices, true ) ? $value : ( reset( $choices ) ?: '' );
}
function nosfirnews_sanitize_integer( $value ) {
    return intval( $value );
}
function nosfirnews_sanitize_percentage( $value ) {
    $v = floatval( $value );
    if ( $v < 0 ) $v = 0; if ( $v > 100 ) $v = 100; return $v;
}