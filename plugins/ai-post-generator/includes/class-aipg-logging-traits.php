<?php
/**
 * Traits de Logging para AI Post Generator
 * 
 * @package AI_Post_Generator
 * @version 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Trait para classes que precisam fazer logging
 */
trait AIPG_Logging {
    /**
     * Instância do logger
     * 
     * @var AIPG_Logger
     */
    protected $logger;

    /**
     * Inicializa logger
     */
    protected function init_logger() {
        $this->logger = AIPG_Logger::get_instance();
    }

    /**
     * Log de erro
     * 
     * @param string $message Mensagem
     * @param array  $context Contexto
     */
    protected function log_error( $message, $context = array() ) {
        if ( ! isset( $this->logger ) ) {
            $this->init_logger();
        }
        $this->logger->error( $message, $context );
    }

    /**
     * Log de aviso
     * 
     * @param string $message Mensagem
     * @param array  $context Contexto
     */
    protected function log_warning( $message, $context = array() ) {
        if ( ! isset( $this->logger ) ) {
            $this->init_logger();
        }
        $this->logger->warning( $message, $context );
    }

    /**
     * Log informativo
     * 
     * @param string $message Mensagem
     * @param array  $context Contexto
     */
    protected function log_info( $message, $context = array() ) {
        if ( ! isset( $this->logger ) ) {
            $this->init_logger();
        }
        $this->logger->info( $message, $context );
    }

    /**
     * Log de debug
     * 
     * @param string $message Mensagem
     * @param array  $context Contexto
     */
    protected function log_debug( $message, $context = array() ) {
        if ( ! isset( $this->logger ) ) {
            $this->init_logger();
        }
        $this->logger->debug( $message, $context );
    }
}