<?php
// Aumentar o limite de tempo de execução do PHP
ini_set('max_execution_time', 300); // Aumenta para 300 segundos (5 minutos)
set_time_limit(300); // Método alternativo para aumentar o limite de tempo

// Adicionar ao wp-config.php
$wp_config_path = realpath(dirname(__FILE__) . '/../../../../wp-config.php');
if (file_exists($wp_config_path)) {
    $config_content = file_get_contents($wp_config_path);
    
    // Verificar se a configuração já existe
    if (strpos($config_content, 'WP_MEMORY_LIMIT') === false) {
        // Adicionar antes de "That's all, stop editing!"
        $insertion_point = strpos($config_content, "/* That's all, stop editing!");
        if ($insertion_point !== false) {
            $new_content = substr($config_content, 0, $insertion_point);
            $new_content .= "define('WP_MEMORY_LIMIT', '256M');\n";
            $new_content .= "define('WP_MAX_MEMORY_LIMIT', '512M');\n";
            $new_content .= "define('CONCATENATE_SCRIPTS', false);\n";
            $new_content .= substr($config_content, $insertion_point);
            
            // Fazer backup do arquivo original
            copy($wp_config_path, $wp_config_path . '.bak');
            
            // Escrever o novo conteúdo
            file_put_contents($wp_config_path, $new_content);
            echo "<p>Configurações adicionadas ao wp-config.php</p>";
        }
    } else {
        echo "<p>Configurações já existem no wp-config.php</p>";
    }
}

// Desativar temporariamente o plugin problemático
$plugin_file = 'woocommerce-paypal-payments/woocommerce-paypal-payments.php';
require_once(dirname(__FILE__) . '/../../../../wp-admin/includes/plugin.php');
if (is_plugin_active($plugin_file)) {
    deactivate_plugins($plugin_file);
    echo "<p>Plugin WooCommerce PayPal Payments desativado temporariamente.</p>";
    echo "<p>Você pode reativá-lo após resolver o problema de tempo de execução.</p>";
}

echo "<p>Limite de tempo de execução aumentado para 300 segundos.</p>";
echo "<p><a href='../../../../wp-admin/'>Voltar para o painel do WordPress</a></p>";
?>