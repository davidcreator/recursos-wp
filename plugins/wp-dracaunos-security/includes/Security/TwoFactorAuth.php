<?php
namespace WPSP\Security;

if (!defined('ABSPATH')) exit;

use WPSP\Core\Database;
use WPSP\Security\TwoFactor\Email;
use WPSP\Security\TwoFactor\Authenticator;
use WPSP\Security\TwoFactor\RecoveryCodes;

class TwoFactorAuth {
    
    private $db;
    private $email;
    private $authenticator;
    private $recovery_codes;
    
    public function __construct() {
        $this->db = new Database();
        $this->email = new Email();
        $this->authenticator = new Authenticator();
        $this->recovery_codes = new RecoveryCodes();
        
        $this->init_hooks();
    }
    
    private function init_hooks() {
        // Adicionar verificação 2FA após login
        add_filter('authenticate', [$this, 'check_2fa_after_login'], 50, 3);
        
        // Adicionar página de verificação 2FA
        add_action('login_form', [$this, 'add_2fa_field']);
        
        // AJAX handlers
        add_action('wp_ajax_wpsp_enable_2fa', [$this, 'ajax_enable_2fa']);
        add_action('wp_ajax_wpsp_disable_2fa', [$this, 'ajax_disable_2fa']);
        add_action('wp_ajax_wpsp_verify_2fa', [$this, 'ajax_verify_2fa']);
        add_action('wp_ajax_wpsp_generate_backup_codes', [$this, 'ajax_generate_backup_codes']);
        add_action('wp_ajax_wpsp_setup_authenticator', [$this, 'ajax_setup_authenticator']);
        add_action('wp_ajax_wpsp_verify_authenticator', [$this, 'ajax_verify_authenticator']);
        add_action('wp_ajax_wpsp_resend_email_code', [$this, 'ajax_resend_email_code']);
        
        // Adicionar configurações 2FA ao perfil do usuário
        add_action('show_user_profile', [$this, 'user_2fa_settings']);
        add_action('edit_user_profile', [$this, 'user_2fa_settings']);
        
        // Shortcode para front-end
        add_shortcode('wpsp_2fa_settings', [$this, 'frontend_2fa_settings']);
        
        // Scripts
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    }
    
    /**
     * Verificar 2FA após login
     */
    public function check_2fa_after_login($user, $username, $password) {
        // Se já houve erro, retornar
        if (is_wp_error($user)) {
            return $user;
        }
        
        // Verificar se é objeto de usuário válido
        if (!is_a($user, 'WP_User')) {
            return $user;
        }
        
        // Verificar se usuário tem 2FA ativo
        if (!$this->db->user_has_2fa_enabled($user->ID)) {
            return $user;
        }
        
        // Verificar se já passou pela verificação 2FA
        if (isset($_POST['wpsp_2fa_code']) && !empty($_POST['wpsp_2fa_code'])) {
            $code = sanitize_text_field($_POST['wpsp_2fa_code']);
            $method = isset($_POST['wpsp_2fa_method']) ? sanitize_text_field($_POST['wpsp_2fa_method']) : 'email';
            
            if ($this->verify_code($user->ID, $code, $method)) {
                // Log sucesso
                $this->db->add_security_log('2fa_success', "User {$user->user_login} completed 2FA verification", $user->ID);
                return $user;
            } else {
                // Log falha
                $this->db->add_security_log('2fa_failed', "User {$user->user_login} failed 2FA verification", $user->ID);
                return new \WP_Error('2fa_failed', __('Invalid verification code.', 'wp-security-pro'));
            }
        }
        
        set_transient('wpsp_2fa_user_' . $user->ID, $user->ID, 300);
        if (!headers_sent()) {
            $cookie_path = defined('COOKIEPATH') ? COOKIEPATH : '/';
            $cookie_domain = defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : '';
            setcookie('wpsp_2fa_user', (string) $user->ID, time() + 300, $cookie_path, $cookie_domain, is_ssl(), true);
        }
        
        // Enviar código se for método email
        $methods = $this->get_user_enabled_methods($user->ID);
        if (in_array('email', $methods)) {
            $this->email->send_code($user->ID);
        }
        
        // Retornar erro para mostrar página de verificação
        return new \WP_Error('2fa_required', __('Two-factor authentication required.', 'wp-security-pro'), [
            'user_id' => $user->ID,
            'methods' => $methods
        ]);
    }
    
