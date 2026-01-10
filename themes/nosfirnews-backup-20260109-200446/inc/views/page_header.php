<?php
function nosfirnews_page_header() {
    echo '<div class="page-header nn-container">';
    if ( is_singular() ) { echo '<h1>' . esc_html( get_the_title() ) . '</h1>'; }
    else { echo '<h1>' . esc_html( get_the_archive_title() ) . '</h1>'; }
    echo '</div>';
}
