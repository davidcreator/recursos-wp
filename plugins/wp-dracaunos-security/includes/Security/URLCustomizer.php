<?php
namespace WPSP\Security;

if (!defined('ABSPATH')) exit;

class URLCustomizer {
    
    private $custom_admin_url;
    private $custom_login_url;
    private $custom_theme_url;
    private $custom_plugins_url;
    private $custom_uploads_url;
    private $custom_includes_url;
    
    public function __construct() {
        $this->load_settings();
        $this->init_hooks();
    }
    
    private function load_settings() {
        $this->custom_admin_url = get_option('wpsp_custom_admin_url', '');
        $this->custom_login_url = get_option('wpsp_custom_login_url', '');
        $this->custom_theme_url = get_option('wpsp_custom_theme_url', '');
        $this->custom_plugins_url = get_option('wpsp_custom_plugins_url', '');
        $this->custom_uploads_url = get_option('wpsp_custom_uploads_url', '');
        $this->custom_includes_url = get_option('wpsp_custom_includes_url', '');
    }
    
    private function init_hooks() {
        // Custom Login URL
        if (!empty($this->custom_login_url)) {
            add_action('init', [$this, 'custom_login_redirect']);
            add_filter('site_url', [$this, 'filter_site_url'], 10, 4);
            add_filter('wp_redirect', [$this, 'filter_wp_redirect'], 10, 2);
        }
        
        // Custom Admin URL
        if (!empty($this->custom_admin_url)) {
            add_action('init', [$this, 'custom_admin_redirect']);
        }
        
        // Block default URLs
        if (get_option('wpsp_block_default_admin', 1)) {
            add_action('init', [$this, 'block_default_admin']);
        }
        
        if (get_option('wpsp_block_wp_includes', 1)) {
            add_action('init', [$this, 'block_wp_includes']);
        }
        
        if (get_option('wpsp_block_wp_content', 1)) {
            add_action('init', [$this, 'block_wp_content']);
        }
        
        // Custom URLs for assets
        if (!empty($this->custom_theme_url)) {
            add_filter('stylesheet_uri', [$this, 'custom_stylesheet_uri'], 999);
            add_filter('template_url', [$this, 'custom_template_url'], 999);
            add_filter('stylesheet_directory_uri', [$this, 'custom_stylesheet_directory_uri'], 999);
        }
        
        if (!empty($this->custom_plugins_url)) {
            add_filter('plugins_url', [$this, 'custom_plugins_url_filter'], 999);
        }
        
        if (!empty($this->custom_uploads_url)) {
            add_filter('upload_dir', [$this, 'custom_upload_dir']);
        }
        
        // Rewrite rules
        add_action('generate_rewrite_rules', [$this, 'add_custom_rewrite_rules']);
    }
    
    /**
     * Custom Login Redirect
     */
    public function custom_login_redirect() {
        $request_uri = $_SERVER['REQUEST_URI'];
        $custom_login = '/' . trim($this->custom_login_url, '/');
        
        // Se acessar o custom login, redirecionar internamente para wp-login.php
        if (strpos($request_uri, $custom_login) !== false && !is_user_logged_in()) {
            $_SERVER['SCRIPT_NAME'] = '/wp-login.php';
            require_once ABSPATH . 'wp-login.php';
            exit;
        }
        
        // Bloquear acesso direto ao wp-login.php
        if (strpos($request_uri, 'wp-login.php') !== false && strpos($request_uri, $custom_login) === false) {
            if (!is_user_logged_in()) {
                wp_die(__('Access Denied', 'wp-security-pro'), 403);
            }
        }
    }
    
    /**
     * Custom Admin Redirect
     */
    public function custom_admin_redirect() {
        $request_uri = $_SERVER['REQUEST_URI'];
        $custom_admin = '/' . trim($this->custom_admin_url, '/');
        
        // Se acessar o custom admin, redirecionar internamente
        if (strpos($request_uri, $custom_admin) !== false) {
            // Remover custom admin da URI e processar como wp-admin
            $_SERVER['REQUEST_URI'] = str_replace($custom_admin, '/wp-admin', $request_uri);
        }
    }
    
    /**
     * Block default admin access
     */
    public function block_default_admin() {
        $request_uri = $_SERVER['REQUEST_URI'];
        
        // Permitir AJAX
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }
        
