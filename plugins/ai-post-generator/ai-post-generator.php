<?php
/**
 * Plugin Name: AI Post Generator Pro
 * Plugin URI: https://github.com/davidcreator/ai-post-generator
 * Description: Gera posts e imagens automaticamente usando APIs de IA
 * Version: 2.1.0
 * Author: David Creator
 * Author URI: https://davidcreator.com
 * License: GPL v2 or later
 * Text Domain: ai-post-generator
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

// Constantes do plugin
define('AIPG_VERSION', '2.1.0');
define('AIPG_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AIPG_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AIPG_INCLUDES_DIR', AIPG_PLUGIN_DIR . 'includes/');

// Autoloader simples
spl_autoload_register(function ($class) {
    $prefix = 'AIPG_';
    $base_dir = AIPG_INCLUDES_DIR;
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . 'class-' . strtolower(str_replace('_', '-', $relative_class)) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Carrega classes principais (Logger primeiro!)
require_once AIPG_INCLUDES_DIR . 'class-aipg-logger.php';
require_once AIPG_INCLUDES_DIR . 'class-aipg-logging-traits.php';
require_once AIPG_INCLUDES_DIR . 'class-ai-post-generator.php';
require_once AIPG_INCLUDES_DIR . 'class-content-generator.php';
require_once AIPG_INCLUDES_DIR . 'class-image-generator.php';

/**
 * Função helper para obter instância do logger
 * 
 * @return AIPG_Logger Instância singleton do logger
 */
function aipg_logger() {
    return AIPG_Logger::get_instance();
}

/**
 * Inicializa o plugin
 */
function aipg_init() {
    // Log de inicialização
    aipg_logger()->info('Plugin AI Post Generator inicializado', array(
        'version' => AIPG_VERSION,
        'php_version' => phpversion(),
        'wp_version' => get_bloginfo('version'),
    ));
    
    return AI_Post_Generator::get_instance();
}
add_action('plugins_loaded', 'aipg_init');

/**
 * Ativação do plugin
 */
register_activation_hook(__FILE__, 'aipg_activate');
function aipg_activate() {
    global $wpdb;
    
    $table = $wpdb->prefix . 'aipg_scheduled';
    $charset = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        topic varchar(255) NOT NULL,
        config text NOT NULL,
        schedule_date datetime NOT NULL,
        status varchar(20) DEFAULT 'pending',
        post_id bigint(20) DEFAULT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // Opções padrão
    $defaults = array(
        'aipg_post_status' => 'draft',
        'aipg_default_author' => get_current_user_id(),
        'aipg_auto_tags' => '0',
        'aipg_seo_optimization' => '0',
        'aipg_auto_featured_image' => '0',
        'aipg_add_internal_links' => '0',
        'aipg_image_width' => '1920',
        'aipg_image_height' => '1080',
        'aipg_image_provider' => 'pollinations',
        'aipg_api_provider' => 'groq',
        'aipg_groq_model' => 'llama-3.3-70b-versatile'
    );
    
    foreach ($defaults as $key => $value) {
        add_option($key, $value);
    }
    
    // Log de ativação
    aipg_logger()->notice('Plugin ativado', array(
        'user_id' => get_current_user_id(),
        'timestamp' => current_time('mysql'),
    ));
}

/**
 * Desativação do plugin
 */
register_deactivation_hook(__FILE__, 'aipg_deactivate');
function aipg_deactivate() {
    wp_clear_scheduled_hook('aipg_scheduled_post');
    
    // Log de desativação
    aipg_logger()->notice('Plugin desativado', array(
        'user_id' => get_current_user_id(),
        'timestamp' => current_time('mysql'),
    ));
}

/**
 * Hook de shutdown para limpeza e logging
 */
add_action('shutdown', function() {
    // Limpa logs antigos uma vez por semana
    $last_cleanup = get_transient('aipg_log_cleanup_done');
    if (false === $last_cleanup) {
        aipg_logger()->cleanup_old_logs();
        set_transient('aipg_log_cleanup_done', 1, WEEK_IN_SECONDS);
    }
});

/**
 * Hook para tratamento de erros não capturados
 */
register_shutdown_function(function() {
    $error = error_get_last();
    
    if ($error && in_array($error['type'], array(E_ERROR, E_PARSE, E_COMPILE_ERROR))) {
        aipg_logger()->emergency(
            'Erro fatal detectado: ' . $error['message'],
            array(
                'file' => $error['file'],
                'line' => $error['line'],
                'type' => $error['type'],
            )
        );
    }
});