<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package NosfirNews
 * @since 2.0.0
 */

?><!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="format-detection" content="telephone=no">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <!-- DNS Prefetch for performance -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    
    <!-- Preconnect for critical resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Theme color for mobile browsers -->
    <meta name="theme-color" content="#2c3e50">
    <meta name="msapplication-TileColor" content="#2c3e50">
    
    <!-- Remove no-js class with JavaScript -->
    <script>document.documentElement.classList.remove('no-js');</script>
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?> itemscope itemtype="https://schema.org/WebPage">

<?php wp_body_open(); ?>

<div id="page" class="site" itemscope itemtype="https://schema.org/WebSite">
    <!-- Skip links for accessibility -->
    <div class="skip-links">
        <a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to main content', 'nosfirnews' ); ?></a>
        <a class="skip-link screen-reader-text" href="#site-navigation"><?php esc_html_e( 'Skip to navigation', 'nosfirnews' ); ?></a>
        <?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
            <a class="skip-link screen-reader-text" href="#secondary"><?php esc_html_e( 'Skip to sidebar', 'nosfirnews' ); ?></a>
        <?php endif; ?>
        <a class="skip-link screen-reader-text" href="#colophon"><?php esc_html_e( 'Skip to footer', 'nosfirnews' ); ?></a>
    </div>
    
    <header id="masthead" class="site-header" role="banner" itemscope itemtype="https://schema.org/WPHeader">
        <div class="container">
            
            <?php get_template_part( 'template-parts/header/site-branding' ); ?>
            
        </div><!-- .container -->
        
        <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'nosfirnews' ); ?>" itemscope itemtype="https://schema.org/SiteNavigationElement">
            <div class="container">
                
                <?php get_template_part( 'template-parts/header/navigation' ); ?>
                
            </div><!-- .container -->
        </nav><!-- #site-navigation -->
        
    </header><!-- #masthead -->
    
    <div id="content" class="site-content" role="main">