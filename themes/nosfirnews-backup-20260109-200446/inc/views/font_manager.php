<?php
function nosfirnews_font_manager_enqueue() {
    if ( function_exists( 'nosfirnews_enqueue_google_fonts' ) ) { nosfirnews_enqueue_google_fonts(); }
}
add_action( 'wp_enqueue_scripts', 'nosfirnews_font_manager_enqueue' );
