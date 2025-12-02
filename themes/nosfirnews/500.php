<?php get_header(); ?>
<div class="container">
<h1><?php echo esc_html( get_theme_mod( 'nn_500_title', __( 'Erro interno do servidor', 'nosfirnews' ) ) ); ?></h1>
<p><?php echo esc_html( get_theme_mod( 'nn_500_message', __( 'Algo deu errado. Tente novamente mais tarde.', 'nosfirnews' ) ) ); ?></p>
<?php if ( (bool) get_theme_mod( 'nn_500_show_search', true ) ) { get_search_form(); } ?>
</div>
<?php get_footer(); ?>
