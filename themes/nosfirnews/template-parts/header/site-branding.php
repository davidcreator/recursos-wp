<?php
/**
 * Template part for displaying site branding
 *
 * @package NosfirNews
 * @since 2.0.0
 */

// Get customizer options
$display_site_title = get_theme_mod( 'nosfirnews_display_site_title', true );
$display_site_description = get_theme_mod( 'nosfirnews_display_site_description', true );
$logo_only_mode = get_theme_mod( 'nosfirnews_logo_only_mode', false );

// If logo only mode is enabled, override individual settings
if ( $logo_only_mode ) {
    $display_site_title = false;
    $display_site_description = false;
}

?>

<div class="site-branding" itemscope itemtype="https://schema.org/Organization">
    
    <?php if ( has_custom_logo() ) : ?>
        <div class="site-logo" itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
            <?php 
            $custom_logo_id = get_theme_mod( 'custom_logo' );
            $logo = wp_get_attachment_image_src( $custom_logo_id, 'full' );
            if ( $logo ) {
                printf(
                    '<a href="%1$s" rel="home" aria-label="%2$s">%3$s</a>',
                    esc_url( home_url( '/' ) ),
                    esc_attr( sprintf( __( 'Ir para página inicial de %s', 'nosfirnews' ), get_bloginfo( 'name' ) ) ),
                    wp_get_attachment_image( 
                        $custom_logo_id, 
                        'full', 
                        false, 
                        array(
                            'class' => 'custom-logo',
                            'itemprop' => 'url',
                            'alt' => get_bloginfo( 'name' )
                        )
                    )
                );
            } else {
                the_custom_logo();
            }
            ?>
            <meta itemprop="url" content="<?php echo esc_url( wp_get_attachment_url( $custom_logo_id ) ); ?>">
        </div>
    <?php endif; ?>
    
    <?php if ( $display_site_title || $display_site_description || is_customize_preview() ) : ?>
        <div class="site-identity" <?php echo ( ! $display_site_title && ! $display_site_description && ! is_customize_preview() ) ? 'style="display: none;"' : ''; ?>>
            
            <?php if ( $display_site_title || is_customize_preview() ) : ?>
                <div class="site-title-wrapper" <?php echo ( ! $display_site_title && ! is_customize_preview() ) ? 'style="display: none;"' : ''; ?>>
                    <?php if ( is_front_page() && is_home() ) : ?>
                        <h1 class="site-title" itemprop="name">
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" 
                               rel="home" 
                               itemprop="url"
                               aria-label="<?php echo esc_attr( sprintf( __( 'Ir para página inicial de %s', 'nosfirnews' ), get_bloginfo( 'name' ) ) ); ?>">
                                <?php bloginfo( 'name' ); ?>
                            </a>
                        </h1>
                    <?php else : ?>
                        <p class="site-title" itemprop="name">
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" 
                               rel="home" 
                               itemprop="url"
                               aria-label="<?php echo esc_attr( sprintf( __( 'Ir para página inicial de %s', 'nosfirnews' ), get_bloginfo( 'name' ) ) ); ?>">
                                <?php bloginfo( 'name' ); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php if ( $display_site_description || is_customize_preview() ) : ?>
                <?php
                $nosfirnews_description = get_bloginfo( 'description', 'display' );
                if ( $nosfirnews_description || is_customize_preview() ) :
                    ?>
                    <div class="site-description-wrapper" <?php echo ( ! $display_site_description && ! is_customize_preview() ) ? 'style="display: none;"' : ''; ?>>
                        <p class="site-description" itemprop="description">
                            <?php echo $nosfirnews_description; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>
                        </p>
                    </div>
                    <?php
                endif;
                ?>
            <?php endif; ?>
            
        </div><!-- .site-identity -->
    <?php endif; ?>
    
    <!-- Hidden microdata for organization -->
    <div style="display: none;">
        <span itemprop="url"><?php echo esc_url( home_url( '/' ) ); ?></span>
        <?php if ( ! $display_site_title ) : ?>
            <span itemprop="name"><?php bloginfo( 'name' ); ?></span>
        <?php endif; ?>
        <?php if ( ! $display_site_description ) : ?>
            <span itemprop="description"><?php bloginfo( 'description' ); ?></span>
        <?php endif; ?>
    </div>
    
    <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
        <span class="screen-reader-text"><?php esc_html_e( 'Menu', 'nosfirnews' ); ?></span>
        <span></span>
    </button>
    
</div><!-- .site-branding -->