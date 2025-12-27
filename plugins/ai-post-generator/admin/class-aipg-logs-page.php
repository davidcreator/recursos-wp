<?php
/**
 * Página de Visualização de Logs
 * 
 * @package AI_Post_Generator
 * @version 2.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class AIPG_Logs_Page {
    use AIPG_Logging;

    /**
     * Construtor
     */
    public function __construct() {
        $this->init_logger();
        add_action('admin_menu', array($this, 'add_logs_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    /**
     * Adiciona menu de logs ao admin
     */
    public function add_logs_menu() {
        add_submenu_page(
            'ai-post-generator',
            __('Logs', 'ai-post-generator'),
            __('Logs', 'ai-post-generator'),
            'manage_options',
            'ai-post-generator-logs',
            array($this, 'render_logs_page')
        );
    }

    /**
     * Enfileira scripts e estilos
     */
    public function enqueue_assets($hook) {
        if ('ai-post-generator_page_ai-post-generator-logs' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'aipg-logs-style',
            AIPG_PLUGIN_URL . 'assets/logs-style.css',
            array(),
            AIPG_VERSION
        );

        wp_enqueue_script(
            'aipg-logs-script',
            AIPG_PLUGIN_URL . 'assets/logs-script.js',
            array('jquery'),
            AIPG_VERSION,
            true
        );
    }

    /**
     * Renderiza página de logs
     */
    public function render_logs_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Permissão negada', 'ai-post-generator'));
        }

        $logger = aipg_logger();
        $logs = $logger->get_recent_logs(100);

        ?>
        <div class="wrap aipg-logs-wrap">
            <h1><?php _e('Logs - AI Post Generator', 'ai-post-generator'); ?></h1>

            <div class="aipg-logs-controls">
                <button id="aipg-logs-refresh" class="button button-primary">
                    <?php _e('🔄 Atualizar', 'ai-post-generator'); ?>
                </button>
                <button id="aipg-logs-clear" class="button button-secondary">
                    <?php _e('🗑️ Limpar Logs', 'ai-post-generator'); ?>
                </button>
                <button id="aipg-logs-download" class="button button-secondary">
                    <?php _e('⬇️ Baixar Logs', 'ai-post-generator'); ?>
                </button>
            </div>

            <div class="aipg-logs-info">
                <p>
                    <strong><?php _e('Arquivo Atual:', 'ai-post-generator'); ?></strong>
                    <code><?php echo esc_html($this->get_log_file_path()); ?></code>
                </p>
                <p>
                    <strong><?php _e('Total de Entradas:', 'ai-post-generator'); ?></strong>
                    <code><?php echo count($logs); ?></code>
                </p>
            </div>

            <div class="aipg-logs-filters">
                <input type="text" id="aipg-logs-search" placeholder="<?php _e('Filtrar logs...', 'ai-post-generator'); ?>" />
                
                <select id="aipg-logs-level">
                    <option value=""><?php _e('Todos os Níveis', 'ai-post-generator'); ?></option>
                    <option value="ERROR"><?php _e('🔴 Erro', 'ai-post-generator'); ?></option>
                    <option value="WARNING"><?php _e('🟡 Aviso', 'ai-post-generator'); ?></option>
                    <option value="INFO"><?php _e('🔵 Info', 'ai-post-generator'); ?></option>
                    <option value="DEBUG"><?php _e('⚪ Debug', 'ai-post-generator'); ?></option>
                </select>
            </div>

            <div class="aipg-logs-container">
                <pre class="aipg-logs-viewer" id="aipg-logs-viewer"><?php
                    if (empty($logs)) {
                        echo __('Nenhum log disponível', 'ai-post-generator');
                    } else {
                        foreach ($logs as $log) {
                            echo esc_html($log);
                        }
                    }
                ?></pre>
            </div>
        </div>
        <?php
    }

    /**
     * Obtém caminho do arquivo de log
     */
    private function get_log_file_path() {
        $upload_dir = wp_upload_dir();
        return $upload_dir['basedir'] . '/aipg-logs/aipg-' . gmdate('Y-m-d') . '.log';
    }
}

// Inicializa página de logs
if (is_admin()) {
    new AIPG_Logs_Page();
}