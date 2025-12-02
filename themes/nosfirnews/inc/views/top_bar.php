<?php
function nosfirnews_top_bar() {
    echo '<div class="nn-top-bar"><div class="nn-container">';
    echo '<span>' . esc_html( get_bloginfo( 'description' ) ) . '</span>';
    echo '</div></div>';
}
