<?php
if ( have_posts() ) {
    echo '<div class="container">';
    while ( have_posts() ) { the_post();
        echo '<article id="post-' . get_the_ID() . '" class="entry">';
        echo '<h2><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h2>';
        nosfirnews_partial_post_meta();
        the_excerpt();
        echo '</article>';
    }
    nosfirnews_pagination();
    echo '</div>';
} else { get_template_part( 'page-templates/content', 'none' ); }