    /**
     * Adicionar campo 2FA no formulário de login
     */
    public function add_2fa_field() {
        $show = false;
        if (isset($_COOKIE['wpsp_2fa_user'])) {
            $uid = intval($_COOKIE['wpsp_2fa_user']);
            if ($uid && get_transient('wpsp_2fa_user_' . $uid)) {
                $show = true;
            }
        }
        if ($show) {
            ?>
            <p>
                <label for="wpsp_2fa_code"><?php _e('Verification Code', 'wp-security-pro'); ?><br />
                <input type="text" name="wpsp_2fa_code" id="wpsp_2fa_code" class="input" size="20" autocomplete="off" /></label>
            </p>
            <p class="wpsp-2fa-methods">
                <small><?php _e('Enter the verification code from your authenticator app or email.', 'wp-security-pro'); ?></small>
            </p>
            <?php
        }
    }
    
    /**
     * Verificar código 2FA
     */
    public function verify_code($user_id, $code, $method = 'email') {
        switch ($method) {
            case 'email':
                return $this->email->verify_code($user_id, $code);
                
            case 'authenticator':
                return $this->authenticator->verify_code($user_id, $code);
                
            case 'backup':
                return $this->recovery_codes->verify_code($user_id, $code);
                
            default:
                return false;
        }
    }
    
    /**
     * Obter métodos 2FA habilitados para usuário
     */
    public function get_user_enabled_methods($user_id) {
        $configs = $this->db->get_2fa_config($user_id);
        $methods = [];
        
        if ($configs) {
            foreach ($configs as $config) {
                if ($config->enabled) {
                    $methods[] = $config->method;
                }
            }
        }
        
        return $methods;
    }
    
