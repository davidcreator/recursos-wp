<?php
function nosfirnews_top_bar() {
    echo '<div class="nn-top-bar"><div class="nn-container">';
    if ( ! get_theme_mod( 'nosfirnews_hide_site_description', false ) ) {
        echo '<span class="site-description">' . esc_html( get_bloginfo( 'description' ) ) . '</span>';
    }
    echo '</div></div>';
}
