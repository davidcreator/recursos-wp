<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package NosfirNews
 * @since 2.0.0
 */

?>

    </div><!-- #content -->
    
    <footer id="colophon" class="site-footer" role="contentinfo" itemscope itemtype="https://schema.org/WPFooter">
        <div class="container">
            
            <?php if ( is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) || is_active_sidebar( 'footer-3' ) ) : ?>
                <section class="footer-widgets" aria-label="<?php esc_attr_e( 'Footer widgets', 'nosfirnews' ); ?>">
                    <div class="footer-widgets-grid">
                        <?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
                            <div class="footer-widget-area footer-widget-1" role="complementary" aria-label="<?php esc_attr_e( 'Footer widget area 1', 'nosfirnews' ); ?>">
                                <?php dynamic_sidebar( 'footer-1' ); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
                            <div class="footer-widget-area footer-widget-2" role="complementary" aria-label="<?php esc_attr_e( 'Footer widget area 2', 'nosfirnews' ); ?>">
                                <?php dynamic_sidebar( 'footer-2' ); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
                            <div class="footer-widget-area footer-widget-3" role="complementary" aria-label="<?php esc_attr_e( 'Footer widget area 3', 'nosfirnews' ); ?>">
                                <?php dynamic_sidebar( 'footer-3' ); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </section><!-- .footer-widgets -->
            <?php endif; ?>
            
            <div class="footer-bottom">
                <?php get_template_part( 'template-parts/footer/site-info' ); ?>
                
                <!-- Back to top button -->
                <button id="back-to-top" class="back-to-top" aria-label="<?php esc_attr_e( 'Back to top', 'nosfirnews' ); ?>" title="<?php esc_attr_e( 'Back to top', 'nosfirnews' ); ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M12 19V5M5 12L12 5L19 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="screen-reader-text"><?php esc_html_e( 'Back to top', 'nosfirnews' ); ?></span>
                </button>
            </div><!-- .footer-bottom -->
            
        </div><!-- .container -->
        
        <!-- Hidden microdata for organization -->
        <div class="hidden-microdata" itemscope itemtype="https://schema.org/Organization">
            <span itemprop="name"><?php bloginfo( 'name' ); ?></span>
            <span itemprop="url"><?php echo esc_url( home_url( '/' ) ); ?></span>
            <?php if ( get_bloginfo( 'description' ) ) : ?>
                <span itemprop="description"><?php bloginfo( 'description' ); ?></span>
            <?php endif; ?>
            <?php if ( get_site_icon_url() ) : ?>
                <span itemprop="logo"><?php echo esc_url( get_site_icon_url() ); ?></span>
            <?php endif; ?>
        </div>
        
    </footer><!-- #colophon -->
    
</div><!-- #page -->

<!-- Performance optimization: Defer non-critical scripts -->
<script>
    // Progressive enhancement for back to top button
    document.addEventListener('DOMContentLoaded', function() {
        const backToTopButton = document.getElementById('back-to-top');
        if (backToTopButton) {
            // Show/hide button based on scroll position
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    backToTopButton.classList.add('visible');
                } else {
                    backToTopButton.classList.remove('visible');
                }
            });
        }
    });
</script>

<?php wp_footer(); ?>

</body>
</html>