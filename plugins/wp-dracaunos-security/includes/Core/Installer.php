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
        
        // Tabela para 2FA
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
        
        // Tabela para logs de segurança
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
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_2fa);
        dbDelta($sql_logs);
    }
    
    private static function set_default_options() {
        $defaults = [
            'wpsp_custom_admin_url' => '',
            'wpsp_custom_login_url' => '',
            'wpsp_block_default_admin' => 1,
            'wpsp_block_wp_includes' => 1,
            'wpsp_block_wp_content' => 1,
            'wpsp_block_xmlrpc' => 1,
            'wpsp_custom_xmlrpc_path' => '',
            'wpsp_2fa_enabled' => 0,
            'wpsp_2fa_methods' => ['email'],
            'wpsp_captcha_enabled' => 0,
            'wpsp_captcha_site_key' => '',
            'wpsp_captcha_secret_key' => '',
            'wpsp_remove_wp_version' => 1,
            'wpsp_remove_meta_generator' => 1,
            'wpsp_disable_emojis' => 1,
            'wpsp_minify_html' => 0,
            'wpsp_minify_css' => 0,
            'wpsp_minify_js' => 0,
            'wpsp_security_headers' => 1,
            'wpsp_custom_theme_url' => '',
            'wpsp_custom_plugins_url' => '',
            'wpsp_custom_uploads_url' => '',
        ];
        
        foreach ($defaults as $key => $value) {
            if (get_option($key) === false) {
                add_option($key, $value);
            }
        }
    }
    
    private static function create_htaccess_rules() {
        $htaccess_file = ABSPATH . '.htaccess';
        
        if (is_writable($htaccess_file)) {
            $rules = "
# BEGIN WP Security Pro
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Block direct access to wp-includes
    RewriteRule ^wp-includes/.*$ - [F,L]
    
    # Block direct access to wp-content/plugins
    RewriteRule ^wp-content/plugins/.*\.php$ - [F,L]
    
    # Block XML-RPC
    RewriteRule ^xmlrpc\.php$ - [F,L]
</IfModule>

# Security Headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options \"nosniff\"
    Header always set X-Frame-Options \"SAMEORIGIN\"
    Header always set X-XSS-Protection \"1; mode=block\"
    Header always set Referrer-Policy \"strict-origin-when-cross-origin\"
</IfModule>
# END WP Security Pro
";
            
            // Adicionar regras se ainda não existirem
            $current_content = file_get_contents($htaccess_file);
            if (strpos($current_content, '# BEGIN WP Security Pro') === false) {
                file_put_contents($htaccess_file, $rules . $current_content);
            }
        }
    }
}