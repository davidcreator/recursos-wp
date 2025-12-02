<?php
function nosfirnews_partial_post_meta() {
    echo '<div class="post-meta">';
    echo '<span class="author">' . esc_html( get_the_author() ) . '</span>';
    echo '<span class="date">' . esc_html( get_the_date() ) . '</span>';
    $cats = get_the_category_list( ', ' ); if ( $cats ) echo '<span class="cats">' . $cats . '</span>';
    echo '</div>';
}
