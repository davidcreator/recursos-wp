<?php
/**
 * Post Meta Component
 *
 * @package NosfirNews
 * @since 1.0.0
 */

// Get post meta settings
$hide_author = get_post_meta( get_the_ID(), '_nosfirnews_hide_author', true );
$hide_date = get_post_meta( get_the_ID(), '_nosfirnews_hide_date', true );
$custom_reading_time = get_post_meta( get_the_ID(), '_nosfirnews_custom_reading_time', true );

// Check if we should show any meta
$show_author = ! $hide_author;
$show_date = ! $hide_date;
$show_categories = true; // Always show categories unless specifically disabled
$show_reading_time = true; // Always show reading time unless specifically disabled

// If nothing to show, return early
if ( ! $show_author && ! $show_date && ! $show_categories && ! $show_reading_time ) {
    return;
}
?>

<div class="post-meta">
    <?php if ( $show_author ) : ?>
        <div class="post-meta-item post-author">
            <span class="post-meta-icon" aria-hidden="true">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
            </span>
            <span class="post-meta-label"><?php esc_html_e( 'By', 'nosfirnews' ); ?></span>
            <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" class="post-author-link" rel="author">
                <?php echo esc_html( get_the_author() ); ?>
            </a>
        </div>
    <?php endif; ?>

    <?php if ( $show_date ) : ?>
        <div class="post-meta-item post-date">
            <span class="post-meta-icon" aria-hidden="true">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                </svg>
            </span>
            <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" class="post-date-link">
                <?php echo esc_html( get_the_date() ); ?>
            </time>
        </div>
    <?php endif; ?>

    <?php if ( $show_categories && has_category() ) : ?>
        <div class="post-meta-item post-categories">
            <span class="post-meta-icon" aria-hidden="true">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.63 5.84C17.27 5.33 16.67 5 16 5L5 5.01C3.9 5.01 3 5.9 3 7v10c0 1.1.9 1.99 2 1.99L16 19c.67 0 1.27-.33 1.63-.84L22 12l-4.37-6.16z"/>
                </svg>
            </span>
            <span class="post-meta-label"><?php esc_html_e( 'In', 'nosfirnews' ); ?></span>
            <span class="post-categories-list">
                <?php
                $categories = get_the_category();
                $category_links = array();
                
                foreach ( $categories as $category ) {
                    $category_links[] = '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" class="post-category-link">' . esc_html( $category->name ) . '</a>';
                }
                
                echo implode( ', ', $category_links );
                ?>
            </span>
        </div>
    <?php endif; ?>

    <?php if ( $show_reading_time ) : ?>
        <div class="post-meta-item post-reading-time">
            <span class="post-meta-icon" aria-hidden="true">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
            </span>
            <span class="reading-time-text">
                <?php
                if ( $custom_reading_time ) {
                    echo esc_html( $custom_reading_time );
                } else {
                    $reading_time = nosfirnews_calculate_reading_time( get_the_content() );
                    printf(
                        /* translators: %s: reading time in minutes */
                        esc_html__( '%s min read', 'nosfirnews' ),
                        $reading_time
                    );
                }
                ?>
            </span>
        </div>
    <?php endif; ?>

    <?php if ( has_tag() ) : ?>
        <div class="post-meta-item post-tags">
            <span class="post-meta-icon" aria-hidden="true">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7z"/>
                </svg>
            </span>
            <span class="post-tags-list">
                <?php
                $tags = get_the_tags();
                $tag_links = array();
                
                foreach ( $tags as $tag ) {
                    $tag_links[] = '<a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '" class="post-tag-link">' . esc_html( $tag->name ) . '</a>';
                }
                
                echo implode( ', ', $tag_links );
                ?>
            </span>
        </div>
    <?php endif; ?>

    <?php
    // Show comments count if comments are open or there are comments
    if ( comments_open() || get_comments_number() ) :
    ?>
        <div class="post-meta-item post-comments">
            <span class="post-meta-icon" aria-hidden="true">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M21 6h-2v9H6v2c0 .55.45 1 1 1h11l4 4V7c0-.55-.45-1-1-1zm-4 6V3c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v14l4-4h11c.55 0 1-.45 1-1z"/>
                </svg>
            </span>
            <a href="<?php echo esc_url( get_comments_link() ); ?>" class="post-comments-link">
                <?php
                $comments_number = get_comments_number();
                if ( $comments_number == 0 ) {
                    esc_html_e( 'No Comments', 'nosfirnews' );
                } elseif ( $comments_number == 1 ) {
                    esc_html_e( '1 Comment', 'nosfirnews' );
                } else {
                    printf(
                        /* translators: %s: number of comments */
                        esc_html__( '%s Comments', 'nosfirnews' ),
                        number_format_i18n( $comments_number )
                    );
                }
                ?>
            </a>
        </div>
    <?php endif; ?>
</div>