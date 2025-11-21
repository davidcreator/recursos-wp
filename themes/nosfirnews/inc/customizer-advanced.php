<?php
/**
 * Advanced Customizer Options
 *
 * @package NosfirNews
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Advanced Customizer Class
 */
class NosfirNews_Advanced_Customizer {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('customize_register', array($this, 'register_advanced_options'), 20);
        add_action('wp_enqueue_scripts', array($this, 'output_customizer_css'), 999);
        add_action('customize_controls_enqueue_scripts', array($this, 'enqueue_customizer_scripts'));
    }

    /**
     * Register advanced customizer options
     */
    public function register_advanced_options($wp_customize) {
        // Advanced Layout Panel
        $wp_customize->add_panel('nosfirnews_advanced_layout', array(
            'title' => __('Layout Avançado', 'nosfirnews'),
            'description' => __('Configurações avançadas de layout e design.', 'nosfirnews'),
            'priority' => 25,
        ));

        // Advanced Colors Panel
        $wp_customize->add_panel('nosfirnews_advanced_colors', array(
            'title' => __('Cores Avançadas', 'nosfirnews'),
            'description' => __('Configurações avançadas de cores e esquemas.', 'nosfirnews'),
            'priority' => 26,
        ));

        // Advanced Typography Panel
        $wp_customize->add_panel('nosfirnews_advanced_typography', array(
            'title' => __('Tipografia Avançada', 'nosfirnews'),
            'description' => __('Configurações avançadas de tipografia.', 'nosfirnews'),
            'priority' => 27,
        ));

        // Performance Panel
        $wp_customize->add_panel('nosfirnews_performance', array(
            'title' => __('Performance', 'nosfirnews'),
            'description' => __('Configurações de otimização e performance.', 'nosfirnews'),
            'priority' => 28,
        ));

        // Register sections and controls
        $this->register_layout_options($wp_customize);
        $this->register_color_options($wp_customize);
        $this->register_typography_options($wp_customize);
        $this->register_header_options($wp_customize);
        $this->register_footer_options($wp_customize);
        $this->register_blog_options($wp_customize);
        $this->register_social_options($wp_customize);
        $this->register_performance_options($wp_customize);
        $this->register_custom_css_options($wp_customize);
    }

    /**
     * Register layout options
     */
    private function register_layout_options($wp_customize) {
        // Container Width Section
        $wp_customize->add_section('nosfirnews_container_options', array(
            'title' => __('Container e Larguras', 'nosfirnews'),
            'panel' => 'nosfirnews_advanced_layout',
            'priority' => 10,
        ));

        // Container Width
        $wp_customize->add_setting('nosfirnews_container_width', array(
            'default' => '1200',
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('nosfirnews_container_width', array(
            'label' => __('Largura do Container (px)', 'nosfirnews'),
            'section' => 'nosfirnews_container_options',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 960,
                'max' => 1920,
                'step' => 10,
            ),
        ));

        // Content Width
        $wp_customize->add_setting('nosfirnews_content_width', array(
            'default' => '70',
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('nosfirnews_content_width', array(
            'label' => __('Largura do Conteúdo (%)', 'nosfirnews'),
            'section' => 'nosfirnews_container_options',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 60,
                'max' => 100,
                'step' => 1,
            ),
        ));

        // Sidebar Width
        $wp_customize->add_setting('nosfirnews_sidebar_width', array(
            'default' => '25',
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('nosfirnews_sidebar_width', array(
            'label' => __('Largura da Sidebar (%)', 'nosfirnews'),
            'section' => 'nosfirnews_container_options',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 20,
                'max' => 40,
                'step' => 1,
            ),
        ));

        // Spacing Section
        $wp_customize->add_section('nosfirnews_spacing_options', array(
            'title' => __('Espaçamentos', 'nosfirnews'),
            'panel' => 'nosfirnews_advanced_layout',
            'priority' => 20,
        ));

        // Section Padding
        $wp_customize->add_setting('nosfirnews_section_padding', array(
            'default' => '60',
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('nosfirnews_section_padding', array(
            'label' => __('Padding das Seções (px)', 'nosfirnews'),
            'section' => 'nosfirnews_spacing_options',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 20,
                'max' => 120,
                'step' => 1,
            ),
        ));

        // Element Margin
        $wp_customize->add_setting('nosfirnews_element_margin', array(
            'default' => '30',
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('nosfirnews_element_margin', array(
            'label' => __('Margem entre Elementos (px)', 'nosfirnews'),
            'section' => 'nosfirnews_spacing_options',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 10,
                'max' => 80,
                'step' => 1,
            ),
        ));

        // Responsive Breakpoints Section
        $wp_customize->add_section('nosfirnews_breakpoints', array(
            'title' => __('Breakpoints Responsivos', 'nosfirnews'),
            'panel' => 'nosfirnews_advanced_layout',
            'priority' => 30,
        ));

        // Mobile Breakpoint
        $wp_customize->add_setting('nosfirnews_mobile_breakpoint', array(
            'default' => '768',
            'sanitize_callback' => 'absint',
        ));

        $wp_customize->add_control('nosfirnews_mobile_breakpoint', array(
            'label' => __('Breakpoint Mobile (px)', 'nosfirnews'),
            'section' => 'nosfirnews_breakpoints',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 320,
                'max' => 1024,
                'step' => 1,
            ),
        ));

        // Tablet Breakpoint
        $wp_customize->add_setting('nosfirnews_tablet_breakpoint', array(
            'default' => '1024',
            'sanitize_callback' => 'absint',
        ));

        $wp_customize->add_control('nosfirnews_tablet_breakpoint', array(
            'label' => __('Breakpoint Tablet (px)', 'nosfirnews'),
            'section' => 'nosfirnews_breakpoints',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 768,
                'max' => 1440,
                'step' => 1,
            ),
        ));
    }

    /**
     * Register color options
     */
    private function register_color_options($wp_customize) {
        // Color Scheme Section
        $wp_customize->add_section('nosfirnews_color_scheme', array(
            'title' => __('Esquema de Cores', 'nosfirnews'),
            'panel' => 'nosfirnews_advanced_colors',
            'priority' => 10,
        ));

        // Accent Color
        $wp_customize->add_setting('nosfirnews_accent_color', array(
            'default' => '#ff6b35',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nosfirnews_accent_color', array(
            'label' => __('Cor de Destaque', 'nosfirnews'),
            'section' => 'nosfirnews_color_scheme',
        )));

        // Background Color
        $wp_customize->add_setting('nosfirnews_background_color', array(
            'default' => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nosfirnews_background_color', array(
            'label' => __('Cor de Fundo', 'nosfirnews'),
            'section' => 'nosfirnews_color_scheme',
        )));

        // Text Color
        $wp_customize->add_setting('nosfirnews_text_color', array(
            'default' => '#333333',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nosfirnews_text_color', array(
            'label' => __('Cor do Texto', 'nosfirnews'),
            'section' => 'nosfirnews_color_scheme',
        )));

        // Link Color
        $wp_customize->add_setting('nosfirnews_link_color', array(
            'default' => '#007cba',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nosfirnews_link_color', array(
            'label' => __('Cor dos Links', 'nosfirnews'),
            'section' => 'nosfirnews_color_scheme',
        )));

        // Link Hover Color
        $wp_customize->add_setting('nosfirnews_link_hover_color', array(
            'default' => '#005a87',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nosfirnews_link_hover_color', array(
            'label' => __('Cor dos Links (Hover)', 'nosfirnews'),
            'section' => 'nosfirnews_color_scheme',
        )));

        // Dark Mode Section
        $wp_customize->add_section('nosfirnews_dark_mode', array(
            'title' => __('Modo Escuro', 'nosfirnews'),
            'panel' => 'nosfirnews_advanced_colors',
            'priority' => 20,
        ));

        // Enable Dark Mode
        $wp_customize->add_setting('nosfirnews_enable_dark_mode', array(
            'default' => false,
            'sanitize_callback' => 'nosfirnews_sanitize_checkbox',
        ));

        $wp_customize->add_control('nosfirnews_enable_dark_mode', array(
            'label' => __('Ativar Modo Escuro', 'nosfirnews'),
            'section' => 'nosfirnews_dark_mode',
            'type' => 'checkbox',
        ));

        // Dark Mode Toggle
        $wp_customize->add_setting('nosfirnews_dark_mode_toggle', array(
            'default' => true,
            'sanitize_callback' => 'nosfirnews_sanitize_checkbox',
        ));

        $wp_customize->add_control('nosfirnews_dark_mode_toggle', array(
            'label' => __('Mostrar Botão de Alternância', 'nosfirnews'),
            'section' => 'nosfirnews_dark_mode',
            'type' => 'checkbox',
        ));

        // Dark Background Color
        $wp_customize->add_setting('nosfirnews_dark_background_color', array(
            'default' => '#1a1a1a',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nosfirnews_dark_background_color', array(
            'label' => __('Cor de Fundo (Modo Escuro)', 'nosfirnews'),
            'section' => 'nosfirnews_dark_mode',
        )));

        // Dark Text Color
        $wp_customize->add_setting('nosfirnews_dark_text_color', array(
            'default' => '#e0e0e0',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nosfirnews_dark_text_color', array(
            'label' => __('Cor do Texto (Modo Escuro)', 'nosfirnews'),
            'section' => 'nosfirnews_dark_mode',
        )));
    }

    /**
     * Register typography options
     */
    private function register_typography_options($wp_customize) {
        // Google Fonts Section
        $wp_customize->add_section('nosfirnews_google_fonts', array(
            'title' => __('Google Fonts', 'nosfirnews'),
            'panel' => 'nosfirnews_advanced_typography',
            'priority' => 10,
        ));

        // Heading Font
        $wp_customize->add_setting('nosfirnews_heading_font', array(
            'default' => 'Roboto',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('nosfirnews_heading_font', array(
            'label' => __('Fonte dos Títulos', 'nosfirnews'),
            'section' => 'nosfirnews_google_fonts',
            'type' => 'select',
            'choices' => $this->get_google_fonts(),
        ));

        // Body Font Weight
        $wp_customize->add_setting('nosfirnews_body_font_weight', array(
            'default' => '400',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('nosfirnews_body_font_weight', array(
            'label' => __('Peso da Fonte do Corpo', 'nosfirnews'),
            'section' => 'nosfirnews_google_fonts',
            'type' => 'select',
            'choices' => array(
                '300' => __('Light (300)', 'nosfirnews'),
                '400' => __('Regular (400)', 'nosfirnews'),
                '500' => __('Medium (500)', 'nosfirnews'),
                '600' => __('Semi Bold (600)', 'nosfirnews'),
                '700' => __('Bold (700)', 'nosfirnews'),
            ),
        ));

        // Heading Font Weight
        $wp_customize->add_setting('nosfirnews_heading_font_weight', array(
            'default' => '600',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('nosfirnews_heading_font_weight', array(
            'label' => __('Peso da Fonte dos Títulos', 'nosfirnews'),
            'section' => 'nosfirnews_google_fonts',
            'type' => 'select',
            'choices' => array(
                '400' => __('Regular (400)', 'nosfirnews'),
                '500' => __('Medium (500)', 'nosfirnews'),
                '600' => __('Semi Bold (600)', 'nosfirnews'),
                '700' => __('Bold (700)', 'nosfirnews'),
                '800' => __('Extra Bold (800)', 'nosfirnews'),
                '900' => __('Black (900)', 'nosfirnews'),
            ),
        ));

        // Font Sizes Section
        $wp_customize->add_section('nosfirnews_font_sizes', array(
            'title' => __('Tamanhos de Fonte', 'nosfirnews'),
            'panel' => 'nosfirnews_advanced_typography',
            'priority' => 20,
        ));

        // Base Font Size
        $wp_customize->add_setting('nosfirnews_base_font_size', array(
            'default' => '16',
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('nosfirnews_base_font_size', array(
            'label' => __('Tamanho Base da Fonte (px)', 'nosfirnews'),
            'section' => 'nosfirnews_font_sizes',
            'type' => 'range',
            'input_attrs' => array(
                'min' => 12,
                'max' => 24,
                'step' => 1,
            ),
        ));

        // H1 Font Size
        $wp_customize->add_setting('nosfirnews_h1_font_size', array(
            'default' => '2.5',
            'sanitize_callback' => 'nosfirnews_sanitize_float',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('nosfirnews_h1_font_size', array(
            'label' => __('Tamanho H1 (em)', 'nosfirnews'),
            'section' => 'nosfirnews_font_sizes',
            'type' => 'range',
            'input_attrs' => array(
                'min' => 1.5,
                'max' => 4,
                'step' => 0.1,
            ),
        ));

        // Line Height
        $wp_customize->add_setting('nosfirnews_line_height', array(
            'default' => '1.6',
            'sanitize_callback' => 'nosfirnews_sanitize_float',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('nosfirnews_line_height', array(
            'label' => __('Altura da Linha', 'nosfirnews'),
            'section' => 'nosfirnews_font_sizes',
            'type' => 'range',
            'input_attrs' => array(
                'min' => 1.2,
                'max' => 2.0,
                'step' => 0.1,
            ),
        ));
    }

    /**
     * Register header options
     */
    private function register_header_options($wp_customize) {
        // Advanced Header Section
        $wp_customize->add_section('nosfirnews_advanced_header', array(
            'title' => __('Header Avançado', 'nosfirnews'),
            'panel' => 'nosfirnews_advanced_layout',
            'priority' => 40,
        ));

        // Sticky Header
        $wp_customize->add_setting('nosfirnews_sticky_header', array(
            'default' => true,
            'sanitize_callback' => 'nosfirnews_sanitize_checkbox',
        ));

        $wp_customize->add_control('nosfirnews_sticky_header', array(
            'label' => __('Header Fixo', 'nosfirnews'),
            'section' => 'nosfirnews_advanced_header',
            'type' => 'checkbox',
        ));

        // Header Height
        $wp_customize->add_setting('nosfirnews_header_height', array(
            'default' => '80',
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('nosfirnews_header_height', array(
            'label' => __('Altura do Header (px)', 'nosfirnews'),
            'section' => 'nosfirnews_advanced_header',
            'type' => 'range',
            'input_attrs' => array(
                'min' => 60,
                'max' => 150,
                'step' => 5,
            ),
        ));

        // Header Background Color
        $wp_customize->add_setting('nosfirnews_header_bg_color', array(
            'default' => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nosfirnews_header_bg_color', array(
            'label' => __('Cor de Fundo do Header', 'nosfirnews'),
            'section' => 'nosfirnews_advanced_header',
        )));

        // Header Shadow
        $wp_customize->add_setting('nosfirnews_header_shadow', array(
            'default' => true,
            'sanitize_callback' => 'nosfirnews_sanitize_checkbox',
        ));

        $wp_customize->add_control('nosfirnews_header_shadow', array(
            'label' => __('Sombra do Header', 'nosfirnews'),
            'section' => 'nosfirnews_advanced_header',
            'type' => 'checkbox',
        ));
    }

    /**
     * Register footer options
     */
    private function register_footer_options($wp_customize) {
        // Advanced Footer Section
        $wp_customize->add_section('nosfirnews_advanced_footer', array(
            'title' => __('Footer Avançado', 'nosfirnews'),
            'panel' => 'nosfirnews_advanced_layout',
            'priority' => 50,
        ));

        // Footer Background Color
        $wp_customize->add_setting('nosfirnews_footer_bg_color', array(
            'default' => '#2c3e50',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nosfirnews_footer_bg_color', array(
            'label' => __('Cor de Fundo do Footer', 'nosfirnews'),
            'section' => 'nosfirnews_advanced_footer',
        )));

        // Footer Text Color
        $wp_customize->add_setting('nosfirnews_footer_text_color', array(
            'default' => '#ecf0f1',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nosfirnews_footer_text_color', array(
            'label' => __('Cor do Texto do Footer', 'nosfirnews'),
            'section' => 'nosfirnews_advanced_footer',
        )));

        // Footer Widget Columns
        $wp_customize->add_setting('nosfirnews_footer_columns', array(
            'default' => '3',
            'sanitize_callback' => 'absint',
        ));

        $wp_customize->add_control('nosfirnews_footer_columns', array(
            'label' => __('Colunas de Widgets no Footer', 'nosfirnews'),
            'section' => 'nosfirnews_advanced_footer',
            'type' => 'select',
            'choices' => array(
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
            ),
        ));
    }

    /**
     * Register blog options
     */
    private function register_blog_options($wp_customize) {
        // Blog Layout Section
        $wp_customize->add_section('nosfirnews_blog_layout', array(
            'title' => __('Layout do Blog', 'nosfirnews'),
            'panel' => 'nosfirnews_advanced_layout',
            'priority' => 60,
        ));

        // Blog Layout Style
        $wp_customize->add_setting('nosfirnews_blog_layout_style', array(
            'default' => 'grid',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('nosfirnews_blog_layout_style', array(
            'label' => __('Estilo do Layout', 'nosfirnews'),
            'section' => 'nosfirnews_blog_layout',
            'type' => 'select',
            'choices' => array(
                'grid' => __('Grade', 'nosfirnews'),
                'list' => __('Lista', 'nosfirnews'),
                'masonry' => __('Masonry', 'nosfirnews'),
                'magazine' => __('Magazine', 'nosfirnews'),
            ),
        ));

        // Posts per Row
        $wp_customize->add_setting('nosfirnews_posts_per_row', array(
            'default' => '3',
            'sanitize_callback' => 'absint',
        ));

        $wp_customize->add_control('nosfirnews_posts_per_row', array(
            'label' => __('Posts por Linha', 'nosfirnews'),
            'section' => 'nosfirnews_blog_layout',
            'type' => 'select',
            'choices' => array(
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
            ),
        ));

        // Show Excerpt
        $wp_customize->add_setting('nosfirnews_show_excerpt', array(
            'default' => true,
            'sanitize_callback' => 'nosfirnews_sanitize_checkbox',
        ));

        $wp_customize->add_control('nosfirnews_show_excerpt', array(
            'label' => __('Mostrar Resumo', 'nosfirnews'),
            'section' => 'nosfirnews_blog_layout',
            'type' => 'checkbox',
        ));

        // Excerpt Length
        $wp_customize->add_setting('nosfirnews_excerpt_length', array(
            'default' => '20',
            'sanitize_callback' => 'absint',
        ));

        $wp_customize->add_control('nosfirnews_excerpt_length', array(
            'label' => __('Tamanho do Resumo (palavras)', 'nosfirnews'),
            'section' => 'nosfirnews_blog_layout',
            'type' => 'range',
            'input_attrs' => array(
                'min' => 10,
                'max' => 100,
                'step' => 5,
            ),
        ));
    }

    /**
     * Register social options
     */
    private function register_social_options($wp_customize) {
        // Social Media Section
        $wp_customize->add_section('nosfirnews_social_media', array(
            'title' => __('Redes Sociais', 'nosfirnews'),
            'priority' => 35,
        ));

        $social_networks = array(
            'facebook' => 'Facebook',
            'twitter' => 'Twitter',
            'instagram' => 'Instagram',
            'youtube' => 'YouTube',
            'linkedin' => 'LinkedIn',
            'pinterest' => 'Pinterest',
            'tiktok' => 'TikTok',
            'whatsapp' => 'WhatsApp',
        );

        foreach ($social_networks as $network => $label) {
            $wp_customize->add_setting("nosfirnews_social_{$network}", array(
                'default' => '',
                'sanitize_callback' => 'esc_url_raw',
            ));

            $wp_customize->add_control("nosfirnews_social_{$network}", array(
                'label' => sprintf(__('URL do %s', 'nosfirnews'), $label),
                'section' => 'nosfirnews_social_media',
                'type' => 'url',
            ));
        }

        // Social Icons Style
        $wp_customize->add_setting('nosfirnews_social_style', array(
            'default' => 'rounded',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('nosfirnews_social_style', array(
            'label' => __('Estilo dos Ícones', 'nosfirnews'),
            'section' => 'nosfirnews_social_media',
            'type' => 'select',
            'choices' => array(
                'rounded' => __('Arredondado', 'nosfirnews'),
                'square' => __('Quadrado', 'nosfirnews'),
                'circle' => __('Circular', 'nosfirnews'),
                'minimal' => __('Minimalista', 'nosfirnews'),
            ),
        ));
    }

    /**
     * Register performance options
     */
    private function register_performance_options($wp_customize) {
        // Performance Section
        $wp_customize->add_section('nosfirnews_performance_options', array(
            'title' => __('Otimização', 'nosfirnews'),
            'panel' => 'nosfirnews_performance',
            'priority' => 10,
        ));

        // Minify CSS
        $wp_customize->add_setting('nosfirnews_minify_css', array(
            'default' => false,
            'sanitize_callback' => 'nosfirnews_sanitize_checkbox',
        ));

        $wp_customize->add_control('nosfirnews_minify_css', array(
            'label' => __('Minificar CSS', 'nosfirnews'),
            'section' => 'nosfirnews_performance_options',
            'type' => 'checkbox',
        ));

        // Lazy Load Images
        $wp_customize->add_setting('nosfirnews_lazy_load', array(
            'default' => true,
            'sanitize_callback' => 'nosfirnews_sanitize_checkbox',
        ));

        $wp_customize->add_control('nosfirnews_lazy_load', array(
            'label' => __('Carregamento Lazy de Imagens', 'nosfirnews'),
            'section' => 'nosfirnews_performance_options',
            'type' => 'checkbox',
        ));

        // Preload Fonts
        $wp_customize->add_setting('nosfirnews_preload_fonts', array(
            'default' => true,
            'sanitize_callback' => 'nosfirnews_sanitize_checkbox',
        ));

        $wp_customize->add_control('nosfirnews_preload_fonts', array(
            'label' => __('Pré-carregar Fontes', 'nosfirnews'),
            'section' => 'nosfirnews_performance_options',
            'type' => 'checkbox',
        ));
    }

    /**
     * Register custom CSS options
     */
    private function register_custom_css_options($wp_customize) {
        // Custom CSS Section
        $wp_customize->add_section('nosfirnews_custom_css', array(
            'title' => __('CSS Personalizado', 'nosfirnews'),
            'priority' => 200,
        ));

        // Additional CSS
        $wp_customize->add_setting('nosfirnews_additional_css', array(
            'default' => '',
            'sanitize_callback' => 'wp_strip_all_tags',
        ));

        $wp_customize->add_control('nosfirnews_additional_css', array(
            'label' => __('CSS Adicional', 'nosfirnews'),
            'section' => 'nosfirnews_custom_css',
            'type' => 'textarea',
            'input_attrs' => array(
                'rows' => 10,
                'placeholder' => __('/* Adicione seu CSS personalizado aqui */', 'nosfirnews'),
            ),
        ));
    }

    /**
     * Get Google Fonts list
     */
    private function get_google_fonts() {
        return array(
            'Inter' => 'Inter',
            'Roboto' => 'Roboto',
            'Open Sans' => 'Open Sans',
            'Lato' => 'Lato',
            'Montserrat' => 'Montserrat',
            'Poppins' => 'Poppins',
            'Source Sans Pro' => 'Source Sans Pro',
            'Nunito' => 'Nunito',
            'Raleway' => 'Raleway',
            'Ubuntu' => 'Ubuntu',
            'Playfair Display' => 'Playfair Display',
            'Merriweather' => 'Merriweather',
            'PT Sans' => 'PT Sans',
            'Oswald' => 'Oswald',
            'Roboto Condensed' => 'Roboto Condensed',
        );
    }

    /**
     * Output customizer CSS
     */
    public function output_customizer_css() {
        $css = $this->generate_customizer_css();
        if (!empty($css)) {
            echo '<style type="text/css" id="nosfirnews-customizer-css">' . $css . '</style>';
        }
    }

    /**
     * Generate customizer CSS
     */
    private function generate_customizer_css() {
        $css = '';

        // Container width
        $container_width = get_theme_mod('nosfirnews_container_width', '1200');
        if ($container_width !== '1200') {
            $css .= ".container { max-width: {$container_width}px; }";
        }

        // Content and sidebar widths (usar seletores reais do tema) com validação
        $content_width = intval( get_theme_mod('nosfirnews_content_width', '70') );
        $sidebar_width = intval( get_theme_mod('nosfirnews_sidebar_width', '25') );
        if ( $content_width < 0 ) { $content_width = 0; }
        if ( $sidebar_width < 0 ) { $sidebar_width = 0; }
        if ( ($content_width + $sidebar_width) > 100 ) {
            $sidebar_width = max( 0, 100 - $content_width );
        }
        $css .= ".content-layout .site-main { flex: 0 0 {$content_width}%; }";
        $css .= ".content-layout .widget-area.sidebar { flex: 0 0 {$sidebar_width}%; }";

        // Colors
        $primary_color = get_theme_mod('nosfirnews_primary_color', '#1a73e8');
        $accent_color = get_theme_mod('nosfirnews_accent_color', '#ff6b35');
        $background_color = get_theme_mod('nosfirnews_background_color', '#ffffff');
        $text_color = get_theme_mod('nosfirnews_text_color', '#333333');
        $link_color = get_theme_mod('nosfirnews_link_color', '#007cba');
        $link_hover_color = get_theme_mod('nosfirnews_link_hover_color', '#005a87');

        $css .= ":root {";
        $css .= "--primary-color: {$primary_color};";
        $css .= "--accent-color: {$accent_color};";
        $css .= "--background-color: {$background_color};";
        $css .= "--text-color: {$text_color};";
        $css .= "--link-color: {$link_color};";
        $css .= "--link-hover-color: {$link_hover_color};";
        $css .= "}";

        $css .= "body { background-color: {$background_color}; color: {$text_color}; }";
        $css .= "a { color: {$link_color}; }";
        $css .= "a:hover { color: {$link_hover_color}; }";

        // Typography
        $base_font_size = get_theme_mod('nosfirnews_base_font_size', '16');
        $h1_font_size = get_theme_mod('nosfirnews_h1_font_size', '2.5');
        $line_height = get_theme_mod('nosfirnews_line_height', '1.6');
        $body_font = get_theme_mod('nosfirnews_body_font', 'Inter');
        $heading_font = get_theme_mod('nosfirnews_heading_font', 'Roboto');
        $body_font_weight = get_theme_mod('nosfirnews_body_font_weight', '400');
        $heading_font_weight = get_theme_mod('nosfirnews_heading_font_weight', '600');

        $css .= "body { font-size: {$base_font_size}px; line-height: {$line_height}; font-family: '{$body_font}', sans-serif; font-weight: {$body_font_weight}; }";
        $css .= "h1, h2, h3, h4, h5, h6 { font-family: '{$heading_font}', sans-serif; font-weight: {$heading_font_weight}; }";
        $css .= "h1 { font-size: {$h1_font_size}em; }";

        // Header
        $header_height = get_theme_mod('nosfirnews_header_height', '80');
        $header_bg_color = get_theme_mod('nosfirnews_header_bg_color', '#ffffff');
        $header_shadow = get_theme_mod('nosfirnews_header_shadow', true);

        $css .= ".site-header { height: {$header_height}px; background-color: {$header_bg_color}; }";
        if ($header_shadow) {
            $css .= ".site-header { box-shadow: 0 2px 4px rgba(0,0,0,0.1); }";
        }

        // Footer
        $footer_bg_color = get_theme_mod('nosfirnews_footer_bg_color', '#2c3e50');
        $footer_text_color = get_theme_mod('nosfirnews_footer_text_color', '#ecf0f1');
        $footer_columns = get_theme_mod('nosfirnews_footer_columns', '3');

        $css .= ".site-footer { background-color: {$footer_bg_color}; color: {$footer_text_color}; }";
        $css .= ".footer-widgets { grid-template-columns: repeat({$footer_columns}, 1fr); }";

        // Spacing
        $section_padding = get_theme_mod('nosfirnews_section_padding', '60');
        $element_margin = get_theme_mod('nosfirnews_element_margin', '30');

        $css .= ".section { padding: {$section_padding}px 0; }";
        $css .= ".element-margin { margin-bottom: {$element_margin}px; }";

        // Dark mode
        if (get_theme_mod('nosfirnews_enable_dark_mode', false)) {
            $dark_bg = get_theme_mod('nosfirnews_dark_background_color', '#1a1a1a');
            $dark_text = get_theme_mod('nosfirnews_dark_text_color', '#e0e0e0');

            $css .= "@media (prefers-color-scheme: dark) {";
            $css .= "body.dark-mode { background-color: {$dark_bg}; color: {$dark_text}; }";
            $css .= "}";
        }

        // Responsive breakpoints
        $mobile_breakpoint = get_theme_mod('nosfirnews_mobile_breakpoint', '768');
        $tablet_breakpoint = get_theme_mod('nosfirnews_tablet_breakpoint', '1024');

        $css .= "@media (max-width: {$mobile_breakpoint}px) { .mobile-hidden { display: none; } }";
        $css .= "@media (min-width: {$tablet_breakpoint}px) { .desktop-only { display: block; } }";

        // Additional CSS
        $additional_css = get_theme_mod('nosfirnews_additional_css', '');
        if (!empty($additional_css)) {
            $css .= $additional_css;
        }

        return $css;
    }

    /**
     * Enqueue customizer scripts
     */
    public function enqueue_customizer_scripts() {
        wp_enqueue_script(
            'nosfirnews-customizer-controls',
            get_template_directory_uri() . '/assets/js/customizer-controls.js',
            array('jquery', 'customize-controls'),
            NOSFIRNEWS_VERSION,
            true
        );
    }
}

/**
 * Sanitize float values
 */
function nosfirnews_sanitize_float($input) {
    return floatval($input);
}

// Initialize the advanced customizer
new NosfirNews_Advanced_Customizer();