<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package NosfirNews
 * @since 2.0.0
 */

get_header(); ?>

<div class="site-content-wrapper">
    <div class="container">
        <div class="row content-layout">
            
            <!-- Conteúdo principal -->
            <main id="main" class="site-main col-md-8" role="main" aria-label="<?php esc_attr_e( 'Page content', 'nosfirnews' ); ?>">
                
                <?php
                while ( have_posts() ) :
                    the_post();
                    
                    // Breadcrumbs para navegação
                    if ( function_exists( 'nosfirnews_breadcrumbs' ) ) :
                        nosfirnews_breadcrumbs();
                    endif;
                    
                    get_template_part( 'template-parts/content/content', 'page' );
                    
                    // Seção de comentários
                    if ( comments_open() || get_comments_number() ) :
                        ?>
                        <section id="comments" class="comments-area" aria-label="<?php esc_attr_e( 'Comments section', 'nosfirnews' ); ?>">
                            <?php comments_template(); ?>
                        </section>
                        <?php
                    endif;
                    
                endwhile; // End loop
                ?>
                
            </main><!-- #main -->

            <!-- Sidebar -->
            <?php 
            if ( ! is_page_template( 'page-templates/full-width.php' ) ) : ?>
                <aside id="secondary" class="widget-area sidebar col-md-4" role="complementary" style="margin-top: 0;">
                    <div class="sticky-sidebar">
                        <?php dynamic_sidebar( 'sidebar-1' ); ?>
                    </div>
                </aside><!-- #secondary -->
            <?php endif; ?>

        </div><!-- .row -->
    </div><!-- .container -->
</div><!-- .site-content-wrapper -->

<?php
get_footer();
