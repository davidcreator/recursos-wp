<?php
namespace NosfirNews\HeaderFooterGrid\Core;
class Customizer {
    public function init() { add_action( 'customize_register', [ $this, 'register' ] ); }
    public function register( \WP_Customize_Manager $wp_customize ) {
        $wp_customize->add_setting( 'nosfirnews_hfg_header_bg', [ 'default' => '#ffffff', 'transport' => 'postMessage' ] );
        if ( class_exists( '\\WP_Customize_Color_Control' ) ) {
            $wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'nosfirnews_hfg_header_bg', [ 'label' => 'Header background', 'section' => 'colors' ] ) );
        }
    }
}
