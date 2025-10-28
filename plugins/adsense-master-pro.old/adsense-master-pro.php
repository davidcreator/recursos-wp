<?php
/**
 * Plugin Name: AdSense Master Pro
 * Plugin URI: https://adsense-master-pro.com
 * Description: Plugin avançado de gerenciamento de anúncios com suporte completo ao Google AdSense, Ad Manager (DFP), Media.net e outros. Inclui A/B testing, analytics avançados, otimização automática, suporte AMP, GDPR compliance e muito mais.
 * Version: 2.0.0
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
define('AMP_VERSION', '2.0.0');
define('AMP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AMP_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('AMP_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('AMP_DB_VERSION', '2.0');
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
        add_action('wp_head', array($this, 'insert_header_ads'));
        add_action('wp_footer', array($this, 'insert_footer_ads'));
        add_action('loop_start', array($this, 'insert_loop_ads'));
        add_action('loop_end', array($this, 'insert_loop_end_ads'));
        
        // Hooks para AMP
        add_action('amp_post_template_head', array($this, 'amp_head_code'));
        add_action('amp_post_template_footer', array($this, 'amp_footer_code'));
        add_filter('amp_post_template_data', array($this, 'amp_template_data'));
        
        // Shortcodes
        add_shortcode('amp_ad', array($this, 'ad_shortcode'));
        add_shortcode('adsense_ad', array($this, 'ad_shortcode'));
        add_shortcode('amp_analytics', array($this, 'analytics_shortcode'));
        add_shortcode('amp_ab_test', array($this, 'ab_test_shortcode'));
        
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
     * Preload critical resources for better performance
     */
    public function preload_resources() {
        // Pré-carregar recursos do Google AdSense para melhorar o desempenho
        echo '<link rel="preconnect" href="https://pagead2.googlesyndication.com">'."\n";
        echo '<link rel="preconnect" href="https://googleads.g.doubleclick.net">'."\n";
        echo '<link rel="preconnect" href="https://tpc.googlesyndication.com">'."\n";
        echo '<link rel="preconnect" href="https://www.google-analytics.com">'."\n";
        echo '<link rel="preconnect" href="https://adservice.google.com">'."\n";
        
        // Pré-carregar o script do AdSense
        if (!empty($this->settings['publisher_id'])) {
            echo '<link rel="preload" as="script" href="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client='.esc_attr($this->settings['publisher_id']).'">'."\n";
        } else {
            echo '<link rel="preload" as="script" href="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js">'."\n";
        }
    }
    
    public function activate() {
        // Verificar versão do banco de dados
        $installed_ver = get_option('amp_db_version');
        
        if ($installed_ver != AMP_DB_VERSION) {
            $this->create_tables();
            update_option('amp_db_version', AMP_DB_VERSION);
        }
        
        // Configurações padrão
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
            'max_ads_per_page' => 10,
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
        
        // Agendar tarefas cron
        if (!wp_next_scheduled('amp_daily_optimization')) {
            wp_schedule_event(time(), 'daily', 'amp_daily_optimization');
        }
        
        if (!wp_next_scheduled('amp_hourly_analytics')) {
            wp_schedule_event(time(), 'hourly', 'amp_hourly_analytics');
        }
        
        // Criar diretório de cache
        $cache_dir = WP_CONTENT_DIR . '/cache/adsense-master-pro/';
        if (!file_exists($cache_dir)) {
            wp_mkdir_p($cache_dir);
            file_put_contents($cache_dir . '.htaccess', 'deny from all');
        }
        
        // Flush rewrite rules
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
        
        // Tabela de cache de performance
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
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_ads);
        dbDelta($sql_analytics);
        dbDelta($sql_ab_tests);
        dbDelta($sql_cache);
        
        add_option('amp_options', $default_options);
    }
    
    public function deactivate() {
        // Limpar hooks agendados
        wp_clear_scheduled_hook('amp_daily_optimization');
        wp_clear_scheduled_hook('amp_hourly_analytics');
        
        // Limpar cache
        $this->clear_cache();
    }
    
    public function uninstall() {
        global $wpdb;
        
        // Remover tabelas
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}amp_ads");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}amp_analytics");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}amp_ab_tests");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}amp_cache");
        
        // Remover opções
        delete_option('amp_options');
        delete_option('amp_db_version');
        
        // Remover diretório de cache
        $cache_dir = WP_CONTENT_DIR . '/cache/adsense-master-pro/';
        if (file_exists($cache_dir)) {
            $this->delete_directory($cache_dir);
        }
    }
    
    private function delete_directory($dir) {
        if (!file_exists($dir)) return;
        
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($path) ? $this->delete_directory($path) : unlink($path);
        }
        rmdir($dir);
    }
    
    // Analytics e Tracking
    public function track_ad_impression($ad_id) {
        if (!$this->options['analytics_tracking']) return;
        
        global $wpdb;
        
        $data = array(
            'ad_id' => intval($ad_id),
            'event_type' => 'impression',
            'page_url' => esc_url($_SERVER['REQUEST_URI']),
            'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT']),
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
            'page_url' => esc_url($_SERVER['REQUEST_URI']),
            'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT']),
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
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        
        if (preg_match('/tablet|ipad/i', $user_agent)) {
            return 'tablet';
        } elseif (preg_match('/mobile|android|iphone/i', $user_agent)) {
            return 'mobile';
        }
        return 'desktop';
    }
    
    private function detect_browser() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        
        if (strpos($user_agent, 'Chrome') !== false) return 'Chrome';
        if (strpos($user_agent, 'Firefox') !== false) return 'Firefox';
        if (strpos($user_agent, 'Safari') !== false) return 'Safari';
        if (strpos($user_agent, 'Edge') !== false) return 'Edge';
        if (strpos($user_agent, 'Opera') !== false) return 'Opera';
        
        return 'Other';
    }
    
    private function get_country_code() {
        // Implementação básica - pode ser melhorada com serviços de geolocalização
        $ip = $this->get_client_ip();
        
        // Cache do país por IP
        $cache_key = 'country_' . md5($ip);
        $country = $this->get_cache($cache_key);
        
        if ($country === false) {
            // Usar serviço gratuito de geolocalização
            $response = wp_remote_get("http://ip-api.com/json/{$ip}?fields=countryCode");
            if (!is_wp_error($response)) {
                $data = json_decode(wp_remote_retrieve_body($response), true);
                $country = isset($data['countryCode']) ? $data['countryCode'] : 'XX';
                $this->set_cache($cache_key, $country, 86400); // Cache por 24 horas
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
    
    // A/B Testing
    public function create_ab_test($name, $description, $ad_a_id, $ad_b_id, $traffic_split = 50) {
        global $wpdb;
        
        $data = array(
            'name' => sanitize_text_field($name),
            'description' => sanitize_textarea_field($description),
            'ad_a_id' => intval($ad_a_id),
            'ad_b_id' => intval($ad_b_id),
            'traffic_split' => intval($traffic_split),
            'status' => 'active'
        );
        
        return $wpdb->insert($wpdb->prefix . 'amp_ab_tests', $data);
    }
    
    public function get_ab_test_ad($test_id) {
        global $wpdb;
        
        $test = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}amp_ab_tests WHERE id = %d AND status = 'active'",
            $test_id
        ));
        
        if (!$test) return false;
        
        // Determinar qual anúncio mostrar baseado no traffic split
        $random = rand(1, 100);
        $ad_id = ($random <= $test->traffic_split) ? $test->ad_a_id : $test->ad_b_id;
        
        return $this->get_ad($ad_id);
    }
    
    public function analyze_ab_test($test_id) {
        global $wpdb;
        
        $test = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}amp_ab_tests WHERE id = %d",
            $test_id
        ));
        
        if (!$test) return false;
        
        // Obter estatísticas para ambos os anúncios
        $stats_a = $this->get_ad_stats($test->ad_a_id);
        $stats_b = $this->get_ad_stats($test->ad_b_id);
        
        // Calcular CTR
        $ctr_a = $stats_a['impressions'] > 0 ? ($stats_a['clicks'] / $stats_a['impressions']) * 100 : 0;
        $ctr_b = $stats_b['impressions'] > 0 ? ($stats_b['clicks'] / $stats_b['impressions']) * 100 : 0;
        
        // Determinar vencedor (simplificado)
        $winner_id = null;
        if ($ctr_a > $ctr_b && $stats_a['impressions'] >= 100) {
            $winner_id = $test->ad_a_id;
        } elseif ($ctr_b > $ctr_a && $stats_b['impressions'] >= 100) {
            $winner_id = $test->ad_b_id;
        }
        
        return array(
            'test' => $test,
            'stats_a' => $stats_a,
            'stats_b' => $stats_b,
            'ctr_a' => $ctr_a,
            'ctr_b' => $ctr_b,
            'winner_id' => $winner_id
        );
    }
    
    private function get_ad_stats($ad_id) {
        global $wpdb;
        
        $impressions = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}amp_analytics WHERE ad_id = %d AND event_type = 'impression'",
            $ad_id
        ));
        
        $clicks = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}amp_analytics WHERE ad_id = %d AND event_type = 'click'",
            $ad_id
        ));
        
        return array(
            'impressions' => intval($impressions),
            'clicks' => intval($clicks)
        );
    }
    
    // Otimização Automática
    public function auto_optimize_ads() {
        if (!$this->options['auto_optimization']) return;
        
        $ads = $this->get_all_ads();
        
        foreach ($ads as $ad) {
            $stats = $this->get_ad_stats($ad->id);
            $performance_score = $this->calculate_performance_score($stats);
            
            // Otimizar baseado na performance
            if ($performance_score < 30) {
                $this->optimize_low_performing_ad($ad);
            } elseif ($performance_score > 80) {
                $this->boost_high_performing_ad($ad);
            }
        }
        
        // Otimizar posicionamento global
        $this->optimize_ad_positions();
    }
    
    private function calculate_performance_score($stats) {
        if ($stats['impressions'] == 0) return 0;
        
        $ctr = ($stats['clicks'] / $stats['impressions']) * 100;
        $impression_score = min($stats['impressions'] / 1000, 1) * 40; // Max 40 pontos
        $ctr_score = min($ctr / 2, 1) * 60; // Max 60 pontos (2% CTR = 60 pontos)
        
        return $impression_score + $ctr_score;
    }
    
    private function optimize_low_performing_ad($ad) {
        // Reduzir prioridade
        global $wpdb;
        
        $new_priority = max(1, $ad->priority - 1);
        
        $wpdb->update(
            $wpdb->prefix . 'amp_ads',
            array('priority' => $new_priority),
            array('id' => $ad->id),
            array('%d'),
            array('%d')
        );
        
        // Log da otimização
        error_log("AMP: Reduced priority for low-performing ad #{$ad->id}");
    }
    
    private function boost_high_performing_ad($ad) {
        // Aumentar prioridade
        global $wpdb;
        
        $new_priority = min(100, $ad->priority + 1);
        
        $wpdb->update(
            $wpdb->prefix . 'amp_ads',
            array('priority' => $new_priority),
            array('id' => $ad->id),
            array('%d'),
            array('%d')
        );
        
        // Log da otimização
        error_log("AMP: Boosted priority for high-performing ad #{$ad->id}");
    }
    
    private function optimize_ad_positions() {
        // Analisar performance por posição
        global $wpdb;
        
        $position_stats = $wpdb->get_results("
            SELECT a.position, 
                   COUNT(CASE WHEN an.event_type = 'impression' THEN 1 END) as impressions,
                   COUNT(CASE WHEN an.event_type = 'click' THEN 1 END) as clicks
            FROM {$wpdb->prefix}amp_ads a
            LEFT JOIN {$wpdb->prefix}amp_analytics an ON a.id = an.ad_id
            WHERE a.status = 'active'
            GROUP BY a.position
        ");
        
        $best_positions = array();
        foreach ($position_stats as $stat) {
            if ($stat->impressions > 0) {
                $ctr = ($stat->clicks / $stat->impressions) * 100;
                $best_positions[$stat->position] = $ctr;
            }
        }
        
        // Ordenar por CTR
        arsort($best_positions);
        
        // Salvar ranking de posições
        $this->set_cache('position_ranking', $best_positions, 86400);
    }
    
    // Posicionamento Inteligente
    public function get_optimal_ad_position($content_type = 'post', $content_length = 0) {
        $position_ranking = $this->get_cache('position_ranking');
        
        if (!$position_ranking) {
            // Posições padrão baseadas em melhores práticas
            $position_ranking = array(
                'after_first_paragraph' => 85,
                'middle_content' => 75,
                'before_content' => 65,
                'after_content' => 60,
                'sidebar' => 45,
                'footer' => 30
            );
        }
        
        // Ajustar baseado no tipo de conteúdo
        if ($content_type === 'page') {
            $position_ranking['before_content'] += 10;
        } elseif ($content_type === 'post') {
            $position_ranking['after_first_paragraph'] += 5;
        }
        
        // Ajustar baseado no comprimento do conteúdo
        if ($content_length > 2000) {
            $position_ranking['middle_content'] += 10;
        } elseif ($content_length < 500) {
            $position_ranking['after_content'] += 15;
        }
        
        // Retornar a melhor posição
        arsort($position_ranking);
        return array_key_first($position_ranking);
    }
    
    public function smart_ad_insertion($content) {
        if (!is_single() && !is_page()) return $content;
        
        $content_length = strlen(strip_tags($content));
        $content_type = get_post_type();
        
        // Obter anúncios ativos ordenados por prioridade
        $ads = $this->get_ads_by_priority();
        
        if (empty($ads)) return $content;
        
        // Inserir anúncios baseado em regras inteligentes
        $modified_content = $this->insert_ads_intelligently($content, $ads, $content_type, $content_length);
        
        return $modified_content;
    }
    
    private function insert_ads_intelligently($content, $ads, $content_type, $content_length) {
        $paragraphs = explode('</p>', $content);
        $total_paragraphs = count($paragraphs);
        
        if ($total_paragraphs < 2) return $content;
        
        $inserted_ads = 0;
        $max_ads = min($this->options['max_ads_per_page'], count($ads));
        
        // Calcular posições ideais
        $ideal_positions = $this->calculate_ideal_positions($total_paragraphs, $max_ads, $content_length);
        
        foreach ($ideal_positions as $position) {
            if ($inserted_ads >= $max_ads) break;
            
            $ad = $ads[$inserted_ads];
            
            // Verificar se o anúncio é adequado para esta posição
            if ($this->is_ad_suitable_for_position($ad, $position, $content_type)) {
                $ad_html = $this->render_ad_with_tracking($ad);
                
                if ($position < count($paragraphs)) {
                    $paragraphs[$position] .= '</p>' . $ad_html;
                } else {
                    $paragraphs[count($paragraphs) - 1] .= '</p>' . $ad_html;
                }
                
                $inserted_ads++;
            }
        }
        
        return implode('</p>', $paragraphs);
    }
    
    private function calculate_ideal_positions($total_paragraphs, $max_ads, $content_length) {
        $positions = array();
        
        if ($max_ads == 1) {
            // Um anúncio: posição ideal baseada no comprimento
            if ($content_length > 1500) {
                $positions[] = intval($total_paragraphs * 0.3); // 30% do conteúdo
            } else {
                $positions[] = max(1, $total_paragraphs - 2); // Próximo ao final
            }
        } else {
            // Múltiplos anúncios: distribuir uniformemente
            $interval = intval($total_paragraphs / ($max_ads + 1));
            
            for ($i = 1; $i <= $max_ads; $i++) {
                $position = $interval * $i;
                if ($position < $total_paragraphs - 1) {
                    $positions[] = $position;
                }
            }
        }
        
        return $positions;
    }
    
    private function is_ad_suitable_for_position($ad, $position, $content_type) {
        // Verificar targeting de dispositivo
        $device_type = $this->detect_device_type();
        if ($ad->device_targeting !== 'all' && $ad->device_targeting !== $device_type) {
            return false;
        }
        
        // Verificar targeting de página
        if (!empty($ad->page_targeting)) {
            $page_rules = json_decode($ad->page_targeting, true);
            if (!$this->check_page_targeting($page_rules, $content_type)) {
                return false;
            }
        }
        
        // Verificar horário (se configurado)
        if (!empty($ad->schedule_start) && !empty($ad->schedule_end)) {
            $now = current_time('mysql');
            if ($now < $ad->schedule_start || $now > $ad->schedule_end) {
                return false;
            }
        }
        
        return true;
    }
    
    private function check_page_targeting($rules, $content_type) {
        if (empty($rules)) return true;
        
        // Verificar tipo de página
        if (isset($rules['post_types']) && !in_array($content_type, $rules['post_types'])) {
            return false;
        }
        
        // Verificar categorias (para posts)
        if ($content_type === 'post' && isset($rules['categories'])) {
            $post_categories = wp_get_post_categories(get_the_ID());
            if (!array_intersect($rules['categories'], $post_categories)) {
                return false;
            }
        }
        
        return true;
    }
    
    private function render_ad_with_tracking($ad) {
        $ad_html = '<div class="amp-ad-container" data-ad-id="' . $ad->id . '">';
        
        // Adicionar lazy loading se habilitado
        if ($this->options['lazy_loading']) {
            $ad_html .= '<div class="amp-ad-lazy" data-src="' . esc_attr($ad->code) . '">';
            $ad_html .= '<div class="amp-ad-placeholder">Carregando anúncio...</div>';
            $ad_html .= '</div>';
        } else {
            $ad_html .= $ad->code;
        }
        
        // Adicionar tracking de impressão
        $ad_html .= '<script>
            document.addEventListener("DOMContentLoaded", function() {
                var observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            // Track impression
                            fetch("' . admin_url('admin-ajax.php') . '", {
                                method: "POST",
                                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                                body: "action=amp_track_impression&ad_id=' . $ad->id . '&nonce=' . wp_create_nonce('amp_track') . '"
                            });
                            observer.unobserve(entry.target);
                        }
                    });
                });
                observer.observe(document.querySelector("[data-ad-id=\'' . $ad->id . '\']"));
            });
        </script>';
        
        $ad_html .= '</div>';
        
        return $ad_html;
    }
    
    private function get_ads_by_priority() {
        global $wpdb;
        
        return $wpdb->get_results("
            SELECT * FROM {$wpdb->prefix}amp_ads 
            WHERE status = 'active' 
            ORDER BY priority DESC, created_at ASC
        ");
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
    
    /**
     * Preload critical resources for better performance
     */
    public function preload_resources() {
        // Pré-carregar recursos do Google AdSense para melhorar o desempenho
        echo '<link rel="preconnect" href="https://pagead2.googlesyndication.com">'."\n";
        echo '<link rel="preconnect" href="https://googleads.g.doubleclick.net">'."\n";
        echo '<link rel="preconnect" href="https://tpc.googlesyndication.com">'."\n";
        echo '<link rel="preconnect" href="https://www.google-analytics.com">'."\n";
        echo '<link rel="preconnect" href="https://adservice.google.com">'."\n";
        
        // Pré-carregar o script do AdSense
        if (!empty($this->settings['publisher_id'])) {
            echo '<link rel="preload" as="script" href="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client='.esc_attr($this->settings['publisher_id']).'">'."\n";
        } else {
            echo '<link rel="preload" as="script" href="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js">'."\n";
        }
    }
    
    /**
     * Add async/defer attributes to scripts for better performance
     */
    public function add_async_defer_attributes($tag, $handle) {
        // Scripts that should be loaded asynchronously
        $async_scripts = array(
            'google-adsense',
            'google-analytics',
            'amp-frontend',
            'adsbygoogle'
        );
        
        // Scripts that should be deferred
        $defer_scripts = array(
            'amp-admin',
            'amp-analytics'
        );
        
        if (in_array($handle, $async_scripts)) {
            $tag = str_replace(' src', ' async src', $tag);
        }
        
        if (in_array($handle, $defer_scripts)) {
            $tag = str_replace(' src', ' defer src', $tag);
        }
        
        return $tag;
    }
    
    /**
     * Display GDPR consent banner for ad personalization
     */
    public function gdpr_consent_banner() {
        if (!isset($this->options['enable_gdpr']) || !$this->options['enable_gdpr']) {
            return;
        }
        
        // Check if user has already given consent
        if (isset($_COOKIE['amp_gdpr_consent'])) {
            return;
        }
        
        ?>
        <div id="amp-gdpr-banner" style="position: fixed; bottom: 0; left: 0; right: 0; background: #333; color: #fff; padding: 15px; z-index: 9999; text-align: center;">
            <p style="margin: 0 0 10px 0;">
                <?php _e('Este site usa cookies para personalizar anúncios. Ao continuar navegando, você concorda com nossa política de cookies.', 'adsense-master-pro'); ?>
            </p>
            <button id="amp-gdpr-accept" style="background: #4CAF50; color: white; border: none; padding: 8px 16px; margin: 0 5px; cursor: pointer;">
                <?php _e('Aceitar', 'adsense-master-pro'); ?>
            </button>
            <button id="amp-gdpr-decline" style="background: #f44336; color: white; border: none; padding: 8px 16px; margin: 0 5px; cursor: pointer;">
                <?php _e('Recusar', 'adsense-master-pro'); ?>
            </button>
        </div>
        <script>
        document.getElementById('amp-gdpr-accept').onclick = function() {
            document.cookie = 'amp_gdpr_consent=accepted; expires=' + new Date(Date.now() + 365*24*60*60*1000).toUTCString() + '; path=/';
            document.getElementById('amp-gdpr-banner').style.display = 'none';
        };
        document.getElementById('amp-gdpr-decline').onclick = function() {
            document.cookie = 'amp_gdpr_consent=declined; expires=' + new Date(Date.now() + 365*24*60*60*1000).toUTCString() + '; path=/';
            document.getElementById('amp-gdpr-banner').style.display = 'none';
        };
        </script>
        <?php
    }
    
    /**
     * Detect ad blockers and show alternative content
     */
    public function ad_blocker_detection() {
        if (!isset($this->options['enable_adblock_detection']) || !$this->options['enable_adblock_detection']) {
            return;
        }
        
        ?>
        <script>
        // Simple ad blocker detection
        var adBlockDetected = false;
        var testAd = document.createElement('div');
        testAd.innerHTML = '&nbsp;';
        testAd.className = 'adsbox';
        testAd.style.position = 'absolute';
        testAd.style.left = '-10000px';
        document.body.appendChild(testAd);
        
        setTimeout(function() {
            if (testAd.offsetHeight === 0) {
                adBlockDetected = true;
                // Show message to users with ad blockers
                var adBlockMessage = document.createElement('div');
                adBlockMessage.innerHTML = '<?php _e("Por favor, desative seu bloqueador de anúncios para apoiar nosso site.", "adsense-master-pro"); ?>';
                adBlockMessage.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; background: #ff9800; color: white; padding: 10px; text-align: center; z-index: 9998;';
                document.body.appendChild(adBlockMessage);
            }
            document.body.removeChild(testAd);
        }, 100);
        </script>
        <?php
    }
    
    /**
     * Register REST API routes for external integrations
     */
    public function register_rest_routes() {
        register_rest_route('adsense-master-pro/v1', '/ads', array(
            'methods' => 'GET',
            'callback' => array($this, 'rest_get_ads'),
            'permission_callback' => array($this, 'rest_permission_check')
        ));
        
        register_rest_route('adsense-master-pro/v1', '/analytics', array(
            'methods' => 'GET',
            'callback' => array($this, 'rest_get_analytics'),
            'permission_callback' => array($this, 'rest_permission_check')
        ));
        
        register_rest_route('adsense-master-pro/v1', '/track', array(
            'methods' => 'POST',
            'callback' => array($this, 'rest_track_event'),
            'permission_callback' => '__return_true'
        ));
    }
    
    /**
     * REST API permission check
     */
    public function rest_permission_check() {
        return current_user_can('manage_options');
    }
    
    /**
     * REST API: Get ads
     */
    public function rest_get_ads($request) {
        return rest_ensure_response($this->ads);
    }
    
    /**
     * REST API: Get analytics data
     */
    public function rest_get_analytics($request) {
        return rest_ensure_response($this->analytics);
    }
    
    /**
     * REST API: Track events
     */
    public function rest_track_event($request) {
        $params = $request->get_params();
        $event_type = sanitize_text_field($params['type'] ?? '');
        $ad_id = intval($params['ad_id'] ?? 0);
        
        if ($event_type === 'impression') {
            $this->track_ad_impression($ad_id);
        } elseif ($event_type === 'click') {
            $this->track_ad_click($ad_id);
        }
        
        return rest_ensure_response(array('success' => true));
    }
    
    /**
     * Daily optimization cron job
     */
    public function daily_optimization() {
        // Run automatic ad optimization
        $this->auto_optimize_ads();
        
        // Clean up old analytics data (keep last 90 days)
        global $wpdb;
        $table_name = $wpdb->prefix . 'amp_analytics';
        $wpdb->query($wpdb->prepare(
            "DELETE FROM $table_name WHERE created_at < %s",
            date('Y-m-d H:i:s', strtotime('-90 days'))
        ));
        
        // Update cache
        $this->clear_cache('optimization_*');
        
        // Log optimization
        error_log('AdSense Master Pro: Daily optimization completed');
    }
    
    /**
     * Hourly analytics processing
     */
    public function hourly_analytics() {
        // Process pending analytics data
        global $wpdb;
        $table_name = $wpdb->prefix . 'amp_analytics';
        
        // Calculate hourly stats
        $current_hour = date('Y-m-d H:00:00');
        $stats = $wpdb->get_results($wpdb->prepare(
            "SELECT ad_id, COUNT(*) as impressions, 
             SUM(CASE WHEN event_type = 'click' THEN 1 ELSE 0 END) as clicks
             FROM $table_name 
             WHERE created_at >= %s AND created_at < %s
             GROUP BY ad_id",
            $current_hour,
            date('Y-m-d H:i:s', strtotime($current_hour . ' +1 hour'))
        ));
        
        // Update analytics cache
        foreach ($stats as $stat) {
            $cache_key = 'hourly_stats_' . $stat->ad_id . '_' . date('YmdH');
            $this->set_cache($cache_key, $stat, 3600); // Cache for 1 hour
        }
        
        // Log analytics processing
        error_log('AdSense Master Pro: Hourly analytics processed for ' . count($stats) . ' ads');
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
    $adsense_master_pro = AdSenseMasterPro::get_instance();
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

// Incluir classes adicionais
if (file_exists(AMP_PLUGIN_PATH . 'includes/class-amp-settings.php')) {
    require_once AMP_PLUGIN_PATH . 'includes/class-amp-settings.php';
}

if (file_exists(AMP_PLUGIN_PATH . 'includes/class-amp-support.php')) {
    require_once AMP_PLUGIN_PATH . 'includes/class-amp-support.php';
}

// Incluir testes apenas se for admin e estiver na página de testes
if (is_admin() && isset($_GET['page']) && $_GET['page'] === 'amp-tests') {
    if (file_exists(AMP_PLUGIN_PATH . 'tests/test-plugin.php')) {
        require_once AMP_PLUGIN_PATH . 'tests/test-plugin.php';
    }
}

// Inicializar classes adicionais
add_action('plugins_loaded', function() {
    if (class_exists('AMP_Settings')) {
        new AMP_Settings();
    }
    
    if (class_exists('AMP_Support')) {
        new AMP_Support();
    }
});

?>