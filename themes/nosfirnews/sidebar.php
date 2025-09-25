<?php
/**
 * The sidebar containing the main widget area
 *
 * @package NosfirNews
 * @since 2.0.0
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
    return;
}
?>

<aside id="secondary" class="widget-area sidebar" role="complementary" aria-label="<?php esc_attr_e( 'Primary sidebar', 'nosfirnews' ); ?>" itemscope itemtype="https://schema.org/WPSideBar">
    <div class="sidebar-container">
        <div class="sidebar-content">
            <?php 
            // Add a skip link for accessibility
            if ( is_active_sidebar( 'sidebar-1' ) ) :
            ?>
                <a class="screen-reader-text" href="#main"><?php esc_html_e( 'Skip sidebar', 'nosfirnews' ); ?></a>
                
                <div class="sidebar-widgets" role="region" aria-label="<?php esc_attr_e( 'Sidebar widgets', 'nosfirnews' ); ?>">
                    <?php dynamic_sidebar( 'sidebar-1' ); ?>
                </div>
                
                <!-- Sidebar bottom actions -->
                <div class="sidebar-actions">
                    <?php if ( ! is_search() ) : ?>
                        <div class="sidebar-search">
                            <?php get_search_form(); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ( is_single() || is_page() ) : ?>
                        <div class="sidebar-share">
                            <h3 class="sidebar-share-title"><?php esc_html_e( 'Share this', 'nosfirnews' ); ?></h3>
                            <div class="share-buttons">
                                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode( get_permalink() ); ?>&text=<?php echo urlencode( get_the_title() ); ?>" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   aria-label="<?php esc_attr_e( 'Share on Twitter', 'nosfirnews' ); ?>"
                                   class="share-twitter">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                    </svg>
                                    <span class="screen-reader-text"><?php esc_html_e( 'Share on Twitter', 'nosfirnews' ); ?></span>
                                </a>
                                
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode( get_permalink() ); ?>" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   aria-label="<?php esc_attr_e( 'Share on Facebook', 'nosfirnews' ); ?>"
                                   class="share-facebook">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                    <span class="screen-reader-text"><?php esc_html_e( 'Share on Facebook', 'nosfirnews' ); ?></span>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
            <?php endif; ?>
        </div>
    </div>
</aside>
