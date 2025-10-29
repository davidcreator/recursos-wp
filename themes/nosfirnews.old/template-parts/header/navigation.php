<?php
/**
 * Template part for displaying navigation
 *
 * @package NosfirNews
 * @since 1.0.0
 */

?>

<?php if ( has_nav_menu( 'primary' ) ) : ?>
    <?php
    wp_nav_menu( array(
        'theme_location' => 'primary',
        'menu_id'        => 'primary-menu',
        'menu_class'     => 'primary-menu',
        'container'      => false,
        'fallback_cb'    => false,
        'depth'          => 2,
    ) );
    ?>
<?php else : ?>
    <ul id="primary-menu" class="primary-menu">
        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'nosfirnews' ); ?></a></li>
        <?php
        wp_list_pages( array(
            'title_li' => '',
            'number'   => 5,
        ) );
        ?>
    </ul>
<?php endif; ?>