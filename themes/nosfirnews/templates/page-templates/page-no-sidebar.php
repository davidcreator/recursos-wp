<?php
/**
 * Template Name: No Sidebar Page
 * 
 * Template for displaying pages without sidebar but with standard container width
 * 
 * @package NosfirNews
 * @since 1.0.0
 */

get_header(); ?>

<div class="site-content no-sidebar-page">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <main id="main" class="site-main no-sidebar-main" role="main">
                    
                    <?php while ( have_posts() ) : the_post(); ?>
                        
                        <article id="post-<?php the_ID(); ?>" <?php post_class('no-sidebar-article'); ?>>
                            
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="page-featured-image">
                                    <figure class="featured-image-wrapper">
                                        <?php the_post_thumbnail('large', array('class' => 'img-fluid featured-image')); ?>
                                        <?php if ( get_the_post_thumbnail_caption() ) : ?>
                                            <figcaption class="featured-image-caption">
                                                <?php echo get_the_post_thumbnail_caption(); ?>
                                            </figcaption>
                                        <?php endif; ?>
                                    </figure>
                                </div>
                            <?php endif; ?>
                            
                            <header class="page-header">
                                <?php the_title( '<h1 class="page-title">', '</h1>' ); ?>
                                
                                <?php if ( get_the_excerpt() ) : ?>
                                    <div class="page-excerpt">
                                        <?php the_excerpt(); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="page-meta">
                                    <span class="page-date">
                                        <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                                        <time datetime="<?php echo get_the_date('c'); ?>">
                                            <?php echo get_the_date(); ?>
                                        </time>
                                    </span>
                                    
                                    <?php if ( get_the_modified_date() !== get_the_date() ) : ?>
                                        <span class="page-modified">
                                            <i class="fas fa-edit" aria-hidden="true"></i>
                                            <?php printf( 
                                                esc_html__( 'Updated: %s', 'nosfirnews' ), 
                                                '<time datetime="' . get_the_modified_date('c') . '">' . get_the_modified_date() . '</time>'
                                            ); ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if ( function_exists('get_field') && get_field('reading_time') ) : ?>
                                        <span class="reading-time">
                                            <i class="fas fa-clock" aria-hidden="true"></i>
                                            <?php printf( 
                                                esc_html__( '%s min read', 'nosfirnews' ), 
                                                get_field('reading_time') 
                                            ); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </header>
                            
                            <div class="page-content">
                                <?php
                                the_content();
                                
                                wp_link_pages( array(
                                    'before' => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'nosfirnews' ) . '</span>',
                                    'after'  => '</div>',
                                    'link_before' => '<span class="page-link">',
                                    'link_after'  => '</span>',
                                ) );
                                ?>
                            </div>
                            
                            <?php if ( has_tag() || has_category() ) : ?>
                                <div class="page-taxonomy">
                                    <?php if ( has_category() ) : ?>
                                        <div class="page-categories">
                                            <span class="taxonomy-label">
                                                <i class="fas fa-folder" aria-hidden="true"></i>
                                                <?php esc_html_e( 'Categories:', 'nosfirnews' ); ?>
                                            </span>
                                            <?php the_category( ', ' ); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ( has_tag() ) : ?>
                                        <div class="page-tags">
                                            <span class="taxonomy-label">
                                                <i class="fas fa-tags" aria-hidden="true"></i>
                                                <?php esc_html_e( 'Tags:', 'nosfirnews' ); ?>
                                            </span>
                                            <?php the_tags( '', ', ' ); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
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
                                        '<span class="edit-link"><i class="fas fa-edit" aria-hidden="true"></i>',
                                        '</span>'
                                    );
                                    ?>
                                </footer>
                            <?php endif; ?>
                            
                        </article>
                        
                        <?php
                        // Navigation between pages
                        $prev_post = get_previous_post();
                        $next_post = get_next_post();
                        
                        if ( $prev_post || $next_post ) : ?>
                            <nav class="page-navigation" aria-label="<?php esc_attr_e( 'Page Navigation', 'nosfirnews' ); ?>">
                                <div class="nav-links">
                                    <?php if ( $prev_post ) : ?>
                                        <div class="nav-previous">
                                            <a href="<?php echo get_permalink( $prev_post->ID ); ?>" rel="prev">
                                                <span class="nav-direction">
                                                    <i class="fas fa-arrow-left" aria-hidden="true"></i>
                                                    <?php esc_html_e( 'Previous', 'nosfirnews' ); ?>
                                                </span>
                                                <span class="nav-title"><?php echo get_the_title( $prev_post->ID ); ?></span>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ( $next_post ) : ?>
                                        <div class="nav-next">
                                            <a href="<?php echo get_permalink( $next_post->ID ); ?>" rel="next">
                                                <span class="nav-direction">
                                                    <?php esc_html_e( 'Next', 'nosfirnews' ); ?>
                                                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                                </span>
                                                <span class="nav-title"><?php echo get_the_title( $next_post->ID ); ?></span>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </nav>
                        <?php endif; ?>
                        
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