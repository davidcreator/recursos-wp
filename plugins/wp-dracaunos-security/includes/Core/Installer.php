<?php
namespace WPSP\Core;

if (!defined('ABSPATH')) exit;

class Installer {
    public static function activate() {
        self::create_tables();
        self::set_default_options();
        self::create_htaccess_rules();
        flush_rewrite_rules();
    }

    public static function deactivate() {
        flush_rewrite_rules();
    }

    private static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $table_2fa = $wpdb->prefix . 'wpsp_two_factor';
        $sql_2fa = "CREATE TABLE IF NOT EXISTS $table_2fa (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            method varchar(50) NOT NULL,
            secret varchar(255) DEFAULT NULL,
            backup_codes text DEFAULT NULL,
            enabled tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id)
        ) $charset_collate;";

        $table_logs = $wpdb->prefix . 'wpsp_security_logs';
        $sql_logs = "CREATE TABLE IF NOT EXISTS $table_logs (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT NULL,
            action varchar(100) NOT NULL,
            ip_address varchar(45) NOT NULL,
            user_agent text DEFAULT NULL,
            details text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY action (action),
            KEY created_at (created_at)
        ) $charset_collate;";

        $table_sessions = $wpdb->prefix . 'wpsp_sessions';
        $sql_sessions = "CREATE TABLE IF NOT EXISTS $table_sessions (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            token varchar(255) NOT NULL,
            ip_address varchar(45) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            expires_at datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY token (token),
            KEY expires_at (expires_at)
        ) $charset_collate;";

        $table_blocked = $wpdb->prefix . 'wpsp_blocked_ips';
        $sql_blocked = "CREATE TABLE IF NOT EXISTS $table_blocked (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            ip_address varchar(45) NOT NULL,
            reason varchar(255) DEFAULT NULL,
            expires_at datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY ip_address (ip_address),
            KEY expires_at (expires_at)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_2fa);
        dbDelta($sql_logs);
        dbDelta($sql_sessions);
        dbDelta($sql_blocked);
    }

    private static function set_default_options() {
        $defaults = [
            // URL customization
            'wpsp_custom_admin_url' => '',
            'wpsp_custom_login_url' => '',
            'wpsp_custom_theme_url' => '',
            'wpsp_custom_plugins_url' => '',
            'wpsp_custom_uploads_url' => '',
            'wpsp_custom_xmlrpc_path' => '',

            // Security toggles
            'wpsp_block_default_admin' => 0,
            'wpsp_block_wp_includes' => 0,
            'wpsp_block_wp_content' => 0,
            'wpsp_block_xmlrpc' => 0,
            'wpsp_security_headers' => 0,
            'wpsp_safe_mode' => 1,

            // Detailed headers
            'wpsp_x_frame_options' => 'SAMEORIGIN',
            'wpsp_referrer_policy' => 'strict-origin-when-cross-origin',
            'wpsp_enable_csp' => 0,
            'wpsp_csp_default_src' => "'self'",
            'wpsp_csp_script_src' => "'self'",
            'wpsp_csp_style_src' => "'self' 'unsafe-inline'",
            'wpsp_csp_img_src' => "'self' data:",
            'wpsp_csp_font_src' => "'self' data:",
            'wpsp_csp_connect_src' => "'self'",
            'wpsp_csp_frame_ancestors' => "'self'",
            'wpsp_enable_hsts' => 0,
            'wpsp_hsts_max_age' => 31536000,
            'wpsp_hsts_include_subdomains' => 1,
            'wpsp_hsts_preload' => 0,
            'wpsp_enable_permissions_policy' => 0,
            'wpsp_permissions_geolocation' => '()',
            'wpsp_permissions_microphone' => '()',
            'wpsp_permissions_camera' => '()',
            'wpsp_permissions_payment' => '()',
            'wpsp_permissions_usb' => '()',

            // 2FA
            'wpsp_2fa_enabled' => 0,
            'wpsp_2fa_methods' => ['email'],

            // Captcha
            'wpsp_captcha_enabled' => 0,
            'wpsp_captcha_site_key' => '',
            'wpsp_captcha_secret_key' => '',
            'wpsp_captcha_comments' => 1,

            // Optimization and header cleanup
            'wpsp_remove_wp_version' => 0,
            'wpsp_remove_meta_generator' => 0,
            'wpsp_disable_emojis' => 0,
            'wpsp_minify_html' => 0,
            'wpsp_minify_css' => 0,
            'wpsp_minify_js' => 0,
            'wpsp_remove_feed_links' => 0,
            'wpsp_remove_rest_api_links' => 0,
            'wpsp_remove_oembed' => 0,
            'wpsp_remove_canonical' => 0,
        ];

        foreach ($defaults as $key => $value) {
            if (get_option($key) === false) {
                add_option($key, $value);
            }
        }
    }

    private static function create_htaccess_rules() {
        $htaccess_file = ABSPATH . '.htaccess';
        if (!is_writable($htaccess_file)) {
            return;
        }

        $safe_mode = (int) get_option('wpsp_safe_mode', 1);
        if ($safe_mode) {
            $current_content = file_exists($htaccess_file) ? file_get_contents($htaccess_file) : '';
            if ($current_content && strpos($current_content, '# BEGIN WP Security Pro') !== false) {
                $pattern = "/# BEGIN WP Security Pro[\\s\\S]*?# END WP Security Pro\\n?/";
                $new_content = preg_replace($pattern, '', $current_content);
                if ($new_content !== null) {
                    file_put_contents($htaccess_file, $new_content);
                }
            }
            return;
        }

        $block_default_admin = (int) get_option('wpsp_block_default_admin', 0);
        $block_xmlrpc = (int) get_option('wpsp_block_xmlrpc', 0);
        $block_wp_includes = (int) get_option('wpsp_block_wp_includes', 0);
        $block_wp_content = (int) get_option('wpsp_block_wp_content', 0);

        $rules = "\n# BEGIN WP Security Pro\n<IfModule mod_rewrite.c>\n    RewriteEngine On\n\n";
        if ($block_wp_includes) {
            $rules .= "    RewriteRule ^wp-includes/.*$ - [F,L]\n\n";
        }
        if ($block_wp_content) {
            $rules .= "    RewriteRule ^wp-content/plugins/.*\\.php$ - [F,L]\n\n";
        }
        if ($block_default_admin) {
            $rules .= "    RewriteRule ^wp-admin/?(.*)$ - [F,L]\n    RewriteRule ^wp-login\\.php$ - [F,L]\n\n";
        }
        if ($block_xmlrpc) {
            $rules .= "    RewriteRule ^xmlrpc\\.php$ - [F,L]\n\n";
        }
        $rules .= "</IfModule>\n\n";
        $emit_headers = (int) get_option('wpsp_security_headers', 0);
        if ($emit_headers) {
            $rules .= "# Security Headers\n<IfModule mod_headers.c>\n    Header always set X-Content-Type-Options \"nosniff\"\n    Header always set X-Frame-Options \"" . esc_attr(get_option('wpsp_x_frame_options', 'SAMEORIGIN')) . "\"\n    Header always set X-XSS-Protection \"1; mode=block\"\n    Header always set Referrer-Policy \"" . esc_attr(get_option('wpsp_referrer_policy', 'strict-origin-when-cross-origin')) . "\"\n</IfModule>\n";
        }
        $rules .= "# END WP Security Pro\n";

        // Security Headers minimal set (others emitted by PHP)
        $rules .= "# Security Headers\n<IfModule mod_headers.c>\n    Header always set X-Content-Type-Options \"nosniff\"\n    Header always set X-Frame-Options \"" . esc_attr(get_option('wpsp_x_frame_options', 'SAMEORIGIN')) . "\"\n    Header always set X-XSS-Protection \"1; mode=block\"\n    Header always set Referrer-Policy \"" . esc_attr(get_option('wpsp_referrer_policy', 'strict-origin-when-cross-origin')) . "\"\n</IfModule>\n# END WP Security Pro\n";

        $current_content = file_exists($htaccess_file) ? file_get_contents($htaccess_file) : '';
        if ($current_content && strpos($current_content, '# BEGIN WP Security Pro') !== false) {
            $pattern = "/# BEGIN WP Security Pro[\\s\\S]*?# END WP Security Pro\\n?/";
            $new_content = preg_replace($pattern, $rules, $current_content);
            if ($new_content !== null) {
                file_put_contents($htaccess_file, $new_content);
            }
        } else {
            file_put_contents($htaccess_file, $rules . $current_content);
        }
    }
}
