<?php
function nosfirnews_view_header_simple() {
    echo '<header class="site-header"><div class="container">';
    if ( function_exists( 'the_custom_logo' ) ) the_custom_logo();
    wp_nav_menu( [ 'theme_location' => 'primary', 'menu_class' => 'nav-menu', 'container' => false ] );
    echo '</div></header>';
}
