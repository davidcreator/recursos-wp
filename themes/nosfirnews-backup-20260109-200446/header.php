<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header" role="banner">
    <?php $logo_align = get_theme_mod( 'nn_logo_alignment', 'left' ); $nav_align = get_theme_mod( 'nn_nav_alignment', 'right' ); ?>
    <?php $order = get_theme_mod( 'nn_header_order', 'logo_first' ); ?>
    <div class="container header-inner <?php echo 'toggle-pos-' . esc_attr( in_array( $nav_align, [ 'left','center','right' ], true ) ? $nav_align : 'right' ); ?>">
        <button class="nav-toggle" aria-controls="mobile-menu" aria-expanded="false" aria-label="Abrir menu">&#9776;</button>
        <?php if ( $order === 'nav_first' ) { ?>
        <?php $location = get_theme_mod( 'nosfirnews_primary_menu_location', 'primary' ); ?>
        <nav class="main-navigation site-nav <?php echo 'nav-pos-' . esc_attr( in_array( $nav_align, [ 'left','center','right' ], true ) ? $nav_align : 'right' ); ?>" role="navigation" aria-label="Primary">
            <?php wp_nav_menu( [ 'theme_location' => $location, 'menu_id' => 'primary-menu', 'menu_class' => 'nav-menu', 'container' => false, 'depth' => 3, 'fallback_cb' => 'wp_page_menu' ] ); ?>
        </nav>
        <?php } ?>
        <div class="site-branding <?php echo 'branding-pos-' . esc_attr( in_array( $logo_align, [ 'left','center','right' ], true ) ? $logo_align : 'left' ); ?>">
            <?php if ( function_exists( 'the_custom_logo' ) ) the_custom_logo(); ?>
            <div>
                <?php if ( is_front_page() && is_home() ) : ?>
                    <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
                <?php else : ?>
                    <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></p>
                <?php endif; ?>
                <p class="site-description"><?php bloginfo( 'description' ); ?></p>
            </div>
        </div>
        <?php if ( $order === 'logo_first' ) { ?>
        <?php $location = get_theme_mod( 'nosfirnews_primary_menu_location', 'primary' ); ?>
        <nav class="main-navigation site-nav <?php echo 'nav-pos-' . esc_attr( in_array( $nav_align, [ 'left','center','right' ], true ) ? $nav_align : 'right' ); ?>" role="navigation" aria-label="Primary">
            <?php wp_nav_menu( [ 'theme_location' => $location, 'menu_id' => 'primary-menu', 'menu_class' => 'nav-menu', 'container' => false, 'depth' => 3, 'fallback_cb' => 'wp_page_menu' ] ); ?>
        </nav>
        <?php } ?>
    </div>
    <?php $mobile_location = get_theme_mod( 'nn_mobile_menu_location', 'mobile' ); ?>
    <div id="mobile-menu" class="nn-mobile-drawer" aria-hidden="true">
        <div class="mobile-nav-menu" role="dialog" aria-modal="true">
            <button class="drawer-close" aria-label="Fechar">&times;</button>
            <?php wp_nav_menu( [ 'theme_location' => $mobile_location, 'menu_id' => 'mobile-menu-list', 'menu_class' => 'mobile-nav', 'container' => false, 'depth' => 3, 'fallback_cb' => 'wp_page_menu' ] ); ?>
        </div>
    </div>
</header>
<div id="content" class="site-content">
