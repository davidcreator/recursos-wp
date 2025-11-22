<?php
function nosfirnews_register_global_hooks() {
    add_filter( 'body_class', function( $classes ) { $classes[] = 'nosfirnews'; return $classes; } );
}