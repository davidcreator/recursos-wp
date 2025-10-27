<?php
namespace WPSP\Security\TwoFactor;

if (!defined('ABSPATH')) exit;

use WPSP\Core\Database;

class RecoveryCodes {
    
    private $db;
    private $codes_count = 10;
    private $code_length = 8;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Gerar códigos de recuperação
     */
    public function generate_codes($user_id, $count = null) {
        $count = $count ?: $this->codes_count;
        $codes = [];
        
        for ($i = 0; $i < $count; $i++) {
            $codes[] = $this->generate_single_code();
        }
        
        // Salvar códigos hasheados no banco
        $hashed_codes = array_map(function($code) {
            return wp_hash_password($code);
        }, $codes);
        
        $this->db->save_2fa_config($user_id, 'backup_codes', null, $hashed_codes);
        $this->db->toggle_2fa($user_id, 'backup_codes', true);
        
        // Log
        $this->db->add_security_log('2fa_backup_codes_generated', "Generated {$count} backup codes", $user_id);
        
        return $codes;
    }
    
    /**
     * Gerar um único código
     */
    private function generate_single_code() {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';
        
        for ($i = 0; $i < $this->code_length; $i++) {
            $code .= $characters[wp_rand(0, strlen($characters) - 1)];
            
            // Adicionar hífen a cada 4 caracteres para legibilidade
            if ($i === 3 && $this->code_length > 4) {
                $code .= '-';
            }
        }
        
        return $code;
    }
    
    /**
     * Verificar código de recuperação
     */
    public function verify_code($user_id, $code) {
        $code = strtoupper(str_replace([' ', '-'], '', $code));
        
        $config = $this->db->get_2fa_config($user_id, 'backup_codes');
        
        if (!$config || !$config->backup_codes) {
            return false;
        }
        
        $stored_codes = json_decode($config->backup_codes, true);
        
        if (!is_array($stored_codes)) {
            return false;
        }
        
        // Verificar cada código armazenado
        foreach ($stored_codes as $key => $hashed_code) {
            if (wp_check_password($code, $hashed_code)) {
                // Remover código usado
                unset($stored_codes[$key]);
                $stored_codes = array_values($stored_codes);
                
                // Atualizar no banco
                $this->db->save_2fa_config($user_id, 'backup_codes', null, $stored_codes);
                
                // Log
                $remaining = count($stored_codes);
                $this->db->add_security_log(
                    '2fa_backup_code_used', 
                    "Backup code used. {$remaining} codes remaining", 
                    $user_id
                );
                
                // Se não restam códigos, desabilitar
                if (empty($stored_codes)) {
                    $this->db->toggle_2fa($user_id, 'backup_codes', false);
                }
                
                return true;
            }
        }
        
        // Log falha
        $this->db->add_security_log('2fa_backup_code_invalid', 'Invalid backup code attempt', $user_id);
        
        return false;
    }
    
    /**
     * Obter códigos restantes (retorna apenas a quantidade, não os códigos)
     */
    public function get_remaining_count($user_id) {
        $config = $this->db->get_2fa_config($user_id, 'backup_codes');
        
        if (!$config || !$config->backup_codes) {
            return 0;
        }
        
        $codes = json_decode($config->backup_codes, true);
        return is_array($codes) ? count($codes) : 0;
    }
    
    /**
     * Verificar se usuário tem códigos de backup
     */
    public function has_backup_codes($user_id) {
        return $this->get_remaining_count($user_id) > 0;
    }
    
    /**
     * Regenerar todos os códigos
     */
    public function regenerate_codes($user_id, $count = null) {
        // Deletar códigos antigos
        $this->delete_codes($user_id);
        
        // Gerar novos códigos
        return $this->generate_codes($user_id, $count);
    }
    
    /**
     * Deletar todos os códigos de backup
     */
    public function delete_codes($user_id) {
        $result = $this->db->delete_2fa_config($user_id, 'backup_codes');
        
        if ($result) {
            $this->db->add_security_log('2fa_backup_codes_deleted', 'All backup codes deleted', $user_id);
        }
        
        return $result;
    }
    
    /**
     * Formatar código para exibição
     */
    public function format_code_for_display($code) {
        $code = strtoupper(str_replace([' ', '-'], '', $code));
        
        if (strlen($code) === 8) {
            return substr($code, 0, 4) . '-' . substr($code, 4, 4);
        }
        
        return $code;
    }
    
    /**
     * Validar formato do código
     */
    public function is_valid_format($code) {
        $code = str_replace([' ', '-'], '', $code);
        return preg_match('/^[0-9A-Z]{8}$/', strtoupper($code));
    }
    
    /**
     * Exportar códigos para download (apenas uma vez, após geração)
     */
    public function export_codes_text($codes, $user_id) {
        $user = get_userdata($user_id);
        $site_name = get_bloginfo('name');
        $date = current_time('Y-m-d H:i:s');
        
        $text = "========================================\n";
        $text .= "{$site_name}\n";
        $text .= "Backup Recovery Codes\n";
        $text .= "========================================\n\n";
        $text .= "User: {$user->user_login} ({$user->user_email})\n";
        $text .= "Generated: {$date}\n\n";
        $text .= "IMPORTANT: Store these codes in a safe place.\n";
        $text .= "Each code can only be used once.\n\n";
        $text .= "Codes:\n";
        $text .= "----------------------------------------\n";
        
        foreach ($codes as $index => $code) {
            $formatted_code = $this->format_code_for_display($code);
            $text .= sprintf("%2d. %s\n", $index + 1, $formatted_code);
        }
        
        $text .= "----------------------------------------\n\n";
        $text .= "If you lose access to your two-factor authentication\n";
        $text .= "device, you can use these codes to log in.\n\n";
        
        return $text;
    }
}