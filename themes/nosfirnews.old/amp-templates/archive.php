<?php
/**
 * AMP Archive Template
 * 
 * @package NosfirNews
 * @since 2.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$this->load_parts( array( 'html-start' ) );
?>

<head>
    <?php $this->load_parts( array( 'meta-viewport', 'meta-charset' ) ); ?>
    
    <title><?php echo esc_html( $this->get( 'document_title' ) ); ?></title>
    
    <?php $this->load_parts( array( 'meta-author', 'meta-canonical' ) ); ?>
    
    <!-- AMP Meta Tags -->
    <meta name="amp-google-client-id-api" content="googleanalytics">
    <meta name="amp-link-variable-allowed-origin" content="<?php echo esc_url( home_url() ); ?>">
    
    <!-- Theme Color -->
    <meta name="theme-color" content="<?php echo esc_attr( get_theme_mod( 'nosfirnews_primary_color', '#2196F3' ) ); ?>">
    
    <?php do_action( 'amp_post_template_head', $this ); ?>
</head>

<body <?php body_class( 'amp-wp amp-archive' ); ?>>

<!-- Header -->
<header class="amp-wp-header">
    <div class="amp-wp-site-icon">
        <?php
        $custom_logo_id = get_theme_mod( 'nosfirnews_amp_logo' );
        if ( ! $custom_logo_id ) {
            $custom_logo_id = get_theme_mod( 'custom_logo' );
        }
        
        if ( $custom_logo_id ) {
            $logo_data = wp_get_attachment_image_src( $custom_logo_id, 'medium' );
            ?>
            <amp-img src="<?php echo esc_url( $logo_data[0] ); ?>" 
                     width="<?php echo intval( min( $logo_data[1], 200 ) ); ?>" 
                     height="<?php echo intval( min( $logo_data[2], 60 ) ); ?>" 
                     alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
            </amp-img>
            <?php
        }
        ?>
    </div>
    <h1 class="amp-wp-site-title">
        <a href="<?php echo esc_url( home_url() ); ?>" rel="home">
            <?php echo esc_html( get_bloginfo( 'name' ) ); ?>
        </a>
    </h1>
    <?php if ( get_bloginfo( 'description' ) ) : ?>
        <p class="amp-wp-site-description"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
    <?php endif; ?>
</header>

<!-- Navigation -->
<nav class="amp-wp-nav">
    <?php
    $menu_items = wp_get_nav_menu_items( 'primary' );
    if ( $menu_items ) :
    ?>
        <ul>
            <li><a href="<?php echo esc_url( home_url() ); ?>"><?php _e( 'Home', 'nosfirnews' ); ?></a></li>
            <?php
            foreach ( $menu_items as $item ) :
                if ( $item->menu_item_parent == 0 ) : // Only top-level items
            ?>
                <li><a href="<?php echo esc_url( $item->url ); ?>"><?php echo esc_html( $item->title ); ?></a></li>
            <?php
                endif;
            endforeach;
            ?>
        </ul>
    <?php endif; ?>
</nav>

<!-- Main Content -->
<main class="amp-wp-content">
    
    <!-- Archive Header -->
    <header class="amp-wp-archive-header">
        <?php
        if ( is_category() ) {
            $title = single_cat_title( '', false );
            $description = category_description();
        } elseif ( is_tag() ) {
            $title = single_tag_title( '', false );
            $description = tag_description();
        } elseif ( is_author() ) {
            $title = get_the_author();
            $description = get_the_author_meta( 'description' );
        } elseif ( is_date() ) {
            if ( is_year() ) {
                $title = get_the_date( 'Y' );
            } elseif ( is_month() ) {
                $title = get_the_date( 'F Y' );
            } else {
                $title = get_the_date();
            }
            $description = '';
        } else {
            $title = get_the_archive_title();
            $description = get_the_archive_description();
        }
        ?>
        
        <h1 class="amp-wp-archive-title"><?php echo esc_html( $title ); ?></h1>
        
        <?php if ( $description ) : ?>
            <div class="amp-wp-archive-description">
                <?php echo wp_kses_post( $description ); ?>
            </div>
        <?php endif; ?>
    </header>
    
    <!-- Posts Grid -->
    <div class="amp-wp-posts-grid">
        <?php
        global $wp_query;
        $posts = $wp_query->posts;
        
        if ( $posts ) :
            foreach ( $posts as $post ) :
                setup_postdata( $post );
        ?>
            <article class="amp-wp-post-item">
                
                <!-- Featured Image -->
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="amp-wp-post-image">
                        <?php
                        $image_data = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium' );
                        $image_alt = get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );
                        ?>
                        <a href="<?php echo esc_url( get_permalink() ); ?>">
                            <amp-img src="<?php echo esc_url( $image_data[0] ); ?>" 
                                     width="<?php echo intval( $image_data[1] ); ?>" 
                                     height="<?php echo intval( $image_data[2] ); ?>" 
                                     layout="responsive"
                                     alt="<?php echo esc_attr( $image_alt ? $image_alt : get_the_title() ); ?>">
                            </amp-img>
                        </a>
                    </div>
                <?php endif; ?>
                
                <!-- Post Content -->
                <div class="amp-wp-post-content">
                    
                    <!-- Categories -->
                    <?php
                    $categories = get_the_category();
                    if ( $categories ) :
                    ?>
                        <div class="amp-wp-post-categories">
                            <?php
                            foreach ( $categories as $category ) {
                                echo '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" class="amp-wp-post-category">' . esc_html( $category->name ) . '</a>';
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Title -->
                    <h2 class="amp-wp-post-title">
                        <a href="<?php echo esc_url( get_permalink() ); ?>">
                            <?php echo esc_html( get_the_title() ); ?>
                        </a>
                    </h2>
                    
                    <!-- Excerpt -->
                    <div class="amp-wp-post-excerpt">
                        <?php echo wp_trim_words( get_the_excerpt(), 20 ); ?>
                    </div>
                    
                    <!-- Meta -->
                    <div class="amp-wp-post-meta">
                        <span class="amp-wp-post-author">
                            <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
                                <?php echo esc_html( get_the_author() ); ?>
                            </a>
                        </span>
                        
                        <span class="amp-wp-post-date">
                            <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                                <?php echo esc_html( get_the_date() ); ?>
                            </time>
                        </span>
                        
                        <?php if ( comments_open() || get_comments_number() ) : ?>
                            <span class="amp-wp-post-comments">
                                <a href="<?php echo esc_url( get_permalink() ); ?>#comments">
                                    <?php
                                    $comments_number = get_comments_number();
                                    if ( $comments_number == 0 ) {
                                        _e( 'Sem comentários', 'nosfirnews' );
                                    } elseif ( $comments_number == 1 ) {
                                        _e( '1 comentário', 'nosfirnews' );
                                    } else {
                                        printf( _n( '%s comentário', '%s comentários', $comments_number, 'nosfirnews' ), $comments_number );
                                    }
                                    ?>
                                </a>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Read More -->
                    <div class="amp-wp-post-read-more">
                        <a href="<?php echo esc_url( get_permalink() ); ?>" class="amp-wp-read-more-link">
                            <?php _e( 'Leia mais', 'nosfirnews' ); ?>
                        </a>
                    </div>
                    
                </div>
                
            </article>
        <?php
            endforeach;
            wp_reset_postdata();
        else :
        ?>
            <div class="amp-wp-no-posts">
                <h2><?php _e( 'Nenhum post encontrado', 'nosfirnews' ); ?></h2>
                <p><?php _e( 'Não há posts para exibir nesta categoria/tag.', 'nosfirnews' ); ?></p>
                <a href="<?php echo esc_url( home_url() ); ?>" class="amp-wp-back-home">
                    <?php _e( 'Voltar ao início', 'nosfirnews' ); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Pagination -->
    <?php
    global $wp_query;
    $total_pages = $wp_query->max_num_pages;
    $current_page = max( 1, get_query_var( 'paged' ) );
    
    if ( $total_pages > 1 ) :
    ?>
        <nav class="amp-wp-pagination">
            <?php if ( $current_page > 1 ) : ?>
                <a href="<?php echo esc_url( get_previous_posts_page_link() ); ?>" class="amp-wp-pagination-prev">
                    <?php _e( '← Anterior', 'nosfirnews' ); ?>
                </a>
            <?php endif; ?>
            
            <span class="amp-wp-pagination-info">
                <?php printf( __( 'Página %d de %d', 'nosfirnews' ), $current_page, $total_pages ); ?>
            </span>
            
            <?php if ( $current_page < $total_pages ) : ?>
                <a href="<?php echo esc_url( get_next_posts_page_link() ); ?>" class="amp-wp-pagination-next">
                    <?php _e( 'Próxima →', 'nosfirnews' ); ?>
                </a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
    
</main>

<!-- Footer -->
<footer class="amp-wp-footer">
    <div class="amp-wp-footer-content">
        <p>&copy; <?php echo date( 'Y' ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>. <?php _e( 'Todos os direitos reservados.', 'nosfirnews' ); ?></p>
        <p>
            <a href="<?php echo esc_url( home_url() ); ?>"><?php _e( 'Voltar ao site', 'nosfirnews' ); ?></a> | 
            <a href="<?php echo esc_url( get_privacy_policy_url() ); ?>"><?php _e( 'Política de Privacidade', 'nosfirnews' ); ?></a>
        </p>
    </div>
</footer>

<?php do_action( 'amp_post_template_footer', $this ); ?>

</body>
</html>