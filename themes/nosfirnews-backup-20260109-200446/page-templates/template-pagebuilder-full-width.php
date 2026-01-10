<?php
get_header();
echo '<main class="container full-width">';
while ( have_posts() ) { the_post(); the_content(); }
echo '</main>';
get_footer();
