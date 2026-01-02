<?php
namespace WPSP\Security;

if (!defined('ABSPATH')) exit;

use WPSP\Core\Database;

class XMLRPCManager {
    
    private $db;
    private $block_xmlrpc;
    private $custom_xmlrpc_path;
    
    public function __construct() {
        $this->db = new Database();
        $this->block_xmlrpc = get_option('wpsp_block_xmlrpc', 0);
        $this->custom_xmlrpc_path = get_option('wpsp_custom_xmlrpc_path', '');
        
        $this->init_hooks();
    }
    
    private function init_hooks() {
        // Bloquear XML-RPC completamente
        if ($this->block_xmlrpc) {
            add_filter('xmlrpc_enabled', '__return_false');
            add_action('init', [$this, 'block_xmlrpc_access']);
        }
        
        // Customizar caminho XML-RPC
        if (!empty($this->custom_xmlrpc_path) && !$this->block_xmlrpc) {
            add_action('init', [$this, 'custom_xmlrpc_redirect']);
            add_filter('xmlrpc_methods', [$this, 'filter_xmlrpc_methods']);
        }
        
        // Remover X-Pingback header
        add_filter('wp_headers', [$this, 'remove_x_pingback_header']);
        add_filter('pings_open', [$this, 'disable_pingbacks'], 10, 2);
        
        // Log de tentativas XML-RPC
        add_action('xmlrpc_call', [$this, 'log_xmlrpc_call']);
    }
    
    /**
     * Bloquear acesso ao XML-RPC
     */
    public function block_xmlrpc_access() {
        if (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) {
            $this->db->add_security_log('xmlrpc_blocked', 'XML-RPC access attempt blocked');
            
            header('HTTP/1.1 403 Forbidden');
            die(__('XML-RPC services are disabled on this site.', 'wp-security-pro'));
        }
    }
    
    /**
     * Customizar redirecionamento XML-RPC
     */
    public function custom_xmlrpc_redirect() {
        $request_uri = $_SERVER['REQUEST_URI'];
        $custom_path = '/' . trim($this->custom_xmlrpc_path, '/');
        
        // Se acessar o caminho customizado, processar como xmlrpc.php
        if (strpos($request_uri, $custom_path) !== false) {
            define('XMLRPC_REQUEST', true);
            require_once ABSPATH . 'xmlrpc.php';
            exit;
        }
        
        // Bloquear acesso direto ao xmlrpc.php
        if (strpos($request_uri, 'xmlrpc.php') !== false && strpos($request_uri, $custom_path) === false) {
            $this->db->add_security_log('xmlrpc_direct_access', 'Direct XML-RPC access blocked');
            
            header('HTTP/1.1 403 Forbidden');
            die(__('Access Denied', 'wp-security-pro'));
        }
    }
    
    /**
     * Filtrar métodos XML-RPC permitidos
     */
    public function filter_xmlrpc_methods($methods) {
        // Remover métodos perigosos
        $dangerous_methods = [
            'pingback.ping',
            'pingback.extensions.getPingbacks',
            'system.multicall',
            'system.listMethods',
            'system.getCapabilities'
        ];
        
        foreach ($dangerous_methods as $method) {
            unset($methods[$method]);
        }
        
        return $methods;
    }
    
    /**
     * Remover header X-Pingback
     */
    public function remove_x_pingback_header($headers) {
        unset($headers['X-Pingback']);
        return $headers;
    }
    
    /**
     * Desabilitar pingbacks
     */
    public function disable_pingbacks($open, $post_id) {
        return false;
    }
    
    /**
     * Log de chamadas XML-RPC
     */
    public function log_xmlrpc_call($method) {
        $this->db->add_security_log('xmlrpc_call', "XML-RPC method called: {$method}");
    }
}
