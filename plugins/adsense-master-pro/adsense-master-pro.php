<?php
/**
 * Plugin Name: AdSense Master Pro
 * Plugin URI: https://example.com/adsense-master-pro
 * Description: Plugin avançado de gerenciamento de anúncios com suporte completo ao Google AdSense, Ad Manager (DFP), Media.net e outros. Insira anúncios em posições ideais com controle total e flexibilidade máxima.
 * Version: 1.0.0
 * Author: Seu Nome
 * Author URI: https://example.com
 * License: GPL v2 or later
 * Text Domain: adsense-master-pro
 * Domain Path: /languages
 */

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Define constantes do plugin
define('AMP_VERSION', '1.0.0');
define('AMP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AMP_PLUGIN_PATH', plugin_dir_path(__FILE__));

class AdSenseMasterPro {
    
    private $ads = array();
    private $options = array();
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'frontend_scripts'));
        add_action('wp_head', array($this, 'add_header_code'));
        add_action('wp_footer', array($this, 'add_footer_code'));
        
        // Hooks para inserção de anúncios
        add_filter('the_content', array($this, 'insert_ads_in_content'));
        add_action('wp_head', array($this, 'insert_header_ads'));
        add_action('wp_footer', array($this, 'insert_footer_ads'));
        
        // Shortcodes
        add_shortcode('amp_ad', array($this, 'ad_shortcode'));
        add_shortcode('adsense_ad', array($this, 'ad_shortcode'));
        
        // Widget
        add_action('widgets_init', array($this, 'register_widget'));
        
        // AJAX handlers
        add_action('wp_ajax_amp_save_ad', array($this, 'save_ad'));
        add_action('wp_ajax_amp_delete_ad', array($this, 'delete_ad'));
        add_action('wp_ajax_amp_preview_ad', array($this, 'preview_ad'));
        add_action('wp_ajax_amp_get_ad', array($this, 'get_ad'));
        add_action('wp_ajax_amp_track_impression', array($this, 'track_impression'));
        add_action('wp_ajax_nopriv_amp_track_impression', array($this, 'track_impression'));
        add_action('wp_ajax_amp_track_click', array($this, 'track_click'));
        add_action('wp_ajax_nopriv_amp_track_click', array($this, 'track_click'));
        
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function init() {
        load_plugin_textdomain('adsense-master-pro', false, dirname(plugin_basename(__FILE__)) . '/languages');
        $this->load_options();
        $this->load_ads();
    }
    
    public function activate() {
        // Criar tabelas do banco de dados
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'amp_ads';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            code text NOT NULL,
            position varchar(50) NOT NULL,
            status varchar(20) DEFAULT 'active',
            options text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Configurações padrão
        $default_options = array(
            'enable_adsense' => 1,
            'adsense_publisher_id' => '',
            'enable_amp' => 0,
            'gdpr_consent' => 1,
            'ad_blocker_detection' => 0,
            'mobile_ads' => 1,
            'desktop_ads' => 1,
        );
        
        add_option('amp_options', $default_options);
    }
    
    public function deactivate() {
        // Limpar caches e jobs agendados
        wp_clear_scheduled_hook('amp_rotate_ads');
    }
    
    public function load_options() {
        $this->options = get_option('amp_options', array());
    }
    
    public function load_ads() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'amp_ads';
        $this->ads = $wpdb->get_results("SELECT * FROM $table_name WHERE status = 'active'", ARRAY_A);
    }
    
    public function admin_menu() {
        add_menu_page(
            __('AdSense Master Pro', 'adsense-master-pro'),
            __('AdSense Master', 'adsense-master-pro'),
            'manage_options',
            'adsense-master-pro',
            array($this, 'admin_page'),
            'dashicons-admin-media',
            30
        );
        
        add_submenu_page(
            'adsense-master-pro',
            __('Gerenciar Anúncios', 'adsense-master-pro'),
            __('Anúncios', 'adsense-master-pro'),
            'manage_options',
            'adsense-master-pro',
            array($this, 'admin_page')
        );
        
        add_submenu_page(
            'adsense-master-pro',
            __('Configurações', 'adsense-master-pro'),
            __('Configurações', 'adsense-master-pro'),
            'manage_options',
            'amp-settings',
            array($this, 'settings_page')
        );
        
        add_submenu_page(
            'adsense-master-pro',
            __('Editor ads.txt', 'adsense-master-pro'),
            __('ads.txt', 'adsense-master-pro'),
            'manage_options',
            'amp-ads-txt',
            array($this, 'ads_txt_page')
        );
    }
    
    public function admin_scripts($hook) {
        if (strpos($hook, 'adsense-master') === false && strpos($hook, 'amp-') === false) {
            return;
        }
        
        wp_enqueue_script('amp-admin', AMP_PLUGIN_URL . 'assets/admin.js', array('jquery'), AMP_VERSION, true);
        wp_enqueue_style('amp-admin', AMP_PLUGIN_URL . 'assets/admin.css', array(), AMP_VERSION);
        wp_enqueue_code_editor(array('type' => 'text/html'));
        
        wp_localize_script('amp-admin', 'amp_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('amp_nonce')
        ));
    }
    
    public function frontend_scripts() {
        if ($this->options['enable_adsense']) {
            wp_enqueue_script('amp-frontend', AMP_PLUGIN_URL . 'assets/frontend.js', array('jquery'), AMP_VERSION, true);
            wp_enqueue_style('amp-frontend', AMP_PLUGIN_URL . 'assets/frontend.css', array(), AMP_VERSION);
            
            wp_localize_script('amp-frontend', 'amp_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('amp_nonce')
            ));
        }
    }
    
    // Include admin pages
    public function admin_page() {
        include AMP_PLUGIN_PATH . 'includes/admin-page.php';
    }
    
    public function settings_page() {
        include AMP_PLUGIN_PATH . 'includes/settings-page.php';
    }
    
    public function ads_txt_page() {
        include AMP_PLUGIN_PATH . 'includes/ads-txt-page.php';
    }
    
    // AJAX handlers
    public function save_ad() {
        check_ajax_referer('amp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Permissão negada.', 'adsense-master-pro'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'amp_ads';
        
        $options = array(
            'css_selector' => sanitize_text_field($_POST['css_selector']),
            'alignment' => sanitize_text_field($_POST['alignment']),
            'show_on_desktop' => intval($_POST['show_on_desktop']),
            'show_on_mobile' => intval($_POST['show_on_mobile']),
            'show_on_homepage' => intval($_POST['show_on_homepage']),
            'show_on_posts' => intval($_POST['show_on_posts']),
            'show_on_pages' => intval($_POST['show_on_pages']),
        );
        
        $data = array(
            'name' => sanitize_text_field($_POST['name']),
            'code' => $_POST['code'], // Não sanitizar para permitir HTML/JS
            'position' => sanitize_text_field($_POST['position']),
            'options' => serialize($options),
            'status' => 'active'
        );
        
        $result = $wpdb->insert($table_name, $data);
        
        if ($result !== false) {
            wp_send_json_success(__('Anúncio salvo com sucesso!', 'adsense-master-pro'));
        } else {
            wp_send_json_error(__('Erro ao salvar anúncio.', 'adsense-master-pro'));
        }
    }
    
    public function get_ad() {
        check_ajax_referer('amp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Permissão negada.', 'adsense-master-pro'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'amp_ads';
        $id = intval($_POST['id']);
        
        $ad = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);
        
        if ($ad) {
            wp_send_json_success($ad);
        } else {
            wp_send_json_error(__('Anúncio não encontrado.', 'adsense-master-pro'));
        }
    }
    
    public function delete_ad() {
        check_ajax_referer('amp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Permissão negada.', 'adsense-master-pro'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'amp_ads';
        $id = intval($_POST['id']);
        
        $result = $wpdb->delete($table_name, array('id' => $id));
        
        if ($result !== false) {
            wp_send_json_success(__('Anúncio excluído!', 'adsense-master-pro'));
        } else {
            wp_send_json_error(__('Erro ao excluir anúncio.', 'adsense-master-pro'));
        }
    }
    
    public function track_impression() {
        check_ajax_referer('amp_nonce', 'nonce');
        
        $ad_id = intval($_POST['ad_id']);
        
        // Aqui você pode salvar as impressões no banco
        // Por exemplo, criar uma tabela de estatísticas
        
        wp_send_json_success();
    }
    
    public function track_click() {
        check_ajax_referer('amp_nonce', 'nonce');
        
        $ad_id = intval($_POST['ad_id']);
        
        // Aqui você pode salvar os cliques no banco
        // Por exemplo, incrementar contador de cliques
        
        wp_send_json_success();
    }
    
    // Funções de inserção de anúncios
    public function insert_ads_in_content($content) {
        if (!is_single() && !is_page()) {
            return $content;
        }
        
        // Verificar se anúncios estão desabilitados para este post
        global $post;
        if (get_post_meta($post->ID, '_amp_disable_ads', true)) {
            return $content;
        }
        
        foreach ($this->ads as $ad) {
            $options = unserialize($ad['options']);
            
            // Verificar condições de exibição
            if (!$this->should_display_ad($ad, $options)) {
                continue;
            }
            
            $ad_html = $this->generate_ad_html($ad, $options);
            
            switch ($ad['position']) {
                case 'before_content':
                    $content = $ad_html . $content;
                    break;
                    
                case 'after_content':
                    $content = $content . $ad_html;
                    break;
                    
                case 'before_paragraph':
                    $paragraphs = explode('</p>', $content);
                    if (count($paragraphs) > 1) {
                        $paragraphs[0] = $ad_html . $paragraphs[0];
                        $content = implode('</p>', $paragraphs);
                    }
                    break;
                    
                case 'after_paragraph':
                    $paragraphs = explode('</p>', $content);
                    if (count($paragraphs) > 1) {
                        $insert_after = min(2, count($paragraphs) - 1);
                        $paragraphs[$insert_after] = $paragraphs[$insert_after] . '</p>' . $ad_html;
                        $content = implode('</p>', $paragraphs);
                    }
                    break;
            }
        }
        
        return $content;
    }
    
    public function insert_header_ads() {
        foreach ($this->ads as $ad) {
            if ($ad['position'] === 'header') {
                $options = unserialize($ad['options']);
                if ($this->should_display_ad($ad, $options)) {
                    echo $this->generate_ad_html($ad, $options);
                }
            }
        }
    }
    
    public function insert_footer_ads() {
        foreach ($this->ads as $ad) {
            if ($ad['position'] === 'footer') {
                $options = unserialize($ad['options']);
                if ($this->should_display_ad($ad, $options)) {
                    echo $this->generate_ad_html($ad, $options);
                }
            }
        }
    }
    
    public function should_display_ad($ad, $options) {
        // Verificar dispositivo
        $is_mobile = wp_is_mobile();
        if ($is_mobile && !$options['show_on_mobile']) {
            return false;
        }
        if (!$is_mobile && !$options['show_on_desktop']) {
            return false;
        }
        
        // Verificar tipo de página
        if (is_home() && !$options['show_on_homepage']) {
            return false;
        }
        if (is_single() && !$options['show_on_posts']) {
            return false;
        }
        if (is_page() && !$options['show_on_pages']) {
            return false;
        }
        
        return true;
    }
    
    public function generate_ad_html($ad, $options) {
        $alignment_class = '';
        switch ($options['alignment']) {
            case 'left':
                $alignment_class = 'amp-ad-left';
                break;
            case 'center':
                $alignment_class = 'amp-ad-center';
                break;
            case 'right':
                $alignment_class = 'amp-ad-right';
                break;
        }
        
        $ad_id = 'amp-ad-' . $ad['id'];
        $css_class = 'amp-ad-container ' . $alignment_class;
        
        // Gerar HTML do anúncio baseado no tipo
        $ad_content = $this->process_ad_code($ad['code']);
        
        $html = '<div id="' . $ad_id . '" class="' . $css_class . '" data-ad-id="' . $ad['id'] . '">';
        $html .= '<div class="amp-ad-content">' . $ad_content . '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    public function process_ad_code($code) {
        // Processar código PHP se necessário
        if (strpos($code, '<?php') !== false) {
            ob_start();
            eval('?>' . $code);
            return ob_get_clean();
        }
        
        return $code;
    }
    
    public function add_header_code() {
        if ($this->options['enable_adsense'] && !empty($this->options['adsense_publisher_id'])) {
            echo '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=' . esc_attr($this->options['adsense_publisher_id']) . '" crossorigin="anonymous"></script>' . "\n";
        }
        
        // CSS personalizado
        echo '<style>
        .amp-ad-container { margin: 15px 0; clear: both; }
        .amp-ad-left { text-align: left; }
        .amp-ad-center { text-align: center; }
        .amp-ad-right { text-align: right; }
        .amp-ad-content { display: inline-block; }
        .amp-ad-container.amp-sticky { position: fixed; z-index: 9999; }
        .amp-ad-label { font-size: 12px; color: #999; margin-bottom: 5px; }
        </style>' . "\n";
        
        // Detecção de Ad Blocker
        if ($this->options['ad_blocker_detection']) {
            echo '<script>
            (function() {
                var test = document.createElement("div");
                test.innerHTML = "&nbsp;";
                test.className = "adsbox";
                document.body.appendChild(test);
                window.setTimeout(function() {
                    if (test.offsetHeight === 0) {
                        document.body.classList.add("amp-adblock-detected");
                        console.log("Ad blocker detectado");
                    }
                    test.remove();
                }, 100);
            })();
            </script>' . "\n";
        }
    }
    
    public function add_footer_code() {
        // Código adicional no footer se necessário
        if ($this->options['gdpr_consent']) {
            echo '<script>
            // Código de consentimento GDPR básico
            if (typeof gtag !== "undefined") {
                gtag("consent", "default", {
                    ad_storage: "denied",
                    analytics_storage: "denied"
                });
            }
            </script>' . "\n";
        }
    }
    
    // Shortcode para inserção manual
    public function ad_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => '',
            'name' => ''
        ), $atts);
        
        if (!empty($atts['id'])) {
            // Buscar anúncio por ID
            global $wpdb;
            $table_name = $wpdb->prefix . 'amp_ads';
            $ad = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d AND status = 'active'", $atts['id']), ARRAY_A);
        } elseif (!empty($atts['name'])) {
            // Buscar anúncio por nome
            global $wpdb;
            $table_name = $wpdb->prefix . 'amp_ads';
            $ad = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE name = %s AND status = 'active'", $atts['name']), ARRAY_A);
        }
        
        if (!$ad) {
            return '';
        }
        
        $options = unserialize($ad['options']);
        if (!$this->should_display_ad($ad, $options)) {
            return '';
        }
        
        return $this->generate_ad_html($ad, $options);
    }
    
    // Widget do WordPress
    public function register_widget() {
        register_widget('AMP_Ad_Widget');
    }
}

