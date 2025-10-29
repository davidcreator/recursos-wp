# WP Dracaunos Security - Exemplos de Código

## 📚 Uso da API do Plugin

### Verificar se usuário tem 2FA ativo

```php
$db = new \WPSP\Core\Database();
$user_id = get_current_user_id();

if ($db->user_has_2fa_enabled($user_id)) {
    echo 'Usuário tem 2FA ativo';
} else {
    echo 'Usuário não tem 2FA ativo';
}
```

### Forçar 2FA para um usuário

```php
$db = new \WPSP\Core\Database();
$user_id = 123; // ID do usuário

// Salvar configuração de email 2FA
$db->save_2fa_config($user_id, 'email');

// Ativar 2FA
$db->toggle_2fa($user_id, 'email', true);
```

### Gerar códigos de backup programaticamente

```php
$recovery_codes = new \WPSP\Security\TwoFactor\RecoveryCodes();
$user_id = get_current_user_id();

// Gerar 10 códigos
$codes = $recovery_codes->generate_codes($user_id);

// Exibir códigos
foreach ($codes as $index => $code) {
    echo ($index + 1) . '. ' . $code . '<br>';
}
```

### Adicionar log de segurança customizado

```php
$db = new \WPSP\Core\Database();

$db->add_security_log(
    'custom_action',           // Ação
    'Detalhes do evento',      // Detalhes
    get_current_user_id()      // User ID (opcional)
);
```

### Bloquear IP temporariamente

```php
$db = new \WPSP\Core\Database();

// Bloquear por 60 minutos
$db->block_ip(
    '192.168.1.100',           // IP
    'Múltiplas tentativas',    // Razão
    60                         // Duração em minutos
);
```

### Verificar se IP está bloqueado

```php
$db = new \WPSP\Core\Database();

if ($db->is_ip_blocked('192.168.1.100')) {
    wp_die('Seu IP está bloqueado');
}
```

## 🎨 Customizações de Template

### Customizar página de 2FA

```php
// No seu tema, crie: wp-content/themes/seu-tema/wpsp/two-factor-form.php

<div class="custom-2fa-form">
    <h2>Verificação de Segurança</h2>
    <p>Digite o código de verificação enviado para seu email.</p>
    
    <form method="post">
        <input type="text" name="wpsp_2fa_code" placeholder="000000" maxlength="6" required>
        <button type="submit">Verificar</button>
    </form>
</div>
```

### Customizar email de 2FA

```php
// Adicione no functions.php do seu tema

add_filter('wpsp_2fa_email_subject', function($subject, $user) {
    return '[' . get_bloginfo('name') . '] Seu Código de Verificação';
}, 10, 2);

add_filter('wpsp_2fa_email_message', function($message, $code, $user) {
    $custom_message = "Olá " . $user->display_name . ",\n\n";
    $custom_message .= "Seu código de verificação é: " . $code . "\n\n";
    $custom_message .= "Este código expira em 5 minutos.\n\n";
    $custom_message .= "Equipe " . get_bloginfo('name');
    
    return $custom_message;
}, 10, 3);
```

## 🔧 Hooks e Filters

### Excluir páginas da minificação

```php
add_filter('wpsp_minify_excluded_pages', function($pages) {
    $pages[] = 'minha-pagina-especial';
    $pages[] = 'checkout';
    return $pages;
});
```

### Modificar tempo de expiração do código 2FA

```php
add_filter('wpsp_2fa_code_expiry', function($seconds) {
    return 600; // 10 minutos ao invés de 5
});
```

### Adicionar método 2FA customizado

```php
add_filter('wpsp_2fa_methods', function($methods) {
    $methods['sms'] = 'SMS Verification';
    return $methods;
});

// Implementar verificação
add_action('wpsp_verify_2fa_sms', function($user_id, $code) {
    // Sua lógica de verificação SMS aqui
    return verify_sms_code($user_id, $code);
}, 10, 2);
```

