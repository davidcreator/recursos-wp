<?php
/**
 * Handler de AJAX com Logging
 * 
 * @package AI_Post_Generator
 * @version 2.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class AIPG_AJAX_Handler {
    use AIPG_Logging;

    /**
     * Construtor
     */
    public function __construct() {
        $this->init_logger();
        add_action('wp_ajax_aipg_clear_logs', array($this, 'ajax_clear_logs'));
    }

    /**
     * AJAX: Limpar logs
     */
    public function ajax_clear_logs() {
        check_ajax_referer('aipg_logs_nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array(
                'message' => __('Permissão negada', 'ai-post-generator')
            ));
        }

        try {
            $this->log_info('Iniciando limpeza manual de logs', array(
                'user_id' => get_current_user_id(),
            ));

            aipg_logger()->cleanup_old_logs();

            $this->log_info('Limpeza de logs concluída', array(
                'user_id' => get_current_user_id(),
            ));

            wp_send_json_success(array(
                'message' => __('Logs limpos com sucesso', 'ai-post-generator')
            ));
        } catch (Exception $e) {
            $this->log_error('Erro ao limpar logs: ' . $e->getMessage(), array(
                'exception' => $e->getCode(),
            ));

            wp_send_json_error(array(
                'message' => __('Erro ao limpar logs', 'ai-post-generator')
            ));
        }
    }
}

// Inicializa handler
if (is_admin()) {
    new AIPG_AJAX_Handler();
}