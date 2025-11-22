<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<section class="no-results not-found">
    <header class="page-header">
        <h2><?php esc_html_e( 'Nada encontrado', 'nosfirnews' ); ?></h2>
    </header>
    <div class="page-content">
        <p><?php esc_html_e( 'Tente buscar novamente com outros termos.', 'nosfirnews' ); ?></p>
        <?php get_search_form(); ?>
    </div>
</section>