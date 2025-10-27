<?php
namespace WPSP\Security;

if (!defined('ABSPATH')) exit;

use WPSP\Core\Database;

class Captcha {
    
    private $db;
    private $site_key;
    private $secret_key;
    private $enabled;
    
    public function __construct() {
        $this->db = new Database();
        $this->site_key = get_option('wpsp_captcha_site_key', '');
        $this->secret_key = get_option('wpsp_captcha_secret_key', '');
        $this->enabled = get_option('wpsp_captcha_enabled', 0);
        
        if ($this->enabled && !empty($this->site_key) && !empty($this->secret_key)) {
            $this->init_hooks();
        }
    }
    
    private function init_hooks() {
        // Login form
        add_action('login_form', [$this, 'add_captcha_to_login']);
        add_filter('wp_authenticate_user', [$this, 'verify_login_captcha'], 10, 2);
        
        // Registration form
        add_action('register_form', [$this, 'add_captcha_to_register']);
        add_filter('registration_errors', [$this, 'verify_register_captcha'], 10, 3);
        
        // Lost password form
        add_action('lostpassword_form', [$this, 'add_captcha_to_lostpassword']);
        add_action('lostpassword_post', [$this, 'verify_lostpassword_captcha']);
        
        // Comment form
        if (get_option('wpsp_captcha_comments', 0)) {
            add_action('comment_form_after_fields', [$this, 'add_captcha_to_comments']);
            add_filter('preprocess_comment', [$this, 'verify_comment_captcha']);
        }
        
        // Scripts
        add_action('login_enqueue_scripts', [$this, 'enqueue_captcha_scripts']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_captcha_scripts']);
    }
    
    /**
     * Enqueue Google reCAPTCHA scripts
     */
    public function enqueue_captcha_scripts() {
        wp_enqueue_script(
            'google-recaptcha',
            'https://www.google.com/recaptcha/api.js',
            [],
            null,
            true
        );
    }
    
    /**
     * Adicionar captcha ao formulário de login
     */
    public function add_captcha_to_login() {
        echo $this->render_captcha();
    }
    
    /**
     * Adicionar captcha ao formulário de registro
     */
    public function add_captcha_to_register() {
        echo $this->render_captcha();
    }
    
    /**
     * Adicionar captcha ao formulário de recuperação de senha
     */
    public function add_captcha_to_lostpassword() {
        echo $this->render_captcha();
    }
    
    /**
     * Adicionar captcha ao formulário de comentários
     */
    public function add_captcha_to_comments() {
        if (!is_user_logged_in()) {
            echo $this->render_captcha();
        }
    }
    
    /**
     * Renderizar captcha HTML
     */
    private function render_captcha() {
        return sprintf(
            '<div class="wpsp-captcha-wrapper" style="margin: 10px 0;">
                <div class="g-recaptcha" data-sitekey="%s"></div>
            </div>',
            esc_attr($this->site_key)
        );
    }
    
    /**
     * Verificar captcha no login
     */
    public function verify_login_captcha($user, $password) {
        if (is_wp_error($user)) {
            return $user;
        }
        
        if (!$this->verify_captcha_response()) {
            $this->db->add_security_log('captcha_failed_login', 'Failed captcha verification on login', $user->ID);
            return new \WP_Error('captcha_error', __('Please complete the captcha verification.', 'wp-security-pro'));
        }
        
        return $user;
    }
    
    /**
     * Verificar captcha no registro
     */
    public function verify_register_captcha($errors, $sanitized_user_login, $user_email) {
        if (!$this->verify_captcha_response()) {
            $errors->add('captcha_error', __('Please complete the captcha verification.', 'wp-security-pro'));
        }
        
        return $errors;
    }
    
    /**
     * Verificar captcha na recuperação de senha
     */
    public function verify_lostpassword_captcha($errors) {
        if (!$this->verify_captcha_response()) {
            $errors->add('captcha_error', __('Please complete the captcha verification.', 'wp-security-pro'));
        }
        
        return $errors;
    }
    
    /**
     * Verificar captcha nos comentários
     */
    public function verify_comment_captcha($commentdata) {
        if (!is_user_logged_in() && !$this->verify_captcha_response()) {
            wp_die(__('Please complete the captcha verification.', 'wp-security-pro'), 403);
        }
        
        return $commentdata;
    }
    
    /**
     * Verificar resposta do captcha com Google
     */
    private function verify_captcha_response() {
        if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
            return false;
        }
        
        $response = sanitize_text_field($_POST['g-recaptcha-response']);
        $remote_ip = $this->get_client_ip();
        
        $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
        
        $response = wp_remote_post($verify_url, [
            'body' => [
                'secret' => $this->secret_key,
                'response' => $response,
                'remoteip' => $remote_ip
            ]
        ]);
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $response_body = wp_remote_retrieve_body($response);
        $result = json_decode($response_body, true);
        
        return isset($result['success']) && $result['success'] === true;
    }
    
    /**
     * Obter IP do cliente
     */
    private function get_client_ip() {
        $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 
                    'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER)) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP)) {
                        return $ip;
                    }
                }
            }
        }
        
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    }
}