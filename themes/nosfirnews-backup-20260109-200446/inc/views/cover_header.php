<?php
function nosfirnews_cover_header( $title = null ) {
    $t = $title ?: ( is_singular() ? get_the_title() : get_bloginfo( 'name' ) );
    echo '<section class="nn-cover-header"><div class="nn-container"><h1>' . esc_html( $t ) . '</h1></div></section>';
}
