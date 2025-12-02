<?php
while ( have_posts() ) { the_post();
    echo '<article id="post-' . get_the_ID() . '" class="single">';
    echo '<h1>' . esc_html( get_the_title() ) . '</h1>';
    get_template_part( 'inc/views/partials/post_meta' );
    the_content();
    nosfirnews_pagination();
    nosfirnews_partial_comments();
    echo '</article>';
}
