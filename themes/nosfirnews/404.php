<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package NosfirNews
 * @since 2.0.0
 */

get_header(); ?>

<div class="site-content-wrapper">
    <div class="content-layout">
        <main id="main" class="site-main" role="main" aria-label="<?php esc_attr_e( '404 Error Page', 'nosfirnews' ); ?>">
            
            <section class="error-404 not-found" role="region" aria-labelledby="error-title" itemscope itemtype="https://schema.org/WebPage">
                
                <!-- 404 Illustration -->
                <div class="error-illustration">
                    <svg width="200" height="150" viewBox="0 0 200 150" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <circle cx="100" cy="75" r="60" stroke="currentColor" stroke-width="2" fill="none" opacity="0.3"/>
                        <text x="100" y="85" text-anchor="middle" font-size="36" font-weight="bold" fill="currentColor">404</text>
                        <path d="M70 45 L130 105 M130 45 L70 105" stroke="currentColor" stroke-width="3" stroke-linecap="round" opacity="0.5"/>
                    </svg>
                </div>
                
                <header class="page-header">
                    <h1 id="error-title" class="page-title" itemprop="name"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'nosfirnews' ); ?></h1>
                    <p class="error-subtitle"><?php esc_html_e( 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'nosfirnews' ); ?></p>
                </header><!-- .page-header -->
                
                <div class="page-content" itemprop="mainContentOfPage">
                    
                    <!-- Search Section -->
                    <div class="error-search-section">
                        <h2><?php esc_html_e( 'Search our site', 'nosfirnews' ); ?></h2>
                        <p><?php esc_html_e( 'Try searching for what you need:', 'nosfirnews' ); ?></p>
                        <?php get_search_form(); ?>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="error-actions">
                        <h2><?php esc_html_e( 'Quick Actions', 'nosfirnews' ); ?></h2>
                        <div class="action-buttons">
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M9 22V12H15V22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <?php esc_html_e( 'Go to Homepage', 'nosfirnews' ); ?>
                            </a>
                            
                            <button onclick="history.back()" class="btn btn-secondary">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <?php esc_html_e( 'Go Back', 'nosfirnews' ); ?>
                            </button>
                            
                            <?php if ( get_option( 'page_for_posts' ) ) : ?>
                                <a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>" class="btn btn-outline">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M14 2V8H20M16 13H8M16 17H8M10 9H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <?php esc_html_e( 'View Blog', 'nosfirnews' ); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Helpful Content -->
                    <div class="error-helpful-content">
                        <div class="helpful-content-grid">
                            
                            <!-- Recent Posts -->
                            <div class="helpful-section">
                                <h3><?php esc_html_e( 'Recent Posts', 'nosfirnews' ); ?></h3>
                                <?php
                                $recent_posts = wp_get_recent_posts( array(
                                    'numberposts' => 5,
                                    'post_status' => 'publish'
                                ) );
                                
                                if ( $recent_posts ) :
                                ?>
                                    <ul class="recent-posts-list">
                                        <?php foreach ( $recent_posts as $post ) : ?>
                                            <li>
                                                <a href="<?php echo esc_url( get_permalink( $post['ID'] ) ); ?>">
                                                    <?php echo esc_html( $post['post_title'] ); ?>
                                                </a>
                                                <time datetime="<?php echo esc_attr( get_the_date( 'c', $post['ID'] ) ); ?>">
                                                    <?php echo esc_html( get_the_date( '', $post['ID'] ) ); ?>
                                                </time>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php
                                wp_reset_postdata();
                                else :
                                ?>
                                    <p><?php esc_html_e( 'No recent posts found.', 'nosfirnews' ); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Popular Categories -->
                            <div class="helpful-section">
                                <h3><?php esc_html_e( 'Popular Categories', 'nosfirnews' ); ?></h3>
                                <ul class="categories-list">
                                    <?php
                                    wp_list_categories( array(
                                        'orderby'    => 'count',
                                        'order'      => 'DESC',
                                        'show_count' => 1,
                                        'title_li'   => '',
                                        'number'     => 8,
                                        'echo'       => 1,
                                    ) );
                                    ?>
                                </ul>
                            </div>
                            
                            <!-- Contact Info -->
                            <div class="helpful-section">
                                <h3><?php esc_html_e( 'Need Help?', 'nosfirnews' ); ?></h3>
                                <p><?php esc_html_e( 'If you believe this is an error, please contact us:', 'nosfirnews' ); ?></p>
                                <div class="contact-options">
                                    <?php if ( get_option( 'admin_email' ) ) : ?>
                                        <a href="mailto:<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" class="contact-link">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="L22 6L12 13L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <?php esc_html_e( 'Email Support', 'nosfirnews' ); ?>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php
                                    $contact_page = get_page_by_path( 'contact' );
                                    if ( $contact_page ) :
                                    ?>
                                        <a href="<?php echo esc_url( get_permalink( $contact_page->ID ) ); ?>" class="contact-link">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <?php esc_html_e( 'Contact Page', 'nosfirnews' ); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    
                </div><!-- .page-content -->
                
                <!-- Hidden microdata -->
                <div style="display: none;">
                    <span itemprop="url"><?php echo esc_url( home_url( $_SERVER['REQUEST_URI'] ) ); ?></span>
                    <span itemprop="datePublished" content="<?php echo esc_attr( current_time( 'c' ) ); ?>"></span>
                </div>
                
            </section><!-- .error-404 -->
            
        </main><!-- #main -->
        
        <?php get_sidebar(); ?>
        
    </div><!-- .content-layout -->
</div><!-- .site-content-wrapper -->

<?php
get_footer();