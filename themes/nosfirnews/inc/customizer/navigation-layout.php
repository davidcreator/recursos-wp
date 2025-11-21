<?php
/**
 * Navigation Layout Customizer Options
 *
 * @package NosfirNews
 * @since 2.0.0
 */

/**
 * Add navigation layout options to customizer
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function nosfirnews_navigation_layout_customizer( $wp_customize ) {
    
    // Navigation Layout Section
    $wp_customize->add_section( 'nosfirnews_navigation_layout', array(
        'title'    => __( 'Layout da Navegação', 'nosfirnews' ),
        'priority' => 30,
        'panel'    => 'nav_menus',
    ) );

    // Navigation Position Setting
    $wp_customize->add_setting( 'nosfirnews_navigation_position', array(
        'default'           => 'right-of-logo',
        'sanitize_callback' => 'nosfirnews_sanitize_navigation_position',
        'transport'         => 'refresh',
    ) );

    $wp_customize->add_control( 'nosfirnews_navigation_position', array(
        'label'    => __( 'Posição da Navegação', 'nosfirnews' ),
        'section'  => 'nosfirnews_navigation_layout',
        'type'     => 'select',
        'choices'  => array(
            'below-header'    => __( 'Abaixo do Header (Padrão)', 'nosfirnews' ),
            'right-of-logo'   => __( 'À Direita do Logo', 'nosfirnews' ),
            'next-to-logo'    => __( 'Próximo ao Logo', 'nosfirnews' ),
            'center-header'   => __( 'Centro do Header', 'nosfirnews' ),
            'right-header'    => __( 'Direita do Header', 'nosfirnews' ),
        ),
        'priority' => 10,
    ) );

    // Navigation Alignment Setting (for inline layouts)
    $wp_customize->add_setting( 'nosfirnews_navigation_alignment', array(
        'default'           => 'left',
        'sanitize_callback' => 'nosfirnews_sanitize_navigation_alignment',
        'transport'         => 'refresh',
    ) );

    $wp_customize->add_control( 'nosfirnews_navigation_alignment', array(
        'label'    => __( 'Alinhamento da Navegação', 'nosfirnews' ),
        'section'  => 'nosfirnews_navigation_layout',
        'type'     => 'select',
        'choices'  => array(
            'left'   => __( 'Esquerda', 'nosfirnews' ),
            'center' => __( 'Centro', 'nosfirnews' ),
            'right'  => __( 'Direita', 'nosfirnews' ),
        ),
        'priority' => 20,
        'active_callback' => 'nosfirnews_is_inline_navigation',
    ) );

    // Navigation Style Setting
    $wp_customize->add_setting( 'nosfirnews_navigation_style', array(
        'default'           => 'default',
        'sanitize_callback' => 'nosfirnews_sanitize_navigation_style',
        'transport'         => 'refresh',
    ) );

    $wp_customize->add_control( 'nosfirnews_navigation_style', array(
        'label'    => __( 'Estilo da Navegação', 'nosfirnews' ),
        'section'  => 'nosfirnews_navigation_layout',
        'type'     => 'select',
        'choices'  => array(
            'default'    => __( 'Padrão', 'nosfirnews' ),
            'minimal'    => __( 'Minimalista', 'nosfirnews' ),
            'boxed'      => __( 'Com Bordas', 'nosfirnews' ),
            'underlined' => __( 'Sublinhado', 'nosfirnews' ),
        ),
        'priority' => 30,
    ) );

    // Mobile Navigation Behavior
    $wp_customize->add_setting( 'nosfirnews_mobile_nav_behavior', array(
        'default'           => 'hamburger',
        'sanitize_callback' => 'nosfirnews_sanitize_mobile_nav_behavior',
        'transport'         => 'refresh',
    ) );

    $wp_customize->add_control( 'nosfirnews_mobile_nav_behavior', array(
        'label'    => __( 'Comportamento Mobile', 'nosfirnews' ),
        'section'  => 'nosfirnews_navigation_layout',
        'type'     => 'select',
        'choices'  => array(
            'hamburger' => __( 'Menu Hambúrguer', 'nosfirnews' ),
            'dropdown'  => __( 'Dropdown', 'nosfirnews' ),
            'slide'     => __( 'Slide Lateral', 'nosfirnews' ),
        ),
        'priority' => 40,
    ) );

    // Navigation Background Color
    $wp_customize->add_setting( 'nosfirnews_navigation_bg_color', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'nosfirnews_navigation_bg_color', array(
        'label'    => __( 'Cor de Fundo da Navegação', 'nosfirnews' ),
        'section'  => 'nosfirnews_navigation_layout',
        'priority' => 50,
    ) ) );

    // Navigation Text Color
    $wp_customize->add_setting( 'nosfirnews_navigation_text_color', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'nosfirnews_navigation_text_color', array(
        'label'    => __( 'Cor do Texto da Navegação', 'nosfirnews' ),
        'section'  => 'nosfirnews_navigation_layout',
        'priority' => 60,
    ) ) );
}
add_action( 'customize_register', 'nosfirnews_navigation_layout_customizer' );

/**
 * Sanitize navigation position
 */
function nosfirnews_sanitize_navigation_position( $input ) {
    $valid = array( 'below-header', 'right-of-logo', 'next-to-logo', 'center-header', 'right-header' );
    return in_array( $input, $valid ) ? $input : 'below-header';
}

/**
 * Sanitize navigation alignment
 */
function nosfirnews_sanitize_navigation_alignment( $input ) {
    $valid = array( 'left', 'center', 'right' );
    return in_array( $input, $valid ) ? $input : 'left';
}

/**
 * Sanitize navigation style
 */
function nosfirnews_sanitize_navigation_style( $input ) {
    $valid = array( 'default', 'minimal', 'boxed', 'underlined' );
    return in_array( $input, $valid ) ? $input : 'default';
}

/**
 * Sanitize mobile navigation behavior
 */
function nosfirnews_sanitize_mobile_nav_behavior( $input ) {
    $valid = array( 'hamburger', 'dropdown', 'slide' );
    return in_array( $input, $valid ) ? $input : 'hamburger';
}

/**
 * Active callback for inline navigation options
 */
function nosfirnews_is_inline_navigation() {
    $position = get_theme_mod( 'nosfirnews_navigation_position', 'below-header' );
    return in_array( $position, array( 'right-of-logo', 'next-to-logo', 'center-header', 'right-header' ) );
}

/**
 * Get navigation layout classes
 */
function nosfirnews_get_navigation_classes() {
    $position = get_theme_mod( 'nosfirnews_navigation_position', 'below-header' );
    $alignment = get_theme_mod( 'nosfirnews_navigation_alignment', 'left' );
    $style = get_theme_mod( 'nosfirnews_navigation_style', 'default' );
    
    $classes = array(
        'main-navigation',
        'nav-position-' . $position,
        'nav-align-' . $alignment,
        'nav-style-' . $style,
    );
    
    return implode( ' ', $classes );
}

/**
 * Get header layout classes
 */
function nosfirnews_get_header_classes() {
    $position = get_theme_mod( 'nosfirnews_navigation_position', 'below-header' );
    
    $classes = array( 'site-header' );
    
    if ( in_array( $position, array( 'right-of-logo', 'next-to-logo', 'center-header', 'right-header' ) ) ) {
        $classes[] = 'header-inline-nav';
        $classes[] = 'nav-position-' . $position;
    }
    
    return implode( ' ', $classes );
}