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

// 1. Melhorar estrutura de navegação com skip links
function nosfirnews_add_skip_links() {
    ?>
    <a class="skip-link screen-reader-text" href="#primary">
        <?php esc_html_e('Pular para o conteúdo', 'nosfirnews'); ?>
    </a>
    <a class="skip-link screen-reader-text" href="#site-navigation">
        <?php esc_html_e('Pular para navegação', 'nosfirnews'); ?>
    </a>
    <?php
}
add_action('wp_body_open', 'nosfirnews_add_skip_links', 1);

// 2. Adicionar atributos ARIA apropriados nos menus
function nosfirnews_nav_menu_args_enhanced($args) {
    if (!isset($args['container_aria_label'])) {
        $args['container_aria_label'] = $args['theme_location'] ?? 'Menu principal';
    }
    
    if (!isset($args['menu_id'])) {
        $args['menu_id'] = 'primary-menu';
    }
    
    return $args;
}
add_filter('wp_nav_menu_args', 'nosfirnews_nav_menu_args_enhanced');

// 3. Melhorar campos de formulário com labels e ARIA
function nosfirnews_enhance_comment_form($fields) {
    $commenter = wp_get_current_commenter();
    $req = get_option('require_name_email');
    $aria_req = ($req ? " aria-required='true'" : '');
    $html_req = ($req ? " required='required'" : '');
    
    $fields['author'] = sprintf(
        '<p class="comment-form-author">
            <label for="author">%s%s</label>
            <input id="author" name="author" type="text" value="%s" size="30" maxlength="245"%s%s aria-describedby="author-notes" />
            <span id="author-notes" class="screen-reader-text">%s</span>
        </p>',
        __('Nome', 'nosfirnews'),
        ($req ? ' <span class="required" aria-hidden="true">*</span>' : ''),
        esc_attr($commenter['comment_author']),
        $aria_req,
        $html_req,
        __('Seu nome completo', 'nosfirnews')
    );
    
    $fields['email'] = sprintf(
        '<p class="comment-form-email">
            <label for="email">%s%s</label>
            <input id="email" name="email" type="email" value="%s" size="30" maxlength="100" aria-describedby="email-notes"%s%s />
            <span id="email-notes" class="screen-reader-text">%s</span>
        </p>',
        __('E-mail', 'nosfirnews'),
        ($req ? ' <span class="required" aria-hidden="true">*</span>' : ''),
        esc_attr($commenter['comment_author_email']),
        $aria_req,
        $html_req,
        __('Seu e-mail não será publicado', 'nosfirnews')
    );
    
    $fields['url'] = sprintf(
        '<p class="comment-form-url">
            <label for="url">%s</label>
            <input id="url" name="url" type="url" value="%s" size="30" maxlength="200" aria-describedby="url-notes" />
            <span id="url-notes" class="screen-reader-text">%s</span>
        </p>',
        __('Website', 'nosfirnews'),
        esc_attr($commenter['comment_author_url']),
        __('URL do seu website (opcional)', 'nosfirnews')
    );
    
    return $fields;
}
add_filter('comment_form_default_fields', 'nosfirnews_enhance_comment_form');

// 4. Adicionar roles e landmarks ARIA
function nosfirnews_add_aria_landmarks() {
    // Adicionar via filtros
    add_filter('body_class', function($classes) {
        return $classes;
    });
}
add_action('after_setup_theme', 'nosfirnews_add_aria_landmarks');

// 5. Melhorar mensagens de erro para screen readers
function nosfirnews_enhance_error_messages($message) {
    if (is_wp_error($message)) {
        $message = '<div role="alert" aria-live="assertive" class="error-message">' . 
                   esc_html($message->get_error_message()) . 
                   '</div>';
    }
    return $message;
}

