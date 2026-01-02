<?php
namespace WPSP\Core;

if (!defined('ABSPATH')) exit;

class Admin {
    
    public function __construct() {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }
    
    public function add_menu() {
        add_menu_page(
            __('Dracaunos Security', 'wp-dracaunos-security'),
            __('Dracaunos Security', 'wp-dracaunos-security'),
            'manage_options',
            'wp-security-pro',
            [$this, 'dashboard_page'],
            'dashicons-shield',
            30
        );
        
        add_submenu_page(
            'wp-security-pro',
            __('Dashboard', 'wp-dracaunos-security'),
            __('Dashboard', 'wp-dracaunos-security'),
            'manage_options',
            'wp-security-pro',
            [$this, 'dashboard_page']
        );
        
        add_submenu_page(
            'wp-security-pro',
            __('URL Settings', 'wp-dracaunos-security'),
            __('URL Settings', 'wp-dracaunos-security'),
            'manage_options',
            'wpsp-url-settings',
            [$this, 'url_settings_page']
        );
        
        add_submenu_page(
            'wp-security-pro',
            __('2FA Settings', 'wp-dracaunos-security'),
            __('2FA Settings', 'wp-dracaunos-security'),
            'manage_options',
            'wpsp-2fa-settings',
            [$this, 'twofa_settings_page']
        );
        
        add_submenu_page(
            'wp-security-pro',
            __('Security Settings', 'wp-dracaunos-security'),
            __('Security', 'wp-dracaunos-security'),
            'manage_options',
            'wpsp-security-settings',
            [$this, 'security_settings_page']
        );
        
        add_submenu_page(
            'wp-security-pro',
            __('Optimization', 'wp-dracaunos-security'),
            __('Optimization', 'wp-dracaunos-security'),
            'manage_options',
            'wpsp-optimization',
            [$this, 'optimization_page']
        );
    }
    
    public function enqueue_assets($hook) {
        if (strpos($hook, 'wp-security-pro') === false && strpos($hook, 'wpsp-') === false) {
            return;
        }
        
        wp_enqueue_style('wpsp-admin', WPSP_PLUGIN_URL . 'assets/css/admin.css', [], WPSP_VERSION);
        wp_enqueue_script('wpsp-admin', WPSP_PLUGIN_URL . 'assets/js/admin.js', ['jquery'], WPSP_VERSION, true);
        
        wp_localize_script('wpsp-admin', 'wpspAdmin', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wpsp_admin_nonce')
        ]);
    }
    
    public function dashboard_page() {
        include WPSP_PLUGIN_DIR . 'templates/admin/dashboard.php';
    }
    
    public function url_settings_page() {
        include WPSP_PLUGIN_DIR . 'templates/admin/url-settings.php';
    }
    
    public function twofa_settings_page() {
        include WPSP_PLUGIN_DIR . 'templates/admin/two-factor-settings.php';
    }
    
    public function security_settings_page() {
        include WPSP_PLUGIN_DIR . 'templates/admin/security-settings.php';
    }
    
    public function optimization_page() {
        include WPSP_PLUGIN_DIR . 'templates/admin/optimization.php';
    }
}
