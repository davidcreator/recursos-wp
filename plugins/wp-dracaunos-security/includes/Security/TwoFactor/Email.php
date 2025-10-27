<?php
namespace WPSP\Security\TwoFactor;

if (!defined('ABSPATH')) exit;

use WPSP\Core\Database;

class Email {
    
    private $db;
    private $code_length = 6;
    private $code_expiry = 300; // 5 minutos
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Enviar código de verificação por email
     */
    public function send_code($user_id) {
        $user = get_userdata($user_id);
        
        if (!$user) {
            return false;
        }
        
        // Gerar código
        $code = $this->generate_code();
        
        // Salvar código no banco de dados
        $this->save_code($user_id, $code);
        
        // Preparar email
        $subject = sprintf(__('[%s] Two-Factor Authentication Code', 'wp-security-pro'), get_bloginfo('name'));
        
        $message = sprintf(
            __('Hello %s,', 'wp-security-pro'),
            $user->display_name
        ) . "\n\n";
        
        $message .= __('Your verification code is:', 'wp-security-pro') . "\n\n";
        $message .= $code . "\n\n";
        $message .= sprintf(__('This code will expire in %d minutes.', 'wp-security-pro'), $this->code_expiry / 60) . "\n\n";
        $message .= __('If you did not request this code, please ignore this email.', 'wp-security-pro') . "\n\n";
        $message .= sprintf(__('Thank you,\n%s Team', 'wp-security-pro'), get_bloginfo('name'));
        
        // Headers
        $headers = [
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
        ];
        
        // Enviar email
        $sent = wp_mail($user->user_email, $subject, $message, $headers);
        
        // Log
        if ($sent) {
            $this->db->add_security_log('2fa_email_sent', "Verification code sent to {$user->user_email}", $user_id);
        } else {
            $this->db->add_security_log('2fa_email_failed', "Failed to send verification code to {$user->user_email}", $user_id);
        }
        
        return $sent;
    }
    
    /**
     * Gerar código aleatório
     */
    private function generate_code() {
        return sprintf('%0' . $this->code_length . 'd', wp_rand(0, pow(10, $this->code_length) - 1));
    }
    
    /**
     * Salvar código no banco de dados
     */
    private function save_code($user_id, $code) {
        $hashed_code = wp_hash_password($code);
        $expiry = time() + $this->code_expiry;
        
        update_user_meta($user_id, '_wpsp_2fa_email_code', $hashed_code);
        update_user_meta($user_id, '_wpsp_2fa_email_code_expiry', $expiry);
        
        return true;
    }
    
    /**
     * Verificar código
     */
    public function verify_code($user_id, $code) {
        $stored_hash = get_user_meta($user_id, '_wpsp_2fa_email_code', true);
        $expiry = get_user_meta($user_id, '_wpsp_2fa_email_code_expiry', true);
        
        // Verificar se código existe
        if (empty($stored_hash) || empty($expiry)) {
            return false;
        }
        
        // Verificar expiração
        if (time() > $expiry) {
            $this->clear_code($user_id);
            return false;
        }
        
        // Verificar código
        if (!wp_check_password($code, $stored_hash)) {
            // Log tentativa falha
            $this->db->add_security_log('2fa_email_verify_failed', 'Invalid email verification code', $user_id);
            return false;
        }
        
        // Limpar código usado
        $this->clear_code($user_id);
        
        // Log sucesso
        $this->db->add_security_log('2fa_email_verify_success', 'Email verification successful', $user_id);
        
        return true;
    }
    
    /**
     * Limpar código usado
     */
    private function clear_code($user_id) {
        delete_user_meta($user_id, '_wpsp_2fa_email_code');
        delete_user_meta($user_id, '_wpsp_2fa_email_code_expiry');
    }
    
    /**
     * Reenviar código
     */
    public function resend_code($user_id) {
        // Limpar código anterior
        $this->clear_code($user_id);
        
        // Enviar novo código
        return $this->send_code($user_id);
    }
    
    /**
     * Verificar se código está expirado
     */
    public function is_code_expired($user_id) {
        $expiry = get_user_meta($user_id, '_wpsp_2fa_email_code_expiry', true);
        
        if (empty($expiry)) {
            return true;
        }
        
        return time() > $expiry;
    }
    
    /**
     * Obter tempo restante do código (em segundos)
     */
    public function get_code_time_remaining($user_id) {
        $expiry = get_user_meta($user_id, '_wpsp_2fa_email_code_expiry', true);
        
        if (empty($expiry)) {
            return 0;
        }
        
        $remaining = $expiry - time();
        return max(0, $remaining);
    }
}