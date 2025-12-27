<?php
/**
 * Logger para AI Post Generator
 * 
 * Sistema centralizado de logging com suporte a múltiplos níveis
 * e contexto detalhado para debugging
 * 
 * @package AI_Post_Generator
 * @version 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AIPG_Logger {
    /**
     * Níveis de log (PSR-3)
     */
    const EMERGENCY = 'emergency';
    const ALERT     = 'alert';
    const CRITICAL  = 'critical';
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const INFO      = 'info';
    const DEBUG     = 'debug';

    /**
     * Arquivo de log
     */
    private $log_file;

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Obtém instância singleton
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Construtor privado
     */
    private function __construct() {
        $this->log_file = $this->get_log_file_path();
    }

    /**
     * Obtém caminho do arquivo de log
     * 
     * @return string Caminho completo do arquivo de log
     */
    private function get_log_file_path() {
        $upload_dir = wp_upload_dir();
        $log_dir    = $upload_dir['basedir'] . '/aipg-logs';

        // Cria diretório se não existir
        if ( ! is_dir( $log_dir ) ) {
            wp_mkdir_p( $log_dir );
        }

        return $log_dir . '/aipg-' . gmdate( 'Y-m-d' ) . '.log';
    }

    /**
     * Log de erro crítico
     * 
     * @param string $message Mensagem de erro
     * @param array  $context Contexto adicional
     */
    public function emergency( $message, $context = array() ) {
        $this->log( self::EMERGENCY, $message, $context );
    }

    /**
     * Log de alerta
     * 
     * @param string $message Mensagem
     * @param array  $context Contexto adicional
     */
    public function alert( $message, $context = array() ) {
        $this->log( self::ALERT, $message, $context );
    }

    /**
     * Log crítico
     * 
     * @param string $message Mensagem
     * @param array  $context Contexto adicional
     */
    public function critical( $message, $context = array() ) {
        $this->log( self::CRITICAL, $message, $context );
    }

    /**
     * Log de erro
     * 
     * @param string $message Mensagem de erro
     * @param array  $context Contexto adicional
     */
    public function error( $message, $context = array() ) {
        $this->log( self::ERROR, $message, $context );
    }

    /**
     * Log de aviso
     * 
     * @param string $message Mensagem
     * @param array  $context Contexto adicional
     */
    public function warning( $message, $context = array() ) {
        $this->log( self::WARNING, $message, $context );
    }

    /**
     * Log informativo
     * 
     * @param string $message Mensagem
     * @param array  $context Contexto adicional
     */
    public function notice( $message, $context = array() ) {
        $this->log( self::NOTICE, $message, $context );
    }

    /**
     * Log de informação
     * 
     * @param string $message Mensagem
     * @param array  $context Contexto adicional
     */
    public function info( $message, $context = array() ) {
        $this->log( self::INFO, $message, $context );
    }

    /**
     * Log de debug
     * 
     * @param string $message Mensagem
     * @param array  $context Contexto adicional
     */
    public function debug( $message, $context = array() ) {
        $this->log( self::DEBUG, $message, $context );
    }

    /**
     * Log genérico
     * 
     * @param string $level   Nível de log
     * @param string $message Mensagem
     * @param array  $context Contexto adicional
     */
    public function log( $level, $message, $context = array() ) {
        if ( ! $this->log_file ) {
            return;
        }

        // Constrói entry de log
        $entry = $this->format_log_entry( $level, $message, $context );

        // Escreve no arquivo
        error_log( $entry . PHP_EOL, 3, $this->log_file );
    }

    /**
     * Formata entrada de log
     * 
     * @param string $level   Nível de log
     * @param string $message Mensagem
     * @param array  $context Contexto adicional
     * @return string Entrada formatada
     */
    private function format_log_entry( $level, $message, $context = array() ) {
        $timestamp = gmdate( 'Y-m-d H:i:s' );
        $level_str = strtoupper( $level );

        // Adiciona informações do stack trace
        $backtrace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 5 );
        $caller     = isset( $backtrace[2] ) ? $backtrace[2] : array();
        
        $file = isset( $caller['file'] ) ? basename( $caller['file'] ) : 'unknown';
        $line = isset( $caller['line'] ) ? $caller['line'] : 0;
        $func = isset( $caller['function'] ) ? $caller['function'] : 'unknown';

        // Constrói contexto formatado
        $context_str = '';
        if ( ! empty( $context ) ) {
            $context_str = ' | ' . wp_json_encode( $context );
        }

        return sprintf(
            '[%s] [%s] %s (%s:%d in %s)%s',
            $timestamp,
            $level_str,
            $message,
            $file,
            $line,
            $func,
            $context_str
        );
    }

    /**
     * Log de requisição API
     * 
     * @param string $provider Nome do provedor
     * @param string $url     URL da requisição
     * @param array  $args    Argumentos da requisição
     */
    public function log_api_request( $provider, $url, $args = array() ) {
        $context = array(
            'provider' => $provider,
            'url'      => $url,
            'method'   => isset( $args['method'] ) ? $args['method'] : 'POST',
        );

        // Não loga dados sensíveis
        if ( isset( $args['headers'] ) ) {
            $context['headers_set'] = count( $args['headers'] );
        }

        $this->debug( "API Request iniciada", $context );
    }

    /**
     * Log de resposta API
     * 
     * @param string $provider      Nome do provedor
     * @param mixed  $response      Resposta da requisição
     * @param array  $request_args  Argumentos originais da requisição
     */
    public function log_api_response( $provider, $response, $request_args = array() ) {
        if ( is_wp_error( $response ) ) {
            $this->error(
                "API Response: Erro de requisição",
                array(
                    'provider' => $provider,
                    'error'    => $response->get_error_code(),
                    'message'  => $response->get_error_message(),
                )
            );
            return;
        }

        $http_code = wp_remote_retrieve_response_code( $response );
        $body      = wp_remote_retrieve_body( $response );

        // Log bem-sucedido
        if ( 200 <= $http_code && $http_code < 300 ) {
            $this->info(
                "API Response: Sucesso",
                array(
                    'provider'  => $provider,
                    'http_code' => $http_code,
                    'body_size' => strlen( $body ),
                )
            );
        } else {
            // Log de erro HTTP
            $this->warning(
                "API Response: Erro HTTP",
                array(
                    'provider'  => $provider,
                    'http_code' => $http_code,
                    'body'      => substr( $body, 0, 500 ), // Primeiros 500 caracteres
                )
            );
        }
    }

    /**
     * Log de geração de conteúdo
     * 
     * @param string $topic   Tópico gerado
     * @param string $provider Provedor de IA usado
     * @param int    $post_id ID do post criado
     * @param array  $data    Dados adicionais
     */
    public function log_content_generation( $topic, $provider, $post_id, $data = array() ) {
        $context = array_merge(
            array(
                'topic'    => $topic,
                'provider' => $provider,
                'post_id'  => $post_id,
                'user_id'  => get_current_user_id(),
            ),
            $data
        );

        $this->info( "Conteúdo gerado com sucesso", $context );
    }

    /**
     * Log de erro de geração
     * 
     * @param string $topic    Tópico
     * @param string $provider Provedor
     * @param mixed  $error    Erro ocorrido
     */
    public function log_generation_error( $topic, $provider, $error ) {
        $error_message = is_wp_error( $error )
            ? $error->get_error_message()
            : (string) $error;

        $context = array(
            'topic'    => $topic,
            'provider' => $provider,
            'error'    => $error_message,
            'user_id'  => get_current_user_id(),
        );

        $this->error( "Erro ao gerar conteúdo", $context );
    }

    /**
     * Log de geração de imagem
     * 
     * @param string $topic    Tópico
     * @param string $provider Provedor
     * @param int    $image_id ID da imagem criada
     */
    public function log_image_generation( $topic, $provider, $image_id ) {
        $this->info(
            "Imagem gerada com sucesso",
            array(
                'topic'    => $topic,
                'provider' => $provider,
                'image_id' => $image_id,
            )
        );
    }

    /**
     * Retorna logs recentes
     * 
     * @param int $lines Número de linhas a retornar
     * @return array Array de linhas de log
     */
    public function get_recent_logs( $lines = 50 ) {
        if ( ! file_exists( $this->log_file ) ) {
            return array();
        }

        $file_content = file( $this->log_file );
        if ( false === $file_content ) {
            return array();
        }

        // Retorna as últimas N linhas
        return array_slice( $file_content, -$lines );
    }

    /**
     * Limpa logs antigos (mais de 30 dias)
     */
    public function cleanup_old_logs() {
        $upload_dir = wp_upload_dir();
        $log_dir    = $upload_dir['basedir'] . '/aipg-logs';

        if ( ! is_dir( $log_dir ) ) {
            return;
        }

        $files = scandir( $log_dir );
        if ( false === $files ) {
            return;
        }

        $thirty_days_ago = time() - ( 30 * DAY_IN_SECONDS );

        foreach ( $files as $file ) {
            if ( '.' === $file || '..' === $file ) {
                continue;
            }

            $file_path = $log_dir . '/' . $file;
            if ( is_file( $file_path ) && filemtime( $file_path ) < $thirty_days_ago ) {
                wp_delete_file( $file_path );
            }
        }
    }
}