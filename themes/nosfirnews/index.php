<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package NosfirNews
 * @since 2.0.0
 */

get_header(); ?>

<div class="site-content-wrapper">
    <div class="container">
        <div class="content-layout">
            <main id="main" class="site-main" role="main" aria-label="<?php esc_attr_e( 'Main content', 'nosfirnews' ); ?>">
                
                <?php if ( have_posts() ) : ?>
                    
                    <?php if ( is_home() && ! is_front_page() ) : ?>
                        <header class="page-header">
                            <h1 class="page-title"><?php single_post_title(); ?></h1>
                            <?php
                            $description = get_the_archive_description();
                            if ( $description ) :
                                ?>
                                <div class="archive-description"><?php echo wp_kses_post( wpautop( $description ) ); ?></div>
                            <?php endif; ?>
                        </header>
                    <?php endif; ?>
                    
                    <section class="posts-grid" aria-label="<?php esc_attr_e( 'Blog posts', 'nosfirnews' ); ?>">
                        <?php
                        // Start the Loop.
                        while ( have_posts() ) :
                            the_post();
                            
                            /*
                             * Include the Post-Type-specific template for the content.
                             * If you want to override this in a child theme, then include a file
                             * called content-___.php (where ___ is the Post Type name) and that will be used instead.
                             */
                            get_template_part( 'template-parts/content/content', get_post_type() );
                            
                        endwhile;
                        ?>
                    </section>
                    
                    <?php
                    // Previous/next page navigation.
                    get_template_part( 'template-parts/components/pagination' );
                    
                else :
                    
                    // If no content, include the "No posts found" template.
                    get_template_part( 'template-parts/content/content', 'none' );
                    
                endif;
                ?>
                
            </main><!-- #main -->
            
            <?php get_sidebar(); ?>
            
        </div><!-- .content-layout -->
    </div><!-- .container -->
</div><!-- .site-content-wrapper -->

<?php
get_footer();