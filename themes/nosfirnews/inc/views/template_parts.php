<?php
function nosfirnews_get_template_part( $slug, $name = null, $args = [] ) {
    $base = get_template_directory() . '/template-parts/';
    $file = $base . $slug . ( $name ? '-' . $name : '' ) . '.php';
    if ( file_exists( $file ) ) { if ( is_array( $args ) ) extract( $args, EXTR_SKIP ); include $file; }
}
