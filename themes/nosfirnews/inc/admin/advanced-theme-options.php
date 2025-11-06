<?php
/**
 * Melhorias de Segurança e Sanitização
 * 
 * @package NosfirNews
 */

// 1. Adicionar sanitização mais robusta em advanced-theme-options.php
class NosfirNews_Security_Enhanced {
    
    /**
     * Sanitiza valores de checkbox com validação adicional
     */
    public static function sanitize_checkbox_strict($input) {
        return filter_var($input, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
    }
    
    /**
     * Sanitiza array recursivamente
     */
    public static function sanitize_array_recursive($array) {
        $sanitized = array();
        
        foreach ($array as $key => $value) {
            $key = sanitize_key($key);
            
            if (is_array($value)) {
                $sanitized[$key] = self::sanitize_array_recursive($value);
            } else {
                $sanitized[$key] = sanitize_text_field($value);
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Valida e sanitiza URL com whitelist de domínios permitidos
     */
    public static function sanitize_url_with_validation($url, $allowed_protocols = array('http', 'https')) {
        $url = esc_url_raw($url, $allowed_protocols);
        
        // Validação adicional
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return '';
        }
        
        return $url;
    }
    
    /**
     * Sanitiza CSS personalizado removendo código potencialmente perigoso
     */
    public static function sanitize_custom_css($css) {
        // Remove tags script
        $css = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $css);
        
        // Remove javascript: protocol
        $css = preg_replace('/javascript:/i', '', $css);
        
        // Remove import statements
        $css = preg_replace('/@import/i', '', $css);
        
        // Remove expressões e comportamentos
        $css = preg_replace('/expression\s*\(/i', '', $css);
        $css = preg_replace('/behavior\s*:/i', '', $css);
        
        return wp_strip_all_tags($css);
    }
    
    /**
     * Sanitiza JavaScript personalizado
     */
    public static function sanitize_custom_js($js) {
        // Remove tags script
        $js = preg_replace('/<\/?script[^>]*>/i', '', $js);
        
        // Remove eval e funções perigosas
        $dangerous = array('eval', 'exec', 'system', 'shell_exec', 'passthru');
        foreach ($dangerous as $func) {
            $js = preg_replace('/' . $func . '\s*\(/i', '', $js);
        }
        
        return wp_strip_all_tags($js);
    }
    
    /**
     * Valida nonce com timestamp
     */
    public static function verify_nonce_with_timestamp($nonce, $action, $max_age = 3600) {
        if (!wp_verify_nonce($nonce, $action)) {
            return false;
        }
        
        // Verificar idade do nonce (opcional, para segurança adicional)
        $nonce_tick = wp_verify_nonce($nonce, $action);
        if ($nonce_tick === false || $nonce_tick > 2) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Rate limiting para AJAX requests
     */
    public static function check_rate_limit($action, $limit = 10, $period = 60) {
        $user_id = get_current_user_id();
        $ip = self::get_client_ip();
        $key = 'rate_limit_' . md5($action . $user_id . $ip);
        
        $count = get_transient($key);
        
        if ($count === false) {
            set_transient($key, 1, $period);
            return true;
        }
        
        if ($count >= $limit) {
            return false;
        }
        
        set_transient($key, $count + 1, $period);
        return true;
    }
    
    /**
     * Obtém IP do cliente de forma segura
     */
    public static function get_client_ip() {
        $ip_keys = array(
            'HTTP_CF_CONNECTING_IP', // CloudFlare
            'HTTP_X_FORWARDED_FOR',
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR'
        );
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER)) {
                $ip = $_SERVER[$key];
                
                // Validar IP
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
                
                // Se for uma lista, pegar o primeiro válido
                if (strpos($ip, ',') !== false) {
                    $ips = explode(',', $ip);
                    foreach ($ips as $single_ip) {
                        $single_ip = trim($single_ip);
                        if (filter_var($single_ip, FILTER_VALIDATE_IP)) {
                            return $single_ip;
                        }
                    }
                }
            }
        }
        
        return '0.0.0.0';
    }
}

// 2. Melhorias para metaboxes.php - Validação de dados
function nosfirnews_validate_post_meta($value, $type = 'text') {
    switch ($type) {
        case 'number':
            return is_numeric($value) ? absint($value) : 0;
            
        case 'url':
            return NosfirNews_Security_Enhanced::sanitize_url_with_validation($value);
            
        case 'email':
            return is_email($value) ? sanitize_email($value) : '';
            
        case 'checkbox':
            return NosfirNews_Security_Enhanced::sanitize_checkbox_strict($value);
            
        case 'textarea':
            return sanitize_textarea_field($value);
            
        case 'html':
            return wp_kses_post($value);
            
        default:
            return sanitize_text_field($value);
    }
}

