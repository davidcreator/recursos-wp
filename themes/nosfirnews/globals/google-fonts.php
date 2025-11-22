<?php
function nosfirnews_google_fonts_url() {
    $families = [ 'Inter:wght@400;500;600', 'Roboto:wght@400;500;700' ];
    $query = [ 'family' => implode( '|', $families ), 'display' => 'swap' ];
    return add_query_arg( $query, 'https://fonts.googleapis.com/css2' );
}
function nosfirnews_enqueue_google_fonts() {
    wp_enqueue_style( 'nosfirnews-fonts', nosfirnews_google_fonts_url(), [], null );
}