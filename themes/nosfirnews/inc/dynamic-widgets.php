<?php
/**
 * Dynamic Widget Areas
 *
 * @package NosfirNews
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Dynamic Widget Areas Class
 */
class NosfirNews_Dynamic_Widgets {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('widgets_init', array($this, 'register_dynamic_widgets'));
        add_action('customize_register', array($this, 'add_widget_customizer_options'));
        add_action('wp_ajax_add_widget_area', array($this, 'ajax_add_widget_area'));
        add_action('wp_ajax_remove_widget_area', array($this, 'ajax_remove_widget_area'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_head', array($this, 'output_widget_styles'));
    }

    /**
     * Register dynamic widget areas
     */
    public function register_dynamic_widgets() {
        // Default widget areas
        $default_widgets = array(
            'sidebar-main' => array(
                'name' => __('Sidebar Principal', 'nosfirnews'),
                'description' => __('Área de widgets da sidebar principal.', 'nosfirnews'),
                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h3 class="widget-title">',
                'after_title' => '</h3>',
            ),
            'header-widgets' => array(
                'name' => __('Widgets do Header', 'nosfirnews'),
                'description' => __('Área de widgets no cabeçalho.', 'nosfirnews'),
                'before_widget' => '<div id="%1$s" class="header-widget %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h4 class="header-widget-title">',
                'after_title' => '</h4>',
            ),
            'footer-1' => array(
                'name' => __('Footer - Coluna 1', 'nosfirnews'),
                'description' => __('Primeira coluna do rodapé.', 'nosfirnews'),
                'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h4 class="footer-widget-title">',
                'after_title' => '</h4>',
            ),
            'footer-2' => array(
                'name' => __('Footer - Coluna 2', 'nosfirnews'),
                'description' => __('Segunda coluna do rodapé.', 'nosfirnews'),
                'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h4 class="footer-widget-title">',
                'after_title' => '</h4>',
            ),
            'footer-3' => array(
                'name' => __('Footer - Coluna 3', 'nosfirnews'),
                'description' => __('Terceira coluna do rodapé.', 'nosfirnews'),
                'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h4 class="footer-widget-title">',
                'after_title' => '</h4>',
            ),
            'before-content' => array(
                'name' => __('Antes do Conteúdo', 'nosfirnews'),
                'description' => __('Área de widgets antes do conteúdo principal.', 'nosfirnews'),
                'before_widget' => '<div id="%1$s" class="before-content-widget %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h3 class="before-content-widget-title">',
                'after_title' => '</h3>',
            ),
            'after-content' => array(
                'name' => __('Depois do Conteúdo', 'nosfirnews'),
                'description' => __('Área de widgets depois do conteúdo principal.', 'nosfirnews'),
                'before_widget' => '<div id="%1$s" class="after-content-widget %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h3 class="after-content-widget-title">',
                'after_title' => '</h3>',
            ),
        );

        // Register default widgets
        foreach ($default_widgets as $id => $widget) {
            register_sidebar(array_merge(array('id' => $id), $widget));
        }

        // Register custom widget areas
        $custom_widgets = get_option('nosfirnews_custom_widget_areas', array());
        foreach ($custom_widgets as $id => $widget) {
            register_sidebar(array_merge(array('id' => $id), $widget));
        }

        // Register page-specific widget areas
        $this->register_page_specific_widgets();
    }

    /**
     * Register page-specific widget areas
     */
    private function register_page_specific_widgets() {
        // Home page widgets
        register_sidebar(array(
            'id' => 'home-hero',
            'name' => __('Home - Hero Section', 'nosfirnews'),
            'description' => __('Área de widgets na seção hero da página inicial.', 'nosfirnews'),
            'before_widget' => '<div id="%1$s" class="hero-widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h2 class="hero-widget-title">',
            'after_title' => '</h2>',
        ));

        register_sidebar(array(
            'id' => 'home-featured',
            'name' => __('Home - Conteúdo em Destaque', 'nosfirnews'),
            'description' => __('Área de widgets para conteúdo em destaque na página inicial.', 'nosfirnews'),
            'before_widget' => '<div id="%1$s" class="featured-widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="featured-widget-title">',
            'after_title' => '</h3>',
        ));

        // Archive page widgets
        register_sidebar(array(
            'id' => 'archive-top',
            'name' => __('Arquivo - Topo', 'nosfirnews'),
            'description' => __('Área de widgets no topo das páginas de arquivo.', 'nosfirnews'),
            'before_widget' => '<div id="%1$s" class="archive-top-widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="archive-widget-title">',
            'after_title' => '</h3>',
        ));

        // Single post widgets
        register_sidebar(array(
            'id' => 'single-before-content',
            'name' => __('Post - Antes do Conteúdo', 'nosfirnews'),
            'description' => __('Área de widgets antes do conteúdo do post.', 'nosfirnews'),
            'before_widget' => '<div id="%1$s" class="single-before-widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="single-widget-title">',
            'after_title' => '</h4>',
        ));

        register_sidebar(array(
            'id' => 'single-after-content',
            'name' => __('Post - Depois do Conteúdo', 'nosfirnews'),
            'description' => __('Área de widgets depois do conteúdo do post.', 'nosfirnews'),
            'before_widget' => '<div id="%1$s" class="single-after-widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="single-widget-title">',
            'after_title' => '</h4>',
        ));

        // Mobile-specific widgets
        register_sidebar(array(
            'id' => 'mobile-menu',
            'name' => __('Menu Mobile', 'nosfirnews'),
            'description' => __('Área de widgets no menu mobile.', 'nosfirnews'),
            'before_widget' => '<div id="%1$s" class="mobile-menu-widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="mobile-widget-title">',
            'after_title' => '</h4>',
        ));
    }

    /**
     * Add widget customizer options
     */
    public function add_widget_customizer_options($wp_customize) {
        // Widget Areas Panel
        $wp_customize->add_panel('nosfirnews_widget_areas', array(
            'title' => __('Áreas de Widgets', 'nosfirnews'),
            'description' => __('Configurações das áreas de widgets.', 'nosfirnews'),
            'priority' => 30,
        ));

        // Widget Visibility Section
        $wp_customize->add_section('nosfirnews_widget_visibility', array(
            'title' => __('Visibilidade dos Widgets', 'nosfirnews'),
            'panel' => 'nosfirnews_widget_areas',
            'priority' => 10,
        ));

        // Show/Hide specific widget areas
        $widget_areas = array(
            'header-widgets' => __('Widgets do Header', 'nosfirnews'),
            'before-content' => __('Antes do Conteúdo', 'nosfirnews'),
            'after-content' => __('Depois do Conteúdo', 'nosfirnews'),
            'home-hero' => __('Hero da Home', 'nosfirnews'),
            'home-featured' => __('Destaque da Home', 'nosfirnews'),
            'archive-top' => __('Topo do Arquivo', 'nosfirnews'),
            'single-before-content' => __('Post - Antes', 'nosfirnews'),
            'single-after-content' => __('Post - Depois', 'nosfirnews'),
            'mobile-menu' => __('Menu Mobile', 'nosfirnews'),
        );

        foreach ($widget_areas as $id => $name) {
            $wp_customize->add_setting("nosfirnews_show_widget_{$id}", array(
                'default' => true,
                'sanitize_callback' => 'nosfirnews_sanitize_checkbox',
            ));

            $wp_customize->add_control("nosfirnews_show_widget_{$id}", array(
                'label' => sprintf(__('Mostrar %s', 'nosfirnews'), $name),
                'section' => 'nosfirnews_widget_visibility',
                'type' => 'checkbox',
            ));
        }

        // Widget Styling Section
        $wp_customize->add_section('nosfirnews_widget_styling', array(
            'title' => __('Estilo dos Widgets', 'nosfirnews'),
            'panel' => 'nosfirnews_widget_areas',
            'priority' => 20,
        ));

        // Widget Title Style
        $wp_customize->add_setting('nosfirnews_widget_title_style', array(
            'default' => 'default',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('nosfirnews_widget_title_style', array(
            'label' => __('Estilo do Título dos Widgets', 'nosfirnews'),
            'section' => 'nosfirnews_widget_styling',
            'type' => 'select',
            'choices' => array(
                'default' => __('Padrão', 'nosfirnews'),
                'bordered' => __('Com Borda', 'nosfirnews'),
                'background' => __('Com Fundo', 'nosfirnews'),
                'underline' => __('Sublinhado', 'nosfirnews'),
                'minimal' => __('Minimalista', 'nosfirnews'),
            ),
        ));

        // Widget Background
        $wp_customize->add_setting('nosfirnews_widget_background', array(
            'default' => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nosfirnews_widget_background', array(
            'label' => __('Cor de Fundo dos Widgets', 'nosfirnews'),
            'section' => 'nosfirnews_widget_styling',
        )));

        // Widget Border
        $wp_customize->add_setting('nosfirnews_widget_border', array(
            'default' => '#e0e0e0',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nosfirnews_widget_border', array(
            'label' => __('Cor da Borda dos Widgets', 'nosfirnews'),
            'section' => 'nosfirnews_widget_styling',
        )));

        // Widget Spacing
        $wp_customize->add_setting('nosfirnews_widget_spacing', array(
            'default' => '20',
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('nosfirnews_widget_spacing', array(
            'label' => __('Espaçamento entre Widgets (px)', 'nosfirnews'),
            'section' => 'nosfirnews_widget_styling',
            'type' => 'range',
            'input_attrs' => array(
                'min' => 10,
                'max' => 60,
                'step' => 5,
            ),
        ));

        // Custom Widget Areas Section
        $wp_customize->add_section('nosfirnews_custom_widget_areas', array(
            'title' => __('Áreas Personalizadas', 'nosfirnews'),
            'panel' => 'nosfirnews_widget_areas',
            'priority' => 30,
        ));

        // Add custom widget area button (handled by JavaScript)
        $wp_customize->add_setting('nosfirnews_add_widget_area', array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('nosfirnews_add_widget_area', array(
            'label' => __('Adicionar Nova Área de Widget', 'nosfirnews'),
            'section' => 'nosfirnews_custom_widget_areas',
            'type' => 'text',
            'description' => __('Digite o nome da nova área de widget e clique em "Adicionar".', 'nosfirnews'),
        ));
    }

    /**
     * AJAX handler to add widget area
     */
    public function ajax_add_widget_area() {
        check_ajax_referer('nosfirnews_widget_nonce', 'nonce');
        
        if (!current_user_can('customize')) {
            wp_die(__('Você não tem permissão para fazer isso.', 'nosfirnews'));
        }

        $name = sanitize_text_field($_POST['name']);
        $id = sanitize_title($name);

        if (empty($name) || empty($id)) {
            wp_send_json_error(__('Nome inválido.', 'nosfirnews'));
        }

        $custom_widgets = get_option('nosfirnews_custom_widget_areas', array());
        
        if (isset($custom_widgets[$id])) {
            wp_send_json_error(__('Área de widget já existe.', 'nosfirnews'));
        }

        $custom_widgets[$id] = array(
            'name' => $name,
            'description' => sprintf(__('Área de widget personalizada: %s', 'nosfirnews'), $name),
            'before_widget' => '<div id="%1$s" class="custom-widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="custom-widget-title">',
            'after_title' => '</h4>',
        );

        update_option('nosfirnews_custom_widget_areas', $custom_widgets);

        wp_send_json_success(array(
            'id' => $id,
            'name' => $name,
            'message' => __('Área de widget adicionada com sucesso!', 'nosfirnews')
        ));
    }

    /**
     * AJAX handler to remove widget area
     */
    public function ajax_remove_widget_area() {
        check_ajax_referer('nosfirnews_widget_nonce', 'nonce');
        
        if (!current_user_can('customize')) {
            wp_die(__('Você não tem permissão para fazer isso.', 'nosfirnews'));
        }

        $id = sanitize_text_field($_POST['id']);
        $custom_widgets = get_option('nosfirnews_custom_widget_areas', array());

        if (!isset($custom_widgets[$id])) {
            wp_send_json_error(__('Área de widget não encontrada.', 'nosfirnews'));
        }

        unset($custom_widgets[$id]);
        update_option('nosfirnews_custom_widget_areas', $custom_widgets);

        wp_send_json_success(__('Área de widget removida com sucesso!', 'nosfirnews'));
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        if ('customize.php' === $hook) {
            wp_enqueue_script(
                'nosfirnews-widget-admin',
                get_template_directory_uri() . '/assets/js/widget-admin.js',
                array('jquery', 'customize-controls'),
                NOSFIRNEWS_VERSION,
                true
            );

            wp_localize_script('nosfirnews-widget-admin', 'nosfirnews_widget_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('nosfirnews_widget_nonce'),
                'strings' => array(
                    'confirm_remove' => __('Tem certeza que deseja remover esta área de widget?', 'nosfirnews'),
                    'add_widget' => __('Adicionar', 'nosfirnews'),
                    'remove_widget' => __('Remover', 'nosfirnews'),
                ),
            ));
        }
    }

    /**
     * Output widget styles
     */
    public function output_widget_styles() {
        $widget_bg = get_theme_mod('nosfirnews_widget_background', '#ffffff');
        $widget_border = get_theme_mod('nosfirnews_widget_border', '#e0e0e0');
        $widget_spacing = get_theme_mod('nosfirnews_widget_spacing', '20');
        $widget_title_style = get_theme_mod('nosfirnews_widget_title_style', 'default');

        $css = '<style type="text/css" id="nosfirnews-widget-styles">';
        
        // Widget container styles
        $css .= '.widget { background-color: ' . $widget_bg . '; border: 1px solid ' . $widget_border . '; margin-bottom: ' . $widget_spacing . 'px; padding: 20px; }';
        
        // Widget title styles
        switch ($widget_title_style) {
            case 'bordered':
                $css .= '.widget-title { border-bottom: 2px solid ' . get_theme_mod('nosfirnews_primary_color', '#1a73e8') . '; padding-bottom: 10px; }';
                break;
            case 'background':
                $css .= '.widget-title { background-color: ' . get_theme_mod('nosfirnews_primary_color', '#1a73e8') . '; color: white; padding: 10px 15px; margin: -20px -20px 20px -20px; }';
                break;
            case 'underline':
                $css .= '.widget-title { position: relative; } .widget-title:after { content: ""; position: absolute; bottom: -5px; left: 0; width: 50px; height: 2px; background-color: ' . get_theme_mod('nosfirnews_primary_color', '#1a73e8') . '; }';
                break;
            case 'minimal':
                $css .= '.widget-title { font-weight: 300; text-transform: uppercase; letter-spacing: 1px; font-size: 0.9em; }';
                break;
        }

        // Responsive widget styles
        $css .= '@media (max-width: 768px) {';
        $css .= '.widget { margin-bottom: ' . ($widget_spacing / 2) . 'px; padding: 15px; }';
        $css .= '}';

        $css .= '</style>';

        echo $css;
    }

    /**
     * Check if widget area should be displayed
     */
    public static function should_display_widget($widget_id) {
        return get_theme_mod("nosfirnews_show_widget_{$widget_id}", true);
    }

    /**
     * Display widget area with conditional visibility
     */
    public static function display_widget_area($widget_id, $wrapper_class = '') {
        if (!self::should_display_widget($widget_id) || !is_active_sidebar($widget_id)) {
            return;
        }

        $wrapper_class = $wrapper_class ? ' ' . $wrapper_class : '';
        
        echo '<div class="widget-area widget-area-' . esc_attr($widget_id) . $wrapper_class . '">';
        dynamic_sidebar($widget_id);
        echo '</div>';
    }

    /**
     * Get all registered widget areas
     */
    public static function get_widget_areas() {
        global $wp_registered_sidebars;
        return $wp_registered_sidebars;
    }

    /**
     * Get custom widget areas
     */
    public static function get_custom_widget_areas() {
        return get_option('nosfirnews_custom_widget_areas', array());
    }
}

// Initialize the dynamic widgets system
new NosfirNews_Dynamic_Widgets();