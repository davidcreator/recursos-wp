<?php
namespace WPSP\Core;

if (!defined('ABSPATH')) exit;

class Installer {
    public function activate() {
        // Defaults for URL customization
        add_option('wpsp_custom_admin_url', 'admin');
        add_option('wpsp_custom_login_url', 'login');
        add_option('wpsp_custom_theme_url', 'themes');
        add_option('wpsp_custom_plugins_url', 'plugins');
        add_option('wpsp_custom_uploads_url', 'files');
        add_option('wpsp_custom_xmlrpc_path', 'xmlrpc');
        
        // Defaults for security toggles
        add_option('wpsp_block_default_admin', true);
        add_option('wpsp_block_wp_includes', true);
        add_option('wpsp_block_wp_content', true);
        add_option('wpsp_block_xmlrpc', true);
        add_option('wpsp_security_headers', true);
        
        // Defaults for detailed headers
        add_option('wpsp_x_frame_options', 'SAMEORIGIN');
        add_option('wpsp_referrer_policy', 'strict-origin-when-cross-origin');
        add_option('wpsp_enable_csp', false);
        add_option('wpsp_csp_default_src', "'self'");
        add_option('wpsp_csp_script_src', "'self'");
        add_option('wpsp_csp_style_src', "'self' 'unsafe-inline'");
        add_option('wpsp_csp_img_src', "'self' data:");
        add_option('wpsp_csp_font_src', "'self' data:");
        add_option('wpsp_csp_connect_src', "'self'");
        add_option('wpsp_csp_frame_ancestors', "'self'");
        add_option('wpsp_enable_hsts', false);
        add_option('wpsp_hsts_max_age', 31536000);
        add_option('wpsp_hsts_include_subdomains', true);
        add_option('wpsp_hsts_preload', false);
        add_option('wpsp_enable_permissions_policy', false);
        add_option('wpsp_permissions_geolocation', '()');
        add_option('wpsp_permissions_microphone', '()');
        add_option('wpsp_permissions_camera', '()');
        add_option('wpsp_permissions_payment', '()');
        add_option('wpsp_permissions_usb', '()');
        
        // Defaults for 2FA
        add_option('wpsp_2fa_enabled', false);
        add_option('wpsp_2fa_methods', ['email']);
        
        // Defaults for Captcha
        add_option('wpsp_captcha_enabled', false);
        add_option('wpsp_captcha_site_key', '');
        add_option('wpsp_captcha_secret_key', '');
        add_option('wpsp_captcha_comments', true);
        
        // Defaults for optimization and header cleanup
        add_option('wpsp_remove_wp_version', true);
        add_option('wpsp_remove_meta_generator', true);
        add_option('wpsp_disable_emojis', true);
        add_option('wpsp_minify_html', false);
        add_option('wpsp_minify_css', false);
        add_option('wpsp_minify_js', false);
        add_option('wpsp_remove_feed_links', true);
        add_option('wpsp_remove_rest_api_links', true);
        add_option('wpsp_remove_oembed', true);
        add_option('wpsp_remove_canonical', false);
    }

    public function deactivate() {
        // No-op for now
    }
}