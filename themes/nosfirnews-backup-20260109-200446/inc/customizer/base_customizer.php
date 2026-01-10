<?php
if ( ! defined( 'ABSPATH' ) ) exit;
function nosfirnews_customizer_register( $wp_customize ) {
    $wp_customize->add_setting( 'nosfirnews_hide_site_title', [ 'default' => false, 'transport' => 'refresh' ] );
    $wp_customize->add_setting( 'nosfirnews_hide_site_description', [ 'default' => false, 'transport' => 'refresh' ] );

    $wp_customize->add_control( 'nosfirnews_hide_site_title', [
        'type' => 'checkbox',
        'section' => 'title_tagline',
        'label' => __( 'Ocultar Título do Site', 'nosfirnews' ),
    ] );
    $wp_customize->add_control( 'nosfirnews_hide_site_description', [
        'type' => 'checkbox',
        'section' => 'title_tagline',
        'label' => __( 'Ocultar Descrição do Site', 'nosfirnews' ),
    ] );

    $wp_customize->add_setting( 'nosfirnews_header_bg_image', [ 'default' => '', 'transport' => 'refresh', 'sanitize_callback' => 'esc_url_raw' ] );
    $wp_customize->add_setting( 'nosfirnews_menu_text_color', [ 'default' => '', 'transport' => 'refresh' ] );
    $wp_customize->add_setting( 'nosfirnews_body_bg_color', [ 'default' => '#ffffff', 'transport' => 'refresh' ] );
    $wp_customize->add_setting( 'nosfirnews_body_bg_image', [ 'default' => '', 'transport' => 'refresh', 'sanitize_callback' => 'esc_url_raw' ] );
    $wp_customize->add_setting( 'nosfirnews_body_text_color', [ 'default' => '', 'transport' => 'refresh' ] );
    $wp_customize->add_setting( 'nosfirnews_widgets_bg_color', [ 'default' => '', 'transport' => 'refresh' ] );
    $wp_customize->add_setting( 'nosfirnews_widgets_text_color', [ 'default' => '', 'transport' => 'refresh' ] );
    $wp_customize->add_setting( 'nosfirnews_footer_bg_color', [ 'default' => '', 'transport' => 'refresh' ] );
    $wp_customize->add_setting( 'nosfirnews_footer_text_color', [ 'default' => '', 'transport' => 'refresh' ] );
    
    

    if ( class_exists( '\\WP_Customize_Image_Control' ) ) {
        $wp_customize->add_control( new \WP_Customize_Image_Control( $wp_customize, 'nosfirnews_header_bg_image', [ 'label' => __( 'Imagem de fundo do cabeçalho', 'nosfirnews' ), 'section' => 'colors' ] ) );
        $wp_customize->add_control( new \WP_Customize_Image_Control( $wp_customize, 'nosfirnews_body_bg_image', [ 'label' => __( 'Imagem de fundo do corpo', 'nosfirnews' ), 'section' => 'colors' ] ) );
    }
    if ( class_exists( '\\WP_Customize_Color_Control' ) ) {
        $wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'nosfirnews_body_bg_color', [ 'label' => __( 'Cor de fundo do corpo', 'nosfirnews' ), 'section' => 'colors' ] ) );
        $wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'nosfirnews_widgets_bg_color', [ 'label' => __( 'Cor de fundo dos widgets', 'nosfirnews' ), 'section' => 'colors' ] ) );
        $wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'nosfirnews_widgets_text_color', [ 'label' => __( 'Cor do texto dos widgets', 'nosfirnews' ), 'section' => 'colors' ] ) );
        $wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'nosfirnews_footer_bg_color', [ 'label' => __( 'Cor de fundo do rodapé', 'nosfirnews' ), 'section' => 'colors' ] ) );
        $wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'nosfirnews_footer_text_color', [ 'label' => __( 'Cor do texto do rodapé', 'nosfirnews' ), 'section' => 'colors' ] ) );
        $wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'nosfirnews_body_text_color', [ 'label' => __( 'Cor do texto do corpo', 'nosfirnews' ), 'section' => 'colors' ] ) );
        $wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'nosfirnews_menu_text_color', [ 'label' => __( 'Cor do texto do menu', 'nosfirnews' ), 'section' => 'colors' ] ) );
    }
}
add_action( 'customize_register', 'nosfirnews_customizer_register' );
