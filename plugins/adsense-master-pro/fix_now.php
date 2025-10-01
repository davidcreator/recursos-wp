<?php
// Script para corrigir o problema do método preload_resources

// Caminho para o arquivo principal do plugin
$plugin_file = __DIR__ . '/adsense-master-pro.php';

// Verificar se o arquivo existe
if (!file_exists($plugin_file)) {
    die("Arquivo do plugin não encontrado!");
}

// Ler o conteúdo do arquivo
$content = file_get_contents($plugin_file);

// Verificar se o método preload_resources está no lugar correto
if (strpos($content, 'public function preload_resources()') !== false) {
    // O método existe, mas pode estar em um local incorreto ou com problemas
    
    // Definir o novo método
    $new_method = <<<'EOD'
    /**
     * Preload critical resources for better performance
     */
    public function preload_resources() {
        // Pré-carregar recursos do Google AdSense para melhorar o desempenho
        echo '<link rel="preconnect" href="https://pagead2.googlesyndication.com">'."\n";
        echo '<link rel="preconnect" href="https://googleads.g.doubleclick.net">'."\n";
        echo '<link rel="preconnect" href="https://tpc.googlesyndication.com">'."\n";
        echo '<link rel="preconnect" href="https://www.google-analytics.com">'."\n";
        echo '<link rel="preconnect" href="https://adservice.google.com">'."\n";
        
        // Pré-carregar o script do AdSense
        if (!empty($this->settings['publisher_id'])) {
            echo '<link rel="preload" as="script" href="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client='.esc_attr($this->settings['publisher_id']).'">'."\n";
        } else {
            echo '<link rel="preload" as="script" href="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js">'."\n";
        }
    }
EOD;

    // Remover o método existente
    $pattern = '/\/\*\*\s*\n\s*\*\s*Preload critical resources for better performance\s*\n\s*\*\/\s*\n\s*public function preload_resources\(\)\s*\{.*?\}/s';
    $content = preg_replace($pattern, '', $content);
    
    // Adicionar o novo método após o método init
    $pattern = '/public function init\(\)\s*\{.*?\}/s';
    if (preg_match($pattern, $content, $matches)) {
        $content = str_replace($matches[0], $matches[0] . "\n\n" . $new_method, $content);
    } else {
        // Se não encontrar o método init, adicionar após o construtor
        $pattern = '/private function __construct\(\)\s*\{.*?\}/s';
        if (preg_match($pattern, $content, $matches)) {
            $content = str_replace($matches[0], $matches[0] . "\n\n" . $new_method, $content);
        }
    }
    
    // Verificar se o hook está registrado corretamente
    $hook_pattern = '/add_action\s*\(\s*[\'"]wp_head[\'"]\s*,\s*array\s*\(\s*\$this\s*,\s*[\'"]preload_resources[\'"]\s*\)\s*,\s*\d+\s*\)\s*;/';
    
    // Se o hook não estiver registrado, adicionar ao método init_hooks
    if (!preg_match($hook_pattern, $content)) {
        $init_hooks_pattern = '/private function init_hooks\(\)\s*\{/';
        if (preg_match($init_hooks_pattern, $content, $matches)) {
            $replacement = $matches[0] . "\n        // Performance Optimization\n        add_action('wp_head', array(\$this, 'preload_resources'), 1);";
            $content = str_replace($matches[0], $replacement, $content);
        }
    }
    
    // Salvar as alterações
    file_put_contents($plugin_file, $content);
    
    echo "Método preload_resources corrigido e hook registrado com sucesso!\n";
} else {
    // O método não existe, vamos adicioná-lo
    $new_method = <<<'EOD'
    /**
     * Preload critical resources for better performance
     */
    public function preload_resources() {
        // Pré-carregar recursos do Google AdSense para melhorar o desempenho
        echo '<link rel="preconnect" href="https://pagead2.googlesyndication.com">'."\n";
        echo '<link rel="preconnect" href="https://googleads.g.doubleclick.net">'."\n";
        echo '<link rel="preconnect" href="https://tpc.googlesyndication.com">'."\n";
        echo '<link rel="preconnect" href="https://www.google-analytics.com">'."\n";
        echo '<link rel="preconnect" href="https://adservice.google.com">'."\n";
        
        // Pré-carregar o script do AdSense
        if (!empty($this->settings['publisher_id'])) {
            echo '<link rel="preload" as="script" href="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client='.esc_attr($this->settings['publisher_id']).'">'."\n";
        } else {
            echo '<link rel="preload" as="script" href="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js">'."\n";
        }
    }
EOD;

    // Adicionar o método após o método init
    $pattern = '/public function init\(\)\s*\{.*?\}/s';
    if (preg_match($pattern, $content, $matches)) {
        $content = str_replace($matches[0], $matches[0] . "\n\n" . $new_method, $content);
    } else {
        // Se não encontrar o método init, adicionar após o construtor
        $pattern = '/private function __construct\(\)\s*\{.*?\}/s';
        if (preg_match($pattern, $content, $matches)) {
            $content = str_replace($matches[0], $matches[0] . "\n\n" . $new_method, $content);
        }
    }
    
    // Verificar se o hook está registrado corretamente
    $hook_pattern = '/add_action\s*\(\s*[\'"]wp_head[\'"]\s*,\s*array\s*\(\s*\$this\s*,\s*[\'"]preload_resources[\'"]\s*\)\s*,\s*\d+\s*\)\s*;/';
    
    // Se o hook não estiver registrado, adicionar ao método init_hooks
    if (!preg_match($hook_pattern, $content)) {
        $init_hooks_pattern = '/private function init_hooks\(\)\s*\{/';
        if (preg_match($init_hooks_pattern, $content, $matches)) {
            $replacement = $matches[0] . "\n        // Performance Optimization\n        add_action('wp_head', array(\$this, 'preload_resources'), 1);";
            $content = str_replace($matches[0], $replacement, $content);
        }
    }
    
    // Salvar as alterações
    file_put_contents($plugin_file, $content);
    
    echo "Método preload_resources adicionado e hook registrado com sucesso!\n";
}

// Limpar o cache do OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache limpo com sucesso!\n";
}

echo "Correção concluída. Por favor, recarregue a página para verificar se o erro foi resolvido.";