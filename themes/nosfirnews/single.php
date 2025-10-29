<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package NosfirNews
 * @since 2.0.0
 */

get_header(); ?>

<div class="site-content-wrapper">
    <div class="container">
        <div class="row content-layout">
            
            <!-- Conteúdo principal -->
            <main id="main" class="site-main col-md-8" role="main" aria-label="<?php esc_attr_e( 'Article content', 'nosfirnews' ); ?>">
                
                <?php
                while ( have_posts() ) :
                    the_post();
                    
                    // Breadcrumbs
                    if ( function_exists( 'nosfirnews_breadcrumbs' ) ) :
                        nosfirnews_breadcrumbs();
                    endif;
                    
                    get_template_part( 'template-parts/content/content', 'single' );
                    
                    // Author bio
                    if ( is_singular( 'post' ) && get_the_author_meta( 'description' ) ) :
                        get_template_part( 'template-parts/components/author-bio' );
                    endif;
                    
                    // Related posts
                    if ( is_singular( 'post' ) ) :
                        get_template_part( 'template-parts/components/related-posts' );
                    endif;
                    
                    // Post navigation
                    $nav_args = array(
                        'prev_text' => '<span class="nav-subtitle" aria-hidden="true">' . esc_html__( 'Previous Article', 'nosfirnews' ) . '</span> <span class="nav-title">%title</span>',
                        'next_text' => '<span class="nav-subtitle" aria-hidden="true">' . esc_html__( 'Next Article', 'nosfirnews' ) . '</span> <span class="nav-title">%title</span>',
                        'screen_reader_text' => esc_html__( 'Post navigation', 'nosfirnews' ),
                    );
                    the_post_navigation( $nav_args );
                    
                    // Comments
                    if ( comments_open() || get_comments_number() ) :
                        ?>
                        <section id="comments" class="comments-area" aria-label="<?php esc_attr_e( 'Comments section', 'nosfirnews' ); ?>">
                            <?php comments_template(); ?>
                        </section>
                        <?php
                    endif;
                    
                endwhile;
                ?>
                
            </main><!-- #main -->
            
            <!-- Sidebar -->
            <?php 
            if ( ! is_page_template( 'page-templates/full-width.php' ) ) : ?>
                <aside id="secondary" class="widget-area sidebar col-md-4" role="complementary">
                    <?php dynamic_sidebar( 'sidebar-1' ); ?>
                </aside><!-- #secondary -->
            <?php endif; ?>
            
        </div><!-- .row -->
    </div><!-- .container -->
</div><!-- .site-content-wrapper -->

<?php
get_footer();