// Widget personalizado
class AMP_Ad_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'amp_ad_widget',
            __('AdSense Master Pro', 'adsense-master-pro'),
            array('description' => __('Exibe anúncios do AdSense Master Pro', 'adsense-master-pro'))
        );
    }
    
    public function widget($args, $instance) {
        if (!empty($instance['ad_id'])) {
            echo $args['before_widget'];
            
            if (!empty($instance['title'])) {
                echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
            }
            
            echo do_shortcode('[amp_ad id="' . $instance['ad_id'] . '"]');
            
            echo $args['after_widget'];
        }
    }
    
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $ad_id = isset($instance['ad_id']) ? $instance['ad_id'] : '';
        
        // Buscar anúncios disponíveis
        global $wpdb;
        $table_name = $wpdb->prefix . 'amp_ads';
        $ads = $wpdb->get_results("SELECT id, name FROM $table_name WHERE status = 'active'", ARRAY_A);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Título:', 'adsense-master-pro'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('ad_id'); ?>"><?php _e('Selecione o Anúncio:', 'adsense-master-pro'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('ad_id'); ?>" name="<?php echo $this->get_field_name('ad_id'); ?>">
                <option value=""><?php _e('Selecione...', 'adsense-master-pro'); ?></option>
                <?php foreach ($ads as $ad): ?>
                <option value="<?php echo $ad['id']; ?>" <?php selected($ad_id, $ad['id']); ?>><?php echo esc_html($ad['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['ad_id'] = (!empty($new_instance['ad_id'])) ? intval($new_instance['ad_id']) : '';
        return $instance;
    }
}

