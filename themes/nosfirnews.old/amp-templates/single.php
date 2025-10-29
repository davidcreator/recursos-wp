<?php
/**
 * AMP Single Post Template
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
    
    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "NewsArticle",
        "headline": "<?php echo esc_js( get_the_title() ); ?>",
        "description": "<?php echo esc_js( wp_trim_words( get_the_excerpt(), 20 ) ); ?>",
        "datePublished": "<?php echo esc_js( get_the_date( 'c' ) ); ?>",
        "dateModified": "<?php echo esc_js( get_the_modified_date( 'c' ) ); ?>",
        "author": {
            "@type": "Person",
            "name": "<?php echo esc_js( get_the_author() ); ?>",
            "url": "<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"
        },
        "publisher": {
            "@type": "Organization",
            "name": "<?php echo esc_js( get_bloginfo( 'name' ) ); ?>",
            "url": "<?php echo esc_url( home_url() ); ?>"
            <?php
            $custom_logo_id = get_theme_mod( 'custom_logo' );
            if ( $custom_logo_id ) {
                $logo_data = wp_get_attachment_image_src( $custom_logo_id, 'full' );
                ?>
                ,"logo": {
                    "@type": "ImageObject",
                    "url": "<?php echo esc_url( $logo_data[0] ); ?>",
                    "width": <?php echo intval( $logo_data[1] ); ?>,
                    "height": <?php echo intval( $logo_data[2] ); ?>
                }
                <?php
            }
            ?>
        }
        <?php
        if ( has_post_thumbnail() ) {
            $image_data = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
            ?>
            ,"image": {
                "@type": "ImageObject",
                "url": "<?php echo esc_url( $image_data[0] ); ?>",
                "width": <?php echo intval( $image_data[1] ); ?>,
                "height": <?php echo intval( $image_data[2] ); ?>
            }
            <?php
        }
        ?>
        ,"mainEntityOfPage": "<?php echo esc_url( get_permalink() ); ?>"
    }
    </script>
    
    <?php do_action( 'amp_post_template_head', $this ); ?>
</head>

<body <?php body_class( 'amp-wp' ); ?>>

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
    <article class="amp-wp-article">
        
        <!-- Featured Image -->
        <?php if ( has_post_thumbnail() ) : ?>
            <div class="amp-wp-article-featured-image">
                <?php
                $image_data = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
                $image_alt = get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );
                ?>
                <amp-img src="<?php echo esc_url( $image_data[0] ); ?>" 
                         width="<?php echo intval( $image_data[1] ); ?>" 
                         height="<?php echo intval( $image_data[2] ); ?>" 
                         layout="responsive"
                         alt="<?php echo esc_attr( $image_alt ? $image_alt : get_the_title() ); ?>">
                </amp-img>
            </div>
        <?php endif; ?>
        
        <!-- Article Header -->
        <header class="amp-wp-article-header">
            <h1 class="amp-wp-title"><?php echo esc_html( get_the_title() ); ?></h1>
            
            <div class="amp-wp-meta">
                <span class="amp-wp-author">
                    <?php _e( 'Por', 'nosfirnews' ); ?> 
                    <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
                        <?php echo esc_html( get_the_author() ); ?>
                    </a>
                </span>
                
                <span class="amp-wp-date">
                    <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                        <?php echo esc_html( get_the_date() ); ?>
                    </time>
                </span>
                
                <?php if ( get_the_modified_date() !== get_the_date() ) : ?>
                    <span class="amp-wp-modified">
                        <?php _e( 'Atualizado em', 'nosfirnews' ); ?> 
                        <time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>">
                            <?php echo esc_html( get_the_modified_date() ); ?>
                        </time>
                    </span>
                <?php endif; ?>
                
                <?php
                $categories = get_the_category();
                if ( $categories ) :
                ?>
                    <span class="amp-wp-categories">
                        <?php _e( 'Categoria:', 'nosfirnews' ); ?> 
                        <?php
                        $cat_links = array();
                        foreach ( $categories as $category ) {
                            $cat_links[] = '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . esc_html( $category->name ) . '</a>';
                        }
                        echo implode( ', ', $cat_links );
                        ?>
                    </span>
                <?php endif; ?>
            </div>
        </header>
        
        <!-- Article Content -->
        <div class="amp-wp-article-content">
            <?php
            $content = get_the_content();
            $content = apply_filters( 'the_content', $content );
            
            // Convert images to amp-img
            $content = preg_replace_callback(
                '/<img([^>]+)>/i',
                function( $matches ) {
                    $img_tag = $matches[0];
                    
                    // Extract attributes
                    preg_match('/src=["\']([^"\']+)["\']/', $img_tag, $src_matches);
                    preg_match('/alt=["\']([^"\']*)["\']/', $img_tag, $alt_matches);
                    preg_match('/width=["\']([^"\']+)["\']/', $img_tag, $width_matches);
                    preg_match('/height=["\']([^"\']+)["\']/', $img_tag, $height_matches);
                    
                    $src = isset( $src_matches[1] ) ? $src_matches[1] : '';
                    $alt = isset( $alt_matches[1] ) ? $alt_matches[1] : '';
                    $width = isset( $width_matches[1] ) ? intval( $width_matches[1] ) : 800;
                    $height = isset( $height_matches[1] ) ? intval( $height_matches[1] ) : 600;
                    
                    if ( ! $width || ! $height ) {
                        $width = 800;
                        $height = 600;
                    }
                    
                    return sprintf(
                        '<amp-img src="%s" width="%d" height="%d" layout="responsive" alt="%s"></amp-img>',
                        esc_url( $src ),
                        $width,
                        $height,
                        esc_attr( $alt )
                    );
                },
                $content
            );
            
            echo $content;
            ?>
        </div>
        
        <!-- Social Sharing -->
        <div class="amp-social-share-container">
            <h3><?php _e( 'Compartilhar', 'nosfirnews' ); ?></h3>
            <amp-social-share type="facebook" width="40" height="40" 
                              data-param-app_id="<?php echo esc_attr( get_theme_mod( 'nosfirnews_facebook_app_id', '' ) ); ?>">
            </amp-social-share>
            <amp-social-share type="twitter" width="40" height="40"></amp-social-share>
            <amp-social-share type="whatsapp" width="40" height="40"></amp-social-share>
            <amp-social-share type="email" width="40" height="40"></amp-social-share>
            <amp-social-share type="linkedin" width="40" height="40"></amp-social-share>
        </div>
        
        <!-- Tags -->
        <?php
        $tags = get_the_tags();
        if ( $tags ) :
        ?>
            <div class="amp-wp-tags">
                <h3><?php _e( 'Tags', 'nosfirnews' ); ?></h3>
                <div class="amp-wp-tag-list">
                    <?php
                    foreach ( $tags as $tag ) {
                        echo '<a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '" class="amp-wp-tag">' . esc_html( $tag->name ) . '</a> ';
                    }
                    ?>
                </div>
            </div>
        <?php endif; ?>
        
    </article>
    
    <!-- Related Posts -->
    <?php
    $related_posts = get_posts( array(
        'post_type' => 'post',
        'posts_per_page' => 3,
        'post__not_in' => array( get_the_ID() ),
        'category__in' => wp_get_post_categories( get_the_ID() ),
        'orderby' => 'rand',
    ) );
    
    if ( $related_posts ) :
    ?>
        <section class="amp-wp-related-posts">
            <h3><?php _e( 'Posts Relacionados', 'nosfirnews' ); ?></h3>
            <div class="amp-wp-related-grid">
                <?php foreach ( $related_posts as $related_post ) : ?>
                    <article class="amp-wp-related-item">
                        <?php if ( has_post_thumbnail( $related_post->ID ) ) : ?>
                            <div class="amp-wp-related-image">
                                <?php
                                $image_data = wp_get_attachment_image_src( get_post_thumbnail_id( $related_post->ID ), 'medium' );
                                ?>
                                <a href="<?php echo esc_url( get_permalink( $related_post->ID ) ); ?>">
                                    <amp-img src="<?php echo esc_url( $image_data[0] ); ?>" 
                                             width="<?php echo intval( $image_data[1] ); ?>" 
                                             height="<?php echo intval( $image_data[2] ); ?>" 
                                             layout="responsive"
                                             alt="<?php echo esc_attr( get_the_title( $related_post->ID ) ); ?>">
                                    </amp-img>
                                </a>
                            </div>
                        <?php endif; ?>
                        <h4 class="amp-wp-related-title">
                            <a href="<?php echo esc_url( get_permalink( $related_post->ID ) ); ?>">
                                <?php echo esc_html( get_the_title( $related_post->ID ) ); ?>
                            </a>
                        </h4>
                        <div class="amp-wp-related-meta">
                            <time datetime="<?php echo esc_attr( get_the_date( 'c', $related_post->ID ) ); ?>">
                                <?php echo esc_html( get_the_date( '', $related_post->ID ) ); ?>
                            </time>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
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