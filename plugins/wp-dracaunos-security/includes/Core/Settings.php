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
        // New: detailed header policies
        register_setting('wpsp_security_settings', 'wpsp_x_frame_options');
        register_setting('wpsp_security_settings', 'wpsp_referrer_policy');
        register_setting('wpsp_security_settings', 'wpsp_enable_csp');
        register_setting('wpsp_security_settings', 'wpsp_csp_default_src');
        register_setting('wpsp_security_settings', 'wpsp_csp_script_src');
        register_setting('wpsp_security_settings', 'wpsp_csp_style_src');
        register_setting('wpsp_security_settings', 'wpsp_csp_img_src');
        register_setting('wpsp_security_settings', 'wpsp_csp_font_src');
        register_setting('wpsp_security_settings', 'wpsp_csp_connect_src');
        register_setting('wpsp_security_settings', 'wpsp_csp_frame_ancestors');
        register_setting('wpsp_security_settings', 'wpsp_enable_hsts');
        register_setting('wpsp_security_settings', 'wpsp_hsts_max_age');
        register_setting('wpsp_security_settings', 'wpsp_hsts_include_subdomains');
        register_setting('wpsp_security_settings', 'wpsp_hsts_preload');
        register_setting('wpsp_security_settings', 'wpsp_enable_permissions_policy');
        register_setting('wpsp_security_settings', 'wpsp_permissions_geolocation');
        register_setting('wpsp_security_settings', 'wpsp_permissions_microphone');
        register_setting('wpsp_security_settings', 'wpsp_permissions_camera');
        register_setting('wpsp_security_settings', 'wpsp_permissions_payment');
        register_setting('wpsp_security_settings', 'wpsp_permissions_usb');
        
        // 2FA Settings
        register_setting('wpsp_2fa_settings', 'wpsp_2fa_enabled');
        register_setting('wpsp_2fa_settings', 'wpsp_2fa_methods');
        
        // Captcha Settings
        register_setting('wpsp_captcha_settings', 'wpsp_captcha_enabled');
        register_setting('wpsp_captcha_settings', 'wpsp_captcha_site_key');
        register_setting('wpsp_captcha_settings', 'wpsp_captcha_secret_key');
        register_setting('wpsp_captcha_settings', 'wpsp_captcha_comments');
        
        // Optimization Settings
        register_setting('wpsp_optimization_settings', 'wpsp_remove_wp_version');
        register_setting('wpsp_optimization_settings', 'wpsp_remove_meta_generator');
        register_setting('wpsp_optimization_settings', 'wpsp_disable_emojis');
        register_setting('wpsp_optimization_settings', 'wpsp_minify_html');
        register_setting('wpsp_optimization_settings', 'wpsp_minify_css');
        register_setting('wpsp_optimization_settings', 'wpsp_minify_js');
        // New: header cleanup toggles
        register_setting('wpsp_optimization_settings', 'wpsp_remove_feed_links');
        register_setting('wpsp_optimization_settings', 'wpsp_remove_rest_api_links');
        register_setting('wpsp_optimization_settings', 'wpsp_remove_oembed');
        register_setting('wpsp_optimization_settings', 'wpsp_remove_canonical');
    }
    
    public static function get($key, $default = false) {
        return get_option($key, $default);
    }
    
    public static function update($key, $value) {
        return update_option($key, $value);
    }
}