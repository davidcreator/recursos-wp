<?php get_header(); ?>
<div class="container">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<header class="entry-header"><h1 class="entry-title"><?php the_title(); ?></h1></header>
<div class="entry-content"><?php the_content(); ?></div>
<footer class="entry-footer"><?php the_tags('',', '); ?></footer>
</article>
<?php comments_template(); ?>
<?php endwhile; endif; ?>
</div>
<?php get_footer(); ?>