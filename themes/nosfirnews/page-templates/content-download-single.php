<?php
while ( have_posts() ) { the_post();
    echo '<article id="post-' . get_the_ID() . '" class="download-single">';
    echo '<h1>' . esc_html( get_the_title() ) . '</h1>';
    the_content();
    echo '</article>';
}
