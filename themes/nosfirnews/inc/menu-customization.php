<?php
/**
 * Menu Customization System
 * 
 * Provides advanced menu customization options including:
 * - Mega Menu support
 * - Multiple menu styles (horizontal, vertical, dropdown)
 * - Icon support
 * - Custom menu layouts
 *
 * @package NosfirNews
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * NosfirNews Menu Customization Class
 */
class NosfirNews_Menu_Customization {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'customize_register', array( $this, 'customize_register' ) );
    }

    /**
     * Initialize menu customization
     */
    public function init() {
        // Register additional menu locations
        register_nav_menus( array(
            'mega_menu'     => esc_html__( 'Mega Menu', 'nosfirnews' ),
            'footer_menu'   => esc_html__( 'Footer Menu', 'nosfirnews' ),
            'social_menu'   => esc_html__( 'Social Menu', 'nosfirnews' ),
            'mobile_menu'   => esc_html__( 'Mobile Menu', 'nosfirnews' ),
        ) );
    }

    /**
     * Enqueue menu customization scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_style( 
            'nosfirnews-menu-styles', 
            get_template_directory_uri() . '/assets/css/menu-customization.css',
            array(),
            wp_get_theme()->get( 'Version' )
        );

        wp_enqueue_script( 
            'nosfirnews-menu-scripts', 
            get_template_directory_uri() . '/assets/js/menu-customization.js',
            array( 'jquery' ),
            wp_get_theme()->get( 'Version' ),
            true
        );

        // Localize script for AJAX
        wp_localize_script( 'nosfirnews-menu-scripts', 'nosfirnews_menu', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'nosfirnews_menu_nonce' ),
        ) );
    }

    /**
     * Add menu customization options to customizer
     */
    public function customize_register( $wp_customize ) {
        // Menu Section
        $wp_customize->add_section( 'nosfirnews_menu_options', array(
            'title'    => esc_html__( 'Menu Options', 'nosfirnews' ),
            'priority' => 30,
        ) );

        // Menu Style
        $wp_customize->add_setting( 'menu_style', array(
            'default'           => 'horizontal',
            'sanitize_callback' => 'sanitize_text_field',
        ) );

        $wp_customize->add_control( 'menu_style', array(
            'label'   => esc_html__( 'Menu Style', 'nosfirnews' ),
            'section' => 'nosfirnews_menu_options',
            'type'    => 'select',
            'choices' => array(
                'horizontal' => esc_html__( 'Horizontal', 'nosfirnews' ),
                'vertical'   => esc_html__( 'Vertical', 'nosfirnews' ),
                'mega'       => esc_html__( 'Mega Menu', 'nosfirnews' ),
                'minimal'    => esc_html__( 'Minimal', 'nosfirnews' ),
                'sidebar'    => esc_html__( 'Sidebar', 'nosfirnews' ),
            ),
        ) );

        // Menu Animation
        $wp_customize->add_setting( 'menu_animation', array(
            'default'           => 'fade',
            'sanitize_callback' => 'sanitize_text_field',
        ) );

        $wp_customize->add_control( 'menu_animation', array(
            'label'   => esc_html__( 'Menu Animation', 'nosfirnews' ),
            'section' => 'nosfirnews_menu_options',
            'type'    => 'select',
            'choices' => array(
                'none'      => esc_html__( 'None', 'nosfirnews' ),
                'fade'      => esc_html__( 'Fade', 'nosfirnews' ),
                'slide'     => esc_html__( 'Slide', 'nosfirnews' ),
                'bounce'    => esc_html__( 'Bounce', 'nosfirnews' ),
                'zoom'      => esc_html__( 'Zoom', 'nosfirnews' ),
            ),
        ) );

        // Enable Mega Menu
        $wp_customize->add_setting( 'enable_mega_menu', array(
            'default'           => false,
            'sanitize_callback' => 'wp_validate_boolean',
        ) );

        $wp_customize->add_control( 'enable_mega_menu', array(
            'label'   => esc_html__( 'Enable Mega Menu', 'nosfirnews' ),
            'section' => 'nosfirnews_menu_options',
            'type'    => 'checkbox',
        ) );

        // Menu Background Color
        $wp_customize->add_setting( 'menu_background_color', array(
            'default'           => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'menu_background_color', array(
            'label'   => esc_html__( 'Menu Background Color', 'nosfirnews' ),
            'section' => 'nosfirnews_menu_options',
        ) ) );

        // Menu Text Color
        $wp_customize->add_setting( 'menu_text_color', array(
            'default'           => '#333333',
            'sanitize_callback' => 'sanitize_hex_color',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'menu_text_color', array(
            'label'   => esc_html__( 'Menu Text Color', 'nosfirnews' ),
            'section' => 'nosfirnews_menu_options',
        ) ) );

        // Menu Hover Color
        $wp_customize->add_setting( 'menu_hover_color', array(
            'default'           => '#007cba',
            'sanitize_callback' => 'sanitize_hex_color',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'menu_hover_color', array(
            'label'   => esc_html__( 'Menu Hover Color', 'nosfirnews' ),
            'section' => 'nosfirnews_menu_options',
        ) ) );
    }

    /* Custom fields for menu items are centralized in NosfirNews_Menu_Custom_Fields */

    /**
     * Get menu style classes
     */
    public static function get_menu_classes() {
        $menu_style = get_theme_mod( 'menu_style', 'horizontal' );
        $menu_animation = get_theme_mod( 'menu_animation', 'fade' );
        
        $classes = array(
            'menu-style-' . $menu_style,
            'menu-animation-' . $menu_animation,
        );

        if ( get_theme_mod( 'enable_mega_menu', false ) ) {
            $classes[] = 'mega-menu-enabled';
        }

        return implode( ' ', $classes );
    }

    /**
     * Get menu custom CSS
     */
    public static function get_menu_custom_css() {
        $bg_color = get_theme_mod( 'menu_background_color', '#ffffff' );
        $text_color = get_theme_mod( 'menu_text_color', '#333333' );
        $hover_color = get_theme_mod( 'menu_hover_color', '#007cba' );

        $css = "
        .main-navigation {
            background-color: {$bg_color};
        }
        .main-navigation a {
            color: {$text_color};
        }
        .main-navigation a:hover,
        .main-navigation a:focus {
            color: {$hover_color};
        }
        ";

        return $css;
    }
}

// Initialize the menu customization
new NosfirNews_Menu_Customization();