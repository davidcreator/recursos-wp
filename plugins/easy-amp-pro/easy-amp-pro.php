<?php
/**
 * Plugin Name: Easy AMP Pro
 * Plugin URI: https://github.com/your-username/easy-amp-pro
 * Description: Plugin AMP profissional para WordPress que automatiza a geração de marcação válida AMP, fornece ferramentas de validação eficazes e suporte completo para desenvolvimento AMP.
 * Version: 1.0.0
 * Author: Seu Nome
 * Author URI: https://seusite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: easy-amp-pro
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.3
 * Requires PHP: 7.4
 * Network: false
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('EASY_AMP_PRO_VERSION', '1.0.0');
define('EASY_AMP_PRO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('EASY_AMP_PRO_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('EASY_AMP_PRO_PLUGIN_FILE', __FILE__);

/**
 * Main Easy AMP Pro Class
 */
class EasyAMPPro {
    
    /**
     * Instance of this class
     */
    private static $instance = null;
    
    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init();
    }
    
    /**
     * Initialize the plugin
     */
    private function init() {
        // Load text domain
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        
        // Initialize components
        add_action('init', array($this, 'init_components'));
        
        // Admin hooks
        if (is_admin()) {
            add_action('admin_menu', array($this, 'add_admin_menu'));
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        }
        
        // Frontend hooks
        add_action('wp_enqueue_scripts', array($this, 'frontend_enqueue_scripts'));
        add_action('template_redirect', array($this, 'template_redirect'));
        
        // AMP hooks
        add_action('amp_post_template_head', array($this, 'amp_post_template_head'));
        add_filter('amp_post_template_data', array($this, 'amp_post_template_data'));
        
        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Load text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain('easy-amp-pro', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }
    
    /**
     * Initialize components
     */
    public function init_components() {
        // Include required files
        require_once EASY_AMP_PRO_PLUGIN_PATH . 'includes/class-amp-validator.php';
        require_once EASY_AMP_PRO_PLUGIN_PATH . 'includes/class-amp-optimizer.php';
        require_once EASY_AMP_PRO_PLUGIN_PATH . 'includes/class-amp-template.php';
        require_once EASY_AMP_PRO_PLUGIN_PATH . 'includes/class-amp-settings.php';
        
        // Initialize classes
        new EasyAMPPro_Validator();
        new EasyAMPPro_Optimizer();
        new EasyAMPPro_Template();
        new EasyAMPPro_Settings();
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Easy AMP Pro', 'easy-amp-pro'),
            __('Easy AMP Pro', 'easy-amp-pro'),
            'manage_options',
            'easy-amp-pro',
            array($this, 'admin_page'),
            'dashicons-performance',
            30
        );
        
        add_submenu_page(
            'easy-amp-pro',
            __('Settings', 'easy-amp-pro'),
            __('Settings', 'easy-amp-pro'),
            'manage_options',
            'easy-amp-pro-settings',
            array($this, 'settings_page')
        );
        
        add_submenu_page(
            'easy-amp-pro',
            __('Validator', 'easy-amp-pro'),
            __('Validator', 'easy-amp-pro'),
            'manage_options',
            'easy-amp-pro-validator',
            array($this, 'validator_page')
        );
    }
    
    /**
     * Admin page callback
     */
    public function admin_page() {
        include EASY_AMP_PRO_PLUGIN_PATH . 'admin/views/dashboard.php';
    }
    
    /**
     * Settings page callback
     */
    public function settings_page() {
        include EASY_AMP_PRO_PLUGIN_PATH . 'admin/views/settings.php';
    }
    
    /**
     * Validator page callback
     */
    public function validator_page() {
        include EASY_AMP_PRO_PLUGIN_PATH . 'admin/views/validator.php';
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function admin_enqueue_scripts($hook) {
        if (strpos($hook, 'easy-amp-pro') !== false) {
            wp_enqueue_style('easy-amp-pro-admin', EASY_AMP_PRO_PLUGIN_URL . 'assets/css/admin.css', array(), EASY_AMP_PRO_VERSION);
            wp_enqueue_script('easy-amp-pro-admin', EASY_AMP_PRO_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), EASY_AMP_PRO_VERSION, true);
            
            wp_localize_script('easy-amp-pro-admin', 'easyAmpPro', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('easy_amp_pro_nonce'),
                'strings' => array(
                    'validating' => __('Validating...', 'easy-amp-pro'),
                    'success' => __('Success!', 'easy-amp-pro'),
                    'error' => __('Error occurred', 'easy-amp-pro'),
                )
            ));
        }
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function frontend_enqueue_scripts() {
        if (easy_amp_pro_is_amp_endpoint()) {
            wp_enqueue_style('easy-amp-pro-frontend', EASY_AMP_PRO_PLUGIN_URL . 'assets/css/amp.css', array(), EASY_AMP_PRO_VERSION);
        }
    }
    
    /**
     * Template redirect
     */
    public function template_redirect() {
        if (easy_amp_pro_is_amp_endpoint()) {
            $this->setup_amp_environment();
        }
    }
    
    /**
     * Setup AMP environment
     */
    private function setup_amp_environment() {
        // Remove incompatible plugins/themes on AMP pages
        $this->remove_incompatible_actions();
        
        // Add AMP-specific optimizations
        add_filter('wp_enqueue_scripts', array($this, 'optimize_amp_scripts'), 999);
    }
    
    /**
     * Remove incompatible actions for AMP
     */
    private function remove_incompatible_actions() {
        // Remove common incompatible actions
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'rsd_link');
        
        // Allow filtering of actions to remove
        $actions_to_remove = apply_filters('easy_amp_pro_remove_actions', array());
        
        foreach ($actions_to_remove as $action) {
            if (isset($action['hook']) && isset($action['function'])) {
                remove_action($action['hook'], $action['function'], $action['priority'] ?? 10);
            }
        }
    }
    
    /**
     * AMP post template head
     */
    public function amp_post_template_head() {
        $settings = get_option('easy_amp_pro_settings', array());
        
        // Add custom AMP head content
        if (!empty($settings['custom_amp_head'])) {
            echo $settings['custom_amp_head'];
        }
        
        // Add structured data
        $this->add_structured_data();
    }
    
    /**
     * Add structured data
     */
    private function add_structured_data() {
        if (is_single() || is_page()) {
            global $post;
            
            $structured_data = array(
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => get_the_title($post->ID),
                'datePublished' => get_the_date('c', $post->ID),
                'dateModified' => get_the_modified_date('c', $post->ID),
                'author' => array(
                    '@type' => 'Person',
                    'name' => get_the_author_meta('display_name', $post->post_author)
                )
            );
            
            // Add featured image if available
            if (has_post_thumbnail($post->ID)) {
                $image_id = get_post_thumbnail_id($post->ID);
                $image_data = wp_get_attachment_image_src($image_id, 'full');
                
                if ($image_data) {
                    $structured_data['image'] = array(
                        '@type' => 'ImageObject',
                        'url' => $image_data[0],
                        'width' => $image_data[1],
                        'height' => $image_data[2]
                    );
                }
            }
            
            echo '<script type="application/ld+json">' . wp_json_encode($structured_data) . '</script>';
        }
    }
    
    /**
     * Filter AMP post template data
     */
    public function amp_post_template_data($data) {
        $settings = get_option('easy_amp_pro_settings', array());
        
        // Customize AMP template data
        if (!empty($settings['site_icon'])) {
            $data['site_icon_url'] = $settings['site_icon'];
        }
        
        return $data;
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create default settings
        $default_settings = array(
            'enable_amp' => true,
            'auto_optimize' => true,
            'enable_validation' => true,
            'remove_incompatible' => true,
            'custom_amp_head' => '',
            'site_icon' => ''
        );
        
        add_option('easy_amp_pro_settings', $default_settings);
        
        // Create database tables if needed
        $this->create_tables();
        
        // Schedule validation cron if enabled
        if (!wp_next_scheduled('easy_amp_pro_validation_check')) {
            wp_schedule_event(time(), 'daily', 'easy_amp_pro_validation_check');
        }
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Remove scheduled events
        wp_clear_scheduled_hook('easy_amp_pro_validation_check');
    }
    
    /**
     * Create plugin tables
     */
    private function create_tables() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'easy_amp_pro_validation';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            url varchar(255) NOT NULL,
            validation_errors text,
            last_checked datetime DEFAULT CURRENT_TIMESTAMP,
            status varchar(20) DEFAULT 'pending',
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY status (status)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

/**
 * Check if AMP endpoint
 */
function easy_amp_pro_is_amp_endpoint() {
    // Check if official AMP plugin function exists and use it
    if (function_exists('amp_is_request')) {
        return amp_is_request();
    }
    
    // Check for AMP query parameter
    if (isset($_GET['amp'])) {
        return true;
    }
    
    // Check for AMP query var
    if (get_query_var('amp', false) !== false) {
        return true;
    }
    
    // Check for AMP in URL path
    if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/amp/') !== false) {
        return true;
    }
    
    return false;
}

// Initialize the plugin
EasyAMPPro::get_instance();