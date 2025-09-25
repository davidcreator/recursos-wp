<?php
/**
 * Template part for displaying page content in page.php
 *
 * @package NosfirNews
 * @since 2.0.0
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemtype="https://schema.org/WebPage">
    <header class="entry-header">
        <?php the_title( '<h1 class="entry-title" itemprop="name">', '</h1>' ); ?>
        
        <?php if ( has_excerpt() ) : ?>
            <div class="page-excerpt" itemprop="description">
                <?php the_excerpt(); ?>
            </div>
        <?php endif; ?>
        
        <?php if ( get_the_modified_date() !== get_the_date() ) : ?>
            <div class="page-meta" role="group" aria-label="<?php esc_attr_e( 'Page metadata', 'nosfirnews' ); ?>">
                <div class="meta-wrapper">
                    <div class="page-updated">
                        <span class="meta-label" aria-label="<?php esc_attr_e( 'Last updated', 'nosfirnews' ); ?>">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                <polyline points="12,6 12,12 16,14" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </span>
                        <time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>" itemprop="dateModified">
                            <?php printf( 
                                esc_html__( 'Updated on %s', 'nosfirnews' ), 
                                get_the_modified_date() 
                            ); ?>
                        </time>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </header><!-- .entry-header -->

    <?php if ( has_post_thumbnail() ) : ?>
        <div class="page-thumbnail" itemprop="image">
            <figure class="page-featured-image">
                <?php nosfirnews_post_thumbnail(); ?>
                <?php if ( get_the_post_thumbnail_caption() ) : ?>
                    <figcaption class="page-thumbnail-caption">
                        <?php echo wp_kses_post( get_the_post_thumbnail_caption() ); ?>
                    </figcaption>
                <?php endif; ?>
            </figure>
        </div>
    <?php endif; ?>

    <div class="entry-content" itemprop="mainContentOfPage">
        <?php
        the_content();

        wp_link_pages(
            array(
                'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Page sections', 'nosfirnews' ) . '"><span class="page-links-title">' . esc_html__( 'Pages:', 'nosfirnews' ) . '</span>',
                'after'  => '</nav>',
                'link_before' => '<span class="page-number">',
                'link_after'  => '</span>',
            )
        );
        ?>
    </div><!-- .entry-content -->

    <?php if ( get_edit_post_link() ) : ?>
        <footer class="entry-footer">
            <div class="page-actions">
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
                        wp_kses_post( get_the_title() )
                    ),
                    '<span class="edit-link">',
                    '</span>'
                );
                ?>
            </div>
            
            <!-- Hidden microdata -->
            <div class="hidden-microdata" style="display: none;">
                <time itemprop="datePublished" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                    <?php echo esc_html( get_the_date() ); ?>
                </time>
                <span itemprop="author" itemscope itemtype="https://schema.org/Person">
                    <span itemprop="name"><?php the_author(); ?></span>
                </span>
                <span itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
                    <span itemprop="name"><?php bloginfo( 'name' ); ?></span>
                    <span itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
                        <span itemprop="url"><?php echo esc_url( get_site_icon_url() ); ?></span>
                    </span>
                </span>
            </div>
        </footer><!-- .entry-footer -->
    <?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->