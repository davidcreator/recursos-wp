<?php
/**
 * Template part for displaying single posts
 *
 * @package NosfirNews
 * @since 2.0.0
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemtype="https://schema.org/BlogPosting">
    <header class="entry-header">
        <?php the_title( '<h1 class="entry-title" itemprop="headline">', '</h1>' ); ?>

        <div class="entry-meta" role="group" aria-label="<?php esc_attr_e( 'Post metadata', 'nosfirnews' ); ?>">
            <div class="meta-wrapper">
                <?php
                nosfirnews_posted_on();
                nosfirnews_posted_by();
                ?>
                
                <?php if ( has_category() ) : ?>
                    <div class="post-categories" itemprop="articleSection">
                        <span class="meta-label" aria-label="<?php esc_attr_e( 'Categories', 'nosfirnews' ); ?>">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M7 7H17V17H7V7Z" stroke="currentColor" stroke-width="2"/>
                                <path d="M14 2L20 8V20C20 21.1046 19.1046 22 18 22H6C4.89543 22 4 21.1046 4 20V4C4 2.89543 4.89543 2 6 2H14Z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </span>
                        <?php the_category( ', ' ); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ( comments_open() || get_comments_number() ) : ?>
                    <div class="post-comments-link">
                        <span class="meta-label" aria-label="<?php esc_attr_e( 'Comments', 'nosfirnews' ); ?>">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </span>
                        <?php comments_popup_link( 
                            esc_html__( 'Leave a comment', 'nosfirnews' ),
                            esc_html__( '1 Comment', 'nosfirnews' ),
                            esc_html__( '% Comments', 'nosfirnews' )
                        ); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ( get_edit_post_link() ) : ?>
                    <div class="edit-link">
                        <?php edit_post_link( 
                            esc_html__( 'Edit', 'nosfirnews' ),
                            '<span class="edit-link-wrapper">',
                            '</span>'
                        ); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div><!-- .entry-meta -->
    </header><!-- .entry-header -->

    <?php if ( has_post_thumbnail() ) : ?>
        <div class="post-thumbnail" itemprop="image">
            <?php nosfirnews_post_thumbnail(); ?>
        </div>
    <?php endif; ?>

    <div class="entry-content" itemprop="articleBody">
        <?php
        the_content();

        wp_link_pages(
            array(
                'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Post pages', 'nosfirnews' ) . '"><span class="page-links-title">' . esc_html__( 'Pages:', 'nosfirnews' ) . '</span>',
                'after'  => '</nav>',
                'link_before' => '<span class="page-number">',
                'link_after'  => '</span>',
            )
        );
        ?>
    </div><!-- .entry-content -->

    <footer class="entry-footer">
        <?php nosfirnews_entry_footer(); ?>
        
        <!-- Hidden microdata -->
        <div class="hidden-microdata">
            <span itemprop="author" itemscope itemtype="https://schema.org/Person">
                <span itemprop="name"><?php the_author(); ?></span>
            </span>
            <time itemprop="datePublished" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                <?php echo esc_html( get_the_date() ); ?>
            </time>
            <time itemprop="dateModified" datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>">
                <?php echo esc_html( get_the_modified_date() ); ?>
            </time>
            <span itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
                <span itemprop="name"><?php bloginfo( 'name' ); ?></span>
                <span itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
                    <span itemprop="url"><?php echo esc_url( get_site_icon_url() ); ?></span>
                </span>
            </span>
        </div>
    </footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->