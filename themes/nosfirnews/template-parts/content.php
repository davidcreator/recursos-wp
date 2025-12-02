<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
    <header class="entry-header">
        <?php if ( (bool) get_theme_mod( 'nosfirnews_show_featured_image', true ) && has_post_thumbnail() ) { echo '<div class="entry-thumb">'; the_post_thumbnail( 'large' ); echo '</div>'; } ?>
        <?php the_title( sprintf( '<h2 class="entry-title"><a href="%s">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
    </header>
    <div class="entry-summary">
        <?php the_excerpt(); ?>
    </div>
</article>
