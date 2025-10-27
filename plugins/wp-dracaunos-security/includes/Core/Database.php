<?php
namespace WPSP\Core;

if (!defined('ABSPATH')) exit;

class Database {
    
    private $wpdb;
    private $table_2fa;
    private $table_logs;
    private $table_sessions;
    private $table_blocked_ips;
    
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_2fa = $wpdb->prefix . 'wpsp_two_factor';
        $this->table_logs = $wpdb->prefix . 'wpsp_security_logs';
        $this->table_sessions = $wpdb->prefix . 'wpsp_sessions';
        $this->table_blocked_ips = $wpdb->prefix . 'wpsp_blocked_ips';
    }
    
    // ==========================================
    // TWO FACTOR AUTHENTICATION METHODS
    // ==========================================
    
    /**
     * Salvar configuração 2FA do usuário
     */
    public function save_2fa_config($user_id, $method, $secret = null, $backup_codes = null) {
        $existing = $this->get_2fa_config($user_id, $method);
        
        if ($existing) {
            return $this->wpdb->update(
                $this->table_2fa,
                [
                    'secret' => $secret,
                    'backup_codes' => is_array($backup_codes) ? json_encode($backup_codes) : $backup_codes,
                    'updated_at' => current_time('mysql')
                ],
                ['user_id' => $user_id, 'method' => $method],
                ['%s', '%s', '%s'],
                ['%d', '%s']
            );
        }
        
        return $this->wpdb->insert(
            $this->table_2fa,
            [
                'user_id' => $user_id,
                'method' => $method,
                'secret' => $secret,
                'backup_codes' => is_array($backup_codes) ? json_encode($backup_codes) : $backup_codes,
                'enabled' => 0,
                'created_at' => current_time('mysql')
            ],
            ['%d', '%s', '%s', '%s', '%d', '%s']
        );
    }
    
    /**
     * Obter configuração 2FA do usuário
     */
    public function get_2fa_config($user_id, $method = null) {
        if ($method) {
            return $this->wpdb->get_row(
                $this->wpdb->prepare(
                    "SELECT * FROM {$this->table_2fa} WHERE user_id = %d AND method = %s",
                    $user_id,
                    $method
                )
            );
        }
        
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table_2fa} WHERE user_id = %d",
                $user_id
            )
        );
    }
    
    /**
     * Ativar/Desativar 2FA para um método
     */
    public function toggle_2fa($user_id, $method, $enabled = true) {
        return $this->wpdb->update(
            $this->table_2fa,
            ['enabled' => $enabled ? 1 : 0, 'updated_at' => current_time('mysql')],
            ['user_id' => $user_id, 'method' => $method],
            ['%d', '%s'],
            ['%d', '%s']
        );
    }
    
    /**
     * Verificar se usuário tem 2FA ativo
     */
    public function user_has_2fa_enabled($user_id) {
        $count = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->table_2fa} WHERE user_id = %d AND enabled = 1",
                $user_id
            )
        );
        
        return $count > 0;
    }
    
    /**
     * Deletar configuração 2FA
     */
    public function delete_2fa_config($user_id, $method = null) {
        if ($method) {
            return $this->wpdb->delete(
                $this->table_2fa,
                ['user_id' => $user_id, 'method' => $method],
                ['%d', '%s']
            );
        }
        
        return $this->wpdb->delete(
            $this->table_2fa,
            ['user_id' => $user_id],
            ['%d']
        );
    }
    
    /**
     * Usar código de backup
     */
    public function use_backup_code($user_id, $code) {
        $config = $this->get_2fa_config($user_id, 'backup_codes');
        
        if (!$config || !$config->backup_codes) {
            return false;
        }
        
        $codes = json_decode($config->backup_codes, true);
        
        if (!is_array($codes)) {
            return false;
        }
        
        $key = array_search($code, $codes);
        
        if ($key === false) {
            return false;
        }
        
        // Remover código usado
        unset($codes[$key]);
        $codes = array_values($codes);
        
        $this->wpdb->update(
            $this->table_2fa,
            ['backup_codes' => json_encode($codes), 'updated_at' => current_time('mysql')],
            ['user_id' => $user_id, 'method' => 'backup_codes'],
            ['%s', '%s'],
            ['%d', '%s']
        );
        
        return true;
    }
    
    // ==========================================
    // SECURITY LOGS METHODS
    // ==========================================
    
    /**
     * Adicionar log de segurança
     */
    public function add_security_log($action, $details = null, $user_id = null) {
        $ip_address = $this->get_client_ip();
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        
        return $this->wpdb->insert(
            $this->table_logs,
            [
                'user_id' => $user_id,
                'action' => $action,
                'ip_address' => $ip_address,
                'user_agent' => $user_agent,
                'details' => is_array($details) ? json_encode($details) : $details,
                'created_at' => current_time('mysql')
            ],
            ['%d', '%s', '%s', '%s', '%s', '%s']
        );
    }
    
    /**
     * Obter logs de segurança
     */
    public function get_security_logs($args = []) {
        $defaults = [
            'limit' => 50,
            'offset' => 0,
            'user_id' => null,
            'action' => null,
            'start_date' => null,
            'end_date' => null,
            'orderby' => 'created_at',
            'order' => 'DESC'
        ];
        
        $args = wp_parse_args($args, $defaults);
        
        $where = ['1=1'];
        $where_values = [];
        
        if ($args['user_id']) {
            $where[] = 'user_id = %d';
            $where_values[] = $args['user_id'];
        }
        
        if ($args['action']) {
            $where[] = 'action = %s';
            $where_values[] = $args['action'];
        }
        
        if ($args['start_date']) {
            $where[] = 'created_at >= %s';
            $where_values[] = $args['start_date'];
        }
        
        if ($args['end_date']) {
            $where[] = 'created_at <= %s';
            $where_values[] = $args['end_date'];
        }
        
        $where_clause = implode(' AND ', $where);
        
        $orderby = in_array($args['orderby'], ['created_at', 'action', 'user_id']) ? $args['orderby'] : 'created_at';
        $order = strtoupper($args['order']) === 'ASC' ? 'ASC' : 'DESC';
        
        $query = "SELECT * FROM {$this->table_logs} WHERE {$where_clause} ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d";
        $where_values[] = $args['limit'];
        $where_values[] = $args['offset'];
        
        if (!empty($where_values)) {
            $query = $this->wpdb->prepare($query, $where_values);
        }
        
        return $this->wpdb->get_results($query);
    }
    
    /**
     * Contar logs de segurança
     */
    public function count_security_logs($args = []) {
        $where = ['1=1'];
        $where_values = [];
        
        if (!empty($args['user_id'])) {
            $where[] = 'user_id = %d';
            $where_values[] = $args['user_id'];
        }
        
        if (!empty($args['action'])) {
            $where[] = 'action = %s';
            $where_values[] = $args['action'];
        }
        
        if (!empty($args['start_date'])) {
            $where[] = 'created_at >= %s';
            $where_values[] = $args['start_date'];
        }
        
        if (!empty($args['end_date'])) {
            $where[] = 'created_at <= %s';
            $where_values[] = $args['end_date'];
        }
        
        $where_clause = implode(' AND ', $where);
        $query = "SELECT COUNT(*) FROM {$this->table_logs} WHERE {$where_clause}";
        
        if (!empty($where_values)) {
            $query = $this->wpdb->prepare($query, $where_values);
        }
        
        return (int) $this->wpdb->get_var($query);
    }
    
    /**
     * Limpar logs antigos
     */
    public function clean_old_logs($days = 30) {
        return $this->wpdb->query(
            $this->wpdb->prepare(
                "DELETE FROM {$this->table_logs} WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
                $days
            )
        );
    }
    
    /**
     * Deletar todos os logs de um usuário
     */
    public function delete_user_logs($user_id) {
        return $this->wpdb->delete(
            $this->table_logs,
            ['user_id' => $user_id],
            ['%d']
        );
    }
    
    // ==========================================
    // BLOCKED IPS METHODS
    // ==========================================
    
    /**
     * Bloquear IP
     */
    public function block_ip($ip_address, $reason = '', $duration = null) {
        $expires_at = null;
        if ($duration) {
            $expires_at = date('Y-m-d H:i:s', strtotime("+{$duration} minutes"));
        }
        
        return $this->wpdb->insert(
            $this->table_blocked_ips,
            [
                'ip_address' => $ip_address,
                'reason' => $reason,
                'expires_at' => $expires_at,
                'created_at' => current_time('mysql')
            ],
            ['%s', '%s', '%s', '%s']
        );
    }
    
    /**
     * Verificar se IP está bloqueado
     */
    public function is_ip_blocked($ip_address) {
        $result = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table_blocked_ips} 
                WHERE ip_address = %s 
                AND (expires_at IS NULL OR expires_at > NOW())",
                $ip_address
            )
        );
        
        return !empty($result);
    }
    
    /**
     * Desbloquear IP
     */
    public function unblock_ip($ip_address) {
        return $this->wpdb->delete(
            $this->table_blocked_ips,
            ['ip_address' => $ip_address],
            ['%s']
        );
    }
    
    /**
     * Listar IPs bloqueados
     */
    public function get_blocked_ips($active_only = true) {
        $query = "SELECT * FROM {$this->table_blocked_ips}";
        
        if ($active_only) {
            $query .= " WHERE expires_at IS NULL OR expires_at > NOW()";
        }
        
        $query .= " ORDER BY created_at DESC";
        
        return $this->wpdb->get_results($query);
    }
    
    /**
     * Limpar bloqueios expirados
     */
    public function clean_expired_blocks() {
        return $this->wpdb->query(
            "DELETE FROM {$this->table_blocked_ips} WHERE expires_at IS NOT NULL AND expires_at <= NOW()"
        );
    }
    
    // ==========================================
    // SESSIONS METHODS
    // ==========================================
    
    /**
     * Criar sessão
     */
    public function create_session($user_id, $token, $expires_at = null) {
        if (!$expires_at) {
            $expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));
        }
        
        return $this->wpdb->insert(
            $this->table_sessions,
            [
                'user_id' => $user_id,
                'token' => $token,
                'ip_address' => $this->get_client_ip(),
                'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
                'expires_at' => $expires_at,
                'created_at' => current_time('mysql')
            ],
            ['%d', '%s', '%s', '%s', '%s', '%s']
        );
    }
    
    /**
     * Validar sessão
     */
    public function validate_session($token) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table_sessions} WHERE token = %s AND expires_at > NOW()",
                $token
            )
        );
    }
    
    /**
     * Deletar sessão
     */
    public function delete_session($token) {
        return $this->wpdb->delete(
            $this->table_sessions,
            ['token' => $token],
            ['%s']
        );
    }
    
    /**
     * Deletar todas as sessões de um usuário
     */
    public function delete_user_sessions($user_id, $except_token = null) {
        if ($except_token) {
            return $this->wpdb->query(
                $this->wpdb->prepare(
                    "DELETE FROM {$this->table_sessions} WHERE user_id = %d AND token != %s",
                    $user_id,
                    $except_token
                )
            );
        }
        
        return $this->wpdb->delete(
            $this->table_sessions,
            ['user_id' => $user_id],
            ['%d']
        );
    }
    
    /**
     * Limpar sessões expiradas
     */
    public function clean_expired_sessions() {
        return $this->wpdb->query(
            "DELETE FROM {$this->table_sessions} WHERE expires_at <= NOW()"
        );
    }
    
    /**
     * Obter sessões ativas do usuário
     */
    public function get_user_sessions($user_id) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table_sessions} WHERE user_id = %d AND expires_at > NOW() ORDER BY created_at DESC",
                $user_id
            )
        );
    }
    
    // ==========================================
    // UTILITY METHODS
    // ==========================================
    
    /**
     * Obter IP do cliente
     */
    private function get_client_ip() {
        $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 
                    'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    }
    
    /**
     * Obter estatísticas gerais
     */
    public function get_statistics() {
        return [
            'total_logs' => $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->table_logs}"),
            'logs_today' => $this->wpdb->get_var(
                "SELECT COUNT(*) FROM {$this->table_logs} WHERE DATE(created_at) = CURDATE()"
            ),
            'logs_week' => $this->wpdb->get_var(
                "SELECT COUNT(*) FROM {$this->table_logs} WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
            ),
            'blocked_ips' => $this->wpdb->get_var(
                "SELECT COUNT(*) FROM {$this->table_blocked_ips} WHERE expires_at IS NULL OR expires_at > NOW()"
            ),
            'active_sessions' => $this->wpdb->get_var(
                "SELECT COUNT(*) FROM {$this->table_sessions} WHERE expires_at > NOW()"
            ),
            'users_with_2fa' => $this->wpdb->get_var(
                "SELECT COUNT(DISTINCT user_id) FROM {$this->table_2fa} WHERE enabled = 1"
            )
        ];
    }
    
    /**
     * Exportar dados para backup
     */
    public function export_data($table = 'all') {
        $data = [];
        
        if ($table === 'all' || $table === '2fa') {
            $data['2fa'] = $this->wpdb->get_results("SELECT * FROM {$this->table_2fa}", ARRAY_A);
        }
        
        if ($table === 'all' || $table === 'logs') {
            $data['logs'] = $this->wpdb->get_results("SELECT * FROM {$this->table_logs}", ARRAY_A);
        }
        
        if ($table === 'all' || $table === 'blocked_ips') {
            $data['blocked_ips'] = $this->wpdb->get_results("SELECT * FROM {$this->table_blocked_ips}", ARRAY_A);
        }
        
        if ($table === 'all' || $table === 'sessions') {
            $data['sessions'] = $this->wpdb->get_results("SELECT * FROM {$this->table_sessions}", ARRAY_A);
        }
        
        return $data;
    }
}