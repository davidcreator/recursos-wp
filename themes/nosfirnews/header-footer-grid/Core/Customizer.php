<?php
namespace NosfirNews\HeaderFooterGrid\Core;
class Customizer {
    public function init() { add_action( 'customize_register', [ $this, 'register' ] ); add_action( 'wp_enqueue_scripts', [ $this, 'apply_css' ] ); }
    public function register( $wp_customize ) {
        $wp_customize->add_setting( 'nosfirnews_hfg_header_bg', [ 'default' => '#ffffff', 'sanitize_callback' => 'sanitize_hex_color' ] );
        $wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'nosfirnews_hfg_header_bg', [ 'label' => __( 'Cor do cabeçalho', 'nosfirnews' ), 'section' => 'colors' ] ) );
    }
    public function apply_css() {
        $color = get_theme_mod( 'nosfirnews_hfg_header_bg', '#ffffff' );
        $gen = new Css_Generator();
        $gen->add( '.site-header', [ 'background-color' => $color ] );
        $gen->enqueue( 'nosfirnews-style' );
    }
}