### Modificar headers de segurança

```php
add_filter('wpsp_security_headers', function($headers) {
    // Adicionar header customizado
    $headers['X-Custom-Header'] = 'custom-value';
    
    // Modificar header existente
    $headers['X-Frame-Options'] = 'DENY';
    
    return $headers;
});
```

### Callback após login com 2FA

```php
add_action('wpsp_2fa_verified', function($user_id, $method) {
    // Executar ação após verificação 2FA bem-sucedida
    update_user_meta($user_id, 'last_2fa_login', current_time('mysql'));
    
    // Log customizado
    error_log("User $user_id verified with $method");
}, 10, 2);
```

### Notificar admin sobre bloqueios

```php
add_action('wpsp_ip_blocked', function($ip_address, $reason) {
    $admin_email = get_option('admin_email');
    $subject = 'IP Bloqueado no Site';
    $message = "IP: $ip_address\nRazão: $reason\nData: " . current_time('mysql');
    
    wp_mail($admin_email, $subject, $message);
}, 10, 2);
```

## 🛠️ Funções Úteis

### Forçar 2FA para determinado role

```php
// Adicione no functions.php

add_action('user_register', function($user_id) {
    $user = get_userdata($user_id);
    
    // Forçar 2FA para administradores
    if (in_array('administrator', $user->roles)) {
        $db = new \WPSP\Core\Database();
        $db->save_2fa_config($user_id, 'email');
        $db->toggle_2fa($user_id, 'email', true);
        
        // Notificar usuário
        $email = new \WPSP\Security\TwoFactor\Email();
        $email->send_code($user_id);
    }
});
```

### Limpar logs automaticamente

```php
// Adicione no functions.php

// Limpar logs com mais de 30 dias
add_action('wp_scheduled_delete', function() {
    $db = new \WPSP\Core\Database();
    $db->clean_old_logs(30);
});
```

### Widget de status de segurança

```php
// Criar widget para dashboard

add_action('wp_dashboard_setup', function() {
    wp_add_dashboard_widget(
        'wpsp_security_widget',
        'Status de Segurança',
        'wpsp_security_widget_content'
    );
});

function wpsp_security_widget_content() {
    $db = new \WPSP\Core\Database();
    $stats = $db->get_statistics();
    
    echo '<ul>';
    echo '<li>Usuários com 2FA: ' . $stats['users_with_2fa'] . '</li>';
    echo '<li>IPs Bloqueados: ' . $stats['blocked_ips'] . '</li>';
    echo '<li>Logs Hoje: ' . $stats['logs_today'] . '</li>';
    echo '</ul>';
}
```

### Shortcode personalizado para stats

```php
// Adicione no functions.php

add_shortcode('wpsp_stats', function($atts) {
    $atts = shortcode_atts([
        'type' => 'users_with_2fa'
    ], $atts);
    
    $db = new \WPSP\Core\Database();
    $stats = $db->get_statistics();
    
    return isset($stats[$atts['type']]) ? $stats[$atts['type']] : 0;
});

// Uso: [wpsp_stats type="users_with_2fa"]
```

### Verificação de segurança customizada

```php
function check_custom_security() {
    $db = new \WPSP\Core\Database();
    
    // Verificar múltiplas tentativas de login
    $recent_failed = $db->get_security_logs([
        'action' => '2fa_failed',
        'start_date' => date('Y-m-d H:i:s', strtotime('-1 hour')),
        'limit' => 5
    ]);
    
    if (count($recent_failed) >= 5) {
        // Bloquear IP
        $ip = \WPSP\Utils\Helpers::get_client_ip();
        $db->block_ip($ip, 'Múltiplas tentativas 2FA falhadas', 60);
        
        wp_die('Muitas tentativas. Tente novamente em 1 hora.');
    }
}

add_action('wp_login_failed', 'check_custom_security');
```

