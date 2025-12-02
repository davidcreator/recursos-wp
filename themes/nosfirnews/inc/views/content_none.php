<?php
function nosfirnews_content_none() {
    echo '<div class="container">';
    echo '<h2>' . esc_html__( 'Nada encontrado', 'nosfirnews' ) . '</h2>';
    get_search_form();
    echo '</div>';
}
