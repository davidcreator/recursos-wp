<?php
namespace WPSP\Core;

if (!defined('ABSPATH')) exit;

class Settings {
    
    public function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
    }
    
    public function register_settings() {
        // URL Customization
        register_setting('wpsp_url_settings', 'wpsp_custom_admin_url');
        register_setting('wpsp_url_settings', 'wpsp_custom_login_url');
        register_setting('wpsp_url_settings', 'wpsp_custom_theme_url');
        register_setting('wpsp_url_settings', 'wpsp_custom_plugins_url');
        register_setting('wpsp_url_settings', 'wpsp_custom_uploads_url');
        register_setting('wpsp_url_settings', 'wpsp_custom_xmlrpc_path');
        
        // Security Settings
        register_setting('wpsp_security_settings', 'wpsp_block_default_admin');
        register_setting('wpsp_security_settings', 'wpsp_block_wp_includes');
        register_setting('wpsp_security_settings', 'wpsp_block_wp_content');
        register_setting('wpsp_security_settings', 'wpsp_block_xmlrpc');
        register_setting('wpsp_security_settings', 'wpsp_security_headers');
        
        // 2FA Settings
        register_setting('wpsp_2fa_settings', 'wpsp_2fa_enabled');
        register_setting('wpsp_2fa_settings', 'wpsp_2fa_methods');
        
        // Captcha Settings
        register_setting('wpsp_captcha_settings', 'wpsp_captcha_enabled');
        register_setting('wpsp_captcha_settings', 'wpsp_captcha_site_key');
        register_setting('wpsp_captcha_settings', 'wpsp_captcha_secret_key');
        
        // Optimization Settings
        register_setting('wpsp_optimization_settings', 'wpsp_remove_wp_version');
        register_setting('wpsp_optimization_settings', 'wpsp_remove_meta_generator');
        register_setting('wpsp_optimization_settings', 'wpsp_disable_emojis');
        register_setting('wpsp_optimization_settings', 'wpsp_minify_html');
        register_setting('wpsp_optimization_settings', 'wpsp_minify_css');
        register_setting('wpsp_optimization_settings', 'wpsp_minify_js');
    }
    
    public static function get($key, $default = false) {
        return get_option($key, $default);
    }
    
    public static function update($key, $value) {
        return update_option($key, $value);
    }
}