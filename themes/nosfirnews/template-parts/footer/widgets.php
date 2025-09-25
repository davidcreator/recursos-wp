<?php
/**
 * Template part for displaying footer widgets
 *
 * @package NosfirNews
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) || is_active_sidebar( 'footer-3' ) || is_active_sidebar( 'footer-4' ) ) :
?>
<div class="footer-widgets">
    <div class="container">
        <div class="footer-widgets-wrapper">
            <?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
                <div class="footer-widget footer-widget-1">
                    <?php dynamic_sidebar( 'footer-1' ); ?>
                </div>
            <?php endif; ?>

            <?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
                <div class="footer-widget footer-widget-2">
                    <?php dynamic_sidebar( 'footer-2' ); ?>
                </div>
            <?php endif; ?>

            <?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
                <div class="footer-widget footer-widget-3">
                    <?php dynamic_sidebar( 'footer-3' ); ?>
                </div>
            <?php endif; ?>

            <?php if ( is_active_sidebar( 'footer-4' ) ) : ?>
                <div class="footer-widget footer-widget-4">
                    <?php dynamic_sidebar( 'footer-4' ); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>