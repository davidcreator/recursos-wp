<?php
function nosfirnews_content_404() {
    echo '<div class="container">';
    echo '<h1>' . esc_html__( 'Página não encontrada', 'nosfirnews' ) . '</h1>';
    get_search_form();
    echo '</div>';
}