## 📊 Relatórios Customizados

### Relatório de uso de 2FA

```php
function wpsp_2fa_usage_report() {
    global $wpdb;
    
    $total_users = count_users()['total_users'];
    
    $users_with_2fa = $wpdb->get_var(
        "SELECT COUNT(DISTINCT user_id) 
        FROM {$wpdb->prefix}wpsp_two_factor 
        WHERE enabled = 1"
    );
    
    $percentage = ($users_with_2fa / $total_users) * 100;
    
    echo "<h3>Relatório de 2FA</h3>";
    echo "<p>Total de usuários: $total_users</p>";
    echo "<p>Usuários com 2FA: $users_with_2fa</p>";
    echo "<p>Porcentagem: " . round($percentage, 2) . "%</p>";
}
```

### Exportar logs de segurança

```php
function wpsp_export_security_logs() {
    $db = new \WPSP\Core\Database();
    $logs = $db->get_security_logs(['limit' => 1000]);
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="security-logs-' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Headers
    fputcsv($output, ['Date', 'Action', 'User', 'IP', 'Details']);
    
    // Data
    foreach ($logs as $log) {
        $user = $log->user_id ? get_userdata($log->user_id)->user_login : '-';
        fputcsv($output, [
            $log->created_at,
            $log->action,
            $user,
            $log->ip_address,
            $log->details
        ]);
    }
    
    fclose($output);
    exit;
}

// Adicionar botão de exportação
add_action('admin_menu', function() {
    add_submenu_page(
        null, // Página oculta
        'Export Logs',
        'Export Logs',
        'manage_options',
        'wpsp-export-logs',
        'wpsp_export_security_logs'
    );
});
```

## 🎯 Integrações

### Integração com WooCommerce

```php
// Forçar 2FA no checkout

add_action('woocommerce_before_checkout_form', function() {
    if (!is_user_logged_in()) {
        return;
    }
    
    $db = new \WPSP\Core\Database();
    $user_id = get_current_user_id();
    
    if (!$db->user_has_2fa_enabled($user_id)) {
        wc_add_notice('Por segurança, recomendamos ativar 2FA antes de fazer pedidos.', 'notice');
    }
});
```

### Integração com Contact Form 7

```php
// Adicionar captcha no CF7

add_filter('wpcf7_form_elements', function($form) {
    if (get_option('wpsp_captcha_enabled')) {
        $captcha = '<div class="g-recaptcha" data-sitekey="' . 
                   get_option('wpsp_captcha_site_key') . '"></div>';
        $form = str_replace('[submit]', $captcha . '[submit]', $form);
    }
    return $form;
});
```

### Webhook após eventos de segurança

```php
add_action('wpsp_security_event', function($event_type, $data) {
    $webhook_url = 'https://your-webhook-url.com/endpoint';
    
    $payload = [
        'event' => $event_type,
        'site' => get_bloginfo('url'),
        'data' => $data,
        'timestamp' => current_time('mysql')
    ];
    
    wp_remote_post($webhook_url, [
        'body' => json_encode($payload),
        'headers' => ['Content-Type' => 'application/json']
    ]);
}, 10, 2);
```

## 📱 API REST Customizada

```php
// Adicionar endpoint REST personalizado

add_action('rest_api_init', function() {
    register_rest_route('wpsp/v1', '/security-status', [
        'methods' => 'GET',
        'callback' => 'wpsp_get_security_status',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        }
    ]);
});

function wpsp_get_security_status() {
    $db = new \WPSP\Core\Database();
    return rest_ensure_response($db->get_statistics());
}

// Uso: GET https://seusite.com/wp-json/wpsp/v1/security-status
```

---

**Dica:** Sempre teste snippets em ambiente de desenvolvimento antes de usar em produção!

Para mais exemplos e documentação, visite: https://davidalmeida.xyz/docs/wp-dracaunos-security