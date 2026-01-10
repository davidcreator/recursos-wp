<?php get_header(); ?>
<div class="container">
<h1><?php printf( esc_html__( 'Resultados para: %s', 'nosfirnews' ), get_search_query() ); ?></h1>
<?php if ( have_posts() ) : ?>
<div class="posts-wrapper">
<?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/content' ); endwhile; ?>
</div>
<?php else : get_template_part( 'template-parts/content', 'none' ); endif; ?>
</div>
<?php get_footer(); ?>