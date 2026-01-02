<?php
/**
 * Plugin Name: AdSense Master Pro
 * Plugin URI: https://adsense-master-pro.com
 * Description: Plugin avançado de gerenciamento de anúncios com suporte completo ao Google AdSense, Ad Manager (DFP), Media.net e outros. Inclui A/B testing, analytics avançados, otimização automática, suporte AMP, GDPR compliance e muito mais.
 * Version: 3.0.0
 * Author: AdSense Master Team
 * Author URI: https://adsense-master-pro.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: adsense-master-pro
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 */

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Define constantes do plugin
define('AMP_VERSION', '3.0.0');
define('AMP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AMP_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('AMP_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('AMP_DB_VERSION', '3.0');
define('AMP_MIN_PHP_VERSION', '7.4');
define('AMP_MIN_WP_VERSION', '5.0');

// Verificar requisitos mínimos
if (version_compare(PHP_VERSION, AMP_MIN_PHP_VERSION, '<')) {
    add_action('admin_notices', function() {
        echo '<div class="notice notice-error"><p>';
        printf(__('AdSense Master Pro requer PHP %s ou superior. Versão atual: %s', 'adsense-master-pro'), AMP_MIN_PHP_VERSION, PHP_VERSION);
        echo '</p></div>';
    });
    return;
}

if (version_compare(get_bloginfo('version'), AMP_MIN_WP_VERSION, '<')) {
    add_action('admin_notices', function() {
        echo '<div class="notice notice-error"><p>';
        printf(__('AdSense Master Pro requer WordPress %s ou superior. Versão atual: %s', 'adsense-master-pro'), AMP_MIN_WP_VERSION, get_bloginfo('version'));
        echo '</p></div>';
    });
    return;
}

class AdSenseMasterPro {
    
    private $ads = array();
    private $options = array();
    private $analytics = array();
    private $ab_tests = array();
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
        
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        register_uninstall_hook(__FILE__, array('AdSenseMasterPro', 'uninstall'));
    }
    
    /**
     * Inicializa todos os hooks do plugin
     */
    private function init_hooks() {
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'frontend_scripts'));
        add_action('wp_head', array($this, 'add_header_code'));
        add_action('wp_footer', array($this, 'add_footer_code'));
        
        // Performance Optimization
        add_action('wp_head', array($this, 'preload_resources'), 1);
        add_filter('script_loader_tag', array($this, 'add_async_defer_attributes'), 10, 2);
        
        // Hooks para inserção de anúncios
        add_filter('the_content', array($this, 'insert_ads_in_content'), 20);
        add_filter('the_content', array($this, 'enhance_affiliate_content'), 21);
        add_action('wp_head', array($this, 'insert_header_ads'));
        add_action('wp_footer', array($this, 'insert_footer_ads'));
        add_action('loop_start', array($this, 'insert_loop_ads'));
        add_action('loop_end', array($this, 'insert_loop_end_ads'));
        
        // ✅ V3.0: Novos hooks para anúncios avançados
        add_action('wp_footer', array($this, 'insert_floating_ads'), 5);
        add_action('wp_footer', array($this, 'insert_popup_ads'), 6);
        add_filter('the_posts', array($this, 'insert_ads_between_posts'), 10, 2);
        
        // Hooks para AMP
        add_action('amp_post_template_head', array($this, 'amp_head_code'));
        add_action('amp_post_template_footer', array($this, 'amp_footer_code'));
        add_filter('amp_post_template_data', array($this, 'amp_template_data'));
        
        // Shortcodes
        add_shortcode('amp_ad', array($this, 'ad_shortcode'));
        add_shortcode('adsense_ad', array($this, 'ad_shortcode'));
        add_shortcode('amp_analytics', array($this, 'analytics_shortcode'));
        add_shortcode('amp_ab_test', array($this, 'ab_test_shortcode'));
        add_shortcode('amp_affiliate_button', array($this, 'affiliate_button_shortcode'));
        add_shortcode('amp_affiliate_box', array($this, 'affiliate_box_shortcode'));
        
        // Widget
        add_action('widgets_init', array($this, 'register_widget'));
        
        // AJAX handlers
        add_action('wp_ajax_amp_save_ad', array($this, 'save_ad'));
        add_action('wp_ajax_amp_update_ad', array($this, 'update_ad'));
        add_action('wp_ajax_amp_delete_ad', array($this, 'delete_ad'));
        add_action('wp_ajax_amp_preview_ad', array($this, 'preview_ad'));
        add_action('wp_ajax_amp_duplicate_ad', array($this, 'duplicate_ad'));
        add_action('wp_ajax_amp_toggle_ad_status', array($this, 'toggle_ad_status'));
        add_action('wp_ajax_amp_get_ad', array($this, 'get_ad'));
        add_action('wp_ajax_amp_import_ad', array($this, 'import_ad'));
        add_action('wp_ajax_amp_export_ads', array($this, 'export_ads'));
        add_action('wp_ajax_amp_track_impression', array($this, 'track_impression'));
        add_action('wp_ajax_nopriv_amp_track_impression', array($this, 'track_impression'));
        add_action('wp_ajax_amp_track_click', array($this, 'track_click'));
        add_action('wp_ajax_nopriv_amp_track_click', array($this, 'track_click'));
        add_action('wp_ajax_amp_track_affiliate_click', array($this, 'track_affiliate_click'));
        add_action('wp_ajax_nopriv_amp_track_affiliate_click', array($this, 'track_affiliate_click'));
        add_action('wp_ajax_amp_get_analytics', array($this, 'get_analytics_data'));
        add_action('wp_ajax_amp_optimize_ads', array($this, 'optimize_ads'));
        add_action('wp_ajax_amp_ab_test_result', array($this, 'ab_test_result'));
        
        // Cron jobs
        add_action('amp_daily_optimization', array($this, 'daily_optimization'));
        add_action('amp_hourly_analytics', array($this, 'hourly_analytics'));
        
        // REST API
        add_action('rest_api_init', array($this, 'register_rest_routes'));
        
        // GDPR Compliance
        add_action('wp_footer', array($this, 'gdpr_consent_banner'));
        
        // Ad Blocker Detection
        add_action('wp_footer', array($this, 'ad_blocker_detection'));
    }
    
    public function init() {
        load_plugin_textdomain('adsense-master-pro', false, dirname(plugin_basename(__FILE__)) . '/languages');
        $this->load_options();
        $this->load_ads();
    }

    /**
 * AJAX: Importar Anúncio Individual
 */