// 6. Adicionar text alternativo em imagens automaticamente
function nosfirnews_auto_alt_text($attr, $attachment) {
    if (empty($attr['alt'])) {
        $attr['alt'] = get_the_title($attachment->ID);
        
        // Se ainda estiver vazio, usar nome do arquivo
        if (empty($attr['alt'])) {
            $attr['alt'] = basename(get_attached_file($attachment->ID), '.' . pathinfo(get_attached_file($attachment->ID), PATHINFO_EXTENSION));
        }
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'nosfirnews_auto_alt_text', 10, 2);

// 7. Melhorar estrutura de paginação
function nosfirnews_accessible_pagination() {
    global $wp_query;
    
    if ($wp_query->max_num_pages <= 1) {
        return;
    }
    
    $paged = get_query_var('paged') ? absint(get_query_var('paged')) : 1;
    $max = intval($wp_query->max_num_pages);
    
    // Adicionar valores se apenas um argumento foi fornecido
    if ($paged >= 1) {
        $links[] = $paged;
    }
    
    if ($paged >= 3) {
        $links[] = $paged - 1;
        $links[] = $paged - 2;
    }
    
    if (($paged + 2) <= $max) {
        $links[] = $paged + 2;
        $links[] = $paged + 1;
    }
    
    echo '<nav class="pagination" role="navigation" aria-label="' . esc_attr__('Navegação de posts', 'nosfirnews') . '">';
    echo '<ul class="pagination-list">';
    
    // Link anterior
    if (get_previous_posts_link()) {
        printf(
            '<li class="pagination-item">%s</li>',
            get_previous_posts_link(
                '<span aria-hidden="true">&larr;</span> ' . 
                '<span class="screen-reader-text">' . __('Anterior', 'nosfirnews') . '</span>'
            )
        );
    }
    
    // Primeira página
    if (!in_array(1, $links)) {
        $class = 1 == $paged ? ' class="current" aria-current="page"' : '';
        printf('<li class="pagination-item"><a href="%s"%s>1</a></li>', esc_url(get_pagenum_link(1)), $class);
        
        if (!in_array(2, $links)) {
            echo '<li class="pagination-ellipsis" aria-hidden="true"><span>&hellip;</span></li>';
        }
    }
    
    // Links de páginas
    sort($links);
    foreach ((array) $links as $link) {
        $class = $paged == $link ? ' class="current" aria-current="page"' : '';
        printf('<li class="pagination-item"><a href="%s"%s>%s</a></li>', esc_url(get_pagenum_link($link)), $class, $link);
    }
    
    // Última página
    if (!in_array($max, $links)) {
        if (!in_array($max - 1, $links)) {
            echo '<li class="pagination-ellipsis" aria-hidden="true"><span>&hellip;</span></li>';
        }
        
        $class = $paged == $max ? ' class="current" aria-current="page"' : '';
        printf('<li class="pagination-item"><a href="%s"%s>%s</a></li>', esc_url(get_pagenum_link($max)), $class, $max);
    }
    
    // Link próximo
    if (get_next_posts_link()) {
        printf(
            '<li class="pagination-item">%s</li>',
            get_next_posts_link(
                '<span class="screen-reader-text">' . __('Próximo', 'nosfirnews') . '</span> ' .
                '<span aria-hidden="true">&rarr;</span>'
            )
        );
    }
    
    echo '</ul>';
    echo '</nav>';
}

// 8. Adicionar suporte a focus visível
function nosfirnews_add_focus_styles() {
    ?>
    <style>
    /* Focus visível para acessibilidade */
    a:focus,
    button:focus,
    input:focus,
    textarea:focus,
    select:focus {
        outline: 2px solid #0073aa;
        outline-offset: 2px;
    }
    
    /* Skip links */
    .skip-link {
        position: absolute;
        top: -40px;
        left: 6px;
        z-index: 100000;
        padding: 8px 12px;
        background-color: #f1f1f1;
        color: #21759b;
        text-decoration: none;
    }
    
    .skip-link:focus {
        top: 6px;
        clip: auto;
        height: auto;
        width: auto;
    }
    
    /* Screen reader only text */
    .screen-reader-text {
        border: 0;
        clip: rect(1px, 1px, 1px, 1px);
        clip-path: inset(50%);
        height: 1px;
        margin: -1px;
        overflow: hidden;
        padding: 0;
        position: absolute;
        width: 1px;
        word-wrap: normal !important;
    }
    
    .screen-reader-text:focus {
        background-color: #f1f1f1;
        border-radius: 3px;
        box-shadow: 0 0 2px 2px rgba(0, 0, 0, 0.6);
        clip: auto !important;
        clip-path: none;
        color: #21759b;
        display: block;
        font-size: 14px;
        font-weight: bold;
        height: auto;
        left: 5px;
        line-height: normal;
        padding: 15px 23px 14px;
        text-decoration: none;
        top: 5px;
        width: auto;
        z-index: 100000;
    }
    </style>
    <?php
}
add_action('wp_head', 'nosfirnews_add_focus_styles', 999);

// 9. Validação de cores para contraste (WCAG AA)
function nosfirnews_check_color_contrast($foreground, $background) {
    // Converter hex para RGB
    $fg_rgb = sscanf($foreground, "#%02x%02x%02x");
    $bg_rgb = sscanf($background, "#%02x%02x%02x");
    
    // Calcular luminância relativa
    $calculate_luminance = function($rgb) {
        $rgb = array_map(function($val) {
            $val = $val / 255;
            return $val <= 0.03928 ? $val / 12.92 : pow(($val + 0.055) / 1.055, 2.4);
        }, $rgb);
        
        return 0.2126 * $rgb[0] + 0.7152 * $rgb[1] + 0.0722 * $rgb[2];
    };
    
    $l1 = $calculate_luminance($fg_rgb);
    $l2 = $calculate_luminance($bg_rgb);
    
    // Calcular contraste
    $contrast = ($l1 > $l2) 
        ? (($l1 + 0.05) / ($l2 + 0.05))
        : (($l2 + 0.05) / ($l1 + 0.05));
    
    // WCAG AA requer contraste mínimo de 4.5:1 para texto normal
    return array(
        'ratio' => $contrast,
        'passes_aa' => $contrast >= 4.5,
        'passes_aaa' => $contrast >= 7
    );
}

// 10. Adicionar suporte a live regions para conteúdo dinâmico
function nosfirnews_live_region_support() {
    ?>
    <div id="nosfirnews-live-region" aria-live="polite" aria-atomic="true" class="screen-reader-text"></div>
    <?php
}
add_action('wp_footer', 'nosfirnews_live_region_support');

// 11. Garantir ordem lógica de tabulação
function nosfirnews_ensure_tab_order() {
    // Remover tabindex positivos (anti-padrão)
    add_filter('the_content', function($content) {
        return preg_replace('/\s?tabindex\s*=\s*["\']?\d+["\']?/i', '', $content);
    }, 999);
}
add_action('init', 'nosfirnews_ensure_tab_order');

// 12. Melhorar mensagens de feedback
class NosfirNews_Feedback_Manager {
    
    /**
     * Adiciona mensagem de sucesso acessível
     */
    public static function success($message) {
        return sprintf(
            '<div class="notice notice-success" role="status" aria-live="polite">
                <p><span class="dashicons dashicons-yes" aria-hidden="true"></span> %s</p>
            </div>',
            esc_html($message)
        );
    }
    
    /**
     * Adiciona mensagem de erro acessível
     */
    public static function error($message) {
        return sprintf(
            '<div class="notice notice-error" role="alert" aria-live="assertive">
                <p><span class="dashicons dashicons-warning" aria-hidden="true"></span> %s</p>
            </div>',
            esc_html($message)
        );
    }
    
    /**
     * Adiciona mensagem de informação acessível
     */
    public static function info($message) {
        return sprintf(
            '<div class="notice notice-info" role="status" aria-live="polite">
                <p><span class="dashicons dashicons-info" aria-hidden="true"></span> %s</p>
            </div>',
            esc_html($message)
        );
    }
}

// 13. Adicionar texto descritivo para ícones
function nosfirnews_accessible_social_icons($icon_html, $label) {
    return sprintf(
        '<span class="social-icon" aria-label="%s">%s<span class="screen-reader-text">%s</span></span>',
        esc_attr($label),
        $icon_html,
        esc_html($label)
    );
}

// 14. Garantir que todos os inputs tenham labels
function nosfirnews_validate_form_accessibility($form_html) {
    // Verificar se todos os inputs têm labels ou aria-label
    preg_match_all('/<input[^>]*>/i', $form_html, $inputs);
    
    foreach ($inputs[0] as $input) {
        // Verificar se tem id
        if (!preg_match('/id\s*=\s*["\']([^"\']+)["\']/i', $input, $id_match)) {
            NosfirNews_Error_Logger::log('Input sem ID encontrado: ' . $input, 'WARNING');
            continue;
        }
        
        $input_id = $id_match[1];
        
        // Verificar se existe label correspondente
        if (!preg_match('/<label[^>]*for\s*=\s*["\']' . preg_quote($input_id, '/') . '["\'][^>]*>/i', $form_html)) {
            // Verificar se tem aria-label
            if (!preg_match('/aria-label\s*=\s*["\'][^"\']+["\']/i', $input)) {
                NosfirNews_Error_Logger::log('Input sem label ou aria-label: ' . $input, 'WARNING');
            }
        }
    }
    
    return $form_html;
}