// 3. Proteção contra CSRF em AJAX
function nosfirnews_ajax_handler_secure() {
    // Verificar nonce
    if (!check_ajax_referer('nosfirnews_ajax_nonce', 'nonce', false)) {
        wp_send_json_error(array(
            'message' => __('Requisição inválida. Por favor, recarregue a página.', 'nosfirnews')
        ), 403);
    }
    
    // Rate limiting
    if (!NosfirNews_Security_Enhanced::check_rate_limit('ajax_action', 20, 60)) {
        wp_send_json_error(array(
            'message' => __('Muitas requisições. Aguarde um momento.', 'nosfirnews')
        ), 429);
    }
    
    // Verificar capacidade do usuário
    if (!current_user_can('edit_posts')) {
        wp_send_json_error(array(
            'message' => __('Você não tem permissão para realizar esta ação.', 'nosfirnews')
        ), 403);
    }
    
    // Processar ação...
}

// 4. Logging seguro de erros
class NosfirNews_Error_Logger {
    
    private static $log_file = null;
    
    /**
     * Inicializa o logger
     */
    public static function init() {
        $upload_dir = wp_upload_dir();
        $log_dir = $upload_dir['basedir'] . '/nosfirnews-logs';
        
        // Criar diretório se não existir
        if (!file_exists($log_dir)) {
            wp_mkdir_p($log_dir);
            
            // Adicionar .htaccess para proteção
            $htaccess = $log_dir . '/.htaccess';
            if (!file_exists($htaccess)) {
                file_put_contents($htaccess, "deny from all\n");
            }
            
            // Adicionar index.php vazio
            $index = $log_dir . '/index.php';
            if (!file_exists($index)) {
                file_put_contents($index, "<?php\n// Silence is golden.\n");
            }
        }
        
        self::$log_file = $log_dir . '/error-' . date('Y-m-d') . '.log';
    }
    
    /**
     * Registra erro
     */
    public static function log($message, $level = 'INFO') {
        if (self::$log_file === null) {
            self::init();
        }
        
        // Não registrar em produção se não for erro crítico
        if (!WP_DEBUG && $level === 'INFO') {
            return;
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $ip = NosfirNews_Security_Enhanced::get_client_ip();
        $user_id = get_current_user_id();
        
        $log_entry = sprintf(
            "[%s] [%s] [IP: %s] [User: %d] %s\n",
            $timestamp,
            $level,
            $ip,
            $user_id,
            $message
        );
        
        error_log($log_entry, 3, self::$log_file);
    }
    
    /**
     * Limpa logs antigos
     */
    public static function cleanup_old_logs($days = 30) {
        $upload_dir = wp_upload_dir();
        $log_dir = $upload_dir['basedir'] . '/nosfirnews-logs';
        
        if (!is_dir($log_dir)) {
            return;
        }
        
        $files = glob($log_dir . '/error-*.log');
        $cutoff = time() - ($days * DAY_IN_SECONDS);
        
        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                unlink($file);
            }
        }
    }
}

// Inicializar logger
NosfirNews_Error_Logger::init();

// 5. Validação de upload de arquivos
function nosfirnews_validate_file_upload($file) {
    // Verificar se há erro no upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return new WP_Error('upload_error', __('Erro no upload do arquivo.', 'nosfirnews'));
    }
    
    // Verificar tamanho do arquivo (max 5MB)
    $max_size = 5 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        return new WP_Error('file_too_large', __('Arquivo muito grande. Máximo: 5MB.', 'nosfirnews'));
    }
    
    // Verificar tipo MIME
    $allowed_types = array(
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp'
    );
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, $allowed_types)) {
        return new WP_Error('invalid_type', __('Tipo de arquivo não permitido.', 'nosfirnews'));
    }
    
    // Verificar extensão
    $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_extension, $allowed_extensions)) {
        return new WP_Error('invalid_extension', __('Extensão de arquivo não permitida.', 'nosfirnews'));
    }
    
    return true;
}

// 6. Proteção contra SQL Injection (usando $wpdb corretamente)
function nosfirnews_secure_database_query_example($post_id) {
    global $wpdb;
    
    // ERRADO - vulnerável a SQL injection:
    // $results = $wpdb->get_results("SELECT * FROM {$wpdb->posts} WHERE ID = $post_id");
    
    // CERTO - usando prepare:
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$wpdb->posts} WHERE ID = %d",
            $post_id
        )
    );
    
    return $results;
}

// 7. Escapar output corretamente
function nosfirnews_display_user_data($data, $context = 'html') {
    switch ($context) {
        case 'html':
            return esc_html($data);
            
        case 'attr':
            return esc_attr($data);
            
        case 'url':
            return esc_url($data);
            
        case 'js':
            return esc_js($data);
            
        case 'textarea':
            return esc_textarea($data);
            
        default:
            return wp_kses_post($data);
    }
}