        // Bloquear /wp-admin se custom admin está configurado
        if (!empty($this->custom_admin_url) && strpos($request_uri, '/wp-admin') !== false) {
            $custom_admin = '/' . trim($this->custom_admin_url, '/');
            if (strpos($request_uri, $custom_admin) === false) {
                wp_die(__('Access Denied', 'wp-security-pro'), 403);
            }
        }
    }
    
    /**
     * Block wp-includes direct access
     */
    public function block_wp_includes() {
        $request_uri = $_SERVER['REQUEST_URI'];
        
        if (strpos($request_uri, '/wp-includes/') !== false) {
            // Permitir arquivos JS e CSS necessários
            $allowed_extensions = ['.js', '.css', '.png', '.jpg', '.gif', '.svg', '.woff', '.woff2', '.ttf', '.eot'];
            
            foreach ($allowed_extensions as $ext) {
                if (substr($request_uri, -strlen($ext)) === $ext) {
                    return;
                }
            }
            
            wp_die(__('Access Denied', 'wp-security-pro'), 403);
        }
    }
    
    /**
     * Block wp-content direct access
     */
    public function block_wp_content() {
        $request_uri = $_SERVER['REQUEST_URI'];
        
        // Bloquear acesso direto a arquivos PHP em plugins e themes
        if (preg_match('/wp-content\/(plugins|themes)\/.*\.php/i', $request_uri)) {
            // Permitir admin-ajax.php e load-scripts.php
            if (strpos($request_uri, 'admin-ajax.php') === false && 
                strpos($request_uri, 'load-scripts.php') === false &&
                strpos($request_uri, 'load-styles.php') === false) {
                wp_die(__('Access Denied', 'wp-security-pro'), 403);
            }
        }
    }
    
    /**
     * Filter site URL for custom login
     */
    public function filter_site_url($url, $path, $scheme, $blog_id) {
        if (strpos($url, 'wp-login.php') !== false && !empty($this->custom_login_url)) {
            $url = str_replace('wp-login.php', $this->custom_login_url, $url);
        }
        return $url;
    }
    
    /**
     * Filter wp_redirect for custom login
     */
    public function filter_wp_redirect($location, $status) {
        if (strpos($location, 'wp-login.php') !== false && !empty($this->custom_login_url)) {
            $location = str_replace('wp-login.php', $this->custom_login_url, $location);
        }
        return $location;
    }
    
    /**
     * Custom stylesheet URI
     */
    public function custom_stylesheet_uri($stylesheet_uri) {
        if (!empty($this->custom_theme_url)) {
            $stylesheet_uri = str_replace('/wp-content/themes/', '/' . trim($this->custom_theme_url, '/') . '/', $stylesheet_uri);
        }
        return $stylesheet_uri;
    }
    
    /**
     * Custom template URL
     */
    public function custom_template_url($template_url) {
        if (!empty($this->custom_theme_url)) {
            $template_url = str_replace('/wp-content/themes/', '/' . trim($this->custom_theme_url, '/') . '/', $template_url);
        }
        return $template_url;
    }
    
    /**
     * Custom stylesheet directory URI
     */
    public function custom_stylesheet_directory_uri($stylesheet_dir_uri) {
        if (!empty($this->custom_theme_url)) {
            $stylesheet_dir_uri = str_replace('/wp-content/themes/', '/' . trim($this->custom_theme_url, '/') . '/', $stylesheet_dir_uri);
        }
        return $stylesheet_dir_uri;
    }
    
    /**
     * Custom plugins URL
     */
    public function custom_plugins_url_filter($url) {
        if (!empty($this->custom_plugins_url)) {
            $url = str_replace('/wp-content/plugins/', '/' . trim($this->custom_plugins_url, '/') . '/', $url);
        }
        return $url;
    }
    
    /**
     * Custom upload directory
     */
    public function custom_upload_dir($uploads) {
        if (!empty($this->custom_uploads_url)) {
            $custom_url = '/' . trim($this->custom_uploads_url, '/');
            $uploads['baseurl'] = str_replace('/wp-content/uploads', $custom_url, $uploads['baseurl']);
            $uploads['url'] = str_replace('/wp-content/uploads', $custom_url, $uploads['url']);
        }
        return $uploads;
    }
    
    /**
     * Add custom rewrite rules
     */
    public function add_custom_rewrite_rules($wp_rewrite) {
        $new_rules = [];
        
        // Custom login rewrite
        if (!empty($this->custom_login_url)) {
            $custom_login = trim($this->custom_login_url, '/');
            $new_rules[$custom_login . '/?] = 'wp-login.php';
        }
        
        // Custom admin rewrite
        if (!empty($this->custom_admin_url)) {
            $custom_admin = trim($this->custom_admin_url, '/');
            $new_rules[$custom_admin . '/?(.*)] = 'wp-admin/$1';
        }
        
        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    }
}