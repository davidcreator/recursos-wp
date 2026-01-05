<?php
function nosfirnews_core_dynamic_css(){
    $color= get_theme_mod('nosfirnews_hfg_header_bg', get_theme_mod('header_bg','#ffffff'));
    $css= '.site-header{background-color:'.$color.';}';
    $logo_h = (int) get_theme_mod( 'nn_header_logo_height', 48 );
    if ( $logo_h > 0 ) { $css .= '.site-header .custom-logo-link img{max-height:'.$logo_h.'px;height:auto;}'; }
    $header_img = get_theme_mod( 'nosfirnews_header_bg_image', '' );
    if ( $header_img ) { $css .= '.site-header{background-image:url(' . esc_url( $header_img ) . ');background-size:cover;background-repeat:no-repeat;background-position:center;}'; }
    $body_text = get_theme_mod( 'nosfirnews_body_text_color', '' );
    if ( $body_text ) { $css .= 'body{color:' . $body_text . ';}'; }
    $body_color = get_theme_mod( 'nosfirnews_body_bg_color', '' );
    if ( $body_color ) { $css .= 'body{background-color:' . $body_color . ';}'; }
    $body_img = get_theme_mod( 'nosfirnews_body_bg_image', '' );
    if ( $body_img ) { $css .= 'body{background-image:url(' . esc_url( $body_img ) . ');background-size:cover;background-repeat:no-repeat;background-position:center;}'; }
    $menu_text = get_theme_mod( 'nosfirnews_menu_text_color', '' );
    if ( $menu_text ) { $css .= '.nav-menu a{color:' . $menu_text . ';}'; }
    $widgets_bg = get_theme_mod( 'nosfirnews_widgets_bg_color', '' );
    if ( $widgets_bg ) { $css .= '.widget{background-color:' . $widgets_bg . ';}'; }
    $widgets_text = get_theme_mod( 'nosfirnews_widgets_text_color', '' );
    if ( $widgets_text ) { $css .= '.widget, .widget a{color:' . $widgets_text . ';}'; }
    $footer_bg = get_theme_mod( 'nosfirnews_footer_bg_color', '' );
    if ( $footer_bg ) { $css .= '.site-footer{background-color:' . $footer_bg . ';}'; }
    $footer_text = get_theme_mod( 'nosfirnews_footer_text_color', '' );
    if ( $footer_text ) { $css .= '.site-footer, .site-footer a{color:' . $footer_text . ';}'; }
    if ( get_theme_mod( 'nosfirnews_hide_site_title', false ) ) {
        $css .= '.site-title{display:none;}';
    }
    if ( get_theme_mod( 'nosfirnews_hide_site_description', false ) ) {
        $css .= '.site-description{display:none;}';
    }
    $css .= '.branding-pos-left{justify-content:flex-start;text-align:left;}';
    $css .= '.branding-pos-center{justify-content:center;text-align:center;}';
    $css .= '.branding-pos-right{justify-content:flex-end;text-align:right;}';
    $css .= '.nav-pos-left .nav-menu{justify-content:flex-start;}';
    $css .= '.nav-pos-center .nav-menu{justify-content:center;}';
    $css .= '.nav-pos-right .nav-menu{justify-content:flex-end;}';
    $bp = (int) get_theme_mod( 'nn_mobile_breakpoint', 998 );
    if ( $bp > 0 ) {
        $css .= '@media (max-width: '.$bp.'px){.main-navigation{display:none;}.nav-toggle{display:inline-block;}}';
        $css .= '@media (min-width: '.($bp+1).'px){.main-navigation{display:block;}.nav-toggle{display:none;}}';
    }
    wp_add_inline_style('nosfirnews-style',$css);
}
add_action('wp_enqueue_scripts','nosfirnews_core_dynamic_css', 20);