    /**
     * Configurações 2FA no perfil do usuário
     */
    public function user_2fa_settings($user) {
        $methods = $this->get_user_enabled_methods($user->ID);
        ?>
        <h2><?php _e('Two-Factor Authentication', 'wp-security-pro'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('2FA Status', 'wp-security-pro'); ?></th>
                <td>
                    <?php if (!empty($methods)): ?>
                        <span class="wpsp-2fa-enabled"><?php _e('Enabled', 'wp-security-pro'); ?></span>
                        <p><?php _e('Active methods:', 'wp-security-pro'); ?> <?php echo implode(', ', $methods); ?></p>
                    <?php else: ?>
                        <span class="wpsp-2fa-disabled"><?php _e('Disabled', 'wp-security-pro'); ?></span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Email Verification', 'wp-security-pro'); ?></th>
                <td>
                    <button type="button" class="button wpsp-enable-2fa" data-method="email" data-user-id="<?php echo $user->ID; ?>">
                        <?php echo in_array('email', $methods) ? __('Disable', 'wp-security-pro') : __('Enable', 'wp-security-pro'); ?>
                    </button>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Authenticator App', 'wp-security-pro'); ?></th>
                <td>
                    <button type="button" class="button wpsp-setup-authenticator" data-user-id="<?php echo $user->ID; ?>">
                        <?php _e('Setup Authenticator', 'wp-security-pro'); ?>
                    </button>
                    <div id="wpsp-qr-code"></div>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Backup Codes', 'wp-security-pro'); ?></th>
                <td>
                    <button type="button" class="button wpsp-generate-backup-codes" data-user-id="<?php echo $user->ID; ?>">
                        <?php _e('Generate Backup Codes', 'wp-security-pro'); ?>
                    </button>
                    <div id="wpsp-backup-codes"></div>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Shortcode para configurações 2FA no front-end
     */
    public function frontend_2fa_settings($atts) {
        if (!is_user_logged_in()) {
            return '<p>' . __('You must be logged in to access this page.', 'wp-security-pro') . '</p>';
        }
        
        ob_start();
        $user = wp_get_current_user();
        $this->user_2fa_settings($user);
        return ob_get_clean();
    }
    
    /**
     * AJAX: Habilitar/Desabilitar 2FA
     */
    public function ajax_enable_2fa() {
        check_ajax_referer('wpsp_admin_nonce', 'nonce');
        
        $user_id = intval($_POST['user_id']);
        $method = sanitize_text_field($_POST['method']);
        
        if ($user_id !== get_current_user_id() && !current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied.', 'wp-security-pro')]);
        }
        
        $enabled = isset($_POST['enabled']) ? (bool) $_POST['enabled'] : true;
        
        if ($enabled) {
            $this->db->save_2fa_config($user_id, $method);
            $this->db->toggle_2fa($user_id, $method, true);
            $message = __('2FA enabled successfully.', 'wp-security-pro');
        } else {
            $this->db->toggle_2fa($user_id, $method, false);
            $message = __('2FA disabled successfully.', 'wp-security-pro');
        }
        
        wp_send_json_success(['message' => $message]);
    }
    
    /**
     * AJAX: Gerar códigos de backup
     */
    public function ajax_generate_backup_codes() {
        check_ajax_referer('wpsp_admin_nonce', 'nonce');
        
        $user_id = intval($_POST['user_id']);
        
        if ($user_id !== get_current_user_id() && !current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied.', 'wp-security-pro')]);
        }
        
        $codes = $this->recovery_codes->generate_codes($user_id);
        
        wp_send_json_success([
            'codes' => $codes,
            'message' => __('Backup codes generated successfully.', 'wp-security-pro')
        ]);
    }
    
    /**
     * AJAX: Setup Authenticator
     */
    public function ajax_setup_authenticator() {
        check_ajax_referer('wpsp_admin_nonce', 'nonce');
        
        $user_id = intval($_POST['user_id']);
        
        if ($user_id !== get_current_user_id() && !current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied.', 'wp-security-pro')]);
        }
        
        // Gerar secret
        $secret = $this->authenticator->generate_secret($user_id);
        $qr_code_url = $this->authenticator->get_qr_code_url($user_id);
        
        wp_send_json_success([
            'secret' => $secret,
            'qr_code_url' => $qr_code_url,
            'message' => __('QR Code generated successfully.', 'wp-security-pro')
        ]);
    }
    
    /**
     * AJAX: Verify Authenticator
     */
    public function ajax_verify_authenticator() {
        check_ajax_referer('wpsp_admin_nonce', 'nonce');
        
        $user_id = intval($_POST['user_id']);
        $code = sanitize_text_field($_POST['code']);
        
        if ($user_id !== get_current_user_id() && !current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied.', 'wp-security-pro')]);
        }
        
        if ($this->authenticator->validate_setup($user_id, $code)) {
            wp_send_json_success([
                'message' => __('Authenticator enabled successfully.', 'wp-security-pro')
            ]);
        } else {
            wp_send_json_error([
                'message' => __('Invalid code. Please try again.', 'wp-security-pro')
            ]);
        }
    }
    
    /**
     * AJAX: Resend Email Code
     */
    public function ajax_resend_email_code() {
        check_ajax_referer('wpsp_admin_nonce', 'nonce');
        
        $user_id = intval($_POST['user_id']);
        
        if ($user_id !== get_current_user_id() && !current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied.', 'wp-security-pro')]);
        }
        
        if ($this->email->resend_code($user_id)) {
            wp_send_json_success([
                'message' => __('Verification code resent successfully.', 'wp-security-pro')
            ]);
        } else {
            wp_send_json_error([
                'message' => __('Failed to send verification code.', 'wp-security-pro')
            ]);
        }
    }
    
    /**
     * Enqueue scripts
     */
    public function enqueue_scripts() {
        if (is_admin() || is_page() || is_user_logged_in()) {
            wp_enqueue_script('wpsp-two-factor', WPSP_PLUGIN_URL . 'assets/js/two-factor.js', ['jquery'], WPSP_VERSION, true);
            wp_localize_script('wpsp-two-factor', 'wpsp2FA', [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wpsp_admin_nonce')
            ]);
        }
    }   
}


