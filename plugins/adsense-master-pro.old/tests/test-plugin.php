<?php
/**
 * Testes para AdSense Master Pro
 * 
 * @package AdSenseMasterPro
 * @version 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class AMP_Plugin_Tests {
    
    private $results = array();
    private $plugin_instance;
    
    public function __construct() {
        $this->plugin_instance = AdSenseMasterPro::get_instance();
    }
    
    public function run_all_tests() {
        $this->results = array();
        
        echo "<h2>Executando Testes do AdSense Master Pro</h2>\n";
        
        // Testes básicos
        $this->test_plugin_activation();
        $this->test_database_tables();
        $this->test_options_creation();
        
        // Testes de funcionalidade
        $this->test_ad_creation();
        $this->test_ad_display();
        $this->test_shortcodes();
        
        // Testes de analytics
        $this->test_analytics_tracking();
        $this->test_ab_testing();
        
        // Testes de otimização
        $this->test_performance_optimization();
        $this->test_cache_system();
        
        // Testes de AMP
        $this->test_amp_support();
        
        // Testes de segurança
        $this->test_security_features();
        
        // Testes de GDPR
        $this->test_gdpr_compliance();
        
        $this->display_results();
        
        return $this->results;
    }
    
    private function test_plugin_activation() {
        $this->log_test("Testando ativação do plugin");
        
        try {
            // Verificar se as constantes foram definidas
            $this->assert(defined('AMP_PLUGIN_VERSION'), "Constante AMP_PLUGIN_VERSION definida");
            $this->assert(defined('AMP_PLUGIN_PATH'), "Constante AMP_PLUGIN_PATH definida");
            $this->assert(defined('AMP_PLUGIN_URL'), "Constante AMP_PLUGIN_URL definida");
            
            // Verificar se a classe principal existe
            $this->assert(class_exists('AdSenseMasterPro'), "Classe AdSenseMasterPro existe");
            
            // Verificar singleton
            $instance1 = AdSenseMasterPro::get_instance();
            $instance2 = AdSenseMasterPro::get_instance();
            $this->assert($instance1 === $instance2, "Padrão Singleton funcionando");
            
            $this->log_success("Ativação do plugin");
            
        } catch (Exception $e) {
            $this->log_error("Ativação do plugin", $e->getMessage());
        }
    }
    
    private function test_database_tables() {
        $this->log_test("Testando criação de tabelas do banco de dados");
        
        global $wpdb;
        
        try {
            $tables = array(
                $wpdb->prefix . 'amp_ads',
                $wpdb->prefix . 'amp_analytics',
                $wpdb->prefix . 'amp_ab_tests',
                $wpdb->prefix . 'amp_cache'
            );
            
            foreach ($tables as $table) {
                $exists = $wpdb->get_var("SHOW TABLES LIKE '$table'") == $table;
                $this->assert($exists, "Tabela $table existe");
            }
            
            $this->log_success("Criação de tabelas");
            
        } catch (Exception $e) {
            $this->log_error("Criação de tabelas", $e->getMessage());
        }
    }
    
    private function test_options_creation() {
        $this->log_test("Testando criação de opções");
        
        try {
            $options = get_option('amp_options');
            $this->assert(is_array($options), "Opções do plugin criadas");
            $this->assert(isset($options['enable_adsense']), "Opção enable_adsense existe");
            
            $this->log_success("Criação de opções");
            
        } catch (Exception $e) {
            $this->log_error("Criação de opções", $e->getMessage());
        }
    }
    
    private function test_ad_creation() {
        $this->log_test("Testando criação de anúncios");
        
        global $wpdb;
        
        try {
            // Criar anúncio de teste
            $test_ad_data = array(
                'name' => 'Teste Anúncio',
                'code' => '<div>Código de teste</div>',
                'position' => 'content_top',
                'status' => 'active',
                'ad_type' => 'display',
                'device_targeting' => 'all',
                'created_at' => current_time('mysql')
            );
            
            $result = $wpdb->insert($wpdb->prefix . 'amp_ads', $test_ad_data);
            $this->assert($result !== false, "Anúncio inserido no banco de dados");
            
            $ad_id = $wpdb->insert_id;
            $this->assert($ad_id > 0, "ID do anúncio válido");
            
            // Verificar se o anúncio foi salvo corretamente
            $saved_ad = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}amp_ads WHERE id = %d",
                $ad_id
            ));
            
            $this->assert($saved_ad !== null, "Anúncio recuperado do banco");
            $this->assert($saved_ad->name === 'Teste Anúncio', "Nome do anúncio correto");
            
            // Limpar dados de teste
            $wpdb->delete($wpdb->prefix . 'amp_ads', array('id' => $ad_id));
            
            $this->log_success("Criação de anúncios");
            
        } catch (Exception $e) {
            $this->log_error("Criação de anúncios", $e->getMessage());
        }
    }
    
    private function test_ad_display() {
        $this->log_test("Testando exibição de anúncios");
        
        try {
            // Testar método de renderização
            $test_ad = (object) array(
                'id' => 999,
                'name' => 'Teste Display',
                'code' => '<div class="test-ad">Anúncio de Teste</div>',
                'position' => 'content_top',
                'status' => 'active'
            );
            
            $rendered = $this->plugin_instance->render_ad_with_tracking($test_ad);
            $this->assert(!empty($rendered), "Anúncio renderizado com sucesso");
            $this->assert(strpos($rendered, 'test-ad') !== false, "Código do anúncio presente");
            
            $this->log_success("Exibição de anúncios");
            
        } catch (Exception $e) {
            $this->log_error("Exibição de anúncios", $e->getMessage());
        }
    }
    
    private function test_shortcodes() {
        $this->log_test("Testando shortcodes");
        
        try {
            // Verificar se os shortcodes estão registrados
            $this->assert(shortcode_exists('amp_ad'), "Shortcode amp_ad registrado");
            $this->assert(shortcode_exists('adsense_ad'), "Shortcode adsense_ad registrado");
            $this->assert(shortcode_exists('amp_analytics'), "Shortcode amp_analytics registrado");
            $this->assert(shortcode_exists('amp_ab_test'), "Shortcode amp_ab_test registrado");
            
            $this->log_success("Shortcodes");
            
        } catch (Exception $e) {
            $this->log_error("Shortcodes", $e->getMessage());
        }
    }
    
    private function test_analytics_tracking() {
        $this->log_test("Testando sistema de analytics");
        
        global $wpdb;
        
        try {
            // Testar rastreamento de impressão
            $this->plugin_instance->track_ad_impression(999, 'http://test.com');
            
            // Verificar se foi salvo no banco
            $analytics = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}amp_analytics WHERE ad_id = %d ORDER BY created_at DESC LIMIT 1",
                999
            ));
            
            $this->assert($analytics !== null, "Impressão rastreada no banco");
            $this->assert($analytics->event_type === 'impression', "Tipo de evento correto");
            
            // Limpar dados de teste
            $wpdb->delete($wpdb->prefix . 'amp_analytics', array('ad_id' => 999));
            
            $this->log_success("Sistema de analytics");
            
        } catch (Exception $e) {
            $this->log_error("Sistema de analytics", $e->getMessage());
        }
    }
    
    private function test_ab_testing() {
        $this->log_test("Testando sistema de A/B testing");
        
        global $wpdb;
        
        try {
            // Criar teste A/B
            $test_data = array(
                'name' => 'Teste A/B',
                'ad_a_id' => 1,
                'ad_b_id' => 2,
                'traffic_split' => 50,
                'status' => 'active',
                'created_at' => current_time('mysql')
            );
            
            $result = $wpdb->insert($wpdb->prefix . 'amp_ab_tests', $test_data);
            $this->assert($result !== false, "Teste A/B criado");
            
            $test_id = $wpdb->insert_id;
            
            // Testar seleção de anúncio
            $selected_ad = $this->plugin_instance->get_ab_test_ad($test_id);
            $this->assert(in_array($selected_ad, array(1, 2)), "Anúncio selecionado corretamente");
            
            // Limpar dados de teste
            $wpdb->delete($wpdb->prefix . 'amp_ab_tests', array('id' => $test_id));
            
            $this->log_success("Sistema de A/B testing");
            
        } catch (Exception $e) {
            $this->log_error("Sistema de A/B testing", $e->getMessage());
        }
    }
    
    private function test_performance_optimization() {
        $this->log_test("Testando otimização de performance");
        
        try {
            // Testar cálculo de score de performance
            $score = $this->plugin_instance->calculate_performance_score(1);
            $this->assert(is_numeric($score), "Score de performance calculado");
            $this->assert($score >= 0 && $score <= 100, "Score dentro do range válido");
            
            $this->log_success("Otimização de performance");
            
        } catch (Exception $e) {
            $this->log_error("Otimização de performance", $e->getMessage());
        }
    }
    
    private function test_cache_system() {
        $this->log_test("Testando sistema de cache");
        
        try {
            // Testar cache
            $cache_key = 'test_cache_key';
            $cache_value = 'test_cache_value';
            
            $this->plugin_instance->set_cache($cache_key, $cache_value, 3600);
            $retrieved = $this->plugin_instance->get_cache($cache_key);
            
            $this->assert($retrieved === $cache_value, "Cache funcionando corretamente");
            
            // Limpar cache de teste
            $this->plugin_instance->clear_cache($cache_key);
            $cleared = $this->plugin_instance->get_cache($cache_key);
            $this->assert($cleared === false, "Cache limpo corretamente");
            
            $this->log_success("Sistema de cache");
            
        } catch (Exception $e) {
            $this->log_error("Sistema de cache", $e->getMessage());
        }
    }
    
    private function test_amp_support() {
        $this->log_test("Testando suporte AMP");
        
        try {
            // Verificar se a classe AMP_Support existe
            $this->assert(class_exists('AMP_Support'), "Classe AMP_Support existe");
            
            // Testar conversão de anúncio para AMP
            if (class_exists('AMP_Support')) {
                $amp_support = new AMP_Support();
                $regular_ad = '<script>google_ad_client="ca-pub-123";</script>';
                $amp_ad = $amp_support->convert_to_amp_ad($regular_ad);
                $this->assert(strpos($amp_ad, 'amp-ad') !== false, "Conversão para AMP funcionando");
            }
            
            $this->log_success("Suporte AMP");
            
        } catch (Exception $e) {
            $this->log_error("Suporte AMP", $e->getMessage());
        }
    }
    
    private function test_security_features() {
        $this->log_test("Testando recursos de segurança");
        
        try {
            // Testar sanitização de dados
            $malicious_code = '<script>alert("xss")</script>';
            $sanitized = sanitize_text_field($malicious_code);
            $this->assert(strpos($sanitized, '<script>') === false, "Sanitização funcionando");
            
            // Testar verificação de nonce (simulado)
            $this->assert(function_exists('wp_verify_nonce'), "Função wp_verify_nonce disponível");
            
            $this->log_success("Recursos de segurança");
            
        } catch (Exception $e) {
            $this->log_error("Recursos de segurança", $e->getMessage());
        }
    }
    
    private function test_gdpr_compliance() {
        $this->log_test("Testando conformidade GDPR");
        
        try {
            $options = get_option('amp_options', array());
            
            // Verificar se as opções GDPR existem
            $gdpr_options = array('gdpr_compliance', 'gdpr_consent_message', 'gdpr_privacy_policy_url');
            
            foreach ($gdpr_options as $option) {
                $this->assert(array_key_exists($option, $options), "Opção GDPR $option existe");
            }
            
            $this->log_success("Conformidade GDPR");
            
        } catch (Exception $e) {
            $this->log_error("Conformidade GDPR", $e->getMessage());
        }
    }
    
    private function assert($condition, $message) {
        if (!$condition) {
            throw new Exception("Falha na asserção: $message");
        }
        return true;
    }
    
    private function log_test($test_name) {
        echo "<h3>🧪 $test_name</h3>\n";
    }
    
    private function log_success($test_name) {
        echo "<p style='color: green;'>✅ $test_name: PASSOU</p>\n";
        $this->results[$test_name] = 'PASSOU';
    }
    
    private function log_error($test_name, $error) {
        echo "<p style='color: red;'>❌ $test_name: FALHOU - $error</p>\n";
        $this->results[$test_name] = 'FALHOU: ' . $error;
    }
    
    private function display_results() {
        echo "<h2>Resumo dos Testes</h2>\n";
        
        $passed = 0;
        $failed = 0;
        
        foreach ($this->results as $test => $result) {
            if (strpos($result, 'FALHOU') === false) {
                $passed++;
            } else {
                $failed++;
            }
        }
        
        $total = $passed + $failed;
        $percentage = $total > 0 ? round(($passed / $total) * 100, 2) : 0;
        
        echo "<div style='background: #f0f0f0; padding: 20px; border-radius: 5px;'>\n";
        echo "<h3>Estatísticas</h3>\n";
        echo "<p><strong>Total de testes:</strong> $total</p>\n";
        echo "<p><strong>Testes aprovados:</strong> <span style='color: green;'>$passed</span></p>\n";
        echo "<p><strong>Testes falharam:</strong> <span style='color: red;'>$failed</span></p>\n";
        echo "<p><strong>Taxa de sucesso:</strong> $percentage%</p>\n";
        echo "</div>\n";
        
        if ($failed > 0) {
            echo "<h3>Testes que Falharam:</h3>\n";
            echo "<ul>\n";
            foreach ($this->results as $test => $result) {
                if (strpos($result, 'FALHOU') !== false) {
                    echo "<li style='color: red;'>$test: $result</li>\n";
                }
            }
            echo "</ul>\n";
        }
    }
    
    // Método para executar teste específico
    public function run_specific_test($test_name) {
        $method_name = 'test_' . $test_name;
        
        if (method_exists($this, $method_name)) {
            echo "<h2>Executando Teste Específico: $test_name</h2>\n";
            $this->$method_name();
            $this->display_results();
        } else {
            echo "<p style='color: red;'>Teste '$test_name' não encontrado.</p>\n";
        }
    }
    
    // Método para benchmark de performance
    public function benchmark_performance() {
        echo "<h2>Benchmark de Performance</h2>\n";
        
        $start_time = microtime(true);
        $start_memory = memory_get_usage();
        
        // Simular carregamento de anúncios
        global $wpdb;
        for ($i = 0; $i < 100; $i++) {
            $wpdb->get_results("SELECT * FROM {$wpdb->prefix}amp_ads WHERE status = 'active' LIMIT 10");
        }
        
        $end_time = microtime(true);
        $end_memory = memory_get_usage();
        
        $execution_time = round(($end_time - $start_time) * 1000, 2);
        $memory_used = round(($end_memory - $start_memory) / 1024, 2);
        
        echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 5px;'>\n";
        echo "<h3>Resultados do Benchmark</h3>\n";
        echo "<p><strong>Tempo de execução:</strong> {$execution_time}ms</p>\n";
        echo "<p><strong>Memória utilizada:</strong> {$memory_used}KB</p>\n";
        echo "<p><strong>Consultas por segundo:</strong> " . round(100 / ($execution_time / 1000), 2) . "</p>\n";
        echo "</div>\n";
    }
}

// Função para executar os testes via admin
function amp_run_tests() {
    if (!current_user_can('manage_options')) {
        wp_die('Você não tem permissão para executar testes.');
    }
    
    $tester = new AMP_Plugin_Tests();
    
    if (isset($_GET['test']) && $_GET['test'] === 'specific' && isset($_GET['name'])) {
        $tester->run_specific_test(sanitize_text_field($_GET['name']));
    } elseif (isset($_GET['test']) && $_GET['test'] === 'benchmark') {
        $tester->benchmark_performance();
    } else {
        $tester->run_all_tests();
    }
}

// Adicionar página de testes no admin
add_action('admin_menu', function() {
    add_submenu_page(
        'adsense-master-pro',
        'Testes do Plugin',
        'Testes',
        'manage_options',
        'amp-tests',
        'amp_run_tests'
    );
});