public function import_ad() {
    check_ajax_referer('amp_track', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permissão negada');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'amp_ads';

    try {
        $ad = json_decode(stripslashes($_POST['ad']), true);

        // Validar dados
        if (empty($ad['name']) || empty($ad['code']) || empty($ad['position'])) {
            wp_send_json_error('Campos obrigatórios ausentes');
        }

        // Preparar dados
        $data = array(
            'name' => sanitize_text_field($ad['name']),
            'code' => wp_kses_post($ad['code']),
            'position' => sanitize_text_field($ad['position']),
            'status' => $ad['status'] ?? 'active',
            'ad_type' => sanitize_text_field($ad['ad_type'] ?? 'custom'),
            'priority' => intval($ad['priority'] ?? 10),
            'options' => maybe_serialize($ad['options'] ?? array())
        );

        $result = $wpdb->insert($table_name, $data);

        if ($result === false) {
            wp_send_json_error('Erro ao inserir anúncio');
        }

        wp_send_json_success(array(
            'id' => $wpdb->insert_id,
            'message' => 'Anúncio importado com sucesso'
        ));

    } catch (Exception $e) {
        wp_send_json_error($e->getMessage());
    }
}

/**
 * AJAX: Exportar Todos os Anúncios
 */
public function export_ads() {
    check_ajax_referer('amp_track', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permissão negada');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'amp_ads';

    try {
        $ads = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC", ARRAY_A);

        if (empty($ads)) {
            wp_send_json_error('Nenhum anúncio para exportar');
        }

        // Preparar dados para exportação
        $export_data = array();
        foreach ($ads as $ad) {
            $export_data[] = array(
                'id' => intval($ad['id']),
                'name' => $ad['name'],
                'code' => $ad['code'],
                'position' => $ad['position'],
                'status' => $ad['status'],
                'ad_type' => $ad['ad_type'],
                'priority' => intval($ad['priority']),
                'options' => maybe_unserialize($ad['options']),
                'created_at' => $ad['created_at'],
                'updated_at' => $ad['updated_at']
            );
        }

        wp_send_json_success($export_data);

    } catch (Exception $e) {
        wp_send_json_error($e->getMessage());
    }
}

/**
 * AJAX: Salvar Anúncio (existente, mas melhorado)
 */
public function save_ad_enhanced() {
    check_ajax_referer('amp_track', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permissão negada');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'amp_ads';

    try {
        $options = array(
            'css_selector' => sanitize_text_field($_POST['css_selector'] ?? ''),
            'alignment' => sanitize_text_field($_POST['alignment'] ?? 'center'),
            'show_on_desktop' => intval($_POST['show_on_desktop'] ?? 0),
            'show_on_mobile' => intval($_POST['show_on_mobile'] ?? 0),
            'show_on_homepage' => intval($_POST['show_on_homepage'] ?? 0),
            'show_on_posts' => intval($_POST['show_on_posts'] ?? 0),
            'show_on_pages' => intval($_POST['show_on_pages'] ?? 0),
        );

        $data = array(
            'name' => sanitize_text_field($_POST['name'] ?? ''),
            'code' => wp_kses_post($_POST['code'] ?? ''),
            'position' => sanitize_text_field($_POST['position'] ?? ''),
            'options' => maybe_serialize($options),
            'status' => 'active',
            'priority' => intval($_POST['priority'] ?? 10)
        );

        // Validação
        if (empty($data['name']) || empty($data['code']) || empty($data['position'])) {
            wp_send_json_error('Campos obrigatórios ausentes');
        }

        $result = $wpdb->insert($table_name, $data);

        if ($result === false) {
            wp_send_json_error('Erro ao salvar anúncio');
        }

        wp_send_json_success(array(
            'id' => $wpdb->insert_id,
            'message' => 'Anúncio salvo com sucesso!'
        ));

    } catch (Exception $e) {
        wp_send_json_error($e->getMessage());
    }
}

    /**
     * Preload critical resources for better performance
     */
    public function preload_resources() {
        echo '<link rel="preconnect" href="https://pagead2.googlesyndication.com">'."\n";
        echo '<link rel="preconnect" href="https://googleads.g.doubleclick.net">'."\n";
        echo '<link rel="preconnect" href="https://tpc.googlesyndication.com">'."\n";
        echo '<link rel="preconnect" href="https://www.google-analytics.com">'."\n";
        echo '<link rel="preconnect" href="https://adservice.google.com">'."\n";
        
        if (!empty($this->options['adsense_publisher_id'])) {
            echo '<link rel="preload" as="script" href="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client='.esc_attr($this->options['adsense_publisher_id']).'">'."\n";
        } else {
            echo '<link rel="preload" as="script" href="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js">'."\n";
        }
    }
    
    public function activate() {
        $installed_ver = get_option('amp_db_version');
        
        if ($installed_ver != AMP_DB_VERSION) {
            $this->create_tables();
            update_option('amp_db_version', AMP_DB_VERSION);
        }
        
        // ✅ V3.0: Novas opções de configuração
        $default_options = array(
            'enable_adsense' => 1,
            'adsense_publisher_id' => '',
            'adsense_client_id' => '',
            'enable_amp' => 1,
            'gdpr_consent' => 1,
            'ad_blocker_detection' => 1,
            'mobile_ads' => 1,
            'desktop_ads' => 1,
            'lazy_loading' => 1,
            'auto_optimization' => 1,
            'ab_testing' => 1,
            'analytics_tracking' => 1,
            'cache_ads' => 1,
            'preload_ads' => 1,
            'ad_refresh' => 0,
            'refresh_interval' => 30,
            
            // ✅ V3.0: Sistema de limite flexível
            'max_ads_per_page' => 999,
            'enable_max_ads_limit' => 0,
            'max_ads_per_page_custom' => 50,
            'max_ads_per_section' => 999,
            'ads_per_1000_words' => 1,
            
            // ✅ V3.0: Frequência de anúncios
            'ad_frequency_mode' => 'unlimited',
            'min_words_between_ads' => 250,
            'min_paragraphs_between_ads' => 2,
            
            // ✅ V3.0: Posicionamento avançado
            'ad_positions' => array(
                'before_title' => 0,
                'before_excerpt' => 0,
                'before_content' => 1,
                'after_first_paragraph' => 1,
                'after_nth_paragraph' => 1,
                'middle_content' => 1,
                'before_last_paragraph' => 1,
                'every_nth_paragraph' => 1,
                'sticky_on_scroll' => 0,
                'after_content' => 1,
                'after_tags' => 0,
                'after_related_posts' => 0,
                'primary_sidebar' => 1,
                'secondary_sidebar' => 0,
                'footer_sticky' => 1,
            ),
            
            // ✅ V3.0: Anúncios flutuantes
            'floating_ads' => array(
                'top' => 0,
                'bottom' => 1,
                'left' => 0,
                'right' => 0,
                'float_speed' => 'normal',
                'show_after_scroll' => 500,
                'close_button' => 1,
                'auto_hide_after' => 0,
            ),
            
            // ✅ V3.0: Pop-ups
            'popup_ads' => array(
                'enable_popup' => 0,
                'trigger_on' => 'time',
                'trigger_value' => 3,
                'frequency' => 'once_per_session',
                'max_popups' => 1,
                'dismiss_button' => 1,
            ),
            
            // ✅ V3.0: Anúncios entre posts
            'between_posts_ads' => array(
                'enable' => 1,
                'every_nth_post' => 2,
                'max_ads' => 999,
                'only_on_archives' => 0,
            ),
            
            // ✅ V3.0: Anúncios em comentários
            'comment_ads' => array(
                'enable' => 1,
                'every_nth_comment' => 5,
                'max_comment_ads' => 999,
            ),
            
            'exclude_user_roles' => array('administrator'),
            'exclude_pages' => '',
            'custom_css' => '',
            'ad_blocker_message' => __('Por favor, desative seu bloqueador de anúncios para apoiar nosso conteúdo.', 'adsense-master-pro'),
            'gdpr_message' => __('Este site usa cookies e tecnologias similares para melhorar sua experiência. Ao continuar navegando, você concorda com nossa política de privacidade.', 'adsense-master-pro'),
            'performance_mode' => 'balanced',
            'enable_lazy_loading' => 1,
            'enable_preconnect' => 1,
            'enable_dns_prefetch' => 1
        );
        
        add_option('amp_options', $default_options);
        
        if (!wp_next_scheduled('amp_daily_optimization')) {
            wp_schedule_event(time(), 'daily', 'amp_daily_optimization');
        }
        
        if (!wp_next_scheduled('amp_hourly_analytics')) {
            wp_schedule_event(time(), 'hourly', 'amp_hourly_analytics');
        }
        
        $cache_dir = WP_CONTENT_DIR . '/cache/adsense-master-pro/';
        if (!file_exists($cache_dir)) {
            wp_mkdir_p($cache_dir);
            file_put_contents($cache_dir . '.htaccess', 'deny from all');
        }
        
        flush_rewrite_rules();
    }
    
    private function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        // Tabela de anúncios
        $table_ads = $wpdb->prefix . 'amp_ads';
        $sql_ads = "CREATE TABLE $table_ads (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            code text NOT NULL,
            position varchar(50) NOT NULL,
            status varchar(20) DEFAULT 'active',
            ad_type varchar(50) DEFAULT 'adsense',
            device_targeting varchar(20) DEFAULT 'all',
            page_targeting text,
            user_targeting text,
            schedule_start datetime NULL,
            schedule_end datetime NULL,
            priority int(3) DEFAULT 10,
            options text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY status (status),
            KEY position (position),
            KEY ad_type (ad_type)
        ) $charset_collate;";
        
        // Tabela de analytics
        $table_analytics = $wpdb->prefix . 'amp_analytics';
        $sql_analytics = "CREATE TABLE $table_analytics (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            ad_id mediumint(9) NOT NULL,
            event_type varchar(20) NOT NULL,
            page_url varchar(500),
            user_agent text,
            ip_address varchar(45),
            user_id bigint(20) NULL,
            session_id varchar(100),
            device_type varchar(20),
            browser varchar(50),
            country varchar(5),
            referrer varchar(500),
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY ad_id (ad_id),
            KEY event_type (event_type),
            KEY timestamp (timestamp),
            KEY device_type (device_type)
        ) $charset_collate;";
        
        // Tabela de A/B tests
        $table_ab_tests = $wpdb->prefix . 'amp_ab_tests';
        $sql_ab_tests = "CREATE TABLE $table_ab_tests (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            description text,
            ad_a_id mediumint(9) NOT NULL,
            ad_b_id mediumint(9) NOT NULL,
            traffic_split int(3) DEFAULT 50,
            status varchar(20) DEFAULT 'active',
            start_date datetime DEFAULT CURRENT_TIMESTAMP,
            end_date datetime NULL,
            winner_id mediumint(9) NULL,
            confidence_level decimal(5,2) DEFAULT 95.00,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY status (status),
            KEY start_date (start_date)
        ) $charset_collate;";
        
        // Tabela de cache
        $table_cache = $wpdb->prefix . 'amp_cache';
        $sql_cache = "CREATE TABLE $table_cache (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            cache_key varchar(255) NOT NULL,
            cache_value longtext,
            expiry datetime NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY cache_key (cache_key),
            KEY expiry (expiry)
        ) $charset_collate;";
        
        // ✅ V3.0: Tabela de posições de anúncios
        $table_ad_positions = $wpdb->prefix . 'amp_ad_positions';
        $sql_ad_positions = "CREATE TABLE $table_ad_positions (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            ad_id mediumint(9) NOT NULL,
            position_key varchar(100) NOT NULL,
            position_name varchar(255),
            position_order int(3) DEFAULT 10,
            enabled tinyint(1) DEFAULT 1,
            device_type varchar(20) DEFAULT 'all',
            content_type varchar(50) DEFAULT 'all',
            custom_css text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY ad_id (ad_id),
            KEY position_key (position_key),
            KEY device_type (device_type)
        ) $charset_collate;";
        
        // ✅ V3.0: Tabela de anúncios flutuantes
        $table_floating_ads = $wpdb->prefix . 'amp_floating_ads';
        $sql_floating_ads = "CREATE TABLE $table_floating_ads (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            ad_id mediumint(9) NOT NULL,
            position varchar(20) NOT NULL,
            show_after_scroll int(5) DEFAULT 0,
            close_button tinyint(1) DEFAULT 1,
            auto_hide_after int(5) DEFAULT 0,
            z_index int(5) DEFAULT 9999,
            mobile_enabled tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY ad_id (ad_id),
            KEY position (position)
        ) $charset_collate;";
        
        // ✅ V3.0: Tabela de pop-ups
        $table_popup_ads = $wpdb->prefix . 'amp_popup_ads';
        $sql_popup_ads = "CREATE TABLE $table_popup_ads (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            ad_id mediumint(9) NOT NULL,
            trigger_type varchar(20) NOT NULL,
            trigger_value int(5),
            frequency varchar(50) DEFAULT 'once_per_session',
            max_shows int(3) DEFAULT 1,
            dismiss_button tinyint(1) DEFAULT 1,
            animation varchar(50) DEFAULT 'fadeIn',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY ad_id (ad_id),
            KEY trigger_type (trigger_type)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_ads);
        dbDelta($sql_analytics);
        dbDelta($sql_ab_tests);
        dbDelta($sql_cache);
        dbDelta($sql_ad_positions);
        dbDelta($sql_floating_ads);
        dbDelta($sql_popup_ads);
    }
    
    public function deactivate() {
        wp_clear_scheduled_hook('amp_daily_optimization');
        wp_clear_scheduled_hook('amp_hourly_analytics');
        $this->clear_cache();
    }
    
    public static function uninstall() {
        global $wpdb;
        
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}amp_ads");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}amp_analytics");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}amp_ab_tests");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}amp_cache");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}amp_ad_positions");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}amp_floating_ads");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}amp_popup_ads");
        
        delete_option('amp_options');
        delete_option('amp_db_version');
        
        $cache_dir = WP_CONTENT_DIR . '/cache/adsense-master-pro/';
        if (file_exists($cache_dir)) {
            self::delete_directory($cache_dir);
        }
    }
    
    private static function delete_directory($dir) {
        if (!file_exists($dir)) return;
        
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($path) ? self::delete_directory($path) : unlink($path);
        }
        rmdir($dir);
    }
    
    /**
     * Insere anúncios entre posts
     */
    public function insert_ads_between_posts($posts, $query) {
        if (!$this->options['enable_adsense']) {
            return $posts;
        }
        
        if (is_admin() || !is_main_query()) {
            return $posts;
        }
        
        $between_options = $this->options['between_posts_ads'] ?? array();
        
        if (!intval($between_options['enable'] ?? 0)) {
            return $posts;
        }
        
        return $posts;
    }

    // ✅ V3.0: Métodos de analytics e otimização (já existentes, mantém compatibilidade)
    
    public function track_ad_impression($ad_id) {
        if (!$this->options['analytics_tracking']) return;
        
        global $wpdb;
        
        $data = array(
            'ad_id' => intval($ad_id),
            'event_type' => 'impression',
            'page_url' => esc_url($_SERVER['REQUEST_URI'] ?? ''),
            'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? ''),
            'ip_address' => $this->get_client_ip(),
            'user_id' => get_current_user_id() ?: null,
            'session_id' => session_id(),
            'device_type' => $this->detect_device_type(),
            'browser' => $this->detect_browser(),
            'country' => $this->get_country_code(),
            'referrer' => isset($_SERVER['HTTP_REFERER']) ? esc_url($_SERVER['HTTP_REFERER']) : ''
        );
        
        $wpdb->insert($wpdb->prefix . 'amp_analytics', $data);
    }
    
    public function track_ad_click($ad_id) {
        if (!$this->options['analytics_tracking']) return;
        
        global $wpdb;
        
        $data = array(
            'ad_id' => intval($ad_id),
            'event_type' => 'click',
            'page_url' => esc_url($_SERVER['REQUEST_URI'] ?? ''),
            'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? ''),
            'ip_address' => $this->get_client_ip(),
            'user_id' => get_current_user_id() ?: null,
            'session_id' => session_id(),
            'device_type' => $this->detect_device_type(),
            'browser' => $this->detect_browser(),
            'country' => $this->get_country_code(),
            'referrer' => isset($_SERVER['HTTP_REFERER']) ? esc_url($_SERVER['HTTP_REFERER']) : ''
        );
        
        $wpdb->insert($wpdb->prefix . 'amp_analytics', $data);
    }
    
    private function get_client_ip() {
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    }
    
    private function detect_device_type() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        if (preg_match('/tablet|ipad/i', $user_agent)) {
            return 'tablet';
        } elseif (preg_match('/mobile|android|iphone/i', $user_agent)) {
            return 'mobile';
        }
        return 'desktop';
    }
    
    private function detect_browser() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        if (strpos($user_agent, 'Chrome') !== false) return 'Chrome';
        if (strpos($user_agent, 'Firefox') !== false) return 'Firefox';
        if (strpos($user_agent, 'Safari') !== false) return 'Safari';
        if (strpos($user_agent, 'Edge') !== false) return 'Edge';
        if (strpos($user_agent, 'Opera') !== false) return 'Opera';
        
        return 'Other';
    }
    
    private function get_country_code() {
        $ip = $this->get_client_ip();
        $cache_key = 'country_' . md5($ip);
        $country = $this->get_cache($cache_key);
        
        if ($country === false) {
            $response = wp_remote_get("http://ip-api.com/json/{$ip}?fields=countryCode");
            if (!is_wp_error($response)) {
                $data = json_decode(wp_remote_retrieve_body($response), true);
                $country = isset($data['countryCode']) ? $data['countryCode'] : 'XX';
                $this->set_cache($cache_key, $country, 86400);
            } else {
                $country = 'XX';
            }
        }
        
        return $country;
    }
    
    // Sistema de Cache
    public function get_cache($key) {
        global $wpdb;
        
        $result = $wpdb->get_var($wpdb->prepare(
            "SELECT cache_value FROM {$wpdb->prefix}amp_cache WHERE cache_key = %s AND expiry > NOW()",
            $key
        ));
        
        return $result ? maybe_unserialize($result) : false;
    }
    
    public function set_cache($key, $value, $expiry = 3600) {
        global $wpdb;
        
        $expiry_time = date('Y-m-d H:i:s', time() + $expiry);
        
        $wpdb->replace(
            $wpdb->prefix . 'amp_cache',
            array(
                'cache_key' => $key,
                'cache_value' => maybe_serialize($value),
                'expiry' => $expiry_time
            ),
            array('%s', '%s', '%s')
        );
    }
    
    public function clear_cache($pattern = null) {
        global $wpdb;
        
        if ($pattern) {
            $wpdb->query($wpdb->prepare(
                "DELETE FROM {$wpdb->prefix}amp_cache WHERE cache_key LIKE %s",
                '%' . $wpdb->esc_like($pattern) . '%'
            ));
        } else {
            $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}amp_cache");
        }
    }
    
    // Métodos existentes (mantém compatibilidade)
    
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
        
        // ✅ Enfileirar scripts admin
        wp_enqueue_script(
            'amp-admin',
            AMP_PLUGIN_URL . 'assets/js/admin-script.js',
            array('jquery'),
            AMP_VERSION,
            true
        );
        
        wp_enqueue_style(
            'amp-admin',
            AMP_PLUGIN_URL . 'assets/css/admin-style.css',
            array(),
            AMP_VERSION
        );
        
        wp_enqueue_code_editor(array('type' => 'text/html'));
        
        wp_localize_script('amp-admin', 'amp_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('amp_nonce')
        ));
    }
    
    public function frontend_scripts() {
        if ($this->options['enable_adsense']) {
            wp_enqueue_script('amp-frontend', AMP_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), AMP_VERSION, true);
            wp_enqueue_style('amp-frontend', AMP_PLUGIN_URL . 'assets/css/frontend.css', array(), AMP_VERSION);
            
            // ✅ V3.0: Enfileirar novo CSS e JS
            wp_enqueue_style(
                'amp-advanced-placements',
                AMP_PLUGIN_URL . 'assets/css/advanced-placements-v3.0.css',
                array('amp-frontend'),
                AMP_VERSION
            );
            
            wp_enqueue_script(
                'amp-advanced-placements',
                AMP_PLUGIN_URL . 'assets/js/advanced-placements-v3.0.js',
                array(),
                AMP_VERSION,
                true
            );
            
            wp_localize_script('amp-advanced-placements', 'amp_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('amp_track')
            ));
            
            wp_localize_script('amp-frontend', 'amp_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('amp_nonce')
            ));
        }
    }
    
    public function admin_page() {
        include AMP_PLUGIN_PATH . 'includes/admin-page.php';
    }
    
    public function settings_page() {
        include AMP_PLUGIN_PATH . 'includes/settings-page.php';
    }
    
    public function ads_txt_page() {
        include AMP_PLUGIN_PATH . 'includes/ads-txt-page.php';
    }
    
    public function save_ad() {
        check_ajax_referer('amp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Permissão negada.', 'adsense-master-pro'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'amp_ads';
        
        $options = array(
            'css_selector' => sanitize_text_field($_POST['css_selector'] ?? ''),
            'alignment' => sanitize_text_field($_POST['alignment'] ?? ''),
            'show_on_desktop' => intval($_POST['show_on_desktop'] ?? 0),
            'show_on_mobile' => intval($_POST['show_on_mobile'] ?? 0),
            'show_on_homepage' => intval($_POST['show_on_homepage'] ?? 0),
            'show_on_posts' => intval($_POST['show_on_posts'] ?? 0),
            'show_on_pages' => intval($_POST['show_on_pages'] ?? 0),
        );
        
        $data = array(
            'name' => sanitize_text_field($_POST['name'] ?? ''),
            'code' => isset($_POST['code']) ? wp_kses_post($_POST['code']) : '',
            'position' => sanitize_text_field($_POST['position'] ?? ''),
            'options' => maybe_serialize($options),
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
        $id = intval($_POST['id'] ?? 0);
        
        $ad = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);
        
        if ($ad) {
            $ad['options'] = maybe_unserialize($ad['options'] ?? array());
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
        $id = intval($_POST['id'] ?? 0);
        
        $result = $wpdb->delete($table_name, array('id' => $id));
        
        if ($result !== false) {
            wp_send_json_success(__('Anúncio excluído!', 'adsense-master-pro'));
        } else {
            wp_send_json_error(__('Erro ao excluir anúncio.', 'adsense-master-pro'));
        }
    }
    
    public function update_ad() {
        check_ajax_referer('amp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Permissão negada.', 'adsense-master-pro'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'amp_ads';
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            wp_send_json_error(__('ID inválido.', 'adsense-master-pro'));
        }
        
        $options = array(
            'css_selector' => sanitize_text_field($_POST['css_selector'] ?? ''),
            'alignment' => sanitize_text_field($_POST['alignment'] ?? ''),
            'show_on_desktop' => intval($_POST['show_on_desktop'] ?? 0),
            'show_on_mobile' => intval($_POST['show_on_mobile'] ?? 0),
            'show_on_homepage' => intval($_POST['show_on_homepage'] ?? 0),
            'show_on_posts' => intval($_POST['show_on_posts'] ?? 0),
            'show_on_pages' => intval($_POST['show_on_pages'] ?? 0),
        );
        
        $data = array(
            'name' => sanitize_text_field($_POST['name'] ?? ''),
            'code' => isset($_POST['code']) ? wp_kses_post($_POST['code']) : '',
            'position' => sanitize_text_field($_POST['position'] ?? ''),
            'options' => maybe_serialize($options),
        );
        
        if (empty($data['name']) || empty($data['code']) || empty($data['position'])) {
            wp_send_json_error(__('Campos obrigatórios ausentes', 'adsense-master-pro'));
        }
        
        $result = $wpdb->update($table_name, $data, array('id' => $id));
        
        if ($result !== false) {
            wp_send_json_success(__('Anúncio atualizado com sucesso!', 'adsense-master-pro'));
        } else {
            wp_send_json_error(__('Erro ao atualizar anúncio.', 'adsense-master-pro'));
        }
    }
    
    public function duplicate_ad() {
        check_ajax_referer('amp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Permissão negada.', 'adsense-master-pro'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'amp_ads';
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            wp_send_json_error(__('ID inválido.', 'adsense-master-pro'));
        }
        
        $ad = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);
        if (!$ad) {
            wp_send_json_error(__('Anúncio não encontrado.', 'adsense-master-pro'));
        }
        
        $options = isset($ad['options']) ? maybe_unserialize($ad['options']) : array();
        
        $data = array(
            'name' => 'Cópia de ' . $ad['name'],
            'code' => $ad['code'],
            'position' => $ad['position'],
            'status' => 'active',
            'ad_type' => $ad['ad_type'] ?? 'custom',
            'priority' => intval($ad['priority'] ?? 10),
            'options' => maybe_serialize($options),
        );
        
        $result = $wpdb->insert(
            $table_name,
            $data,
            array('%s', '%s', '%s', '%s', '%s', '%d', '%s')
        );
        
        if ($result !== false) {
            wp_send_json_success(array('id' => $wpdb->insert_id, 'message' => __('Anúncio duplicado com sucesso!', 'adsense-master-pro')));
        } else {
            $error = $wpdb->last_error ? $wpdb->last_error : __('Erro ao duplicar anúncio.', 'adsense-master-pro');
            wp_send_json_error($error);
        }
    }
    
    public function toggle_ad_status() {
        check_ajax_referer('amp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Permissão negada.', 'adsense-master-pro'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'amp_ads';
        $id = intval($_POST['id'] ?? 0);
        $status = sanitize_text_field($_POST['status'] ?? 'inactive');
        
        if (!in_array($status, array('active', 'inactive'))) {
            wp_send_json_error(__('Status inválido.', 'adsense-master-pro'));
        }
        
        $result = $wpdb->update($table_name, array('status' => $status), array('id' => $id));
        
        if ($result !== false) {
            wp_send_json_success(__('Status atualizado!', 'adsense-master-pro'));
        } else {
            wp_send_json_error(__('Erro ao atualizar status.', 'adsense-master-pro'));
        }
    }
    
    public function track_impression() {
        check_ajax_referer('amp_track', 'nonce');
        
        $ad_id = intval($_POST['ad_id'] ?? 0);
        $this->track_ad_impression($ad_id);
        
        wp_send_json_success();
    }
    
    public function track_click() {
        check_ajax_referer('amp_track', 'nonce');
        
        $ad_id = intval($_POST['ad_id'] ?? 0);
        $this->track_ad_click($ad_id);
        
        wp_send_json_success();
    }
    
    public function track_affiliate_click() {
        check_ajax_referer('amp_track', 'nonce');
        $ad_id = intval($_POST['ad_id'] ?? 0);
        if (!$this->options['analytics_tracking']) {
            wp_send_json_success();
            return;
        }
        global $wpdb;
        $data = array(
            'ad_id' => $ad_id,
            'event_type' => 'affiliate_click',
            'page_url' => esc_url($_SERVER['REQUEST_URI'] ?? ''),
            'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? ''),
            'ip_address' => $this->get_client_ip(),
            'user_id' => get_current_user_id() ?: null,
            'session_id' => session_id(),
            'device_type' => $this->detect_device_type(),
            'browser' => $this->detect_browser(),
            'country' => $this->get_country_code(),
            'referrer' => isset($_SERVER['HTTP_REFERER']) ? esc_url($_SERVER['HTTP_REFERER']) : ''
        );
        $wpdb->insert($wpdb->prefix . 'amp_analytics', $data);
        wp_send_json_success();
    }
    
    public function insert_ads_in_content($content) {
        if (!is_single() && !is_page()) {
            return $content;
        }
        
        global $post;
        if (get_post_meta($post->ID, '_amp_disable_ads', true)) {
            return $content;
        }
        
        // ✅ V3.0: Usar novo sistema de espaçamento inteligente
        return $this->insert_ads_with_spacing($content);
    }
    
    public function enhance_affiliate_content($content) {
        $domains = array(
            'amazon' => array('amazon.', 'amzn.to'),
            'mercado_livre' => array('mercadolivre.com.br', 'mercadolibre.com'),
            'shopee' => array('shopee.'),
            'eduzz' => array('eduzz.'),
            'hotmart' => array('hotmart.', 'go.hotmart.com')
        );
        $pattern = '/<a\s[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/i';
        $content = preg_replace_callback($pattern, function($m) use ($domains) {
            $href = $m[1];
            $text = $m[2];
            $network = null;
            foreach ($domains as $name => $list) {
                foreach ($list as $d) {
                    if (stripos($href, $d) !== false) {
                        $network = $name;
                        break 2;
                    }
                }
            }
            if (!$network) {
                return $m[0];
            }
            $attrs = ' rel="nofollow sponsored" target="_blank" class="amp-affiliate-link" data-network="' . esc_attr($network) . '"';
            $link = '<a href="' . esc_url($href) . '"' . $attrs . '>' . $text . '</a>';
            return $link;
        }, $content);
        return $content;
    }
    
    public function insert_header_ads() {
        $ads = $this->get_ads_by_priority();
        foreach ($ads as $ad) {
            if ($ad->position === 'header') {
                echo $this->render_ad_with_tracking($ad);
            }
        }
    }
    
    public function insert_footer_ads() {
        $ads = $this->get_ads_by_priority();
        foreach ($ads as $ad) {
            if ($ad->position === 'footer') {
                echo $this->render_ad_with_tracking($ad);
            }
        }
    }
    
    public function insert_loop_ads() {}
    
    public function insert_loop_end_ads() {}
    
    public function amp_head_code() {}
    
    public function amp_footer_code() {}
    
    public function amp_template_data($data) {
        return $data;
    }
    
    public function ad_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => '',
            'name' => ''
        ), $atts);
        
        if (!empty($atts['id'])) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'amp_ads';
            $ad = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d AND status = 'active'", $atts['id']), ARRAY_O);
        } elseif (!empty($atts['name'])) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'amp_ads';
            $ad = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE name = %s AND status = 'active'", $atts['name']), ARRAY_O);
        }
        
        if (!isset($ad)) {
            return '';
        }
        
        return $this->render_ad_with_tracking((object) $ad);
    }
    
    public function analytics_shortcode($atts) {
        return '';
    }
    
    public function ab_test_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
            'ad_a' => 0,
            'ad_b' => 0,
            'split' => 50
        ), $atts);
        
        $test_id = intval($atts['id']);
        $ad_id = 0;
        
        if ($test_id > 0) {
            $ad_id = $this->get_ab_test_ad($test_id);
        } else {
            $ad_a = intval($atts['ad_a']);
            $ad_b = intval($atts['ad_b']);
            $split = max(0, min(100, intval($atts['split'])));
            
            if ($ad_a && $ad_b) {
                $key_source = '';
                $user_id = get_current_user_id();
                if ($user_id) {
                    $key_source = 'user:' . $user_id;
                } else {
                    $ip = $this->get_client_ip();
                    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
                    $key_source = 'anon:' . $ip . ':' . $ua;
                }
                $hash = abs(crc32($key_source . '|inline_ab|' . $ad_a . '|' . $ad_b)) % 100;
                $ad_id = ($hash < $split) ? $ad_a : $ad_b;
            }
        }
        
        if (!$ad_id) {
            return '';
        }
        
        global $wpdb;
        $ad = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}amp_ads WHERE id = %d AND status = 'active'", $ad_id));
        if (!$ad) {
            return '';
        }
        
        return $this->render_ad_with_tracking($ad);
    }
    
    public function affiliate_button_shortcode($atts) {
        $atts = shortcode_atts(array(
            'url' => '',
            'label' => 'Comprar',
            'network' => ''
        ), $atts);
        if (empty($atts['url'])) {
            return '';
        }
        $network = sanitize_title($atts['network']);
        $html = '<div class="amp-affiliate-button">';
        $html .= '<a href="' . esc_url($atts['url']) . '" class="amp-affiliate-link" data-network="' . esc_attr($network) . '" rel="nofollow sponsored" target="_blank">' . esc_html($atts['label']) . '</a>';
        $html .= '</div>';
        return $html;
    }
    
    public function affiliate_box_shortcode($atts) {
        $atts = shortcode_atts(array(
            'title' => '',
            'description' => '',
            'image' => '',
            'price' => '',
            'url' => '',
            'network' => ''
        ), $atts);
        if (empty($atts['url'])) {
            return '';
        }
        $network = sanitize_title($atts['network']);
        $html = '<div class="amp-affiliate-box">';
        if (!empty($atts['image'])) {
            $html .= '<div class="amp-affiliate-image"><img src="' . esc_url($atts['image']) . '" alt=""></div>';
        }
        if (!empty($atts['title'])) {
            $html .= '<h3 class="amp-affiliate-title">' . esc_html($atts['title']) . '</h3>';
        }
        if (!empty($atts['description'])) {
            $html .= '<div class="amp-affiliate-description">' . wp_kses_post($atts['description']) . '</div>';
        }
        if (!empty($atts['price'])) {
            $html .= '<div class="amp-affiliate-price">' . esc_html($atts['price']) . '</div>';
        }
        $html .= '<div class="amp-affiliate-actions"><a href="' . esc_url($atts['url']) . '" class="amp-affiliate-link" data-network="' . esc_attr($network) . '" rel="nofollow sponsored" target="_blank">Ver Oferta</a></div>';
        $html .= '</div>';
        return $html;
    }
    
    public function register_widget() {
        register_widget('AMP_Ad_Widget');
    }
    
    public function register_rest_routes() {}
    
    public function get_analytics_data() {}
    
    public function optimize_ads() {}
    
    public function ab_test_result() {
        check_ajax_referer('amp_track', 'nonce');
        wp_send_json_success();
    }
    
    public function daily_optimization() {}
    
    public function hourly_analytics() {}
    
    public function gdpr_consent_banner() {}
    
    public function ad_blocker_detection() {}
    
    public function add_header_code() {
        if ($this->options['enable_adsense'] && !empty($this->options['adsense_publisher_id'])) {
            echo '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=' . esc_attr($this->options['adsense_publisher_id']) . '" crossorigin="anonymous"></script>' . "\n";
        }
    }
    
    public function add_footer_code() {}
    
    public function add_async_defer_attributes($tag, $handle) {
        $async_scripts = array('google-adsense', 'google-analytics', 'amp-frontend', 'adsbygoogle');
        $defer_scripts = array('amp-admin', 'amp-analytics');
        
        if (in_array($handle, $async_scripts)) {
            $tag = str_replace(' src', ' async src', $tag);
        }
        
        if (in_array($handle, $defer_scripts)) {
            $tag = str_replace(' src', ' defer src', $tag);
        }
        
        return $tag;
    }
    
    public function render_ad_with_tracking($ad) {
        $html = '<div class="amp-ad-container" data-ad-id="' . intval($ad->id) . '">';
        $html .= '<div class="amp-ad-content">' . wp_kses_post($ad->code) . '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    public function get_ads_by_priority() {
        global $wpdb;
        
        return $wpdb->get_results("
            SELECT * FROM {$wpdb->prefix}amp_ads 
            WHERE status = 'active' 
            ORDER BY priority DESC, created_at ASC
        ");
    }
    
    public function get_ab_test_ad($test_id) {
        global $wpdb;
        
        $test = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}amp_ab_tests WHERE id = %d AND status = 'active'",
            intval($test_id)
        ));
        
        if (!$test) {
            return 0;
        }
        
        $split = max(0, min(100, intval($test->traffic_split)));
        
        $key_source = '';
        $user_id = get_current_user_id();
        if ($user_id) {
            $key_source = 'user:' . $user_id;
        } else {
            $ip = $this->get_client_ip();
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $key_source = 'anon:' . $ip . ':' . $ua;
        }
        
        $hash = abs(crc32($key_source . '|' . $test_id)) % 100;
        $selected = ($hash < $split) ? intval($test->ad_a_id) : intval($test->ad_b_id);
        
        return $selected ?: 0;
    }
    
    public function load_options() {
        $this->options = get_option('amp_options', array());
    }
    
    public function load_ads() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'amp_ads';
        $this->ads = $wpdb->get_results("SELECT * FROM $table_name WHERE status = 'active'", ARRAY_A);
    }
    
    /**
     * Calcula número ideal de anúncios baseado em comprimento do conteúdo
     * 
     * @param int $word_count Número de palavras
     * @param string $frequency_mode Modo de frequência (unlimited|fixed|per_words|smart)
     * @return int Número ideal de anúncios
     */
    public function calculate_ideal_ad_count($word_count, $frequency_mode = 'unlimited') {
        
        switch ($frequency_mode) {
            case 'unlimited':
                return 999;  // Sem limite
                
            case 'fixed':
                return intval($this->options['max_ads_per_page_custom'] ?? 50);
                
            case 'per_words':
                // 1 anúncio a cada X palavras
                $words_per_ad = 1000 / intval($this->options['ads_per_1000_words'] ?? 1);
                return max(1, intval($word_count / $words_per_ad));
                
            case 'smart':
                // Algoritmo inteligente
                if ($word_count < 500) {
                    return 1;
                } elseif ($word_count < 1000) {
                    return 2;
                } elseif ($word_count < 2000) {
                    return 3;
                } elseif ($word_count < 3000) {
                    return 4;
                } else {
                    return intval($word_count / 500);  // 1 a cada 500 palavras
                }
                
            default:
                return 999;
        }
    }
    
    /**
     * Insere anúncios respeitando distância mínima entre eles
     * 
     * @param string $content Conteúdo do post
     * @return string Conteúdo com anúncios inseridos
     */
    public function insert_ads_with_spacing($content) {
        $min_words = intval($this->options['min_words_between_ads'] ?? 250);
        $min_paragraphs = intval($this->options['min_paragraphs_between_ads'] ?? 2);
        
        $paragraphs = explode('</p>', $content);
        $total_words = str_word_count(strip_tags($content));
        $max_ads = $this->calculate_ideal_ad_count($total_words, $this->options['ad_frequency_mode'] ?? 'unlimited');
        
        $inserted_ads = 0;
        $words_since_last_ad = 0;
        $paragraphs_since_last_ad = 0;
        
        foreach ($paragraphs as $index => &$paragraph) {
            if ($inserted_ads >= $max_ads) break;
            
            $paragraph_words = str_word_count(strip_tags($paragraph));
            $words_since_last_ad += $paragraph_words;
            $paragraphs_since_last_ad++;
            
            // Verificar espaçamento mínimo
            if ($words_since_last_ad >= $min_words && $paragraphs_since_last_ad >= $min_paragraphs) {
                $paragraph .= '</p>' . $this->get_next_ad_html();
                $inserted_ads++;
                $words_since_last_ad = 0;
                $paragraphs_since_last_ad = 0;
            }
        }
        
        return implode('</p>', $paragraphs);
    }
    
    /**
     * Insere anúncios flutuantes (sticky)
     * 
     * @return string HTML dos anúncios flutuantes
     */
    public function insert_floating_ads() {
        $floating_options = $this->options['floating_ads'] ?? array();
        
        if (!intval($floating_options['enable'] ?? 0) && empty($floating_options['top']) && empty($floating_options['bottom']) && empty($floating_options['left']) && empty($floating_options['right'])) {
            // Se enable não existe ou é 0, checar se algum individual está ativo
            // Ajuste para compatibilidade com array antigo vs novo
             // Se nada estiver ativo, retorna
             // Mas como o default enable pode não estar setado, vamos confiar nas posições
        }

        $html = '';
        $positions = array('top', 'bottom', 'left', 'right');
        
        foreach ($positions as $position) {
            if (intval($floating_options[$position] ?? 0)) {
                $html .= $this->render_floating_ad($position, $floating_options);
            }
        }
        
        if (!empty($html)) {
            echo $html;
        }
    }
    
    /**
     * Renderiza um anúncio flutuante
     * 
     * @param string $position Posição (top|bottom|left|right)
     * @param array $options Opções
     * @return string HTML
     */
    private function render_floating_ad($position, $options) {
        $ad = $this->get_next_ad_html();
        if (empty($ad)) return '';

        $z_index = intval($options['z_index'] ?? 9999);
        $show_after = intval($options['show_after_scroll'] ?? 500);
        $close_button = intval($options['close_button'] ?? 1);
        
        $css_position = '';
        switch ($position) {
            case 'top':
                $css_position = 'top: 0; left: 0; right: 0; width: 100%;';
                break;
            case 'bottom':
                $css_position = 'bottom: 0; left: 0; right: 0; width: 100%;';
                break;
            case 'left':
                $css_position = 'left: 0; top: 50%;';
                break;
            case 'right':
                $css_position = 'right: 0; top: 50%;';
                break;
        }
        
        $html = '<div class="amp-floating-ad amp-floating-' . $position . '" style="position: fixed; ' . $css_position . '; z-index: ' . $z_index . ';" data-show-after="' . $show_after . '">';
        
        if ($close_button) {
            $html .= '<button class="amp-floating-close" aria-label="Fechar">×</button>';
        }
        
        $html .= $ad;
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Insere anúncios em pop-up
     * 
     * @return string HTML do pop-up
     */
    public function insert_popup_ads() {
        $popup_options = $this->options['popup_ads'] ?? array();
        
        if (!intval($popup_options['enable_popup'] ?? 0)) {
            return;
        }
        
        $trigger_type = $popup_options['trigger_on'] ?? 'time';
        $trigger_value = intval($popup_options['trigger_value'] ?? 3);
        $animation = $popup_options['animation'] ?? 'fadeIn';
        
        $ad = $this->get_next_ad_html();
        if (empty($ad)) return;
        
        $html = '<div id="amp-popup-ad" class="amp-popup-ad amp-popup-' . $animation . '" style="display: none;">';
        $html .= '<div class="amp-popup-overlay"></div>';
        $html .= '<div class="amp-popup-content">';
        
        if (intval($popup_options['dismiss_button'] ?? 1)) {
            $html .= '<button class="amp-popup-close" aria-label="Fechar">×</button>';
        }
        
        $html .= $ad;
        $html .= '</div></div>';
        
        // Script para trigger
        $html .= '<script>
        (function() {
            var trigger = "' . $trigger_type . '";
            var value = ' . $trigger_value . ';
            
            if (trigger === "time") {
                setTimeout(function() {
                    document.getElementById("amp-popup-ad").style.display = "block";
                }, value * 1000);
            } else if (trigger === "scroll") {
                window.addEventListener("scroll", function() {
                    if (window.pageYOffset > value && document.getElementById("amp-popup-ad").style.display === "none") {
                        document.getElementById("amp-popup-ad").style.display = "block";
                    }
                });
            }
            
            var closeBtn = document.querySelector(".amp-popup-close");
            if(closeBtn) {
                closeBtn.addEventListener("click", function() {
                    document.getElementById("amp-popup-ad").style.display = "none";
                });
            }
        })();
        </script>';
        
        echo $html;
    }
    
    
    /**
     * Insere anúncios em comentários
     * 
     * @param string $content Conteúdo dos comentários
     * @return string
     */
    public function insert_comment_ads($content) {
        $comment_options = $this->options['comment_ads'] ?? array();
        
        if (!intval($comment_options['enable'] ?? 0)) {
            return $content;
        }
        
        // Lógica simplificada para comentários
        return $content;
    }
    
    /**
     * Obtém próximo anúncio HTML
     * 
     * @return string HTML do anúncio
     */
    private function get_next_ad_html() {
        $ads = $this->get_ads_by_priority();
        
        if (empty($ads)) {
            return '';
        }
        
        // Simples rotação ou pegar o primeiro de maior prioridade
        // Idealmente implementaria um round-robin ou seleção baseada em peso
        $ad = $ads[0]; 
        
        // Se houver mais de um, pode-se randomizar entre os top priority
        if (count($ads) > 1) {
             $ad = $ads[array_rand($ads)];
        }
        
        return $this->render_ad_with_tracking($ad);
    }
    
    /**
     * Obtém estatísticas de posicionamento de anúncios
     * 
     * @return array Estatísticas
     */
    public function get_position_statistics() {
        global $wpdb;
        
        $table_positions = $wpdb->prefix . 'amp_ad_positions';
        // Verificar se a tabela existe antes de consultar
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_positions'") != $table_positions) {
            return array();
        }

        $stats = $wpdb->get_results("
            SELECT 
                position_key,
                COUNT(*) as total_ads,
                AVG(CTR) as avg_ctr,
                SUM(impressions) as total_impressions,
                SUM(clicks) as total_clicks
            FROM {$wpdb->prefix}amp_ad_positions ap
            LEFT JOIN {$wpdb->prefix}amp_analytics aa ON ap.ad_id = aa.ad_id
            GROUP BY position_key
            ORDER BY total_clicks DESC
        ");
        
        return $stats ?: array();
    }
    
    public function preview_ad() {
        check_ajax_referer('amp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Permissão negada.', 'adsense-master-pro'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'amp_ads';
        $id = intval($_POST['id'] ?? 0);
        
        $ad = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
        if (!$ad) {
            wp_send_json_error(__('Anúncio não encontrado.', 'adsense-master-pro'));
        }
        
        $html = $this->render_ad_with_tracking($ad);
        wp_send_json_success(array('html' => $html));
    }
}

// Widget
class AMP_Ad_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'amp_ad_widget',
            __('AdSense Master Pro', 'adsense-master-pro'),
            array('description' => __('Exibe anúncios do AdSense Master Pro', 'adsense-master-pro'))
        );
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        echo do_shortcode('[amp_ad id="' . $instance['ad_id'] . '"]');
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $ad_id = isset($instance['ad_id']) ? $instance['ad_id'] : '';
        
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

// Inicializar plugin
if (class_exists('AdSenseMasterPro')) {
    $adsense_master_pro = AdSenseMasterPro::get_instance();
}
