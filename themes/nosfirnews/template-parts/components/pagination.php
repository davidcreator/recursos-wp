<?php
/**
 * Pagination Component
 *
 * @package NosfirNews
 * @since 1.0.0
 */

// Get pagination data
$pagination = nosfirnews_get_pagination();

if ( empty( $pagination ) ) {
    return;
}
?>

<nav class="pagination-wrapper" aria-label="<?php esc_attr_e( 'Posts Navigation', 'nosfirnews' ); ?>">
    <div class="container">
        <div class="pagination">
            <?php if ( $pagination['prev_url'] ) : ?>
                <a href="<?php echo esc_url( $pagination['prev_url'] ); ?>" class="pagination-link pagination-prev" rel="prev">
                    <span class="pagination-icon" aria-hidden="true">&laquo;</span>
                    <span class="pagination-text"><?php esc_html_e( 'Previous', 'nosfirnews' ); ?></span>
                </a>
            <?php endif; ?>

            <div class="pagination-numbers">
                <?php foreach ( $pagination['pages'] as $page ) : ?>
                    <?php if ( $page['is_current'] ) : ?>
                        <span class="pagination-link pagination-current" aria-current="page">
                            <?php echo esc_html( $page['number'] ); ?>
                        </span>
                    <?php elseif ( $page['is_dots'] ) : ?>
                        <span class="pagination-dots" aria-hidden="true">&hellip;</span>
                    <?php else : ?>
                        <a href="<?php echo esc_url( $page['url'] ); ?>" class="pagination-link">
                            <?php echo esc_html( $page['number'] ); ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <?php if ( $pagination['next_url'] ) : ?>
                <a href="<?php echo esc_url( $pagination['next_url'] ); ?>" class="pagination-link pagination-next" rel="next">
                    <span class="pagination-text"><?php esc_html_e( 'Next', 'nosfirnews' ); ?></span>
                    <span class="pagination-icon" aria-hidden="true">&raquo;</span>
                </a>
            <?php endif; ?>
        </div>

        <?php if ( $pagination['total_pages'] > 1 ) : ?>
            <div class="pagination-info">
                <span class="pagination-info-text">
                    <?php
                    printf(
                        /* translators: 1: current page number, 2: total pages */
                        esc_html__( 'Page %1$s of %2$s', 'nosfirnews' ),
                        '<span class="current-page">' . esc_html( $pagination['current_page'] ) . '</span>',
                        '<span class="total-pages">' . esc_html( $pagination['total_pages'] ) . '</span>'
                    );
                    ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
</nav>

<?php
/**
 * Get pagination data
 *
 * @return array|false Pagination data or false if no pagination needed
 */
function nosfirnews_get_pagination() {
    global $wp_query;

    // Check if pagination is needed
    if ( $wp_query->max_num_pages <= 1 ) {
        return false;
    }

    $current_page = max( 1, get_query_var( 'paged' ) );
    $total_pages = $wp_query->max_num_pages;
    $range = 2; // Number of pages to show on each side of current page

    $pagination = array(
        'current_page' => $current_page,
        'total_pages' => $total_pages,
        'prev_url' => ( $current_page > 1 ) ? get_pagenum_link( $current_page - 1 ) : false,
        'next_url' => ( $current_page < $total_pages ) ? get_pagenum_link( $current_page + 1 ) : false,
        'pages' => array()
    );

    // Always show first page
    if ( $current_page > $range + 2 ) {
        $pagination['pages'][] = array(
            'number' => 1,
            'url' => get_pagenum_link( 1 ),
            'is_current' => false,
            'is_dots' => false
        );

        // Add dots if there's a gap
        if ( $current_page > $range + 3 ) {
            $pagination['pages'][] = array(
                'number' => '...',
                'url' => '',
                'is_current' => false,
                'is_dots' => true
            );
        }
    }

    // Pages around current page
    for ( $i = max( 1, $current_page - $range ); $i <= min( $total_pages, $current_page + $range ); $i++ ) {
        $pagination['pages'][] = array(
            'number' => $i,
            'url' => get_pagenum_link( $i ),
            'is_current' => ( $i === $current_page ),
            'is_dots' => false
        );
    }

    // Always show last page
    if ( $current_page < $total_pages - $range - 1 ) {
        // Add dots if there's a gap
        if ( $current_page < $total_pages - $range - 2 ) {
            $pagination['pages'][] = array(
                'number' => '...',
                'url' => '',
                'is_current' => false,
                'is_dots' => true
            );
        }

        $pagination['pages'][] = array(
            'number' => $total_pages,
            'url' => get_pagenum_link( $total_pages ),
            'is_current' => false,
            'is_dots' => false
        );
    }

    return apply_filters( 'nosfirnews_pagination', $pagination );
}