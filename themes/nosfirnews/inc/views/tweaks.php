<?php
function nosfirnews_tweaks_boot() {
    add_filter( 'excerpt_more', function() { return '…'; } );
}
add_action( 'after_setup_theme', 'nosfirnews_tweaks_boot' );
