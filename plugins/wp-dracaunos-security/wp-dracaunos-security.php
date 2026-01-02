<?php
/**
 * Plugin Name: Dracaunos Security
 * Plugin URI: https://davidalmeida.xyz/wp-dracaunos-security
 * Description: Plugin completo de segurança WordPress com personalização de URLs, 2FA, captcha e otimizações
 * Version: 1.0.0
 * Author: David Almeida
 * Author URI: https://davidalmeida.xyz
 * License: GPL v2 or later
 * Text Domain: wp-dracaunos-security
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

// Constantes do Plugin
define('WPSP_VERSION', '1.0.0');
define('WPSP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WPSP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WPSP_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'WPSP\\';
    $base_dir = WPSP_PLUGIN_DIR . 'includes/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Classe Principal
final class WP_Security_Pro {
    
    private static $instance = null;
    
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
        $this->load_dependencies();
    }
    
    private function init_hooks() {
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);
        add_action('plugins_loaded', [$this, 'init']);
    }
    
    private function load_dependencies() {
        // Core
        require_once WPSP_PLUGIN_DIR . 'includes/Core/Installer.php';
        require_once WPSP_PLUGIN_DIR . 'includes/Core/Settings.php';
        require_once WPSP_PLUGIN_DIR . 'includes/Core/Admin.php';
        
        // Security Features
        require_once WPSP_PLUGIN_DIR . 'includes/Security/URLCustomizer.php';
        require_once WPSP_PLUGIN_DIR . 'includes/Security/TwoFactorAuth.php';
        require_once WPSP_PLUGIN_DIR . 'includes/Security/Captcha.php';
        require_once WPSP_PLUGIN_DIR . 'includes/Security/XMLRPCManager.php';
        require_once WPSP_PLUGIN_DIR . 'includes/Security/HeadersCleaner.php';
        require_once WPSP_PLUGIN_DIR . 'includes/Security/SecurityHeaders.php';
        
        require_once WPSP_PLUGIN_DIR . 'includes/Optimization/Minifier.php';
    }
    
    public function init() {
        new WPSP\Core\Settings();
        new WPSP\Core\Admin();
        
        $safe_mode = (int) get_option('wpsp_safe_mode', 1);
        if ($safe_mode && !get_option('wpsp_htaccess_purged', 0)) {
            WPSP\Core\Installer::activate();
            update_option('wpsp_htaccess_purged', 1);
        }
        if (!$safe_mode) {
            new WPSP\Security\URLCustomizer();
            new WPSP\Security\TwoFactorAuth();
            new WPSP\Security\Captcha();
            new WPSP\Security\XMLRPCManager();
            new WPSP\Security\HeadersCleaner();
            new WPSP\Security\SecurityHeaders();
            new WPSP\Optimization\Minifier();
        }
        
        load_plugin_textdomain('wp-dracaunos-security', false, dirname(WPSP_PLUGIN_BASENAME) . '/languages');
    }
    
    public function activate() {
        WPSP\Core\Installer::activate();
    }
    
    public function deactivate() {
        WPSP\Core\Installer::deactivate();
    }
}

// Inicializar Plugin
function wpsp() {
    return WP_Security_Pro::instance();
}

wpsp();
