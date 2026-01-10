<?php if ( ! defined( 'ABSPATH' ) ) exit; ?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#1a73e8">
<?php wp_head(); ?>
</head>
<body <?php body_class('pwa'); ?>>
<?php wp_body_open(); ?>
<header class="site-header" role="banner">
<div class="container">
<div class="site-branding">
<?php if ( function_exists( 'the_custom_logo' ) ) the_custom_logo(); ?>
<div>
<?php if ( is_front_page() && is_home() ) : ?>
<h1 class="site-title"><a href="<?php echo esc_url( home_url('/') ); ?>"><?php bloginfo('name'); ?></a></h1>
<?php else : ?>
<p class="site-title"><a href="<?php echo esc_url( home_url('/') ); ?>"><?php bloginfo('name'); ?></a></p>
<?php endif; ?>
<p class="site-description"><?php bloginfo('description'); ?></p>
</div>
</div>
<nav class="main-navigation" role="navigation" aria-label="Primary">
<?php wp_nav_menu( [ 'theme_location' => 'primary', 'menu_id' => 'primary-menu', 'menu_class' => 'nav-menu' ] ); ?>
</nav>
</div>
</header>
<div id="content" class="site-content">