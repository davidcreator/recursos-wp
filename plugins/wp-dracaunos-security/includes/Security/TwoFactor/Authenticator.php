<?php
namespace WPSP\Security\TwoFactor;

if (!defined('ABSPATH')) exit;

use WPSP\Core\Database;

class Authenticator {
    
    private $db;
    private $code_length = 6;
    private $time_step = 30; // segundos
    private $tolerance = 1; // permitir 1 step antes/depois
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Gerar secret para o usuário
     */
    public function generate_secret($user_id) {
        $secret = $this->create_secret();
        
        // Salvar no banco
        $this->db->save_2fa_config($user_id, 'authenticator', $secret);
        
        return $secret;
    }
    
    /**
     * Criar secret aleatório (Base32)
     */
    private function create_secret($length = 32) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        $chars_length = strlen($chars);
        
        for ($i = 0; $i < $length; $i++) {
            $secret .= $chars[random_int(0, $chars_length - 1)];
        }
        
        return $secret;
    }
    
    /**
     * Obter secret do usuário
     */
    public function get_secret($user_id) {
        $config = $this->db->get_2fa_config($user_id, 'authenticator');
        return $config ? $config->secret : null;
    }
    
    /**
     * Gerar URL para QR Code
     */
    public function get_qr_code_url($user_id) {
        $secret = $this->get_secret($user_id);
        
        if (!$secret) {
            $secret = $this->generate_secret($user_id);
        }
        
        $user = get_userdata($user_id);
        $issuer = get_bloginfo('name');
        $label = $user->user_email;
        
        $otpauth = sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s',
            rawurlencode($issuer),
            rawurlencode($label),
            $secret,
            rawurlencode($issuer)
        );
        
        // Usar Google Charts API para gerar QR Code
        return 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=' . urlencode($otpauth);
    }
    
    /**
     * Verificar código TOTP
     */
    public function verify_code($user_id, $code) {
        $secret = $this->get_secret($user_id);
        
        if (!$secret) {
            return false;
        }
        
        $code = preg_replace('/[^0-9]/', '', $code);
        
        if (strlen($code) !== $this->code_length) {
            return false;
        }
        
        // Verificar código atual e com tolerância
        $current_time = time();
        
        for ($i = -$this->tolerance; $i <= $this->tolerance; $i++) {
            $time_slice = floor($current_time / $this->time_step) + $i;
            $generated_code = $this->generate_totp($secret, $time_slice);
            
            if ($this->timing_safe_equals($code, $generated_code)) {
                // Log sucesso
                $this->db->add_security_log('2fa_authenticator_success', 'Authenticator verification successful', $user_id);
                return true;
            }
        }
        
        // Log falha
        $this->db->add_security_log('2fa_authenticator_failed', 'Invalid authenticator code', $user_id);
        return false;
    }
    
    /**
     * Gerar código TOTP
     */
    private function generate_totp($secret, $time_slice) {
        $secret_key = $this->base32_decode($secret);
        $time = pack('N*', 0) . pack('N*', $time_slice);
        $hash = hash_hmac('sha1', $time, $secret_key, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $truncated_hash = substr($hash, $offset, 4);
        $code = unpack('N', $truncated_hash)[1] & 0x7FFFFFFF;
        
        return str_pad($code % pow(10, $this->code_length), $this->code_length, '0', STR_PAD_LEFT);
    }
    
    /**
     * Decodificar Base32
     */
    private function base32_decode($input) {
        $input = strtoupper($input);
        $lut = [
            'A' => 0,  'B' => 1,  'C' => 2,  'D' => 3,
            'E' => 4,  'F' => 5,  'G' => 6,  'H' => 7,
            'I' => 8,  'J' => 9,  'K' => 10, 'L' => 11,
            'M' => 12, 'N' => 13, 'O' => 14, 'P' => 15,
            'Q' => 16, 'R' => 17, 'S' => 18, 'T' => 19,
            'U' => 20, 'V' => 21, 'W' => 22, 'X' => 23,
            'Y' => 24, 'Z' => 25, '2' => 26, '3' => 27,
            '4' => 28, '5' => 29, '6' => 30, '7' => 31
        ];
        
        $input = preg_replace('/[^A-Z2-7]/', '', $input);
        $output = '';
        $v = 0;
        $vbits = 0;
        
        for ($i = 0, $j = strlen($input); $i < $j; $i++) {
            $v <<= 5;
            $v += $lut[$input[$i]];
            $vbits += 5;
            
            while ($vbits >= 8) {
                $vbits -= 8;
                $output .= chr($v >> $vbits);
                $v &= ((1 << $vbits) - 1);
            }
        }
        
        return $output;
    }
    
    /**
     * Comparação segura contra timing attacks
     */
    private function timing_safe_equals($safe, $user) {
        if (function_exists('hash_equals')) {
            return hash_equals($safe, $user);
        }
        
        $safe_len = strlen($safe);
        $user_len = strlen($user);
        
        if ($user_len !== $safe_len) {
            return false;
        }
        
        $result = 0;
        for ($i = 0; $i < $user_len; $i++) {
            $result |= (ord($safe[$i]) ^ ord($user[$i]));
        }
        
        return $result === 0;
    }
    
    /**
     * Validar setup do authenticator
     */
    public function validate_setup($user_id, $code) {
        if ($this->verify_code($user_id, $code)) {
            $this->db->toggle_2fa($user_id, 'authenticator', true);
            return true;
        }
        return false;
    }
    
    /**
     * Remover authenticator
     */
    public function remove_authenticator($user_id) {
        return $this->db->delete_2fa_config($user_id, 'authenticator');
    }
}