// Funções utilitárias
function amp_display_ad($id_or_name) {
    echo do_shortcode('[amp_ad ' . (is_numeric($id_or_name) ? 'id' : 'name') . '="' . $id_or_name . '"]');
}

function amp_get_ad($id_or_name) {
    return do_shortcode('[amp_ad ' . (is_numeric($id_or_name) ? 'id' : 'name') . '="' . $id_or_name . '"]');
}

// Ganchos adicionais para desenvolvedores
function amp_before_ad_display($ad_id) {
    do_action('amp_before_ad_display', $ad_id);
}

function amp_after_ad_display($ad_id) {
    do_action('amp_after_ad_display', $ad_id);
}

// Inicializar plugin
if (class_exists('AdSenseMasterPro')) {
    $adsense_master_pro = new AdSenseMasterPro();
}

// Função para desinstalar
function amp_uninstall() {
    global $wpdb;
    
    // Remover tabela
    $table_name = $wpdb->prefix . 'amp_ads';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
    
    // Remover opções
    delete_option('amp_options');
    
    // Limpar cache
    wp_cache_flush();
}
register_uninstall_hook(__FILE__, 'amp_uninstall');

// Adicionar links na página de plugins
function amp_plugin_action_links($links) {
    $settings_link = '<a href="admin.php?page=adsense-master-pro">' . __('Configurações', 'adsense-master-pro') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'amp_plugin_action_links');

// Hook para adicionar meta box nos posts
function amp_add_meta_boxes() {
    add_meta_box(
        'amp-ad-settings',
        __('Configurações de Anúncios', 'adsense-master-pro'),
        'amp_meta_box_callback',
        array('post', 'page'),
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'amp_add_meta_boxes');

function amp_meta_box_callback($post) {
    wp_nonce_field('amp_meta_box', 'amp_meta_box_nonce');
    
    $disable_ads = get_post_meta($post->ID, '_amp_disable_ads', true);
    ?>
    <p>
        <label>
            <input type="checkbox" name="amp_disable_ads" value="1" <?php checked($disable_ads, 1); ?>>
            <?php _e('Desabilitar anúncios neste post/página', 'adsense-master-pro'); ?>
        </label>
    </p>
    <?php
}

// Salvar configurações do meta box
function amp_save_meta_box($post_id) {
    if (!isset($_POST['amp_meta_box_nonce']) || !wp_verify_nonce($_POST['amp_meta_box_nonce'], 'amp_meta_box')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    $disable_ads = isset($_POST['amp_disable_ads']) ? 1 : 0;
    update_post_meta($post_id, '_amp_disable_ads', $disable_ads);
}
add_action('save_post', 'amp_save_meta_box');

?>