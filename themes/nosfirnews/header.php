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
    <?php $align = get_theme_mod( 'nn_logo_alignment', 'left' ); $align_class = 'header-inner ' . ( in_array( $align, [ 'left','center','right' ], true ) ? 'logo-' . $align : 'logo-left' ); ?>
    <div class="container <?php echo esc_attr( $align_class ); ?>">
        <button class="nav-toggle" aria-controls="mobile-menu" aria-expanded="false" aria-label="Abrir menu">&#9776;</button>
        <div class="site-branding">
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
        <?php $location = get_theme_mod( 'nosfirnews_primary_menu_location', 'primary' ); ?>
        <nav class="main-navigation site-nav" role="navigation" aria-label="Primary">
            <?php wp_nav_menu( [ 'theme_location' => $location, 'menu_id' => 'primary-menu', 'menu_class' => 'nav-menu', 'container' => false, 'depth' => 3, 'fallback_cb' => 'wp_page_menu' ] ); ?>
        </nav>
    </div>
    <?php $mobile_location = get_theme_mod( 'nn_mobile_menu_location', 'mobile' ); ?>
    <div id="mobile-menu" class="nn-mobile-drawer" aria-hidden="true">
        <?php wp_nav_menu( [ 'theme_location' => $mobile_location, 'menu_id' => 'mobile-menu-list', 'menu_class' => 'mobile-nav-menu', 'container' => false, 'depth' => 3, 'fallback_cb' => 'wp_page_menu' ] ); ?>
    </div>
</header>
<div id="content" class="site-content">
