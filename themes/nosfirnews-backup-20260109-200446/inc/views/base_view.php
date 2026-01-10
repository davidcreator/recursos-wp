<?php
function nosfirnews_view( $template, $args = [] ) {
    $file = get_template_directory() . '/inc/views/' . $template . '.php';
    if ( file_exists( $file ) ) { if ( is_array( $args ) ) extract( $args, EXTR_SKIP ); include $file; }
}
