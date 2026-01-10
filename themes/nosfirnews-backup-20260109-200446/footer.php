<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
</div>
<footer id="colophon" class="site-footer" role="contentinfo">
    <div class="container">
        <?php
        $show_logo = (bool) get_theme_mod( 'nn_footer_show_logo', false );
        $logo_url = get_theme_mod( 'nn_footer_logo', '' );
        $desc = get_theme_mod( 'nn_footer_description', '' );
        $show_social = (bool) get_theme_mod( 'nn_footer_show_social', true );
        $links_loc = get_theme_mod( 'nn_footer_links_menu_location', 'footer' );
        if ( $show_logo || $desc ) {
            echo '<div class="footer-brand">';
            if ( $show_logo ) {
                echo '<div class="footer-logo">';
                if ( $logo_url ) { echo '<img src="' . esc_url( $logo_url ) . '" alt="' . esc_attr( get_bloginfo('name') ) . '" />'; }
                else if ( function_exists( 'the_custom_logo' ) ) { the_custom_logo(); }
                echo '</div>';
            }
            if ( $desc ) { echo '<div class="footer-desc">' . wp_kses_post( $desc ) . '</div>'; }
            echo '</div>';
        }
        if ( has_nav_menu( $links_loc ) ) {
            wp_nav_menu( [ 'theme_location' => $links_loc, 'container' => false, 'menu_class' => 'footer-links' ] );
        }
        if ( $show_social && has_nav_menu( 'social' ) ) {
            wp_nav_menu( [ 'theme_location' => 'social', 'container' => false, 'menu_class' => 'footer-social' ] );
        }
        ?>
        <?php
        $cols = max( 1, min( 4, (int) get_theme_mod( 'nn_footer_columns', 2 ) ) );
        $align = get_theme_mod( 'nn_footer_align', 'stretch' );
        $gap = (int) get_theme_mod( 'nn_footer_gap', 20 );
        $has_any = false;
        for ( $i = 1; $i <= $cols; $i++ ) { if ( is_active_sidebar( 'footer-' . $i ) ) { $has_any = true; break; } }
        if ( $has_any ) {
            echo '<div class="footer-widgets footer-cols-' . esc_attr( $cols ) . ' footer-align-' . esc_attr( $align ) . '" style="--nn-footer-gap:' . esc_attr( $gap ) . 'px">';
            for ( $i = 1; $i <= $cols; $i++ ) {
                echo '<div class="footer-col">';
                if ( is_active_sidebar( 'footer-' . $i ) ) { dynamic_sidebar( 'footer-' . $i ); }
                echo '</div>';
            }
            echo '</div>';
        }
        ?>
        <p>&copy; <?php echo esc_html( date('Y') ); ?> <?php bloginfo('name'); ?></p>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
