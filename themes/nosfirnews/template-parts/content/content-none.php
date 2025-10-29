<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package NosfirNews
 * @since 2.0.0
 */

?>

<section class="no-results not-found" role="region" aria-labelledby="no-results-title">
    
    <header class="page-header">
        <h1 id="no-results-title" class="page-title">
            <?php 
            if ( is_search() ) :
                esc_html_e( 'No search results found', 'nosfirnews' );
            elseif ( is_404() ) :
                esc_html_e( 'Page not found', 'nosfirnews' );
            else :
                esc_html_e( 'Nothing here', 'nosfirnews' );
            endif;
            ?>
        </h1>
        
        <?php if ( is_search() ) : ?>
            <p class="search-query">
                <?php 
                printf( 
                    esc_html__( 'Your search for "%s" did not return any results.', 'nosfirnews' ),
                    '<strong>' . get_search_query() . '</strong>'
                );
                ?>
            </p>
        <?php endif; ?>
    </header><!-- .page-header -->
    
    <div class="page-content">
        <?php
        if ( is_home() && current_user_can( 'publish_posts' ) ) :
            ?>
            <div class="admin-message">
                <div class="message-icon" aria-hidden="true">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    </svg>
                </div>
                <?php
                printf(
                    '<p>' . wp_kses(
                        /* translators: 1: link to WP admin new post page. */
                        __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'nosfirnews' ),
                        array(
                            'a' => array(
                                'href' => array(),
                                'class' => array(),
                            ),
                        )
                    ) . '</p>',
                    esc_url( admin_url( 'post-new.php' ) )
                );
                ?>
            </div>
            <?php
        elseif ( is_search() ) :
            ?>
            <div class="search-suggestions">
                <div class="suggestion-icon" aria-hidden="true">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                        <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </div>
                <p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'nosfirnews' ); ?></p>
                
                <div class="search-form-wrapper">
                    <h2 class="search-form-title"><?php esc_html_e( 'Try a new search', 'nosfirnews' ); ?></h2>
                    <?php get_search_form(); ?>
                </div>
                
                <?php if ( have_posts() ) : ?>
                    <div class="search-suggestions-list">
                        <h3><?php esc_html_e( 'You might be interested in:', 'nosfirnews' ); ?></h3>
                        <ul>
                            <?php
                            $recent_posts = wp_get_recent_posts( array( 'numberposts' => 5 ) );
                            foreach( $recent_posts as $post ) :
                                ?>
                                <li>
                                    <a href="<?php echo esc_url( get_permalink( $post['ID'] ) ); ?>">
                                        <?php echo esc_html( $post['post_title'] ); ?>
                                    </a>
                                </li>
                                <?php
                            endforeach;
                            wp_reset_query();
                            ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            <?php
        else :
            ?>
            <div class="general-message">
                <div class="message-icon" aria-hidden="true">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                        <path d="M9,9H15V15H9V9Z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </div>
                <p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'nosfirnews' ); ?></p>
                
                <div class="search-form-wrapper">
                    <h2 class="search-form-title"><?php esc_html_e( 'Search our content', 'nosfirnews' ); ?></h2>
                    <?php get_search_form(); ?>
                </div>
                
                <div class="helpful-links">
                    <h3><?php esc_html_e( 'Helpful links', 'nosfirnews' ); ?></h3>
                    <ul>
                        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home Page', 'nosfirnews' ); ?></a></li>
                        <?php if ( get_option( 'page_for_posts' ) ) : ?>
                            <li><a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>"><?php esc_html_e( 'Blog', 'nosfirnews' ); ?></a></li>
                        <?php endif; ?>
                        <?php
                        $pages = get_pages( array( 'number' => 5, 'sort_column' => 'menu_order' ) );
                        foreach ( $pages as $page ) :
                            ?>
                            <li><a href="<?php echo esc_url( get_permalink( $page->ID ) ); ?>"><?php echo esc_html( $page->post_title ); ?></a></li>
                            <?php
                        endforeach;
                        ?>
                    </ul>
                </div>
            </div>
            <?php
        endif;
        ?>
    </div><!-- .page-content -->
    
</section><!-- .no-results -->