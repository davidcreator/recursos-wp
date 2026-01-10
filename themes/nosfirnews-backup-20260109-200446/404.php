<?php get_header(); ?>
<div class="container">
<h1><?php echo esc_html( get_theme_mod( 'nn_404_title', __( 'Página não encontrada', 'nosfirnews' ) ) ); ?></h1>
<p><?php echo esc_html( get_theme_mod( 'nn_404_message', __( 'Tente buscar novamente.', 'nosfirnews' ) ) ); ?></p>
<?php if ( (bool) get_theme_mod( 'nn_404_show_search', true ) ) { get_search_form(); } ?>
</div>
<?php get_footer(); ?>
