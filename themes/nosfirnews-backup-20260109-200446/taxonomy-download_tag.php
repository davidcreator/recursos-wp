<?php get_header(); ?>
<div class="container">
<header class="page-header">
<h1 class="page-title"><?php single_term_title(); ?></h1>
<?php $desc = term_description(); if ( $desc ) echo '<div class="taxonomy-description">' . $desc . '</div>'; ?>
</header>
<?php if ( have_posts() ) : ?>
<div class="posts-wrapper">
<?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/content' ); endwhile; ?>
</div>
<?php the_posts_pagination(); ?>
<?php else : get_template_part( 'template-parts/content', 'none' ); endif; ?>
</div>
<?php get_footer(); ?>