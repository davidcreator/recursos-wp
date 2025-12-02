<?php
while ( have_posts() ) { the_post();
    echo '<article id="post-' . get_the_ID() . '" class="page">';
    the_content();
    echo '</article>';
}
