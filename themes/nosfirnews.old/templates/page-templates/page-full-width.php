<?php
/**
 * Template Name: Full Width Page
 * 
 * Template for displaying pages without sidebar in full width
 * 
 * @package NosfirNews
 * @since 1.0.0
 */

get_header(); ?>

<div class="site-content full-width-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <main id="main" class="site-main full-width-main" role="main">
                    
                    <?php while ( have_posts() ) : the_post(); ?>
                        
                        <article id="post-<?php the_ID(); ?>" <?php post_class('full-width-article'); ?>>
                            
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="page-featured-image">
                                    <?php the_post_thumbnail('full', array('class' => 'img-fluid')); ?>
                                </div>
                            <?php endif; ?>
                            
                            <header class="page-header">
                                <?php the_title( '<h1 class="page-title">', '</h1>' ); ?>
                                
                                <?php if ( get_the_excerpt() ) : ?>
                                    <div class="page-excerpt">
                                        <?php the_excerpt(); ?>
                                    </div>
                                <?php endif; ?>
                            </header>
                            
                            <div class="page-content">
                                <?php
                                the_content();
                                
                                wp_link_pages( array(
                                    'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'nosfirnews' ),
                                    'after'  => '</div>',
                                ) );
                                ?>
                            </div>
                            
                            <?php if ( get_edit_post_link() ) : ?>
                                <footer class="entry-footer">
                                    <?php
                                    edit_post_link(
                                        sprintf(
                                            wp_kses(
                                                /* translators: %s: Name of current post. Only visible to screen readers */
                                                __( 'Edit <span class="screen-reader-text">%s</span>', 'nosfirnews' ),
                                                array(
                                                    'span' => array(
                                                        'class' => array(),
                                                    ),
                                                )
                                            ),
                                            get_the_title()
                                        ),
                                        '<span class="edit-link">',
                                        '</span>'
                                    );
                                    ?>
                                </footer>
                            <?php endif; ?>
                            
                        </article>
                        
                        <?php
                        // If comments are open or we have at least one comment, load up the comment template.
                        if ( comments_open() || get_comments_number() ) :
                            comments_template();
                        endif;
                        ?>
                        
                    <?php endwhile; ?>
                    
                </main>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>