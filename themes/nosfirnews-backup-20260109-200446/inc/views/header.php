<?php
function nosfirnews_view_header_simple() {
    $logo_align = get_theme_mod( 'nn_logo_alignment', 'left' );
    $nav_align = get_theme_mod( 'nn_nav_alignment', 'right' );
    echo '<header class="site-header"><div class="container header-inner">';
    echo '<button class="nav-toggle" aria-controls="mobile-menu" aria-expanded="false" aria-label="Abrir menu">&#9776;</button>';
    echo '<div class="site-branding branding-pos-' . esc_attr( ( in_array( $logo_align, [ 'left','center','right' ], true ) ? $logo_align : 'left' ) ) . '">';
    if ( function_exists( 'the_custom_logo' ) ) the_custom_logo();
    $location = get_theme_mod( 'nosfirnews_primary_menu_location', 'primary' );
    echo '</div>';
    echo '<nav class="site-nav nav-pos-' . esc_attr( ( in_array( $nav_align, [ 'left','center','right' ], true ) ? $nav_align : 'right' ) ) . '" role="navigation" aria-label="Primary">';
    wp_nav_menu( [
        'theme_location' => $location,
        'menu_class'     => 'nav-menu',
        'menu_id'        => 'primary-menu',
        'container'      => false,
        'depth'          => 3,
        'fallback_cb'    => 'wp_page_menu',
        'walker'         => new \NosfirNews_Nav_Walker(),
    ] );
    echo '</nav>';
    echo '</div>';
    $mobile_location = get_theme_mod( 'nn_mobile_menu_location', 'mobile' );
    echo '<div id="mobile-menu" class="nn-mobile-drawer" aria-hidden="true">';
    wp_nav_menu( [
        'theme_location' => $mobile_location,
        'menu_class'     => 'mobile-nav-menu',
        'menu_id'        => 'mobile-menu-list',
        'container'      => false,
        'depth'          => 3,
        'fallback_cb'    => 'wp_page_menu',
        'walker'         => new \NosfirNews_Nav_Walker(),
    ] );
    echo '</div>';
    echo '</header>';
}
