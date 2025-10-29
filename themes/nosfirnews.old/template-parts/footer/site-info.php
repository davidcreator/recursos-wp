<?php
/**
 * Template part for displaying site info in footer
 *
 * @package NosfirNews
 * @since 1.0.0
 */

?>

<div class="site-info">
    
    <div class="copyright">
        <?php
        echo wp_kses_post( get_theme_mod( 'nosfirnews_copyright_text', sprintf( esc_html__( '© %s. All rights reserved.', 'nosfirnews' ), date( 'Y' ) ) ) );
        ?>
    </div>
    
    <div class="theme-credit">
        <?php
        printf( 
            /* translators: 1: Theme name, 2: Author name, 3: Author email */
            esc_html__( 'Theme: %1$s by %2$s', 'nosfirnews' ), 
            '<strong>NosfirNews</strong>', 
            '<a href="mailto:contato@davidalmeida.xyz">David L. Almeida</a>' 
        );
        ?>
    </div>
    
</div><!-- .site-info -->