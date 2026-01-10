<?php
function nosfirnews_breadcrumbs() {
    echo '<nav class="nn-breadcrumbs" aria-label="Breadcrumb">';
    echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Início', 'nosfirnews' ) . '</a>';
    if ( is_single() || is_page() ) { echo ' / ' . esc_html( get_the_title() ); }
    elseif ( is_archive() ) { echo ' / ' . esc_html( get_the_archive_title() ); }
    elseif ( is_search() ) { echo ' / ' . esc_html__( 'Pesquisa', 'nosfirnews' ); }
    echo '</nav>';
}
