<?php
/**
 * NosfirNews Theme Customizer
 *
 * @package NosfirNews
 * @since 1.0.0
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function nosfirnews_customize_register( $wp_customize ) {
    
    // Transport settings
    $wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
    $wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
    $wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

    // Remove default sections we don't need
    $wp_customize->remove_section( 'colors' );

    // Theme Options Panel
    $wp_customize->add_panel( 'nosfirnews_theme_options', array(
        'title'       => esc_html__( 'Theme Options', 'nosfirnews' ),
        'description' => esc_html__( 'Customize your theme settings.', 'nosfirnews' ),
        'priority'    => 30,
    ) );

    // Header Section
    $wp_customize->add_section( 'nosfirnews_header_options', array(
        'title'    => esc_html__( 'Header Options', 'nosfirnews' ),
        'panel'    => 'nosfirnews_theme_options',
        'priority' => 10,
    ) );

    // Site Title Display
    $wp_customize->add_setting( 'nosfirnews_display_site_title', array(
        'default'           => true,
        'sanitize_callback' => 'nosfirnews_sanitize_checkbox',
        'transport'         => 'postMessage',
    ) );

    $wp_customize->add_control( 'nosfirnews_display_site_title', array(
        'label'       => esc_html__( 'Exibir Título do Site', 'nosfirnews' ),
        'description' => esc_html__( 'Marque para exibir o título do site no cabeçalho.', 'nosfirnews' ),
        'section'     => 'nosfirnews_header_options',
        'type'        => 'checkbox',
        'priority'    => 5,
    ) );

    // Site Description Display
    $wp_customize->add_setting( 'nosfirnews_display_site_description', array(
        'default'           => true,
        'sanitize_callback' => 'nosfirnews_sanitize_checkbox',
        'transport'         => 'postMessage',
    ) );

    $wp_customize->add_control( 'nosfirnews_display_site_description', array(
        'label'       => esc_html__( 'Exibir Descrição do Site', 'nosfirnews' ),
        'description' => esc_html__( 'Marque para exibir a descrição do site no cabeçalho.', 'nosfirnews' ),
        'section'     => 'nosfirnews_header_options',
        'type'        => 'checkbox',
        'priority'    => 6,
    ) );

    // Logo Only Mode
    $wp_customize->add_setting( 'nosfirnews_logo_only_mode', array(
        'default'           => false,
        'sanitize_callback' => 'nosfirnews_sanitize_checkbox',
        'transport'         => 'postMessage',
    ) );

    $wp_customize->add_control( 'nosfirnews_logo_only_mode', array(
        'label'       => esc_html__( 'Modo Apenas Logo', 'nosfirnews' ),
        'description' => esc_html__( 'Ative para exibir apenas o logo, ocultando título e descrição automaticamente.', 'nosfirnews' ),
        'section'     => 'nosfirnews_header_options',
        'type'        => 'checkbox',
        'priority'    => 7,
    ) );

    // Header Layout
    $wp_customize->add_setting( 'nosfirnews_header_layout', array(
        'default'           => 'default',
        'sanitize_callback' => 'nosfirnews_sanitize_select',
    ) );

    $wp_customize->add_control( 'nosfirnews_header_layout', array(
        'label'    => esc_html__( 'Header Layout', 'nosfirnews' ),
        'section'  => 'nosfirnews_header_options',
        'type'     => 'select',
        'priority' => 10,
        'choices'  => array(
            'default' => esc_html__( 'Default', 'nosfirnews' ),
            'center'  => esc_html__( 'Centered', 'nosfirnews' ),
            'minimal' => esc_html__( 'Minimal', 'nosfirnews' ),
        ),
    ) );

    // Colors Section
    $wp_customize->add_section( 'nosfirnews_color_options', array(
        'title'    => esc_html__( 'Color Options', 'nosfirnews' ),
        'panel'    => 'nosfirnews_theme_options',
        'priority' => 20,
    ) );

    // Primary Color
    $wp_customize->add_setting( 'nosfirnews_primary_color', array(
        'default'           => '#1a73e8',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'nosfirnews_primary_color', array(
        'label'    => esc_html__( 'Primary Color', 'nosfirnews' ),
        'section'  => 'nosfirnews_color_options',
    ) ) );

    // Secondary Color
    $wp_customize->add_setting( 'nosfirnews_secondary_color', array(
        'default'           => '#34a853',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'nosfirnews_secondary_color', array(
        'label'    => esc_html__( 'Secondary Color', 'nosfirnews' ),
        'section'  => 'nosfirnews_color_options',
    ) ) );

    // Typography Section
    $wp_customize->add_section( 'nosfirnews_typography_options', array(
        'title'    => esc_html__( 'Typography', 'nosfirnews' ),
        'panel'    => 'nosfirnews_theme_options',
        'priority' => 30,
    ) );

    // Body Font
    $wp_customize->add_setting( 'nosfirnews_body_font', array(
        'default'           => 'Inter',
        'sanitize_callback' => 'nosfirnews_sanitize_select',
    ) );

    $wp_customize->add_control( 'nosfirnews_body_font', array(
        'label'    => esc_html__( 'Body Font', 'nosfirnews' ),
        'section'  => 'nosfirnews_typography_options',
        'type'     => 'select',
        'choices'  => array(
            'Inter'     => 'Inter',
            'Roboto'    => 'Roboto',
            'Open Sans' => 'Open Sans',
            'Lato'      => 'Lato',
            'Montserrat' => 'Montserrat',
        ),
    ) );

    // Layout Section
    $wp_customize->add_section( 'nosfirnews_layout_options', array(
        'title'    => esc_html__( 'Layout Options', 'nosfirnews' ),
        'panel'    => 'nosfirnews_theme_options',
        'priority' => 40,
    ) );

    // Sidebar Position
    $wp_customize->add_setting( 'nosfirnews_sidebar_position', array(
        'default'           => 'right',
        'sanitize_callback' => 'nosfirnews_sanitize_select',
    ) );

    $wp_customize->add_control( 'nosfirnews_sidebar_position', array(
        'label'    => esc_html__( 'Sidebar Position', 'nosfirnews' ),
        'section'  => 'nosfirnews_layout_options',
        'type'     => 'select',
        'choices'  => array(
            'right' => esc_html__( 'Right', 'nosfirnews' ),
            'left'  => esc_html__( 'Left', 'nosfirnews' ),
            'none'  => esc_html__( 'No Sidebar', 'nosfirnews' ),
        ),
    ) );

    // Footer Section
    $wp_customize->add_section( 'nosfirnews_footer_options', array(
        'title'    => esc_html__( 'Footer Options', 'nosfirnews' ),
        'panel'    => 'nosfirnews_theme_options',
        'priority' => 50,
    ) );

    // Copyright Text
    $wp_customize->add_setting( 'nosfirnews_copyright_text', array(
        'default'           => sprintf( esc_html__( '© %s. All rights reserved.', 'nosfirnews' ), date( 'Y' ) ),
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'postMessage',
    ) );

    $wp_customize->add_control( 'nosfirnews_copyright_text', array(
        'label'    => esc_html__( 'Copyright Text', 'nosfirnews' ),
        'section'  => 'nosfirnews_footer_options',
        'type'     => 'textarea',
    ) );

    // Thumbnail Options Section
    $wp_customize->add_section( 'nosfirnews_thumbnail_options', array(
        'title'    => esc_html__( 'Opções de Thumbnail', 'nosfirnews' ),
        'panel'    => 'nosfirnews_theme_options',
        'priority' => 20,
    ) );

    // Featured Image Size
    $wp_customize->add_setting( 'nosfirnews_featured_image_size', array(
        'default'           => 'nosfirnews-featured',
        'sanitize_callback' => 'nosfirnews_sanitize_select',
    ) );

    $wp_customize->add_control( 'nosfirnews_featured_image_size', array(
        'label'    => esc_html__( 'Tamanho da Imagem Destacada', 'nosfirnews' ),
        'section'  => 'nosfirnews_thumbnail_options',
        'type'     => 'select',
        'priority' => 10,
        'choices'  => array(
            'thumbnail'           => esc_html__( 'Miniatura (300x200)', 'nosfirnews' ),
            'medium'             => esc_html__( 'Médio (300x300)', 'nosfirnews' ),
            'medium_large'       => esc_html__( 'Médio Grande (768x0)', 'nosfirnews' ),
            'large'              => esc_html__( 'Grande (1024x1024)', 'nosfirnews' ),
            'nosfirnews-small'   => esc_html__( 'Pequeno (400x225)', 'nosfirnews' ),
            'nosfirnews-medium'  => esc_html__( 'Médio (600x338)', 'nosfirnews' ),
            'nosfirnews-featured'=> esc_html__( 'Destacado (1200x675)', 'nosfirnews' ),
            'nosfirnews-widget'  => esc_html__( 'Widget (300x169)', 'nosfirnews' ),
            'full'               => esc_html__( 'Tamanho Original', 'nosfirnews' ),
        ),
    ) );

    // Archive Image Size
    $wp_customize->add_setting( 'nosfirnews_archive_image_size', array(
        'default'           => 'nosfirnews-medium',
        'sanitize_callback' => 'nosfirnews_sanitize_select',
    ) );

    $wp_customize->add_control( 'nosfirnews_archive_image_size', array(
        'label'    => esc_html__( 'Tamanho da Imagem no Arquivo', 'nosfirnews' ),
        'section'  => 'nosfirnews_thumbnail_options',
        'type'     => 'select',
        'priority' => 20,
        'choices'  => array(
            'thumbnail'           => esc_html__( 'Miniatura (300x200)', 'nosfirnews' ),
            'medium'             => esc_html__( 'Médio (300x300)', 'nosfirnews' ),
            'medium_large'       => esc_html__( 'Médio Grande (768x0)', 'nosfirnews' ),
            'large'              => esc_html__( 'Grande (1024x1024)', 'nosfirnews' ),
            'nosfirnews-small'   => esc_html__( 'Pequeno (400x225)', 'nosfirnews' ),
            'nosfirnews-medium'  => esc_html__( 'Médio (600x338)', 'nosfirnews' ),
            'nosfirnews-featured'=> esc_html__( 'Destacado (1200x675)', 'nosfirnews' ),
            'nosfirnews-widget'  => esc_html__( 'Widget (300x169)', 'nosfirnews' ),
        ),
    ) );

    // Widget Image Size
    $wp_customize->add_setting( 'nosfirnews_widget_image_size', array(
        'default'           => 'nosfirnews-small',
        'sanitize_callback' => 'nosfirnews_sanitize_select',
    ) );

    $wp_customize->add_control( 'nosfirnews_widget_image_size', array(
        'label'    => esc_html__( 'Tamanho da Imagem em Widgets', 'nosfirnews' ),
        'section'  => 'nosfirnews_thumbnail_options',
        'type'     => 'select',
        'priority' => 30,
        'choices'  => array(
            'thumbnail'           => esc_html__( 'Miniatura (300x200)', 'nosfirnews' ),
            'medium'             => esc_html__( 'Médio (300x300)', 'nosfirnews' ),
            'nosfirnews-small'   => esc_html__( 'Pequeno (400x225)', 'nosfirnews' ),
            'nosfirnews-medium'  => esc_html__( 'Médio (600x338)', 'nosfirnews' ),
            'nosfirnews-widget'  => esc_html__( 'Widget (300x169)', 'nosfirnews' ),
        ),
    ) );

    // Enable Lazy Loading
    $wp_customize->add_setting( 'nosfirnews_enable_lazy_loading', array(
        'default'           => true,
        'sanitize_callback' => 'nosfirnews_sanitize_checkbox',
    ) );

    $wp_customize->add_control( 'nosfirnews_enable_lazy_loading', array(
        'label'       => esc_html__( 'Ativar Carregamento Lazy', 'nosfirnews' ),
        'description' => esc_html__( 'Carrega imagens apenas quando necessário para melhorar a performance.', 'nosfirnews' ),
        'section'     => 'nosfirnews_thumbnail_options',
        'type'        => 'checkbox',
        'priority'    => 40,
    ) );

    // Image Quality
    $wp_customize->add_setting( 'nosfirnews_image_quality', array(
        'default'           => 85,
        'sanitize_callback' => 'absint',
    ) );

    $wp_customize->add_control( 'nosfirnews_image_quality', array(
        'label'       => esc_html__( 'Qualidade da Imagem (%)', 'nosfirnews' ),
        'description' => esc_html__( 'Defina a qualidade das imagens (1-100). Menor valor = menor tamanho de arquivo.', 'nosfirnews' ),
        'section'     => 'nosfirnews_thumbnail_options',
        'type'        => 'range',
        'priority'    => 50,
        'input_attrs' => array(
            'min'  => 1,
            'max'  => 100,
            'step' => 1,
        ),
    ) );

    if ( isset( $wp_customize->selective_refresh ) ) {
        
        // Site title
        $wp_customize->selective_refresh->add_partial( 'blogname', array(
            'selector'        => '.site-title a',
            'render_callback' => 'nosfirnews_customize_partial_blogname',
        ) );
        
        // Site description
        $wp_customize->selective_refresh->add_partial( 'blogdescription', array(
            'selector'        => '.site-description',
            'render_callback' => 'nosfirnews_customize_partial_blogdescription',
        ) );
        
        // Copyright text
        $wp_customize->selective_refresh->add_partial( 'nosfirnews_copyright_text', array(
            'selector'        => '.site-info',
            'render_callback' => 'nosfirnews_customize_partial_copyright',
        ) );
        
    }
}
add_action( 'customize_register', 'nosfirnews_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function nosfirnews_customize_partial_blogname() {
    bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function nosfirnews_customize_partial_blogdescription() {
    bloginfo( 'description' );
}

/**
 * Render the copyright text for the selective refresh partial.
 *
 * @return void
 */
function nosfirnews_customize_partial_copyright() {
    echo wp_kses_post( get_theme_mod( 'nosfirnews_copyright_text', sprintf( esc_html__( '© %s. All rights reserved.', 'nosfirnews' ), date( 'Y' ) ) ) );
}

/**
 * Sanitize select fields.
 */
function nosfirnews_sanitize_select( $input, $setting ) {
    $input = sanitize_key( $input );
    $choices = $setting->manager->get_control( $setting->id )->choices;
    return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
}

/**
 * Sanitize checkbox fields.
 */
function nosfirnews_sanitize_checkbox( $checked ) {
    return ( ( isset( $checked ) && true === $checked ) ? true : false );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function nosfirnews_customize_preview_js() {
    wp_enqueue_script( 'nosfirnews-customizer', get_template_directory_uri() . '/assets/js/customizer.js', array( 'customize-preview' ), NOSFIRNEWS_VERSION, true );
}
add_action( 'customize_preview_init', 'nosfirnews_customize_preview_js' );